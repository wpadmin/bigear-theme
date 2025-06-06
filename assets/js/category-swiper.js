(function ($) {
    'use strict';

    // Инициализация всех Swiper на странице
    function initSwipers() {
        $('.category-swiper-container').each(function () {
            const container = $(this);
            const swiperId = container.data('swiper-id');
            const categoryId = container.data('category');
            let currentPage = 1;
            let maxPages = container.data('max');
            let isLoading = false;

            // Инициализация Swiper
            const swiper = new Swiper(`#${swiperId}`, {
                slidesPerView: 1,
                spaceBetween: 20,
                pagination: {
                    el: `#${swiperId} .swiper-pagination`,
                    clickable: true,
                },
                navigation: {
                    nextEl: `#${swiperId} .swiper-button-next`,
                    prevEl: `#${swiperId} .swiper-button-prev`,
                },
                breakpoints: {
                    // когда ширина окна >= 480px
                    480: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    },

                    // когда ширина окна >= 640px
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 10
                    },
                    // когда ширина окна >= 768px
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 15
                    },
                    // когда ширина окна >= 1024px
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                },
                on: {
                    // Когда дошли до конца слайдов
                    reachEnd: function () {
                        // Если не загружаем сейчас и есть еще страницы
                        if (!isLoading && currentPage < maxPages) {
                            loadMoreSlides();
                        }
                    }
                }
            });

            // Функция для создания HTML с обрезанным текстом и градиентом
            function createTruncatedExcerpt(excerpt, length) {
                const words = excerpt.split(' ');

                if (words.length > length) {
                    const truncated = words.slice(0, length).join(' ');
                    return `<div class="truncated-excerpt"><span>${truncated}...</span><div class="excerpt-gradient"></div></div>`;
                }

                return `<div class="truncated-excerpt"><span>${excerpt}</span></div>`;
            }

            // Функция загрузки новых слайдов
            function loadMoreSlides() {
                if (isLoading) return;

                isLoading = true;

                // Показываем индикатор загрузки на кнопке вперед
                const nextButton = $(`#${swiperId} .swiper-button-next`);
                nextButton.addClass('loading');

                // Запоминаем текущую высоту слайдера
                const currentHeight = $(`#${swiperId}`).height();
                $(`#${swiperId}`).css('height', currentHeight + 'px');

                // AJAX запрос
                $.ajax({
                    url: catSwiperData.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'category_swiper_load_more',
                        nonce: catSwiperData.nonce,
                        category_id: categoryId,
                        page: currentPage + 1
                    },
                    success: function (response) {
                        if (response.success) {
                            const excerptLength = container.data('excerpt-length') || 15;

                            // Добавляем новые слайды
                            response.data.slides.forEach(function (slide) {
                                let thumbnailHtml = '';
                                if (slide.thumbnail) {
                                    thumbnailHtml = `
                                <div class="post-thumbnail position-absolute top-0 start-0 w-100 h-100">
                                    <a href="${slide.permalink}" class="d-block h-100">
                                        <img src="${slide.thumbnail}" 
                                            alt="${slide.title}" 
                                            class="img-fluid rounded-start w-100 h-100 object-fit-cover"
                                            style="aspect-ratio: 4/3;">
                                    </a>
                                </div>`;
                                }

                                const truncatedExcerpt = createTruncatedExcerpt(slide.excerpt, excerptLength);

                                swiper.appendSlide(`
                                    <div class="swiper-slide h-100">
                                        <div class="card mb-3 shadow-sm h-100">
                                            <div class="row g-0 h-100">
                                                <div class="col-md-4 position-relative">
                                                    ${thumbnailHtml}
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="card-body d-flex flex-column h-100">
                                                        <p class="card-title lead">
                                                            <a href="${slide.permalink}" class="text-decoration-none text-black">
                                                                ${slide.title}
                                                            </a>
                                                        </p>
                                                        <div class="card-text small flex-grow-1">
                                                            ${truncatedExcerpt}
                                                        </div>
                                                        <div class="post-read-more mt-auto">
                                                            <a href="${slide.permalink}" class="text-secondary card-link">
                                                                Читать далее
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            });

                            // Увеличиваем номер страницы
                            currentPage++;
                            container.attr('data-page', currentPage);

                            // После загрузки слайдов сделаем обновление, чтобы все было корректно отрисовано
                            setTimeout(function () {
                                // Сбрасываем фиксацию высоты
                                $(`#${swiperId}`).css('height', '');
                                // Обновляем Swiper
                                swiper.update();
                                // Снимаем состояние загрузки
                                isLoading = false;
                                nextButton.removeClass('loading');
                            }, 300);
                        } else {
                            console.error('Ошибка загрузки дополнительных записей');
                            isLoading = false;
                            nextButton.removeClass('loading');
                            $(`#${swiperId}`).css('height', '');
                        }
                    },
                    error: function () {
                        console.error('AJAX запрос не удался');
                        isLoading = false;
                        nextButton.removeClass('loading');
                        $(`#${swiperId}`).css('height', '');
                    }
                });
            }
        });
    }

    // Инициализация при загрузке страницы
    $(document).ready(function () {
        initSwipers();
    });

})(jQuery);