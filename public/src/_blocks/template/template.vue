<style lang="scss">
.cms-template { 
    position: relative; 
    box-sizing: border-box;
    min-width: 200px; 
    min-height: 200px;
    border: 1px solid #ccc; 
    background-repeat: no-repeat; 
    border-radius: 4px; 
    box-shadow: 5px 5px 15px rgba(black, .5); 
    overflow: hidden;
    background: white;
    &__location {
        position: absolute;
    }
}
</style>

<template>
  <div class="cms-template" :style="cssStyle">
    <template v-if="editMode">
      <input type="hidden" name="width" :value="item.width">
      <input type="hidden" name="height" :value="item.height">
    </template>
    <template v-for="(location, index) in item.locations_info">
      <cms-location 
        class="cms-template__location" 
        v-if="location.urn" 
        :item="location" 
        :edit-mode="editMode"
        :style="{ left: location.x + 'px', top: location.y + 'px', width: location.width + 'px', height: location.height + 'px', }"
        @update:item="handleLocationChange($event, index)"
      ></cms-location>
    </template>
  </div>
</template>

<script>
const GRID = 10;

export default {
    props: {
        /**
         * Данные шаблона
         * @type {Object}
         */
        item: {
            type: Object,
            required: true,
        },
        /**
         * Типы блоков
         * @type {Array} <pre><code>array<{
         *     classname: String Имя класса
         * }></code></pre>
         */
        blockTypes: {
            type: Array,
            default() {
                return [];
            },
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
            $(this.$el).resizable({
                autoHide: true,
                delay: 125,
                grid: [GRID, GRID],
                resize: this.handleChange.bind(this),
            });
        }
    },
    methods: {
        /**
         * Обрабатывает изменения в размере шаблона
         */
        handleChange() {
            const inputData = JSON.parse(JSON.stringify(this.item));
            inputData.width = Math.round(parseFloat($(this.$el).css('width')) / GRID) * GRID;
            inputData.height = Math.round(parseFloat($(this.$el).css('height')) / GRID) * GRID;
            this.$emit('update:item', inputData);
        },
        /**
         * Обрабатывает изменения в размере/положении размещения
         * @param  {Object} locationData Данные размещения
         * @param  {Number} index 0-базированный индекс размещения
         */
        handleLocationChange(locationData, index) {
            const inputData = JSON.parse(JSON.stringify(this.item));
            inputData['locations_info'][index] = locationData;
            this.$emit('update:item', inputData);
        },
    },
    computed: {
        /**
         * Набор CSS-стилей шаблона
         * @return {Object}
         */
        cssStyle() {
            const result = {
                width: this.item.width + 'px',
                height: this.item.height + 'px',
            };
            return result;
        }
    }
}
</script>