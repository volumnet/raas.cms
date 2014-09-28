<?php
namespace RAAS\CMS;

class CopySnippetForm extends EditSnippetForm
{
    public function __construct(array $params = array())
    {
        $params['selfUrl'] = Sub_Dev::i()->url . '&action=edit_snippet&id=%d';
        $params['newUrl'] = Sub_Dev::i()->url . '&action=edit_snippet';
        $params['caption'] = $this->view->_('COPY_SNIPPET');
        parent::__construct($params);
        $Item = isset($params['Item']) ? $params['Item'] : null;
        foreach ($this->children as $row) {
            if ($Item->{$row->name}) {
                $row->default = $Item->{$row->name};
            }
        }
    }
}