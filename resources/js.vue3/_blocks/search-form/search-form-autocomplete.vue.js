/**
 * Класс автоподстановки в поисковой строке
 */
export default {
    props: {
        /**
         * Данные автозаполнения
         * @type {Object} <pre><code>{
         *     searchString: String Поисковая строка,
         *     pagination: {
         *         page: Number Номер текущей страницы,
         *         rowsPerPage: Number Элементов на страницу,
         *         pages: Number Страниц,
         *         from: Number Отображаются элементы с номера,
         *         to: Number Отображаются элементы по номер,
         *         count: Number Общее количество элементов,
         *     } Постраничная разбивка,
         *     'pages'|'materials'|catalog: {
         *         id: Number ID# элемента,
         *         name: String Наименование элемента,
         *         url: URL элемента,
         *         type: 'page'|'material' Тип элемента,
         *         date:? String Форматированная дата новости (для новостей),
         *         description: String Краткое описание элемента,
         *         image: {
         *             url: String URL изображения,
         *             name:? String Наименование изображения
         *         } Изображение,
         *     } Набор страниц, не товарных и товарных материалов соответственно
         * }</code></pre>
         */
        autocomplete: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {};
    },
    mounted() {
        $(this.$el).on('keydown', (e) => {
            let delta = 0;
            switch (e.keyCode) {
                case 38: // Стрелка вверх
                    delta = -1;
                    break;
                case 40: // Стрелка вниз
                    delta = 1;
                    break;
            }
            if (delta) {
                let $links = $('a', this.$el);
                let currentIndex = $links.index(
                    $links.filter(':focus')[0]
                );
                let newIndex = (currentIndex + delta) % $links.length;
                $links.eq(newIndex)[0].focus();
                e.preventDefault();
            }
        });
    }
}