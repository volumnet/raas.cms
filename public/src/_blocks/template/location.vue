<style lang="scss">
.cms-location { 
    $self: &;

    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 3px;
    min-height: 60px; 
    min-width: 140px; 
    padding: 3px; 
    box-sizing: border-box; 
    border: 1px dashed #ccc; 
    background: rgba(255, 255, 255, 0.9);
    &_standalone {
        width: 100%;
    }
    &_editable {
        cursor: move;
    }
    &__title {
        margin: 0; 
        color: gray; 
        font-size: 12px;
        font-weight: bold;
    }
    &__blocks {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        #{$self}:not(#{$self}_horizontal) & {
            flex-direction: column;
        }
        #{$self}_horizontal & {
            flex-direction: row;
        }
    }
}
</style>


<template>
  <div 
    class="cms-location" 
    :class="{ 
        'cms-location_horizontal': horizontal, 
        'cms-location_vertical': !horizontal, 
        'cms-location_standalone': !item.urn, 
        'cms-location_editable': editMode, 
    }"
  >
    <template v-if="editMode">
      <input type="hidden" name="location[]" :value="item.urn">
      <input type="hidden" name="location-width[]" :value="item.width">
      <input type="hidden" name="location-height[]" :value="item.height">
      <input type="hidden" name="location-left[]" :value="item.x">
      <input type="hidden" name="location-top[]" :value="item.y">
    </template>
    <div class="cms-location__title" v-if="item.urn">{{ item.urn }}</div>
    <div class="cms-location__blocks" v-if="!editMode && item.blocks">
      <cms-block v-for="block in item.blocks" :key="block.id" :item="block"></cms-block>
    </div>
  </div>
</template>


<script>
import WithContextMenu from 'kernel/_blocks/menu-context/with-context-menu.mixin.vue.js';

const GRID = 10;

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
        /**
         * Минимальная высота для признания размещения вертикальным
         * @type {Object}
         */
        verticalMinHeight: {
            type: Number,
            default: 90,
        },
        /**
         * Режим редактирования
         * @type {Boolean}
         */
        editMode: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update:item'],
    mounted() {
        if (this.editMode) {
            const commonConfig = {
                delay: 125,
                grid: [GRID, GRID],
                containment: 'parent',
            };
            $(this.$el).resizable({
                ...commonConfig,
                autoHide: true,
                handles: 'all',
                resize: this.handleChange.bind(this),
            }).draggable({
                ...commonConfig,
                scroll: false,
                stop: this.handleChange.bind(this), // 2024-03-04, AVS: заменил drag на stop, иначе конфликты синхронности и подвинуть не получается
            });
        }
    },
    methods: {
        /**
         * Обрабатывает изменения в размере/положении размещения
         */
        handleChange() {
            const inputData = JSON.parse(JSON.stringify(this.item));
            inputData.width = Math.round(parseFloat($(this.$el).css('width')) / GRID) * GRID;
            inputData.height = Math.round(parseFloat($(this.$el).css('height')) / GRID) * GRID;
            inputData.x = Math.round(parseFloat($(this.$el).css('left')) / GRID) * GRID;
            inputData.y = Math.round(parseFloat($(this.$el).css('top')) / GRID) * GRID;
            this.$emit('update:item', inputData);
        },
    },
    computed: {
        /**
         * Является ли размещение горизонтальным
         * @return {Boolean}
         */
        horizontal() {
            return this.item.urn && (this.item.height < this.verticalMinHeight);
        },
        /**
         * Контекстное меню
         * @return {Array}
         */
        contextMenu() {
            return this.editMode ? null : (this.item.contextMenu || []);
        },
    },
};
</script>