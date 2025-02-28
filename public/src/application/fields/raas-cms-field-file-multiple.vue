<style lang="scss">
.raas-cms-field-file-multiple {
    &__list {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 20px;
    }
    &__add {
        display: inline-flex;
        gap: .5rem;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        cursor: pointer;
        align-self: flex-start;
        // padding: 2rem;
        // border: 1px solid var(--success);
        // border-radius: 10px;
        // font-size: clamp(14px, 1.25vw, 20px);
        // background: color-mix(in srgb, var(--success), white 80%);
        &:hover, &_drag {
            background: var(--info);
            // border-color: var(--primary);
        }
        &:before {
            @include fa('plus');
        }
    }
    &__input {
        position: absolute;
        opacity: 0; 
        pointer-events: none; 
    }
}
</style>


<template>
  <div class="raas-cms-field-file-multiple">
    <div class="raas-cms-field-file-multiple__list" ref="list">
      <div class="raas-cms-field-file-multiple__item" v-for="(fileData, i) in pValue" :key="i + '_' + sortCounter">
        <!-- Поскольку raas-cms-field-file не наследует атрибуты, как указано в RAASField -->
        <raas-cms-field-file 
          :multiple="true"
          :type="type"
          :accept="accept"
          :name="name"
          :model-value="fileData"
          @update:model-value="pValue[i] = $event; $emit('update:modelValue', pValue)"
          @delete="deleteItem(i)"
        ></raas-cms-field-file>
      </div>
      <label 
        class="raas-cms-field-file-multiple__item raas-cms-field-file-multiple__add btn btn-info"
        :class="{ 
            'raas-cms-field-file-multiple__add_drag': dragOver, 
            'raas-cms-field-file-multiple__add_file': (type != 'image'),
            'raas-cms-field-file-multiple__add_image': (type == 'image'),
        }" 
        @dragover.prevent="dragOver = true" 
        @dragleave="dragOver = false"
        @drop.prevent="handleDrop($event); dragOver = false;"
      >
        <input 
          type="file" 
          class="raas-cms-field-file-multiple__input"
          :accept="accept" 
          multiple
          ref="input" 
          @change="changeFile($event)" 
        >
        {{ $root.translations.CHOOSE_OR_DRAG_FILES }}
      </label>
    </div>
  </div>
</template>


<script>
import RAASFieldFile from 'cms/application/fields/raas-field-file.vue.js';
export default {
    mixins: [RAASFieldFile],
    props: {
        /**
         * Название поля
         * @type {Object}
         */
        name: {
            type: String,
            required: true,
        },

    },
    data() {
        return {
            sortCounter: 0,
        };
    },
    mounted() {
        $(this.$refs.list).sortable(this.sortableParams);
    },
    methods: {
        /**
         * Обрабатывает входящий набор файлов
         * @param {File[]} files Файлы
         * @return {File[]} Добавленные файлы
         */
        async handleFilesChange(files) {
            const result = [];
            const filesToAdd = files.filter(file => this.checkIfFileIsAllowed(file));
            const valuesToAdd = [];
            for (let file of filesToAdd) {
                const upload = { file };
                if (this.type == 'image') {
                    upload.dataURL = await this.getFileDataURL(file);
                }
                const valueToAdd = { 
                    vis: 1, 
                    attachment: 0, 
                    name: '', 
                    description: '', 
                    file: null, 
                    upload, 
                };
                valuesToAdd.push(valueToAdd);
                result.push(file);
            }
            if (valuesToAdd) {
                this.pValue = [...this.pValue, ...valuesToAdd];
                this.$emit('update:modelValue', this.pValue);
            }
            return result;
        },
        /**
         * Удаляет элемент по индексу
         * @param {Number} index Индекс элемента для удаления
         */
        deleteItem(index) {
            let newValue = [ ...this.pValue ];
            newValue.splice(index, 1);
            this.pValue = newValue;
            this.sortCounter++;
            this.$emit('update:modelValue', this.pValue);
        },

        /**
         * Передвигает элемент
         * @param {Number} originalPosition Изначальная позиция
         * @param {Number} position Текущая позиция
         */
        move(originalPosition, position) {
            if (position == originalPosition) {
                return;
            }
            if ((position < 0) || (position > this.pValue.length - 1)) {
                return;
            }

            const newValue = [...this.pValue];
            newValue.splice(position, 0, newValue.splice(originalPosition, 1)[0]);
            // console.log(newValue);
            this.pValue = [...newValue];
            this.$emit('update:modelValue', this.pValue);
            this.sortCounter++;
        },

        /**
         * Обработчик смены файла
         * @param  {Event} e Событие
         */
        async changeFile(e) {
            const tgt = e.target || window.event.srcElement;
            const filesArr = Array.from(tgt.files);
            const added = await this.handleFilesChange(filesArr);
            this.$refs.input.value = '';
        },
        
        async handleDrop(e) {
            const files = e.dataTransfer.files;
            const filesArr = Array.from(files);
            const added = await this.handleFilesChange(filesArr);
        },
    },
    computed: {
        /**
         * Параметры jQueryUI-виджета Sortable
         * @type {Object}
         */
        sortableParams() {
            let originalPosition = null;
            let result = {
                start: (event, ui) => {
                    originalPosition = ui.item.parent().children().index(ui.item);
                },
                stop: (event, ui) => {
                    this.move(originalPosition, ui.item.parent().children().index(ui.item));
                    originalPosition = null;
                },
            };
            result.items = '.raas-cms-field-file-multiple__item:not(.raas-cms-field-file-multiple__add)';
            result.handle = '.raas-cms-field-file__button_move';
            return result;
        },
    }
}
</script>