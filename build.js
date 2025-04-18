const esbuild = require('esbuild');
const { sassPlugin } = require('esbuild-sass-plugin');
const fs = require('fs-extra');
const path = require('path');
const { PurgeCSS } = require('purgecss');
const gaze = require('gaze'); // Заменяем chokidar на gaze

// Функция обновления версии в style.css
async function updateThemeVersion() {
    const stylePath = path.join(process.cwd(), 'style.css');
    if (await fs.pathExists(stylePath)) {
        let content = await fs.readFile(stylePath, 'utf8');
        const buildNumber = Math.floor(Date.now() / 1000); // Используем timestamp

        // Обновляем версию в style.css
        content = content.replace(
            /Version: .*$/m,
            `Version: ${process.env.npm_package_version}.${buildNumber}`
        );

        await fs.writeFile(stylePath, content);
        console.log('✅ Версия темы обновлена');
    }
}

// Конфигурация
const config = {
    srcDir: './assets',
    outDir: './public',
    entryPoints: {
        js: './assets/js/main.js',
        css: './assets/css/main.scss'
    }
};

// Очистка директории и копирование изображений
async function setupDirectory() {
    await fs.emptyDir(config.outDir);
    console.log('🧹 Очистка public директории выполнена');

    const imagesDir = path.join(config.srcDir, 'images');
    const publicImagesDir = path.join(config.outDir, 'images');

    if (await fs.pathExists(imagesDir)) {
        await fs.copy(imagesDir, publicImagesDir);
        console.log('🖼️  Изображения скопированы');
    }
}

// Функция для PurgeCSS
async function purgeAndTransformCSS(css) {
    try {
        const purgeCSSInstance = new PurgeCSS();
        const purgeCSSResult = await purgeCSSInstance.purge({
            content: [
                './**/*.php',
                './assets/js/**/*.js'
            ],
            css: [{
                raw: css,
                extension: 'scss'
            }],
            safelist: {
                standard: [
                    /^wp-/,
                    /^has-/,
                    /^is-/,
                    /^align/,
                    /^theme-/
                ],
                deep: [/editor-styles-wrapper$/],
                greedy: [/^wp-block-/]
            },
            defaultExtractor: content => {
                return content.match(/[A-Za-z0-9-_/:]*[A-Za-z0-9-_/]+/g) || [];
            }
        });

        return purgeCSSResult[0]?.css || css;
    } catch (error) {
        console.error('PurgeCSS Error:', error);
        return css; // Возвращаем оригинальный CSS в случае ошибки
    }
}

// Базовая конфигурация esbuild
const baseConfig = {
    bundle: true,
    minify: process.env.NODE_ENV === 'production',
    sourcemap: process.env.NODE_ENV !== 'production',
    loader: {
        '.png': 'file',
        '.jpg': 'file',
        '.jpeg': 'file',
        '.gif': 'file',
        '.svg': 'file',
        '.woff': 'file',
        '.woff2': 'file',
        '.eot': 'file',
        '.ttf': 'file'
    },
    assetNames: 'assets/[name]-[hash]',
    plugins: [
        sassPlugin({
            async transform(source) {
                return await purgeAndTransformCSS(source);
            }
        })
    ]
};

// Функция полной пересборки при изменении PHP файлов
async function fullRebuild() {
    console.log('🔄 PHP файл изменен, запуск полной пересборки...');

    try {
        // Пересобираем CSS
        await esbuild.build({
            ...baseConfig,
            entryPoints: [config.entryPoints.css],
            outdir: path.join(config.outDir, 'css'),
            logLevel: 'info'
        });

        console.log('✅ Пересборка CSS завершена');
    } catch (error) {
        console.error('❌ Ошибка при пересборке:', error);
    }
}

// Функция сборки
async function build({ watch = false } = {}) {
    try {
        await setupDirectory();

        // Обновляем версию только при production сборке
        if (process.env.NODE_ENV === 'production') {
            await updateThemeVersion();
        }

        const commonConfig = {
            ...baseConfig,
            logLevel: 'info',
            metafile: true
        };

        if (watch) {
            // Создаем контекст для JS
            const jsContext = await esbuild.context({
                ...commonConfig,
                entryPoints: [config.entryPoints.js],
                outdir: path.join(config.outDir, 'js')
            });

            // Создаем контекст для CSS
            const cssContext = await esbuild.context({
                ...commonConfig,
                entryPoints: [config.entryPoints.css],
                outdir: path.join(config.outDir, 'css')
            });

            // Запускаем watch режим для JS и CSS
            await Promise.all([
                jsContext.watch(),
                cssContext.watch()
            ]);

            console.log('👀 Watching for changes in JS and CSS files...');

            // Настройка наблюдения за PHP файлами с использованием gaze
            const phpPattern = './**/*.php';
            let gazeWatcher = null;

            // Функция для запуска наблюдения
            function startPhpWatcher() {
                gaze(phpPattern, { debounceDelay: 300, interval: 300 }, function (err, watcher) {
                    if (err) {
                        console.error('❌ Ошибка инициализации gaze:', err);
                        return;
                    }

                    gazeWatcher = this;
                    console.log('✅ PHP watcher initialized and ready');

                    // Событие при изменении файла
                    this.on('all', (event, filepath) => {
                        console.log(`📄 PHP файл ${event}: ${filepath}`);
                        fullRebuild();
                    });

                    // Событие при добавлении новых файлов
                    this.on('added', filepath => {
                        console.log(`➕ Новый PHP файл обнаружен: ${filepath}`);
                    });

                    // Событие при удалении файлов
                    this.on('deleted', filepath => {
                        console.log(`❌ PHP файл удален: ${filepath}`);
                    });
                });
            }

            // Запускаем наблюдение за PHP файлами
            startPhpWatcher();
            console.log('👀 Watching for changes in PHP files...');

            // Обработка завершения
            const cleanup = async () => {
                await Promise.all([
                    jsContext.dispose(),
                    cssContext.dispose()
                ]);

                // Закрываем gaze watcher
                if (gazeWatcher) {
                    gazeWatcher.close();
                }

                console.log('👋 Cleaned up and exiting');
                process.exit(0);
            };

            process.on('SIGINT', cleanup);
            process.on('SIGTERM', cleanup);

        } else {
            // Обычная сборка без watch режима
            await Promise.all([
                esbuild.build({
                    ...commonConfig,
                    entryPoints: [config.entryPoints.js],
                    outdir: path.join(config.outDir, 'js')
                }),
                esbuild.build({
                    ...commonConfig,
                    entryPoints: [config.entryPoints.css],
                    outdir: path.join(config.outDir, 'css')
                })
            ]);

            console.log('✅ Сборка завершена');
        }
    } catch (error) {
        console.error('❌ Ошибка:', error);
        process.exit(1);
    }
}

// Запуск сборки
const args = process.argv.slice(2);
const watchMode = args.includes('--watch');
build({ watch: watchMode }).catch(error => {
    console.error('❌ Критическая ошибка:', error);
    process.exit(1);
});