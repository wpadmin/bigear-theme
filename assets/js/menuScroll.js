export function scrollMenu() {
    /**
     * Скрипт для плавной прокрутки элементов списка внутри <ul id="menu-primary">
     * Прокручивает пункты меню вверх, плавно перемещая первый элемент в конец списка через заданный интервал.
     * Подключить после загрузки DOM.
     */

    document.addEventListener("DOMContentLoaded", function () {
        const menu = document.getElementById("menu-primary");
        if (!menu) return;

        // Высота одного элемента меню (li)
        function getItemHeight() {
            const first = menu.querySelector('li');
            return first ? first.offsetHeight : 0;
        }

        // Основная функция для анимации прокрутки одного элемента
        function scrollMenu() {
            const firstItem = menu.querySelector('li');
            const itemHeight = getItemHeight();

            if (!firstItem || itemHeight === 0) return;

            // Устанавливаем начальные стили
            menu.style.transition = "none";
            menu.style.transform = "translateY(0)";

            // Запуск анимации
            requestAnimationFrame(() => {
                menu.style.transition = "transform 0.5s cubic-bezier(0.4,0,0.2,1)";
                menu.style.transform = `translateY(-${itemHeight}px)`;

                // После завершения анимации перемещаем первый элемент в конец и сбрасываем сдвиг
                setTimeout(() => {
                    menu.style.transition = "none";
                    menu.style.transform = "translateY(0)";
                    menu.appendChild(firstItem);
                }, 500);
            });
        }

        // Запуск прокрутки каждые 2 секунды
        setInterval(scrollMenu, 2000);
    });
}