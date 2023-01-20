<?php
/**
 * Абстрактный контроллер
 */
namespace RAAS\CMS;

use RAAS\Abstract_Package_Controller as RAASAbstractPackageController;
use RAAS\Application;

/**
 * Класс абстрактного контроллера
 */
abstract class Abstract_Controller extends RAASAbstractPackageController
{
    protected static $instance;

    protected function execute()
    {
        switch ($this->sub) {
            case 'dev':
            case 'feedback':
                parent::execute();
                break;
            default:
                Sub_Main::i()->run();
                break;
        }
        if (!$this->model->registryGet('clear_cache_manually') || Application::i()->debug) {
            $this->model->clearCache();
        }
    }


    public function config()
    {
        return [
            [
                'type' => 'number',
                'name' => 'tnsize',
                'caption' => $this->view->_('THUMBNAIL_SIZE')
            ],
            [
                'type' => 'number',
                'name' => 'maxsize',
                'caption' => $this->view->_('MAX_IMAGE_SIZE')
            ],
            [
                'name' => 'sms_gate',
                'caption' => $this->view->_('SMS_GATE'),
                'data-hint' => $this->view->_('SMS_GATE_HINT'),
            ],
            [
                'type' => 'checkbox',
                'name' => 'diag',
                'caption' => $this->view->_('ENABLE_DIAGNOSTICS')
            ],
            [
                'type' => 'checkbox',
                'name' => 'clear_cache_manually',
                'caption' => $this->view->_('CLEAR_CACHE_MANUALLY')
            ],
            [
                'type' => 'number',
                'name' => 'clear_cache_by_time',
                'caption' => $this->view->_('CLEAR_CACHE_BY_TIME')
            ],
            [
                'type' => 'number',
                'name' => 'cache_leave_free_space',
                'caption' => $this->view->_('CACHE_LEAVE_FREE_SPACE')
            ],
        ];
    }
}
