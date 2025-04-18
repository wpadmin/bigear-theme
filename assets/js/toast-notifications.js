// toast-notifications.js

// База данных имен в формате JSON
const namesData = {
    "male": [
        "Александр", "Алексей", "Анатолий", "Андрей", "Антон", "Аркадий", "Артем", "Борис",
        "Вадим", "Валентин", "Валерий", "Василий", "Виктор", "Виталий", "Владимир", "Владислав",
        "Геннадий", "Георгий", "Глеб", "Григорий", "Даниил", "Денис", "Дмитрий", "Евгений",
        "Егор", "Иван", "Игорь", "Илья", "Кирилл", "Константин", "Лев", "Леонид",
        "Максим", "Матвей", "Михаил", "Никита", "Николай", "Олег", "Павел", "Петр",
        "Роман", "Руслан", "Сергей", "Станислав", "Степан", "Тимофей", "Федор", "Юрий"
    ],
    "female": [
        "Александра", "Алина", "Алла", "Анастасия", "Анна", "Валентина", "Валерия", "Вера",
        "Виктория", "Галина", "Дарья", "Диана", "Евгения", "Екатерина", "Елена", "Елизавета",
        "Ирина", "Карина", "Кира", "Кристина", "Ксения", "Лариса", "Лидия", "Любовь",
        "Людмила", "Маргарита", "Марина", "Мария", "Надежда", "Наталья", "Нина", "Оксана",
        "Олеся", "Ольга", "Полина", "Раиса", "Светлана", "София", "Тамара", "Татьяна",
        "Ульяна", "Юлия", "Яна"
    ]
};

// Список товаров/книг для разных типов уведомлений
const productsData = {
    "orders": [
        "смартфон Samsung Galaxy", "ноутбук Apple MacBook", "наушники Sony", "планшет iPad",
        "умные часы Garmin", "электрическую зубную щетку", "фитнес-браслет", "кофемашину",
        "робот-пылесос", "игровую приставку", "беспроводные наушники", "электросамокат",
        "увлажнитель воздуха", "мультиварку", "телевизор LG OLED"
    ],
    "books": [
        "книгу 'Мастер и Маргарита'", "роман 'Война и мир'", "сборник стихов Есенина",
        "детектив Акунина", "научную литературу по физике", "книгу по программированию",
        "историческую книгу о Второй мировой", "биографию Стива Джобса",
        "фантастический роман 'Дюна'", "учебник по маркетингу", "кулинарную книгу",
        "сборник рассказов Чехова", "книгу о саморазвитии", "сказки для детей",
        "атлас мира", "медицинскую энциклопедию"
    ]
};

// Функция для получения случайного элемента из массива
function getRandomItem(array) {
    return array[Math.floor(Math.random() * array.length)];
}

// Функция для получения случайного имени
function getRandomName() {
    // Случайно выбираем пол
    const gender = Math.random() > 0.5 ? "male" : "female";
    return getRandomItem(namesData[gender]);
}

// Функция для создания случайного сообщения о заказе
function generateOrderMessage() {
    const name = getRandomName();
    const product = getRandomItem(productsData.orders);
    return `${name} сделал${name.endsWith('а') ? 'а' : ''} заказ на ${product}`;
}

// Функция для создания случайного сообщения о просмотре книги
function generateBookViewMessage() {
    const name = getRandomName();
    const book = getRandomItem(productsData.books);
    return `${name} смотрит ${book}`;
}

// Функция для создания и показа уведомления
function showToast(message) {
    // Проверяем, инициализирован ли bootstrap
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap не найден. Убедитесь, что bootstrap подключен до вызова showToast');
        return;
    }

    // Создаем элемент Toast
    const toastElement = document.createElement('div');
    toastElement.className = 'toast';
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');

    // Генерируем случайное время для отображения
    const minutes = Math.floor(Math.random() * 30) + 1;

    // Создаем содержимое тоста
    toastElement.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">Активность на сайте</strong>
            <small class="text-muted">${minutes} мин. назад</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;

    // Ищем или создаем контейнер для тостов
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    // Добавляем тост в контейнер
    toastContainer.appendChild(toastElement);

    // Инициализируем тост через Bootstrap
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });

    // Показываем тост
    toast.show();

    // Удаляем тост после скрытия
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

// Функция для определения типа страницы WordPress
function isWordPressSinglePage() {
    // В WordPress обычно на одиночных записях есть класс .single
    return document.body.classList.contains('single') ||
        document.querySelector('.single-post') !== null ||
        document.querySelector('article.post') !== null;
}

// Функция для определения архивной страницы WordPress
function isWordPressArchivePage() {
    // На архивных страницах обычно есть класс .archive
    return document.body.classList.contains('archive') ||
        document.body.classList.contains('category') ||
        document.body.classList.contains('tag') ||
        document.querySelector('.archive') !== null;
}

// Функция для инициализации показа тостов
function initToasts() {
    // Добавляем стили для toast-container, если их еще нет
    if (!document.querySelector('style#toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            .toast-container {
                position: fixed;
                bottom: 15px;
                right: 15px;
                z-index: 999;
            }
        `;
        document.head.appendChild(style);
    }

    // Проверяем тип страницы и показываем соответствующие уведомления
    if (isWordPressSinglePage()) {
        // На одиночных страницах показываем сообщения о просмотре книги
        showRandomToasts(generateBookViewMessage);
    } else if (isWordPressArchivePage()) {
        // На архивных страницах показываем сообщения о заказах
        showRandomToasts(generateOrderMessage);
    }
}

// Функция для показа случайных тостов через случайные интервалы
function showRandomToasts(messageGenerator) {
    // Показываем первый тост через 3 секунды после загрузки страницы
    setTimeout(() => {
        showToast(messageGenerator());

        // Запускаем интервал для последующих тостов
        setInterval(() => {
            showToast(messageGenerator());
        }, Math.random() * 25000 + 15000); // Случайный интервал от 15 до 40 секунд
    }, 3000);
}

// Экспортируем функцию инициализации для использования в других модулях
export { initToasts };