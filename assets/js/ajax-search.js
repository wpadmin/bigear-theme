/**
 * AJAX-поиск с автозаполнением для WordPress
 */
document.addEventListener('DOMContentLoaded', function () {
    // Ищем все формы поиска на странице
    const searchForms = document.querySelectorAll('.ajax-search-form');

    searchForms.forEach(form => {
        const searchInput = form.querySelector('#ajax-search-input');
        const resultsContainer = form.closest('.search-container').querySelector('.ajax-search-results');
        const spinner = form.querySelector('.spinner-border');
        const buttonText = form.querySelector('.search-text');

        // Таймер для дебаунса запросов
        let searchTimer;

        // Минимальное количество символов для начала поиска
        const minChars = parseInt(ajax_search_params.min_chars) || 2;

        // Обработчик ввода в поле поиска
        searchInput.addEventListener('input', function () {
            // Очищаем предыдущий таймер
            clearTimeout(searchTimer);

            const searchValue = this.value.trim();

            // Скрываем результаты, если поле пустое
            if (searchValue.length === 0) {
                resultsContainer.classList.add('d-none');
                return;
            }

            // Устанавливаем новый таймер для дебаунса
            searchTimer = setTimeout(() => {
                if (searchValue.length >= minChars) {
                    performSearch(searchValue);
                }
            }, 300); // 300ms паузы перед запросом
        });

        // Обработчик фокуса на поле поиска
        searchInput.addEventListener('focus', function () {
            // Если есть содержимое и длина >= минимальной, показываем результаты
            const searchValue = this.value.trim();
            if (searchValue.length >= minChars && !resultsContainer.classList.contains('d-none')) {
                resultsContainer.classList.remove('d-none');
            }
        });

        // Обработчик клика вне поля поиска и результатов
        document.addEventListener('click', function (event) {
            if (!form.contains(event.target) && !resultsContainer.contains(event.target)) {
                resultsContainer.classList.add('d-none');
            }
        });

        // Обработчик отправки формы
        form.addEventListener('submit', function (event) {
            const searchValue = searchInput.value.trim();

            // Если поле пустое или содержит менее 2 символов, отменяем отправку
            if (searchValue.length < minChars) {
                event.preventDefault();
                return false;
            }

            // Если есть видимые результаты, и пользователь не выбрал конкретный результат,
            // можно отправить форму для перехода на страницу со всеми результатами
        });

        // Функция выполнения AJAX-поиска
        function performSearch(searchQuery) {
            // Показываем спиннер и скрываем текст кнопки
            spinner.classList.remove('d-none');
            buttonText.classList.add('d-none');

            // Формируем данные для запроса
            const formData = new FormData();
            formData.append('action', 'ajax_search');
            formData.append('security', ajax_search_params.ajax_nonce);
            formData.append('search_query', searchQuery);

            // Выполняем AJAX-запрос
            fetch(ajax_search_params.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Скрываем спиннер и показываем текст кнопки
                    spinner.classList.add('d-none');
                    buttonText.classList.remove('d-none');

                    // Очищаем контейнер результатов
                    resultsContainer.innerHTML = '';

                    if (data.success) {
                        // Показываем контейнер результатов
                        resultsContainer.classList.remove('d-none');

                        // Добавляем заголовок результатов
                        const resultsHeader = document.createElement('div');
                        resultsHeader.className = 'results-header pb-2 mb-2 border-bottom';
                        resultsHeader.innerHTML = `<small class="text-muted">${data.data.message}</small>`;
                        resultsContainer.appendChild(resultsHeader);

                        // Добавляем результаты
                        data.data.results.forEach(result => {
                            const resultCard = createResultCard(result);
                            resultsContainer.appendChild(resultCard);
                        });
                    } else {
                        // Если ничего не найдено, показываем сообщение
                        resultsContainer.classList.remove('d-none');
                        const noResults = document.createElement('div');
                        noResults.className = 'no-results-message';
                        noResults.textContent = data.data.message || 'Ничего не найдено';
                        resultsContainer.appendChild(noResults);
                    }
                })
                .catch(error => {
                    console.error('Ошибка AJAX-запроса:', error);
                    // Скрываем спиннер и показываем текст кнопки
                    spinner.classList.add('d-none');
                    buttonText.classList.remove('d-none');
                });
        }

        // Функция создания карточки для результата поиска
        function createResultCard(result) {
            const card = document.createElement('div');
            card.className = 'search-result-card card mb-2';

            // Создаем разметку карточки
            card.innerHTML = `
                <div class="row g-0">
                    <div class="col-3 col-md-2">
                        <img src="${result.thumbnail}" class="img-fluid rounded-start" alt="${result.title}" style="object-fit: cover; height: 100%; min-height: 60px;">
                    </div>
                    <div class="col-9 col-md-10">
                        <div class="card-body py-2 px-3">
                            <h6 class="card-title mb-1">
                                <a href="${result.url}" class="text-decoration-none stretched-link">${result.title}</a>
                            </h6>
                            <p class="card-text small mb-1">${result.excerpt}</p>
                            <p class="card-text"><small class="text-muted">${result.date}</small></p>
                        </div>
                    </div>
                </div>
            `;

            // Обработчик клика для перехода по ссылке
            card.addEventListener('click', function () {
                window.location.href = result.url;
            });

            return card;
        }
    });
});