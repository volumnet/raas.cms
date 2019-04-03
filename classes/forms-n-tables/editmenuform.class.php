<?php
/**
 * Форма редактирования меню
 */
namespace RAAS\CMS;

use RAAS\Form as RAASForm;

/**
 * Класс формы редактирования меню
 * @property-read ViewSub_Dev $view Представление
 */
class EditMenuForm extends RAASForm
{
    public function __get($var)
    {
        switch ($var) {
            case 'view':
                return ViewSub_Dev::i();
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function __construct(array $params = [])
    {
        $view = $this->view;
        $Item = isset($params['Item']) ? $params['Item'] : null;
        if (!$Item->id && $params['Parent']->id && $params['Parent']->domain_id) {
            $Item->domain_id = $params['Parent']->domain_id;
        }
        $Parent = isset($params['Parent']) ? $params['Parent'] : null;
        $domainsIds = PageRecursiveCache::i()->getChildrenIds(0);
        $defaultParams = [
            'caption' => $Item->id
                      ?  $Item->name
                      :  (
                            $Parent->id ?
                            $this->view->_('CREATING_NOTE') :
                            $this->view->_('CREATING_MENU')
                        ),
            'parentUrl' => $this->view->url . '&action=menus&id=%s',
            'export' => function ($Form) use ($Parent) {
                $Form->exportDefault();
                if (!$Form->Item->id) {
                    $Form->Item->pid = (int)$Parent->id;
                }
            },
            'children' => []
        ];
        $defaultParams['children']['pid'] = [
            'type' => 'hidden',
            'name' => 'pid',
            'export' => 'is_null',
            'import' => function () use ($Parent) {
                return (int)$Parent->id;
            },
            'default' => (int)$Parent->id
        ];
        $defaultParams['children']['vis'] = [
            'type' => 'checkbox',
            'name' => 'vis',
            'caption' => $this->view->_('VISIBLE'),
            'default' => 1
        ];
        if ((count($domainsIds) > 1) && !$params['Parent']->id) {
            $defaultParams['children']['domain_id'] = [
                'type' => 'select',
                'name' => 'domain_id',
                'caption' => $this->view->_('DOMAIN'),
                'children' => $this->getDomains(),
                'placeholder' => '-- ' . $this->view->_('NOT_SELECTED') . ' --',
            ];
        }
        $defaultParams['children']['page_id'] = [
            'type' => 'select',
            'name' => 'page_id',
            'caption' => $this->view->_('PAGE'),
            'children' => $this->getPages(null, (int)$Item->domain_id),
        ];
        $defaultParams['children']['inherit'] = [
            'type' => 'number',
            'name' => 'inherit',
            'caption' => $this->view->_('INHERIT_LEVEL'),
            'check' => function ($Field) use ($Parent) {
                if (!$Parent->id &&
                    (int)$_POST['page_id'] &&
                    !(isset($_POST['inherit']) &&
                    (int)$_POST['inherit'])
                ) {
                    return [
                        'name' => 'MISSED',
                        'value' => $Field->name,
                        'description' => 'ERR_NO_MENU_INHERIT'
                    ];
                }
            },
        ];
        $defaultParams['children']['name'] = [
            'name' => 'name',
            'caption' => $this->view->_('NAME'),
            'required' => 'required'
        ];
        if (!$Parent->id) {
            $defaultParams['children']['urn'] = [
                'name' => 'urn',
                'caption' => $view->_('URN')
            ];
        }
        $defaultParams['children']['url'] = [
            'name' => 'url',
            'caption' => $this->view->_('URL'),
            'check' => function ($Field) use ($Parent) {
                if ($Parent->id &&
                    !(int)$_POST['page_id'] &&
                    !trim($_POST['url'])
                ) {
                    return [
                        'name' => 'MISSED',
                        'value' => $Field->name,
                        'description' => 'ERR_NO_URL'
                    ];
                }
            }
        ];
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }


    /**
     * Получает список доменов для отображения в поле "Домен",
     * отсортированные по наименованию
     * @return array<[
     *             'value' => int ID# домена,
     *             'caption' => string Наименование домена
     *         ]>
     */
    public function getDomains()
    {
        $pageCache = PageRecursiveCache::i();
        $domainsIds = $pageCache->getChildrenIds(0);
        $result = [];
        foreach ($domainsIds as $domainId) {
            $domainData = $pageCache->cache[$domainId];
            $result[] = [
                'value' => (int)$domainData['id'],
                'caption' => $domainData['name']
            ];
        }
        usort($result, function ($a, $b) {
            return strnatcmp($a['caption'], $b['caption']);
        });
        return $result;
    }


    /**
     * Получает список страниц для отображения в поле "Страница"
     * @param int|null $pid ID# родительской страницы или null,
     *                      если нужно отобразить корневую страницу
     * @param int|null $domainId ID# домена (только для фильтрации корневых страниц)
     * @return array<[
     *             'value' => int ID# страницы,
     *             'caption' => string Наименование страницы,
     *             'data-src' => string URL страницы,
     *             'children' => *рекурсивно*
     *         ]>
     */
    public function getPages($pid = null, $domainId = null)
    {
        $pageCache = PageRecursiveCache::i();
        $result = [];
        if ($pid === null) {
            $result[] = [
                'value' => '',
                'caption' => '--',
                'children' => $this->getPages(0, $domainId),
            ];
        } else {
            if (!$pid && $domainId) {
                $pagesIds = [$domainId];
            } else {
                $pagesIds = $pageCache->getChildrenIds($pid);
            }
            $pagesData = [];
            foreach ($pagesIds as $pageId) {
                $pageData = $pageCache->cache[$pageId];
                $pagesData[] = [
                    'value' => (int)$pageData['id'],
                    'caption' => $pageData['menu_name'] ?: $pageData['name'],
                    'data-src' => $pageData['cache_url'],
                ];
            }
            foreach ($pagesData as $pageData) {
                if ($children = $this->getPages((int)$pageData['value'], null)) {
                    $pageData['children'] = $children;
                }
                $result[] = $pageData;
            }
        }
        return $result;
    }
}
