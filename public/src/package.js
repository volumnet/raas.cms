import designComponents from 'app/_blocks/design';
import templateComponents from 'app/_blocks/template';
import RaasFieldHtmlcodearea from 'app/application/fields/raas-field-htmlcodearea.vue';

window.raasComponents = {
    ...window.raasComponents,
    ...designComponents,
    ...templateComponents,
    'raas-field-htmlcodearea': RaasFieldHtmlcodearea,
};