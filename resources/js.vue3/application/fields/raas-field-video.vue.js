import RAASField from './raas-field.vue.js';

/**
 * Поле видео с YouTube
 */
export default {
    mixins: [RAASField],
    mounted() {
        this.$el.classList.remove('form-control');
    },
    updated() {
        this.$el.classList.remove('form-control');
    },
    methods: {
        clear() {
            this.$emit('update:modelValue', '')
        }
    },
    computed: {
        ytId() {
            let rx = /^((http(s?):\/\/.*?(((\?|&)v=)|(embed\/)|(youtu\.be\/)))([\w\-\_]+).*?)$/gi;
            let regs = rx.exec(this.modelValue);
            if (regs) {
                return regs[9];
            } 
            return null;
        },
        coverURL() {
            if (this.ytId) {
                return 'https://i.ytimg.com/vi/' + this.ytId + '/hqdefault.jpg';
            }
            return null;
        },
        videoURL() {
            if (this.ytId) {
                return 'https://youtube.com/watch?v=' + this.ytId;
            }
            return '#';
        },
    },
};