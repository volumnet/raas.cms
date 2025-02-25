import designComponents from 'app/components/design';
import templateComponents from 'app/components/template';
import RaasFieldHtmlcodearea from 'app/application/fields/raas-field-htmlcodearea.vue';

window.raasComponents = {
    ...window.raasComponents,
    ...designComponents,
    ...templateComponents,
    'raas-field-htmlcodearea': RaasFieldHtmlcodearea,
};