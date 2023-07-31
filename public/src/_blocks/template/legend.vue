<style lang="scss">
.cms-legend {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 3px;
}
</style>

<template>
  <div class="cms-legend">
    <cms-block v-for="(block, i) in legend" :key="i" :item="block" class="cms-legend__item"></cms-block>
  </div>
</template>

<script>
export default {
    props: {
        /**
         * Данные легенды
         * @type {Object[]}
         */
        legend: {
            type: Array,
            required: true,
        },
    },
}
</script>