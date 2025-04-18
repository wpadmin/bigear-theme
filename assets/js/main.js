// Импортируем компоненты Bootstrap JS
import Collapse from 'bootstrap/js/dist/collapse';
import Modal from 'bootstrap/js/dist/modal';
import Tooltip from 'bootstrap/js/dist/tooltip';
import Popover from 'bootstrap/js/dist/popover';

// или импортируйте только нужные компоненты:
/*
import 'bootstrap/js/dist/alert';
import 'bootstrap/js/dist/button';
import 'bootstrap/js/dist/carousel';
import 'bootstrap/js/dist/collapse';
import 'bootstrap/js/dist/dropdown';
import 'bootstrap/js/dist/modal';
import 'bootstrap/js/dist/offcanvas';
import 'bootstrap/js/dist/popover';
import 'bootstrap/js/dist/scrollspy';
import 'bootstrap/js/dist/tab';
import 'bootstrap/js/dist/toast';
import 'bootstrap/js/dist/tooltip';
*/

// Инициализация Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    try {
        // Инициализация всех элементов с классом collapse
        const collapseElementList = document.querySelectorAll('.collapse');
        if (collapseElementList.length > 0) {
            const collapseList = Array.from(collapseElementList).map(collapseEl => {
                return new Collapse(collapseEl, {
                    toggle: false // По умолчанию меню будет скрыто
                });
            });
            console.log(`Инициализировано ${collapseList.length} collapse элементов`);
        } else {
            console.info('На странице не найдено элементов с классом .collapse');
        }

        // Инициализация всех модальных окон на странице
        const modalElementList = document.querySelectorAll('.modal');
        if (modalElementList.length > 0) {
            const modalList = Array.from(modalElementList).map(modalEl => {
                return new Modal(modalEl, {
                    // Здесь можно указать дополнительные опции при необходимости
                    // backdrop: 'static',
                    // keyboard: false
                });
            });
            console.log(`Инициализировано ${modalList.length} модальных окон`);
        } else {
            console.info('На странице не найдено элементов с классом .modal');
        }

        // Специфическая инициализация для навбара
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        if (navbarToggler && navbarCollapse) {
            // Этот код можно использовать, если вам нужна дополнительная
            // кастомная функциональность для навбара
            navbarToggler.addEventListener('click', () => {
                // Например, добавление анимации или других эффектов
                // при открытии/закрытии меню
            });
            console.log('Навбар успешно инициализирован');
        } else {
            console.info('Элементы навбара не найдены на странице');
        }

        // Инициализация тултипов
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length > 0) {
            const tooltipList = Array.from(tooltipTriggerList).map(tooltipTriggerEl =>
                new Tooltip(tooltipTriggerEl)
            );
            console.log(`Инициализировано ${tooltipList.length} тултипов`);
        } else {
            console.info('На странице не найдено элементов с атрибутом data-bs-toggle="tooltip"');
        }

        // Инициализация поповеров
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        if (popoverTriggerList.length > 0) {
            const popoverList = Array.from(popoverTriggerList).map(popoverTriggerEl =>
                new Popover(popoverTriggerEl)
            );
            console.log(`Инициализировано ${popoverList.length} поповеров`);
        } else {
            console.info('На странице не найдено элементов с атрибутом data-bs-toggle="popover"');
        }

        console.log('Bootstrap компоненты успешно инициализированы');
    } catch (error) {
        console.error('Произошла ошибка при инициализации Bootstrap компонентов:', error);
    }
});


// Плеер
document.addEventListener('DOMContentLoaded', function () {
    // Получаем ссылки на элементы DOM
    const audioElement = document.getElementById('audio-element');
    const playPauseBtn = document.getElementById('play-pause-btn');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const currentTimeElement = document.getElementById('current-time');
    const durationElement = document.getElementById('duration');
    const volumeSlider = document.getElementById('volume-slider');

    // Проверяем наличие аудиоэлемента на странице
    if (!audioElement) {
        console.info('Аудиоплеер не инициализирован: элемент #audio-element не найден на странице');
        return; // Завершаем выполнение функции, если аудиоэлемент отсутствует
    }

    // Проверяем наличие остальных необходимых элементов управления
    if (!playPauseBtn || !progressContainer || !progressBar || !currentTimeElement || !durationElement || !volumeSlider) {
        console.warn('Некоторые элементы управления аудиоплеером не найдены на странице. Функциональность может быть ограничена.');
        // Продолжаем выполнение, но будем проверять каждый элемент перед использованием
    }

    // Форматирование времени в минуты:секунды
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    // Обновление прогресса воспроизведения
    function updateProgress() {
        if (progressBar && currentTimeElement && audioElement.duration) {
            const percent = (audioElement.currentTime / audioElement.duration) * 100;
            progressBar.style.width = `${percent}%`;
            currentTimeElement.textContent = formatTime(audioElement.currentTime);
        }
    }

    // Воспроизведение/пауза при нажатии на кнопку
    if (playPauseBtn) {
        playPauseBtn.addEventListener('click', function () {
            if (audioElement.paused) {
                audioElement.play();
                playPauseBtn.textContent = '❚❚';
            } else {
                audioElement.pause();
                playPauseBtn.textContent = '▶';
            }
        });
    }

    // Перемотка при клике на прогресс-бар
    if (progressContainer) {
        progressContainer.addEventListener('click', function (e) {
            if (audioElement.duration) {
                const clickPosition = (e.offsetX / this.offsetWidth);
                const seekTime = clickPosition * audioElement.duration;
                audioElement.currentTime = seekTime;
            }
        });
    }

    // Изменение громкости
    if (volumeSlider) {
        volumeSlider.addEventListener('input', function () {
            audioElement.volume = this.value;
        });
    }

    // Обновление времени и прогресс-бара
    audioElement.addEventListener('timeupdate', updateProgress);

    // Отображение длительности после загрузки метаданных
    audioElement.addEventListener('loadedmetadata', function () {
        if (durationElement) {
            durationElement.textContent = formatTime(audioElement.duration);
        }
    });

    // Сброс кнопки воспроизведения после окончания трека
    audioElement.addEventListener('ended', function () {
        if (playPauseBtn) {
            playPauseBtn.textContent = '▶';
        }
    });

    console.log('Инициализация аудиоплеера завершена успешно');
});