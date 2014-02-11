<?php
namespace RAAS\CMS;

class EditSnippetForm extends \RAAS\Form
{
    public function __construct(array $params = array())
    {
        $view = isset($params['view']) ? $params['view'] : null;
        unset($params['view']);
        $Item = isset($params['Item']) ? $params['Item'] : null;
        $CONTENT = array('pid' => array(new Snippet_Folder(array('name' => $this->view->_('ROOT_FOLDER'), 'id' => 0))));
        $filter = function($x) use ($Item) { return $x->id != $Item->id; };
        $defaultParams = array(
            'caption' => $view->_('EDIT_SNIPPET'),
            'parentUrl' => Sub_Dev::i()->url . '&action=snippets',
            'children' => array(
                array('name' => 'name', 'caption' => $view->_('NAME'), 'required' => 'required'), 
                array('name' => 'urn', 'caption' => $view->_('URN')), 
                array('type' => 'select', 'name' => 'pid', 'caption' => $view->_('PARENT_FOLDER'), 'children' => array('Set' => $CONTENT['pid'])),
                array('type' => 'codearea', 'name' => 'description', 'caption' => $view->_('SOURCE_CODE'))
            )
        );
        $arr = array_merge($defaultParams, $params);
        parent::__construct($arr);
    }
}