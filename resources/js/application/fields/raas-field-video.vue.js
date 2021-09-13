import RAASField from './raas-field.vue.js';

/**
 * Поле видео с YouTube
 */
export default {
    mixins: [RAASField],
    mounted: function () {
        this.$el.classList.remove('form-control');
    },
    updated: function () {
        this.$el.classList.remove('form-control');
    },
    methods: {
        clear: function () {
            this.$emit('input', '')
        }
    },
    computed: {
        ytId: function () {
            let rx = /^((http(s?):\/\/.*?(((\?|&)v=)|(embed\/)|(youtu\.be\/)))([\w\-\_]+).*?)$/gi;
            let regs = rx.exec(this.value);
            if (regs) {
                return regs[9];
            } 
            return null;
        },
        coverURL: function () {
            if (this.ytId) {
                return 'https://i.ytimg.com/vi/' + this.ytId + '/hqdefault.jpg';
            }
            return null;
        },
        videoURL: function () {
            if (this.ytId) {
                return 'https://youtube.com/watch?v=' + this.ytId;
            }
            return '#';
        },
    },
};