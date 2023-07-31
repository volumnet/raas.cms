<style lang="scss">
.cms-page-layout {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
    &__template {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        align-items: stretch;
        min-width: 640px;
    }
    &__legend {
        min-width: 170px;
        max-width: 620px;
    }
}
</style>

<template>
  <div class="cms-page-layout">
    <div class="cms-page-layout__template">
      <cms-template v-if="templateData" :item="templateData"></cms-template>
      <cms-location v-if="emptyLocation" :item="emptyLocation"></cms-location>
    </div>
    <div class="cms-page-layout__legend">
      <cms-legend v-if="legend" :legend="legend"></cms-legend>
    </div>
  </div>
</template>

<script>
export default {
    props: {
        /**
         * Данные шаблона
         * @type {Object}
         */
        templateData: {
            type: Object,
            default: null,
        },
        /**
         * Данные по пустому размещению
         * @type {Object}
         */
        emptyLocation: {
            type: Object,
            default: null,
        },
        /**
         * Данные по легенде (доступные блоки)
         * @type {Object[]}
         */
        legend: {
            type: Array,
            default: null,
        },
    },
}
</script>