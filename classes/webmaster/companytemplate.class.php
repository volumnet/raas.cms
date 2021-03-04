<?php
/**
 * Шаблон типа материалов "Компания"
 */
namespace RAAS\CMS;

use RAAS\Application;
use RAAS\Attachment;

/**
 * Класс шаблона типа материалов "Компания"
 */
class CompanyTemplate extends MaterialTypeTemplate
{
    public function createFields()
    {
        $logoField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('LOGO'),
            'urn' => 'logo',
            'datatype' => 'image',
            'show_in_table' => 1,
        ]);
        $logoField->commit();

        $postalCodeField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('POSTAL_CODE'),
            'urn' => 'postal_code',
            'datatype' => 'text',
        ]);
        $postalCodeField->commit();

        $cityField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('CITY'),
            'urn' => 'city',
            'datatype' => 'text',
        ]);
        $cityField->commit();

        $streetAddressField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('STREET_ADDRESS'),
            'urn' => 'street_address',
            'datatype' => 'text',
        ]);
        $streetAddressField->commit();

        $mapCodeField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('MAP_CODE'),
            'urn' => 'map',
            'datatype' => 'textarea',
        ]);
        $mapCodeField->commit();

        $officeField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('OFFICE'),
            'urn' => 'office',
            'datatype' => 'text',
        ]);
        $officeField->commit();

        $phoneField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('PHONE'),
            'urn' => 'phone',
            'multiple' => 1,
            'datatype' => 'tel',
        ]);
        $phoneField->commit();

        $emailField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('EMAIL'),
            'urn' => 'email',
            'multiple' => 1,
            'datatype' => 'email',
        ]);
        $emailField->commit();

        $scheduleField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('SCHEDULE'),
            'urn' => 'schedule',
            'datatype' => 'text',
        ]);
        $scheduleField->commit();

        $transportField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('TRANSPORT'),
            'urn' => 'transport',
            'datatype' => 'textarea',
        ]);
        $transportField->commit();

        $socialsField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('SOCIAL_NETWORKS'),
            'urn' => 'socials',
            'multiple' => 1,
            'datatype' => 'text',
        ]);
        $socialsField->commit();

        $copyrightsField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('COPYRIGHTS'),
            'urn' => 'copyrights',
            'datatype' => 'text',
        ]);
        $copyrightsField->commit();

        $legalNameField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('LEGAL_NAME'),
            'urn' => 'legal_name',
            'datatype' => 'text',
        ]);
        $legalNameField->commit();

        $legalAddressField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('LEGAL_ADDRESS'),
            'urn' => 'legal_address',
            'datatype' => 'text',
        ]);
        $legalAddressField->commit();

        $legalEmailField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('LEGAL_EMAIL'),
            'urn' => 'legal_email',
            'datatype' => 'email',
        ]);
        $legalEmailField->commit();

        $taxIDField = new Material_Field([
            'pid' => $this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('TAX_ID'),
            'urn' => 'tax_id',
            'datatype' => 'text',
        ]);
        $taxIDField->commit();

        return [
            $logoField->urn => $logoField,
            $postalCodeField->urn => $postalCodeField,
            $cityField->urn => $cityField,
            $streetAddressField->urn => $streetAddressField,
            $mapCodeField->urn => $mapCodeField,
            $officeField->urn => $officeField,
            $phoneField->urn => $phoneField,
            $emailField->urn => $emailField,
            $scheduleField->urn => $scheduleField,
            $transportField->urn => $transportField,
            $socialsField->urn => $socialsField,
            $copyrightsField->urn => $copyrightsField,
            $legalNameField->urn => $legalNameField,
            $legalAddressField->urn => $legalAddressField,
            $legalEmailField->urn => $legalEmailField,
            $taxIDField->urn => $taxIDField,
        ];
    }


    /**
     * Создает сниппет логотипа
     * @return Snippet
     */
    public function createLogoBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/logo.tmp.php';
        $urn = 'logo';
        $name = View_Web::i()->_('LOGO');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }



    /**
     * Создает сниппет страницы политики обработки персональных данных
     * @return Snippet
     */
    public function createPrivacyBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/privacy.tmp.php';
        $urn = 'privacy';
        $name = View_Web::i()->_('PRIVACY_PAGE_NAME');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок логотипа
     * @return Block_Material
     */
    public function createLogoBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'logo',
                'name' => $widget->name,
                'inherit' => 1,
                'cats' => $page->selfAndChildrenIds,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает сниппет контактов в шапке
     * @return Snippet
     */
    public function createContactsTopBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/contacts_top.tmp.php';
        $urn = 'contacts_top';
        $name = View_Web::i()->_('CONTACTS_TOP');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок контактов в шапке
     * @return Block_Material
     */
    public function createContactsTopBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'contacts_top',
                'name' => $widget->name,
                'inherit' => 1,
                'cats' => $page->selfAndChildrenIds,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает сниппет социальных сетей в шапке
     * @return Snippet
     */
    public function createSocialsTopBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/socials.tmp.php';
        $urn = 'socials_top';
        $name = View_Web::i()->_('SOCIALS_TOP');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок социальных сетей в шапке
     * @return Block_Material
     */
    public function createSocialsTopBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'socials_top',
                'name' => $widget->name,
                'inherit' => 1,
                'cats' => $page->selfAndChildrenIds,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает сниппет копирайтов
     * @return Snippet
     */
    public function createCopyrightsBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/copyrights.tmp.php';
        $urn = 'copyrights';
        $name = View_Web::i()->_('COPYRIGHTS');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок копирайтов
     * @return Block_Material
     */
    public function createCopyrightsBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'copyrights',
                'name' => $widget->name,
                'inherit' => 1,
                'cats' => $page->selfAndChildrenIds,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает сниппет контактов в подвале
     * @return Snippet
     */
    public function createContactsBottomBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/contacts_bottom.tmp.php';
        $urn = 'contacts_bottom';
        $name = View_Web::i()->_('CONTACTS_BOTTOM');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок контактов в подвале
     * @return Block_Material
     */
    public function createContactsBottomBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'contacts_bottom',
                'name' => $widget->name,
                'inherit' => 1,
                'cats' => $page->selfAndChildrenIds,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает сниппет социальных сетей в подвале
     * @return Snippet
     */
    public function createSocialsBottomBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/socials.tmp.php';
        $urn = 'socials_bottom';
        $name = View_Web::i()->_('SOCIALS_BOTTOM');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок социальных сетей в подвале
     * @return Block_Material
     */
    public function createSocialsBottomBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'socials_bottom',
                'name' => $widget->name,
                'inherit' => 1,
                'cats' => $page->selfAndChildrenIds,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    /**
     * Создает сниппет контактов для страницы контактов
     * @return Snippet
     */
    public function createContactsBlockSnippet()
    {
        $filename = Package::i()->resourcesDir
                  . '/widgets/materials/company/contacts.tmp.php';
        $urn = 'contacts';
        $name = View_Web::i()->_('CONTACTS');

        $snippet = $this->webmaster->createSnippet(
            $urn,
            $name,
            (int)$this->widgetsFolder->id,
            $filename,
            $this->getReplaceData($name, $urn)
        );
        return $snippet;
    }


    /**
     * Создает блок контактов для страницы контактов
     * @return Block_Material
     */
    public function createContactsBlock(
        Page $page,
        Snippet $widget = null,
        array $additionalData = []
    ) {
        $additionalData = array_merge(
            [
                'pages_var_name' => '',
                'rows_per_page' => 1,
                'location' => 'content',
                'name' => $widget->name,
            ],
            $additionalData
        );
        return parent::createBlock($page, $widget, $additionalData);
    }


    public function createMaterials(array $pagesIds = [])
    {
        $result = [];
        $item = new Material([
            'pid' => (int)$this->materialType->id,
            'vis' => 1,
            'name' => View_Web::i()->_('TEST_COMPANY'),
            'sitemaps_priority' => 0.5
        ]);
        $item->commit();
        $att = Attachment::createFromFile(
            Package::i()->resourcesDir . '/logo.png',
            $this->materialType->fields['logo']
        );
        $item->fields['logo']->addValue(json_encode([
            'vis' => 1,
            'name' => '',
            'description' => View_Web::i()->_('TEST_COMPANY_SLOGAN'),
            'attachment' => (int)$att->id
        ]));
        $item->fields['postal_code']->addValue('000000');
        $item->fields['city']->addValue(View_Web::i()->_('CITY'));
        $item->fields['street_address']->addValue(View_Web::i()->_('TEST_COMPANY_STREET_ADDRESS'));
        $item->fields['map']->addValue('<script type="text/javascript" charset="utf-8" src="//api-maps.yandex.ru/services/constructor/1.0/js/?sid=ac2qYbmG3G-Jl487_Mu2VedJiQSpaZLo&amp;width=100%25&amp;height=300&amp;lang=ru_RU&amp;sourceType=constructor&amp;scroll=false"></script>');
        $item->fields['office']->addValue(View_Web::i()->_('TEST_COMPANY_OFFICE'));
        $item->fields['phone']->addValue('+7 999 000-00-00');
        $item->fields['email']->addValue('test@test.org');
        $item->fields['schedule']->addValue(View_Web::i()->_('TEST_COMPANY_SCHEDULE'));
        $item->fields['transport']->addValue('...');
        $item->fields['socials']->addValue(View_Web::i()->_('https://vk.com/test'));
        $item->fields['socials']->addValue(View_Web::i()->_('https://facebook.com/test'));
        $item->fields['socials']->addValue(View_Web::i()->_('https://instagram.com/test'));
        $item->fields['socials']->addValue(View_Web::i()->_('https://youtube.com/test'));
        $item->fields['socials']->addValue(View_Web::i()->_('https://twitter.com/test'));
        $item->fields['socials']->addValue(View_Web::i()->_('https://wa.me/79990000000'));
        $item->fields['copyrights']->addValue(
            '© ' . View_Web::i()->_('COMPANY') . ', ' . date('Y') . '. ' .
            View_Web::i()->_('ALL_RIGHTS_RESERVED') . '.'
        );
        $item->fields['legal_name']->addValue(View_Web::i()->_('TEST_COMPANY_LEGAL_NAME'));
        $item->fields['legal_address']->addValue(View_Web::i()->_('TEST_COMPANY_LEGAL_ADDRESS'));
        $item->fields['legal_email']->addValue(View_Web::i()->_('info@test.org'));
        $item->fields['tax_id']->addValue(View_Web::i()->_('0000000000'));
        $result[] = $item;
        return $result;
    }
}
