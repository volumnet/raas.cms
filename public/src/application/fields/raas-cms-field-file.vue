<style lang="scss" scoped>
@use 'cms/_shared/mixins/filetype.scss' as *;

.raas-cms-field-file {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: relMin(20px, $min: 10px);
    padding: 10px;
    padding-right: relMin(40px, $min: 10px);
    min-height: 40px;
    background-color: var(--gray-f);
    border: 1px solid var(--gray-e);
    border-radius: var(--border-radius-sm);
    box-shadow: inset 0 1px 1px rgba(black, .05);
    transition: all .25s;
    @include viewport('<xs') {
        flex-direction: column;
        align-items: flex-start;
    }
    &_invisible {
        opacity: 0.5;
    }
    &_drag {
        &:after-c {
            position: absolute;
            pointer-events: none;
            inset: 0;
            background: color-mix(in srgb, var(--success), white 40%);
            opacity: 0.5;
        }
    }
    &__controls {
        position: absolute;
        top: 0;
        right: 0;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }
    &__button {
        @include center-alignment(40px, 20px);
        color: rgba(black, .4);
        background: transparent;
        border: none;
        padding: 0;
        transition: all .25s;
        opacity: 0.5;
        &:hover {
            opacity: 1;
        }
        &_revert {
            font-size: 16px;
            &:after {
                @include fa('rotate-left');
            }
        }
        &_delete {
            &:after {
                content: '×';
                font-weight: bold;
            }
        }
        &_move {
            font-size: 16px;
            cursor: ns-resize;
            &:after {
                @include fa('arrows-up-down');
            }
        }
    }
    &__file {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        width: 64px;
        @include viewport('<xs') {
            width: 100%;
        }
    }
    &__title {
        font-size: 8px;
        color: var(--gray-8);
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    &__image {
        $image: &;
        display: block;
        width: 100%;
        aspect-ratio: 1/1;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        @include viewport('<xs') {
            width: 64px;
        }
        &_no-file, &_file {
            border: 1px solid var(--gray-d);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-a);
            font-size: 24px;
            transition: all .25s;
        }
        &_no-file, &_file:is(a[href]) {
            cursor: pointer;
        }
        &_no-file {
            background: var(--gray-e) !important;
            &:hover {
                background: white !important;
            }
            &#{$image}_image {
                &:before {
                    @include fa('camera');
                }
            }
            &#{$image}_file {
                &:before {
                    @include fa('folder-open');
                }
            }
        }
        &_file {
            @include filetype();
            &:before {
                @include fa('file');
            }
            &:not(#{$image}_no-file) {
                background: white;
            }
        }
        &_updated {
            &:after-c {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: red;
                pointer-events: none;
            }
        }
        img {
            display: block;
            size: 100%;
            object-fit: cover;
        }
    }
    &__fields {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 5px;
        flex-grow: 1;
        @include viewport('<xs') {
            width: 100%;
        }
        > input, > textarea {
            width: 100%;
        }
    }
    &__header {
        display: flex;
        align-items: center;
        gap: 5px;
        @include viewport('<sm') {
           display: contents; 
        }
    }
    &__vis-label {
        display: flex;
        align-items: center;
        gap: 5px;
        input {
            margin-top: 0;
        }
    }
}
</style>


<template>
  <div 
    class="raas-cms-field-file" 
    :class="{ 'raas-cms-field-file_invisible': !pValue.vis, 'raas-cms-field-file_drag': dragOver }"
    @dragover.prevent="dragOver = true" 
    @dragleave="dragOver = false"
    @drop.prevent="handleDrop($event); dragOver = false;"
  >
    <input type="hidden" :name="getComponentURN('vis')" :value="pValue.vis">
    <input type="hidden" :name="getComponentURN('attachment')" :value="pValue.attachment || 0">
    <div class="raas-cms-field-file__controls">
      <button 
        v-if="pValue.attachment || pValue.upload || multiple"
        type="button" 
        class="raas-cms-field-file__button raas-cms-field-file__button_delete" 
        @click="multiple ? $emit('delete') : requestDelete()" 
        :title="$root.translations.DELETE"
      ></button>
      <div 
        v-if="multiple"
        class="raas-cms-field-file__button raas-cms-field-file__button_move" 
        :title="$root.translations.MOVE"
      ></div>
    </div>
    <div class="raas-cms-field-file__file" :title="realFilename">
      <template v-if="pValue.upload || pValue.file">
        <div v-if="pValue.upload" :class="imageCSSClass">
          <img v-if="type == 'image'" :src="pValue.upload.dataURL" alt="">
        </div>
        <a v-else :href="pValue.file.fileURL" target="_blank" :class="imageCSSClass">
          <img v-if="type == 'image'" :src="pValue.file.tnURL" alt="">
        </a>
      </template>
      <div v-else :class="imageCSSClass" @click="$refs.input.click()"></div>

      <div class="raas-cms-field-file__title" v-if="realFilename">
        {{ realFilename }}
      </div>
    </div>
    <div class="raas-cms-field-file__fields">
      <div class="raas-cms-field-file__header">
        <input 
          type="file" 
          v-bind="$attrs" 
          :name="name" 
          ref="input" 
          :accept="accept"
          class="raas-cms-field-file__file-input"
          @change="changeFile($event)" 
        >
        <label class="raas-cms-field-file__vis-label">
          <input 
            type="checkbox" 
            value="1" 
            :checked="pValue.vis" 
            @input="pValue.vis = ($event.target.checked ? 1 : 0); $emit('update:modelValue', pValue);"
          >
          {{ $root.translations.VISIBLE }}
        </label>
      </div>
      <input 
        type="text" 
        :name="getComponentURN('name')" 
        :value="pValue.name" 
        :placeholder="$root.translations[type == 'image' ? 'IMG_NAME_ALT_TITLE' : 'NAME']"
        @input="pValue.name = $event.target.value; $emit('update:modelValue', pValue);"
      >
      <textarea 
        :name="getComponentURN('description')" 
        :value="pValue.description" 
        :placeholder="$root.translations.DESCRIPTION"
        @input="pValue.description = $event.target.value; $emit('update:modelValue', pValue);"
      ></textarea>
    </div>
  </div>
</template>


<script>
import RAASFieldFile from 'cms/application/fields/raas-field-file.vue.js';
export default {
    mixins: [RAASFieldFile],
    props: {
        /**
         * URN поля
         * @type {String}
         */
        name: {
            type: String,
            required: true,
        },
        /**
         * Множественное поле
         * @type {Object}
         */
        multiple: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['delete'],
    mounted() {
        if (this.pValue?.upload?.file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(this.pValue?.upload?.file);
            this.$refs.input.files = dataTransfer.files;
        }
    },
    methods: {
        /**
         * Обрабатывает входящий набор файлов
         * @param {File[]} files Файлы
         * @return {File[]} Добавленные файлы
         */
        async handleFilesChange(files) {
            const result = [];
            const filesToAdd = files.filter(file => this.checkIfFileIsAllowed(file)).slice(0, 1);
            for (let file of filesToAdd) {
                const upload = { file };
                if (this.type == 'image') {
                    upload.dataURL = await this.getFileDataURL(file);
                }
                this.pValue.upload = upload;
                this.$emit('update:modelValue', this.pValue);
                result.push(file);
            }
            return result;
        },

        /**
         * Обработчик смены файла
         * @param  {Event} e Событие
         */
        async changeFile(e) {
            const tgt = e.target || window.event.srcElement;
            const filesArr = Array.from(tgt.files);
            const added = await this.handleFilesChange(filesArr);
            if (!added.length) {
                this.$refs.input.value = '';
                this.pValue.upload = null;
                this.$emit('update:modelValue', this.pValue);
            }
        },

        async handleDrop(e) {
            const filesArr = Array.from(e.dataTransfer.files);
            const added = await this.handleFilesChange(filesArr);
            if (added.length) {
                const dataTransfer = new DataTransfer();
                for (let file of added) {
                    dataTransfer.items.add(file);
                }
                this.$refs.input.files = dataTransfer.files;
            }
        },

        clearFile() {
            this.$refs.input.value = '';
            this.pValue = { 
                vis: 1, 
                attachment: 0, 
                name: '', 
                description: '', 
                file: null, 
                upload: null, 
            };
            this.$emit('update:modelValue', this.pValue);
        },

        /**
         * Запрашивает удаление файла
         */
        requestDelete() {
            if (confirm(this.$root.translations[this.type == 'image' ? 'DELETE_IMAGE_TEXT' : 'DELETE_FILE_TEXT'])) {
                this.clearFile();
            }
        },

        /**
         * Получает URN компонента
         * @param {String} component Имя компонета
         * @return {String}
         */
        getComponentURN(component = null) {
            if (component) {
                const rx = /^([\w-]+)(\[.*?\])?$/.exec(this.name);
                return rx[1] + '@' + component + (rx[2] || '');
            } else {
                return this.name;
            }
        },
    },
    computed: {
        /**
         * CSS классы иконки по расширению файла (включая основной класс и отсутствие файла)
         * @return {Object}
         */
        imageCSSClass() {
            const result = { 'raas-cms-field-file__image': true };
            if (this.type == 'image') {
                result['raas-cms-field-file__image_image'] = true;
            } else {
                result['raas-cms-field-file__image_file'] = true;
            }
            if (this.pValue.upload) {
                result['raas-cms-field-file__image_updated'] = true;
            }
            if (this.pValue.upload || this.pValue.file) {
                if (this.type != 'image') {
                    result['raas-cms-field-file__image_file_' + this.getExtension(this.realFilename)] = true;
                }
            } else {
                result['raas-cms-field-file__image_no-file'] = true;
            }
            return result;
        },

        realFilename() {
            if (this.pValue.upload) {
                return this.pValue.upload.file.name;
            } else if (this.pValue.file) {
                const arr = this.pValue.file.fileURL.split('/');
                return arr[arr.length - 1];
            }
        },
    }
};
</script>