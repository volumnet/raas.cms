import designComponents from 'app/components/design';
import templateComponents from 'app/components/template';
import RaasFieldHtmlcodearea from 'app/application/fields/raas-field-htmlcodearea.vue';
import RaasCmsFieldFile from 'app/application/fields/raas-cms-field-file.vue';
import RaasCmsFieldFileMultiple from 'app/application/fields/raas-cms-field-file-multiple.vue';
import RaasCmsFieldMaterial from 'app/application/fields/raas-cms-field-material.vue';

window.raasComponents = {
    ...window.raasComponents,
    ...designComponents,
    ...templateComponents,
    'raas-field-htmlcodearea': RaasFieldHtmlcodearea,
    'raas-cms-field-file': RaasCmsFieldFile,
    'raas-cms-field-file-multiple': RaasCmsFieldFileMultiple,
    'raas-cms-field-material': RaasCmsFieldMaterial,
};