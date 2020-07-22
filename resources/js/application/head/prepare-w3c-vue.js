export default function () {
    new VueW3CValid({
        el: '.body'
    });
    document.querySelectorAll('[data-inline-template]').forEach(function (x) {
        x.removeAttribute('data-inline-template');
        x.setAttribute('inline-template', null);
    }); 
};