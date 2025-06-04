// Импортируем компоненты Bootstrap JS
import Collapse from 'bootstrap/js/dist/collapse';
import Modal from 'bootstrap/js/dist/modal';
import Tooltip from 'bootstrap/js/dist/tooltip';
import Popover from 'bootstrap/js/dist/popover';
import Dropdown from 'bootstrap/js/dist/dropdown'; // Добавляем для выпадающих меню
import Offcanvas from 'bootstrap/js/dist/offcanvas'; // Добавляем для мобильного меню

import { scrollMenu } from './menuScroll.js';

document.addEventListener('DOMContentLoaded', () => {
    scrollMenu();
});

// Инициализация Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    try {
        // Инициализация collapse элементов
        const collapseElementList = document.querySelectorAll('.collapse');
        if (collapseElementList.length > 0) {
            Array.from(collapseElementList).map(collapseEl =>
                new Collapse(collapseEl, { toggle: false })
            );
        }

        // Инициализация модальных окон
        const modalElementList = document.querySelectorAll('.modal');
        if (modalElementList.length > 0) {
            Array.from(modalElementList).map(modalEl => new Modal(modalEl));
        }

        // Инициализация тултипов
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipTriggerList.length > 0) {
            Array.from(tooltipTriggerList).map(tooltipTriggerEl =>
                new Tooltip(tooltipTriggerEl)
            );
        }

        // Инициализация поповеров
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        if (popoverTriggerList.length > 0) {
            Array.from(popoverTriggerList).map(popoverTriggerEl =>
                new Popover(popoverTriggerEl)
            );
        }

        // Инициализация выпадающих меню
        const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
        if (dropdownElementList.length > 0) {
            Array.from(dropdownElementList).map(dropdownToggleEl =>
                new Dropdown(dropdownToggleEl)
            );
        }

        // Инициализация offcanvas элементов
        const offcanvasElementList = document.querySelectorAll('[data-bs-toggle="offcanvas"]');
        if (offcanvasElementList.length > 0) {
            Array.from(offcanvasElementList).map(offcanvasEl =>
                new Offcanvas(offcanvasEl)
            );
        }

        // Дополнительная инициализация для навбара, если нужна
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        if (navbarToggler && navbarCollapse) {
            // Здесь можно добавить кастомное поведение для навбара
            // если требуется особая логика
        }

    } catch (error) {
        console.error('Ошибка при инициализации Bootstrap компонентов:', error);
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


// Оверлей с спиннером загрузки Bootstrap
document.addEventListener('DOMContentLoaded', function () {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const showSpinnerBtn = document.getElementById('showSpinnerBtn');

    // Функция для показа спиннера
    function showSpinner() {
        loadingOverlay.classList.remove('hidden');
    }

    // Функция для скрытия спиннера
    function hideSpinner() {
        loadingOverlay.classList.add('hidden');
    }

    // Автоматически скрыть спиннер через 5 секунд для случаев с медленным интернетом
    const maxLoadingTime = 5000; // 5 секунд

    const loadingTimer = setTimeout(() => {
        hideSpinner();
    }, maxLoadingTime);

    // Скрыть спиннер когда страница полностью загружена
    window.addEventListener('load', function () {
        clearTimeout(loadingTimer); // Очистим таймер, если страница загрузилась быстрее
        hideSpinner();
    });

    // Кнопка для демонстрации работы спиннера
    showSpinnerBtn.addEventListener('click', function () {
        showSpinner();
        setTimeout(hideSpinner, 2000); // Скрыть через 2 секунды для демонстрации
    });
});