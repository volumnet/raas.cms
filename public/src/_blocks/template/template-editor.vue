<style lang="scss">
.cms-template-editor {
    margin-bottom: 2rem;
}
</style>

<template>
  <cms-template 
    class="cms-template-editor" 
    :item="templateData" 
    :edit-mode="true" 
    @update:item="templateData = $event"
  ></cms-template>
</template>

<script>
export default {
    props: {
        /**
         * Начальное значение
         * @type {Object}
         */
        initialData: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            templateData: JSON.parse(JSON.stringify(this.initialData)),
        };
    },
    watch: {
        initialData(newVal, oldVal) {
            if (JSON.stringify(newVal) != JSON.stringify(oldVal)) {
                this.templateData = JSON.parse(JSON.stringify(this.initialData));
            }
        },
    },
};
</script>