<?php
/**
 * Файл стандартного интерфейса формы
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Attachment;
use RAAS\Application;
use Mustache_Engine;
use RAAS\Controller_Frontend as RAASControllerFrontend;

/**
 * Класс стандартного интерфейса формы
 */
class FormInterface extends AbstractInterface
{
    /**
     * Конструктор класса
     * @param Block_Form|null $block Блок, для которого применяется
     *                               интерфейс
     * @param Page|null $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        Block_Form $block = null,
        Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct(
            $block,
            $page,
            $get,
            $post,
            $cookie,
            $session,
            $server,
            $files
        );
    }


    public function process()
    {
        $result = [];
        $form = $this->block->Form;
        if ($form->id) {
            $localError = [];
            if ($this->isFormProceed(
                $this->block,
                $form,
                $this->server['REQUEST_METHOD'],
                $this->post
            )) {
                // Проверка полей на корректность
                $localError = $this->check(
                    $form,
                    $this->post,
                    $this->session,
                    $this->files
                );

                if (!$localError) {
                    $result = array_merge($result, $this->processForm(
                        $form,
                        $this->page,
                        $this->post,
                        $this->server,
                        $this->files
                    ));
                    $result['success'][(int)$this->block->id] = true;
                }
                $result['DATA'] = $this->post;
                $result['localError'] = $localError;
            } else {
                $result['DATA'] = [];
                foreach ($form->fields as $key => $row) {
                    if ($row->defval) {
                        $result['DATA'][$key] = $row->defval;
                    }
                }
                $result['localError'] = [];
            }
        }
        $result['Form'] = $form;

        return $result;
    }


    /**
     * Проверяет, действительно ли форма отправлена
     * @param Block $block Блок для обработки
     * @param Form $form Форма для обработки
     * @param 'GET'|'POST' $requestMethod Метод запроса
     * @param array<string[] => mixed> $post Данные POST-запроса
     * @return bool
     */
    protected function isFormProceed(
        Block $block,
        Form $form,
        $requestMethod = 'GET',
        array $post = []
    ) {
        if ($form->signature) {
            if (isset($post['form_signature'])) {
                $signature = md5('form' . (int)$form->id . (int)$block->id);
                return $post['form_signature'] == $signature;
            }
        } else {
            return mb_strtolower($requestMethod) == 'post';
        }
        return false;
    }


    /**
     * Проверяет правильность заполнения формы
     * @param Form $form Форма
     * @param array $post Данные $_POST-полей
     * @param array $files Данные $_FILES-полей
     * @param array $files Данные $_SESSION-полей
     * @return array<string[] URN поля => string Текстовое описание ошибки>
     */
    protected function check(
        Form $form,
        array $post = [],
        array $session = [],
        array $files = []
    ) {
        $localError = [];
        foreach ($form->fields as $fieldURN => $field) {
            switch ($field->datatype) {
                case 'file':
                case 'image':
                    if ($fieldError = $this->checkFileField($field, $files)) {
                        $localError[$fieldURN] = $fieldError;
                    }
                    break;
                default:
                    if ($fieldError = $this->checkRegularField($field, $post)) {
                        $localError[$fieldURN] = $fieldError;
                    }
                    break;
            }
        }

        // Проверка на антиспам
        if ($fieldError = $this->checkAntispamField($form, $post, $session)) {
            $localError[$fieldURN] = $fieldError;
        }
        return $localError;
    }


    /**
     * Проверка на корректность регулярного поля
     * @param Form_Field $field Поле для проверки
     * @param array $post Данные $_POST-полей
     * @return string|null Текстовое описание ошибки, либо null,
     *                     если ошибка отсутствует
     */
    protected function checkRegularField(Form_Field $field, array $post = [])
    {
        $fieldURN = $field->urn;
        $val = isset($post[$fieldURN]) ? $post[$fieldURN] : null;
        if ($val && $field->multiple) {
            $val = (array)$val;
            $val = array_shift($val);
        }
        if (!isset($val) || !$field->isFilled($val)) {
            if ($field->required) {
                return sprintf(ERR_CUSTOM_FIELD_REQUIRED, $field->name);
            }
        } elseif (!$field->multiple) {
            if (($field->datatype == 'password') &&
                ($post[$fieldURN] != $post[$fieldURN . '@confirm'])
            ) {
                return sprintf(
                    ERR_CUSTOM_PASSWORD_DOESNT_MATCH_CONFIRM,
                    $field->name
                );
            } elseif (!$field->validate($val)) {
                return sprintf(ERR_CUSTOM_FIELD_INVALID, $field->name);
            }
        }
        return null;
    }


    /**
     * Проверка на корректность регулярного поля
     * @param Form_Field $field Поле для проверки
     * @param array $files Данные $_FILES-полей
     * @return string|null Текстовое описание ошибки, либо null,
     *                     если ошибка отсутствует
     */
    protected function checkFileField(Form_Field $field, array $files = [])
    {
        $fieldURN = $field->urn;
        $val = isset($files[$fieldURN]['tmp_name'])
             ? $files[$fieldURN]['tmp_name']
             : null;
        if ($val && $field->multiple) {
            $val = (array)$val;
            $val = array_shift($val);
        }
        if (!isset($val) || !$field->isFilled($val)) {
            if ($field->required && !$field->countValues()) {
                return sprintf(ERR_CUSTOM_FIELD_REQUIRED, $field->name);
            }
        } elseif (!$field->multiple) {
            if (!$field->validate($val)) {
                return sprintf(ERR_CUSTOM_FIELD_INVALID, $field->name);
            }
        }
        $allowedExtensions = preg_split('/\\W+/umis', $field->source);
        if ($allowedExtensions) {
            $possibleExtensionError = sprintf(
                INVALID_FILE_EXTENSION,
                implode(', ', $allowedExtensions)
            );
            $fileTmpNameArr = (array)$files[$fieldURN]['tmp_name'];
            $fileNameArr = (array)$files[$fieldURN]['name'];
            foreach ($fileTmpNameArr as $i => $val) {
                if (!$this->checkFileMatchesAllowedExtensions(
                    $fileNameArr[$i],
                    $val,
                    $allowedExtensions
                )) {
                    return $possibleExtensionError;
                }
            }
        }
        return null;
    }


    /**
     * Проверяет, соответствует ли имя файла допустимым расширениям
     * @param string $filename Имя файла
     * @param string $filepath Реальный путь к файлу
     * @param array<string> $allowedExtensions Список допустимых расширений
     * @return bool
     */
    protected function checkFileMatchesAllowedExtensions(
        $filename,
        $filepath,
        array $allowedExtensions = []
    ) {
        if (is_uploaded_file($filepath)) {
            $ext = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedExtensions = array_map(
                'mb_strtolower',
                array_filter($allowedExtensions, 'trim')
            );
            return in_array($ext, $allowedExtensions);
        }
        return true;
    }


    /**
     * Проверяет на корректность антиспам-поля
     * @param Form $form Форма
     * @param array $post Данные $_POST-полей
     * @param array $session Данные $_SESSION-полей
     * @return string|null Текстовое описание ошибки, либо null,
     *                     если ошибка отсутствует
     */
    protected function checkAntispamField(
        Form $form,
        array $post = [],
        array $session = []
    ) {
        $antispamType = $form->antispam;
        $fieldURN = $form->antispam_field_name;
        if ($antispamType && $fieldURN) {
            switch ($antispamType) {
                case 'captcha':
                    if (!isset($post[$fieldURN]) ||
                        !isset($session['captcha_keystring']) ||
                        ($post[$fieldURN] != $session['captcha_keystring'])
                    ) {
                        return ERR_CAPTCHA_FIELD_INVALID;
                    }
                    break;
                case 'hidden':
                    if (isset($post[$fieldURN]) && $post[$fieldURN]) {
                        return ERR_CAPTCHA_FIELD_INVALID;
                    }
                    break;
            }
        }
        return null;
    }


    /**
     * Обрабатывает форму
     * @param Form $form Форма
     * @param Page $page Текущая страница
     * @param array $post Данные $_POST-полей
     * @param array $server Данные $_SERVER-полей
     * @param array $files Данные $_FILES-полей
     * @return [
     *             'Item' =>? Feedback Уведомление обратной связи,
     *             'Material' =>? Material Созданный материал
     *         ]
     */
    protected function processForm(
        Form $form,
        Page $page,
        array $post = [],
        array $server = [],
        array $files = []
    ) {
        $feedback = $this->getRawFeedback($form);

        // Для AJAX'а
        $this->processFeedbackReferer($feedback, $page, $server);
        $user = RAASControllerFrontend::i()->user;
        $feedback->uid = ($user instanceof User) ? (int)$user->id : 0;
        $this->processUserData($feedback, $server);
        $objects = [$feedback];
        if ($material = $this->getRawMaterial($form)) {
            if (!$form->Material_Type->global_type) {
                $material->cats = [(int)$feedback->page_id];
            }
            $objects[] = $material;
        }

        foreach ($objects as $object) {
            $this->processObject($object, $feedback, $post, $server, $files);
        }
        if ($form->email) {
            $this->notify($feedback, $material);
        }
        if (!$form->create_feedback) {
            Feedback::delete($feedback);
        }
        return ['Item' => $feedback, 'Material' => $material];
    }


    /**
     * Получает "сырое" созданное уведомление (без commit'а и заполненных полей)
     * @param Form $form Форма обратной связи
     * @return Feedback
     */
    protected function getRawFeedback(Form $form)
    {
        return new Feedback(['pid' => (int)$form->id]);
    }


    /**
     * Получает "сырой" созданный материал (без commit'а и заполненных полей),
     * если форма поддерживает создание материала
     * @return Material|null null, если форма не поддерживает создание материала
     */
    protected function getRawMaterial(Form $form)
    {
        if ($mTypeId = $form->Material_Type->id) {
            return new Material(['pid' => (int)$mTypeId, 'vis' => 0]);
        }
        return null;
    }


    /**
     * Устанавливает страницу и материал уведомлению обратной связи
     * @param Feedback $feedback Уведомление обратной связи
     * @param Page $page Текущая страница
     * @param arrary $server Данные $_SERVER-полей
     */
    protected function processFeedbackReferer(
        Feedback $feedback,
        Page $page,
        array $server = []
    ) {
        $refererURL = $server['HTTP_REFERER'];
        $referer = Page::importByURL($server['HTTP_REFERER']);

        $refererRelativeURL = parse_URL($refererURL, PHP_URL_PATH);
        $refererURLArray = explode('/', trim($refererRelativeURL, '/'));
        $refererMaterialURN = $refererURLArray[count($refererURLArray) - 1];
        $refererMaterial = Material::importByURN($refererMaterialURN);

        $feedback->page_id = (int)$referer->id ?: (int)$page->id;
        if ($refererMaterial) {
            $feedback->material_id = (int)$refererMaterial->id;
        } elseif ($materialId = $page->Material->id) {
            $feedback->material_id = (int)$materialId;
        }
    }


    /**
     * Подставляет данные пользователя в объект
     * @param SOME $object Объект для заполнения
     * @param array $server Данные $_SERVER-полей
     */
    protected function processUserData(SOME $object, array $server = [])
    {
        foreach ([
            'ip' => 'REMOTE_ADDR',
            'user_agent' => 'HTTP_USER_AGENT'
        ] as $key => $val) {
            $object->$key = trim($server[$val]);
        }
    }



    /**
     * Обрабатывает объект, порождаемый формой (материал или уведомление)
     * @param Material|Feedback $object Объект для заполнения
     * @param Feedback $feedback Уведомление обратной связи
     * @param array $post Данные $_POST-полей
     * @param array $server Данные $_SERVER-полей
     * @param array $files Данные $_FILES-полей
     */
    public function processObject(
        SOME $object,
        Feedback $feedback,
        array $post = [],
        array $server = [],
        array $files = []
    ) {
        // Заполняем основные данные создаваемого материала
        if ($object instanceof Material) {
            $this->processMaterialHeader($object, $feedback);
        }

        $object->commit();

        // Автоматически подставляем недостающие поля даты/времени у материала
        if ($object instanceof Material) {
            $this->processObjectDates($object, $post);
        }

        // Заполним кастомные поля
        $this->processObjectFields($object, $feedback->parent, $post, $files);

        // Заполняем данные пользователя в полях материала
        if ($object instanceof Material) {
            $this->processObjectUserData($object, $server);
        }
    }


    /**
     * Обрабатывает название и описание материала
     * @param Material $material Материал для заполнения
     * @param Feedback $feedback Уведомление формы обратной связи
     */
    protected function processMaterialHeader(
        Material $material,
        Feedback $feedback
    ) {
        if (isset($feedback->fields['_name_'])) {
            $material->name = $feedback->fields['_name_']->getValue();
        } else {
            $material->name = $feedback->parent->Material_Type->name . ': '
                            . date(DATETIMEFORMAT);
        }
        if (isset($feedback->fields['_description_'])) {
            $material->description = $feedback->fields['_description_']->getValue();
        }
    }


    /**
     * Подставляет даты в объект
     * @param SOME $object Объект для заполнения
     * @param Feedback $feedback Уведомление формы обратной связи
     */
    protected function processObjectDates(SOME $object, array $post = [])
    {
        foreach ($object->fields as $fieldURN => $field) {
            if (!isset($post[$fieldURN])) {
                switch ($field->datatype) {
                    case 'datetime':
                    case 'datetime-local':
                        $field->addValue(date('Y-m-d H:i:s'));
                        break;
                    case 'date':
                        $field->addValue(date('Y-m-d'));
                        break;
                    case 'time':
                        $field->addValue(date('H:i:s'));
                        break;
                }
            }
        }
    }


    /**
     * Подставляет данные пользователя в объект
     * @param SOME $object Объект для заполнения
     * @param array $server Данные $_SERVER-полей
     */
    protected function processObjectUserData(SOME $object, array $server = [])
    {
        foreach ([
            'ip' => 'REMOTE_ADDR',
            'user_agent' => 'HTTP_USER_AGENT'
        ] as $key => $val) {
            if (isset($object->fields[$key]) &&
                ($field = $object->fields[$key])
            ) {
                $field->deleteValues();
                $field->addValue(trim($server[$val]));
            }
        }
    }


    /**
     * Обрабатывает кастомные поля объекта
     * @param Material|Feedback $object Объект для заполнения
     * @param Form $form Форма обратной связи
     * @param array $post Данные $_POST-полей
     * @param array $files Данные $_FILES-полей
     */
    protected function processObjectFields(
        SOME $object,
        Form $form,
        array $post = [],
        array $files = []
    ) {
        foreach ($form->fields as $fieldURN => $temp) {
            if (isset($object->fields[$fieldURN])) {
                $field = $object->fields[$fieldURN];
                switch ($field->datatype) {
                    case 'file':
                    case 'image':
                        $this->processFileField($field, $object, $post, $files);
                        $field->clearLostAttachments();
                        break;
                    default:
                        $this->processRegularField($field, $post);
                        break;
                }
            }
        }
    }


    /**
     * Обрабатывает регулярное поле
     * @param Field $field Поле для заполнения (у материала или уведомления)
     * @param array $post Данные $_POST-полей
     */
    protected function processRegularField(Field $field, array $post = [])
    {
        $field->deleteValues();
        $fieldURN = $field->urn;
        if (isset($post[$fieldURN])) {
            foreach ((array)$post[$fieldURN] as $val) {
                // 2019-01-22, AVS: закрываем XSS-уязвимость
                $field->addValue(strip_tags($val));
            }
        }
    }


    /**
     * Обрабатывает файловое поле
     * @param Field $field Поле для заполнения (у материала или уведомления)
     * @param SOME $parent Родительский объект для вложения
     * @param array $post Данные $_POST-полей
     * @param array $files Данные $_FILES-полей
     */
    protected function processFileField(
        Field $field,
        SOME $parent,
        array $post = [],
        array $files = []
    ) {
        $field->deleteValues();
        $fieldURN = $field->urn;
        $visArr = (array)$post[$fieldURN . '@vis'];
        $nameArr = (array)$post[$fieldURN . '@name'];
        $descriptionArr = (array)$post[$fieldURN . '@description'];
        $attachmentArr = (array)$post[$fieldURN . '@attachment'];
        $fileTmpNameArr = (array)$files[$fieldURN]['tmp_name'];
        $fileNameArr = (array)$files[$fieldURN]['name'];
        $fileTypeArr = (array)$files[$fieldURN]['type'];

        foreach ($fileTmpNameArr as $key => $val) {
            $data = [
                'vis' => isset($visArr[$key]) ? (int)$visArr[$key] : 1,
                'name' => trim($nameArr[$key]),
                'description' => trim($descriptionArr[$key]),
                'attachment' => (int)$attachmentArr[$key],
            ];
            if (is_uploaded_file($val) && $field->validate($val)) {
                $att = new Attachment((int)$data['attachment']);
                $att->upload = $val;
                $att->filename = $fileNameArr[$key];
                $att->mime = $fileTypeArr[$key];
                $att->parent = $parent;
                if ($field->datatype == 'image') {
                    $att->image = 1;
                    if ($maxSize = (int)Package::i()->registryGet('maxsize')) {
                        $att->maxWidth = $att->maxHeight = $maxSize;
                    }
                    if ($tnSize = (int)Package::i()->registryGet('tnsize')) {
                        $att->tnsize = $tnSize;
                    }
                }
                $att->copy = true;
                $att->commit();
                $data['attachment'] = (int)$att->id;
                $field->addValue(json_encode($data));
            } elseif ($data['attachment']) {
                $field->addValue(json_encode($data));
            }
        }
    }


    /**
     * Уведомление администратора о заполненной форме
     * @param Feedback $feedback Уведомление формы обратной связи
     * @param Material $material Созданный материал
     * @param bool $debug Режим отладки
     * @return array<[
     *             'emails' => array<string> e-mail адреса,
     *             'subject' => string Тема письма,
     *             'message' => string Тело письма,
     *             'from' => string Поле "от",
     *             'fromEmail' => string Обратный адрес
     *         ]>|null Набор отправляемых писем либо URL SMS-шлюза
     *                            (только в режиме отладки)*/
    protected function notify(
        Feedback $feedback,
        Material $material = null,
        $debug = false
    ) {
        if (!$feedback->parent->Interface->id) {
            return;
        }
        $form = $feedback->parent;
        $formAddresses = $this->parseFormAddresses($form);
        $template = $form->Interface;

        $notificationData = [
            'Item' => $feedback,
            'Material' => $material,
            'formInterface' => $this,
        ];

        $subject = $this->getEmailSubject($feedback);
        $message = $this->getMessageBody(
            $template,
            array_merge($notificationData, ['SMS' => false])
        );
        $smsMessage = $this->getMessageBody(
            $template,
            array_merge($notificationData, ['SMS' => true])
        );
        $fromName = $this->getFromName();
        $fromEmail = $this->getFromEmail();

        if ($emails = $formAddresses['emails']) {
            if ($debug) {
                $debugMessages[] = [
                    'emails' => $emails,
                    'subject' => $subject,
                    'message' => $message,
                    'from' => $fromName,
                    'fromEmail' => $fromEmail,
                ];
            } else {
                Application::i()->sendmail(
                    $emails,
                    $subject,
                    $message,
                    $fromName,
                    $fromEmail
                );
            }
        }

        if ($smsEmails = $formAddresses['smsEmails']) {
            if ($debug) {
                $debugMessages[] = [
                    'emails' => $emails,
                    'subject' => $subject,
                    'message' => $message,
                    'from' => $fromName,
                    'fromEmail' => $fromEmail,
                ];
            } else {
                Application::i()->sendmail(
                    $smsEmails,
                    $subject,
                    $smsMessage,
                    $fromName,
                    $fromEmail,
                    false
                );
            }
        }

        if ($smsPhones = $formAddresses['smsPhones']) {
            $urlTemplate = Package::i()->registryGet('sms_gate');
            $m = new Mustache_Engine();
            foreach ($smsPhones as $phone) {
                $url = $m->render($urlTemplate, [
                    'PHONE' => urlencode($phone),
                    'TEXT' => urlencode($smsMessage)
                ]);
                if ($debug) {
                    $debugMessages[] = $url;
                } else {
                    $result = file_get_contents($url);
                }
            }
        }
        if ($debug) {
            return $debugMessages;
        }
    }


    /**
     * Получает список адресов формы
     * @param Form $form Форма обратной связи
     * @return [
     *             'emails' => array<string> Список настоящих e-mail,
     *             'smsEmails' => array<string> Список e-mail
     *                                          для SMS-уведомлений,
     *             'smsPhones' => array<string> Список телефонов
     *                                          для SMS-уведомлений
     *                                          в формате +79990000000
     *                                          или 79990000000
     *         ]
     */
    protected function parseFormAddresses(Form $form)
    {
        $addresses = preg_split('/( |;|,)/', trim($form->email));
        $addresses = array_map('trim', $addresses);
        $addresses = array_values(array_filter($addresses));
        $result = [];
        foreach ($addresses as $address) {
            if (($address[0] == '[') &&
                ($address[strlen($address) - 1] == ']')
            ) {
                $address = trim(substr($address, 1, -1));
                if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    $result['smsEmails'][] = $address;
                } elseif (preg_match('/(\\+)?\\d+/umi', $address)) {
                    $result['smsPhones'][] = $address;
                }
            } else {
                if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    $result['emails'][] = $address;
                }
            }
        }
        return $result;
    }


    /**
     * Получает заголовок e-mail сообщения
     * @param Feedback $feedback Уведомление обратной связи
     * @return string
     */
    protected function getEmailSubject(Feedback $feedback)
    {
        $subject = date(DATETIMEFORMAT) . ' ' . sprintf(
            FEEDBACK_STANDARD_HEADER,
            $feedback->parent->name,
            $feedback->page->name
        );
        return $subject;
    }


    /**
     * Получает тело сообщения
     * @param Snippet $template Шаблон уведомления
     * @param array $data Данные для отработки шаблона
     * @return string
     */
    protected function getMessageBody(Snippet $template, array $data = [])
    {
        ob_start();
        $template->process($data);
        $message = ob_get_clean();
        return $message;
    }


    /**
     * Получает значение поля "От"
     * @return string
     */
    protected function getFromName()
    {
        $host = $this->server['HTTP_HOST'];
        if (function_exists('idn_to_utf8')) {
            $host = idn_to_utf8($host);
        }
        return ADMINISTRATION_OF_SITE . ' ' . $host;
    }


    /**
     * Получает значение обратного адреса
     * @return string
     */
    protected function getFromEmail()
    {
        $host = $this->server['HTTP_HOST'];
        return 'info@' . $host;
    }
}
