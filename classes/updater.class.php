<?php
namespace RAAS\CMS\Shop;
use \RAAS\IContext;

class Updater extends \RAAS\Updater
{
    public function __construct(IContext $Context)
    {
        parent::__construct($Context);
        $this->checkStdSnippets();
    }

    protected function checkStdSnippets()
    {
        $Item = Snippet::importByURN('__RAAS_shop_cart_interface');
        if (!$Item->id) {
            $Item = new Snippet(array(
                'pid' => Snippet_Folder::importByURN('__RAAS_interfaces')->id, 
                'urn' => '__RAAS_shop_cart_interface', 
                'name' => $this->view->_('CART_INTERFACE'), 
                'locked' => 1
            ));
        }
        $Item->description = $this->stdCartInterface;
        $Item->commit();
    }
}