<style lang="scss">
.cms-block { 
    $self: &;

    display: block; 
    position: relative; 
    display: flex;
    border: 1px solid rgba(white, .5);
    min-height: 24px; 
    box-shadow: 2px 2px 2px rgba(black, .5); 
    text-decoration: none !important;
    border-radius: 3px;
    box-sizing: border-box;
    padding: 6px; 
    background-color: var(--background-color);
    overflow: hidden;
    @include smartprops((
        --background-color: (
            '': #eee,
            '&_html': #9ff,
            '&_php': #f99,
            '&_material': #9f9,
            '&_menu': #ff9,
            '&_form': #9cf,
            '&_search': #c9f,
            '&_module': #fc9,
        ),
    ));
    &_editable:hover {
        opacity: 0.75;
    }
    &__title { // &-name для совместимости
        font-size: 11px; 
        line-height: 1.1;
        color: black;
        #{$self}_invis & {
            color: #999;
        }
    }
}
</style>

<template>
  <div :is="editMode ? 'a' : 'span'" class="cms-block" :class="cssClass" :href="editMode ? item.url : null">
    <span class="cms-block__title">
      {{ item.name }}
    </span>
  </div>
</template>

<script>
import WithContextMenu from 'kernel/_blocks/menu-context/with-context-menu.mixin.vue.js';

export default {
    mixins: [WithContextMenu],
    props: {
        /**
         * Данные размещения
         * @type {Object}
         */
        item: {
            type: Object,
            required: true,
        },
    },
    computed: {
        /**
         * Набор CSS-классов блока
         * @return {Object}
         */
        cssClass() {
            const result = {};
            if (this.item.cssClass) {
                result[this.item.cssClass] = true;
            }
            if (!this.item.vis) {
                result['cms-block_invis'] = true;
            }
            if (this.editMode) {
                result['cms-block_editable'] = true; 
            }
            return result;
        },
        /**
         * Контекстное меню
         * @return {Array}
         */
        contextMenu() {
            return this.editMode ? (this.item.contextMenu || []) : null;
        },
        /**
         * Режим редактирования
         * @return {Boolean}
         */
        editMode() {
            return !!(this.item.href || (this.item.contextMenu && this.item.contextMenu.length));
        },
    }
}
</script>