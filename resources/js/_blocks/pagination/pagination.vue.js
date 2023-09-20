/**
 * Постраничная разбивка
 * @requires window.queryString
 */
export default {
    props: {
        /**
         * Текущая страница
         * @type {Number}
         */
        page: {
            type: Number,
            default: 1,
        },
        /**
         * Количество страниц
         * @type {Number}
         */
        pages: {
            type: Number,
            default: 1,
        },
        /**
         * Трассировка соседних страниц
         * @type {Number}
         */
        trace: {
            type: Number,
            default: 2,
        },
    },
    computed: {
        /**
         * Ссылки для отображения
         * @return {Object[]} <pre><code>array<{
         *     href?: String Ссылка,
         *     text?: String Текст ссылки,
         *     active?: Boolean Активна ли ссылка
         *     ellipsis?: Boolean Многоточие
         * }></code></pre>
         */
        links() {
            let links = [];
            const currentQuery = window.queryString.parse(document.location.search, { arrayFormat: 'bracket' });

            if (this.page > 1) {
                let query = { ...currentQuery};
                if (this.page > 2) {
                    query.page = (this.page - 1);
                } else {
                    delete query.page;
                }
                links.push({ href: query, text: '«' });
            }
            if (this.page > 1 + this.trace) {
                let query = { ...currentQuery };
                delete query.page;
                links.push({ href: query, text: '1' });
            }
            if (this.page == 3 + this.trace) {
                links.push({ href: { ...currentQuery, page: 2 }, text: '2' });
            } else if (this.page > 2 + this.trace) {
                links.push({ ellipsis: true });
            }
            for (let i = Math.max(1, this.page - this.trace); i <= Math.min(this.page + this.trace, this.pages); i++) {
                let query = { ...currentQuery };
                if (i > 1) {
                    query.page = i;
                } else {
                    delete query.page;
                }
                links.push({ href: query, text: i + '', active: (this.page == i) });
            }
            if (this.page == this.pages - this.trace - 2) {
                links.push({ href: { ...currentQuery, page: (this.pages - 1) }, text: (this.pages - 1) + '' });
            } else if (this.page < this.pages - this.trace - 1) {
                links.push({ ellipsis: true });
            }
            if (this.page < this.pages - this.trace) {
                links.push({ href: { ...currentQuery, page: this.pages }, text: this.pages + '' });
            }
            if (this.page < this.pages) {
                links.push({ href: { ...currentQuery, page: (this.page + 1) }, text: '»' });
            }
            links = links.map(x => {
                if (x.href) {
                    x.href = window.queryString.stringify(x.href, { arrayFormat: 'bracket' });
                    x.href = x.href ? ('?' + x.href) : window.location.pathname;
                }
                return x;
            });
            return links;
        },
    }
}