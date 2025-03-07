<?php
/**
 * Стандартный интерфейс формы
 */
declare(strict_types=1);

namespace RAAS\CMS;

use SOME\SOME;
use SOME\Text;
use SOME\Thumbnail;
use RAAS\Attachment;
use RAAS\Application;
use RAAS\Controller_Frontend as RAASControllerFrontend;
use RAAS\DatatypeInvalidValueException;
use RAAS\DatatypeImageTypeMismatchException;
use RAAS\DatatypeFileTypeMismatchException;
use RAAS\DateTimeDatatypeStrategy;
use RAAS\View_Web as RAASViewWeb;

/**
 * Стандартный интерфейс формы
 */
class FormInterface extends BlockInterface
{
    /**
     * Максимальный размер (px, по большей стороне) изображений для письма
     */
    const MAIL_SIZE = 600;

    /**
     * Условно обязательные поля
     * @var array <pre><code>array<
     *     string[] URN поля => function (
     *         Form_Field $field Поле,
     *         array $post POST- или FILES- (для файловых полей) данные
     *     ): bool Требуется ли поле
     * ></code></pre>
     */
    public $conditionalRequiredFields = [];

    /**
     * Конструктор класса
     * @param ?Block_Form $block Блок, для которого применяется
     *                               интерфейс
     * @param ?Page $page Страница, для которой применяется интерфейс
     * @param array $get Поля $_GET параметров
     * @param array $post Поля $_POST параметров
     * @param array $cookie Поля $_COOKIE параметров
     * @param array $session Поля $_SESSION параметров
     * @param array $server Поля $_SERVER параметров
     * @param array $files Поля $_FILES параметров
     */
    public function __construct(
        ?Block_Form $block = null,
        ?Page $page = null,
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $session = [],
        array $server = [],
        array $files = []
    ) {
        parent::__construct($block, $page, $get, $post, $cookie, $session, $server, $files);
    }


    public function process(): array
    {
        $result = [];
        $form = $this->block->Form;
        if ($form->id) {
            $localError = [];
            if ($this->isFormProceed($this->block, $form, $this->server['REQUEST_METHOD'] ?? 'GET', $this->post)) {
                // 2019-10-02, AVS: добавили для совместимости с виджетом, где даже
                // в случае ошибок проверяется соответствие
                // ($Item instanceof Feedback)
                // 2019-11-14, AVS: перенес сюда, иначе при AJAX-запросе
                // первая попавшаяся форма отключает
                $result['Item'] = $this->getRawFeedback($form);
                // Проверка полей на корректность
                $localError = $this->check($form, $this->post, $this->session, $this->files);

                if (!$localError) {
                    $result = array_merge(
                        $result,
                        $this->processForm($form, $this->page, $this->post, $this->server, $this->files)
                    );
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
    public function isFormProceed(Block $block, Form $form, $requestMethod = 'GET', array $post = [])
    {
        if ($form->signature) {
            if (isset($post['form_signature'])) {
                return $post['form_signature'] == $form->getSignature($block);
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
    public function check(Form $form, array $post = [], array $session = [], array $files = [])
    {
        $localError = [];
        foreach ($form->fields as $fieldURN => $field) {
            if ($field->datatypeStrategy->isMedia()) {
                if ($fieldError = $this->checkFileField($field, $files)) {
                    $localError[$fieldURN] = $fieldError;
                }
            } else {
                if ($fieldError = $this->checkRegularField($field, $post)) {
                    $localError[$fieldURN] = $fieldError;
                }
            }
        }

        // Проверка на антиспам
        if ($fieldError = $this->checkAntispamField($form, $post, $session)) {
            $localError[$form->antispam_field_name] = $fieldError;
        }
        return $localError;
    }


    /**
     * Проверка на корректность регулярного поля
     * @param Field $field Поле для проверки
     * @param array $post Данные $_POST-полей
     * @return string|null Текстовое описание ошибки, либо null,
     *                     если ошибка отсутствует
     */
    public function checkRegularField(Field $field, array $post = [])
    {
        $fieldURN = $field->urn;
        $postArr = $field->datatypeStrategy->getPostData($field, true, $post);
        $val = $postArr;
        $val = array_shift($val);
        $isFilled = false;
        foreach ($postArr as $value) {
            if ($field->datatypeStrategy->isFilled($value)) {
                $isFilled = true;
                break;
            }
        }

        $localError = null;
        if (!$isFilled) {
            if ($conditionalRequiredCallback = ($this->conditionalRequiredFields[$fieldURN] ?? null)) {
                $required = $conditionalRequiredCallback($field, $post);
            } else {
                $required = (bool)$field->required;
            }
            if ($required) {
                $localError = 'ERR_CUSTOM_FIELD_REQUIRED';
            }
        } elseif (!$field->multiple) {
            if (($field->datatype == 'password') && ($post[$fieldURN] != $post[$fieldURN . '@confirm'])) {
                $localError = 'ERR_CUSTOM_PASSWORD_DOESNT_MATCH_CONFIRM';
            } else {
                try {
                    $field->datatypeStrategy->validate($val, $field->Field);
                } catch (DatatypeInvalidValueException $e) {
                    $localError = 'ERR_CUSTOM_FIELD_INVALID';
                }
            }
        }
        if ($localError) {
            return sprintf(View_Web::i()->_($localError), $field->name);
        }
        return null;
    }


    /**
     * Проверка на корректность файлового поля
     * @param Field $field Поле для проверки
     * @param array $files Данные $_FILES-полей
     * @param bool $debug Режим отладки
     * @return string|null Текстовое описание ошибки, либо null,
     *                     если ошибка отсутствует
     * @todo Нужна проверка множественных требуемых полей изображений
     */
    public function checkFileField(Field $field, array $files = [], $debug = false)
    {
        $fieldURN = $field->urn;
        $filesArr = $field->datatypeStrategy->getFilesData($field, true, false, $files);
        $val = $filesArr;
        $val = array_shift($val);
        $isFilled = false;
        foreach ($filesArr as $file) {
            if ($field->datatypeStrategy->isFilled($file['tmp_name'], $debug)) {
                $isFilled = true;
                break;
            }
        }

        if (!$isFilled) {
            if ($conditionalRequiredCallback = ($this->conditionalRequiredFields[$fieldURN] ?? null)) {
                $required = $conditionalRequiredCallback($field, $files);
            } else {
                $required = $field->required;
            }
            if ($required && !$field->countValues()) {
                return sprintf(View_Web::i()->_('ERR_CUSTOM_FIELD_REQUIRED'), $field->name);
            }
        } elseif (!$field->multiple) {
            try {
                $field->datatypeStrategy->validate($val, $field->Field);
            } catch (DatatypeImageTypeMismatchException $e) {
                return sprintf(View_Web::i()->_('ERR_INVALID_IMAGE_FORMAT'), $field->name);
            } catch (DatatypeFileTypeMismatchException $e) {
                $allowedExtensions = trim((string)$field->source) ? preg_split('/\\W+/umis', $field->source) : [];
                if ($allowedExtensions) {
                    $allowedExtensions = mb_strtoupper(implode(', ', $allowedExtensions));
                    return sprintf(
                        View_Web::i()->_('ERR_CUSTOM_FILE_INVALID_WITH_TYPES'),
                        $field->name,
                        $allowedExtensions
                    );
                // @codeCoverageIgnoreStart
                // 2024-03-13, AVS: По факту сейчас такого быть не может, поскольку DatatypeFileTypeMismatchException
                // в чистом виде (не DatatypeImageTypeMismatchException) выбрасывается только в слуае несовпадения с
                // accept (он же allowedExtensions)
                } else {
                    return sprintf(View_Web::i()->_('ERR_CUSTOM_FILE_INVALID'), $field->name);
                }
                // @codeCoverageIgnoreEnd
            } catch (DatatypeInvalidValueException $e) {
                 return sprintf(View_Web::i()->_('ERR_CUSTOM_FIELD_INVALID'), $field->name);
            }
        }
        return null;
    }


    /**
     * Проверяет, соответствует ли имя файла допустимым расширениям
     * @param string $filename Имя файла
     * @param string $filepath Реальный путь к файлу
     * @param array<string> $allowedExtensions Список допустимых расширений
     * @param bool $debug Режим отладки
     * @return bool
     * @deprecated 2023-12-04, AVS: используется стратегия типов данных
     */
    public function checkFileMatchesAllowedExtensions(
        $filename,
        $filepath,
        array $allowedExtensions = [],
        $debug = false
    ) {
        if (is_uploaded_file($filepath) || $debug) {
            $ext = mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedExtensions = array_map('mb_strtolower', array_filter($allowedExtensions, 'trim'));
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
    public function checkAntispamField(Form $form, array $post = [], array $session = [])
    {
        $antispamType = $form->antispam;
        $fieldURN = $form->antispam_field_name;
        if ($antispamType == 'smart') {
            $antispam = new Antispam($form, $this->page->lang);
            if (!$antispam->check($post)) {
                return View_Web::i()->_('ERR_CAPTCHA_FIELD_INVALID');
            }
        }
        if ($antispamType && $fieldURN) {
            switch ($antispamType) {
                case 'captcha':
                    if (!isset($post[$fieldURN]) ||
                        !isset($session['captcha_keystring']) ||
                        ($post[$fieldURN] != $session['captcha_keystring'])
                    ) {
                        return View_Web::i()->_('ERR_CAPTCHA_FIELD_INVALID');
                    }
                    break;
                case 'hidden':
                case 'smart':
                    if (isset($post[$fieldURN]) && $post[$fieldURN]) {
                        return View_Web::i()->_('ERR_CAPTCHA_FIELD_INVALID');
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
    public function processForm(Form $form, Page $page, array $post = [], array $server = [], array $files = [])
    {
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
            $this->processObject($object, $form, $post, $server, $files);
        }
        $this->notify($feedback, $material);
        if (!$form->create_feedback) {
            Feedback::delete($feedback);
            $feedback = null;
        }
        $result = [];
        // 2020-03-10, AVS: сделал условие для добавления в результат, чтобы
        // null не перекрывал стандартное значение $result['Item'], т.к. оно
        // используется для проверки AJAX-версии формы
        if ($feedback) {
            $result['Item'] = $feedback;
        }
        if ($material) {
            $result['Material'] = $material;
        }
        return $result;
    }


    /**
     * Получает "сырое" созданное уведомление (без commit'а и заполненных полей)
     * @param Form $form Форма обратной связи
     * @return Feedback
     */
    public function getRawFeedback(Form $form)
    {
        return new Feedback(['pid' => (int)$form->id]);
    }


    /**
     * Получает "сырой" созданный материал (без commit'а и заполненных полей),
     * если форма поддерживает создание материала
     * @return Material|null null, если форма не поддерживает создание материала
     */
    public function getRawMaterial(Form $form)
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
    public function processFeedbackReferer(Feedback $feedback, Page $page, array $server = [])
    {
        $referer = $refererMaterial = null;
        if ($refererURL = ($server['HTTP_REFERER'] ?? null)) {
            $referer = Page::importByURL($server['HTTP_REFERER']);

            $refererRelativeURL = parse_URL($refererURL, PHP_URL_PATH);
            $refererURLArray = array_filter(
                explode('/', trim((string)$refererRelativeURL, '/'))
            );
            if ($refererURLArray) {
                $refererMaterialURN = $refererURLArray[count($refererURLArray) - 1];
                $refererMaterial = Material::importByURN($refererMaterialURN);
            }
        }

        if ($referer && $referer->id) {
            $feedback->page_id = (int)$referer->id;
        } else {
            $feedback->page_id = (int)$page->id;
        }
        if ($refererMaterial) {
            $feedback->material_id = (int)$refererMaterial->id;
        } elseif ($materialId = ($page->Material->id ?? 0)) {
            $feedback->material_id = (int)$materialId;
        }
    }


    /**
     * Подставляет данные пользователя в объект
     * @param SOME $object Объект для заполнения
     * @param array $server Данные $_SERVER-полей
     */
    public function processUserData(SOME $object, array $server = [])
    {
        if (isset($server['HTTP_X_FORWARDED_FOR']) && $server['HTTP_X_FORWARDED_FOR']) {
            $forwardedFor = explode(',', (string)$server['HTTP_X_FORWARDED_FOR']);
            $forwardedFor = array_map('trim', $forwardedFor);
            $object->ip = $forwardedFor[0];
        } elseif (isset($server['REMOTE_ADDR'])) {
            $object->ip = $server['REMOTE_ADDR'];
        }
        foreach (['user_agent' => 'HTTP_USER_AGENT'] as $key => $val) {
            $object->$key = trim((string)($server[$val] ?? ''));
        }
    }



    /**
     * Обрабатывает объект, порождаемый формой (материал или уведомление)
     * @param Material|Feedback $object Объект для заполнения
     * @param Form $form Текущая форма
     * @param array $post Данные $_POST-полей
     * @param array $server Данные $_SERVER-полей
     * @param array $files Данные $_FILES-полей
     */
    public function processObject(SOME $object, Form $form, array $post = [], array $server = [], array $files = [])
    {
        $new = !$object->id;
        // Заполняем основные данные создаваемого материала
        if ($object instanceof Material) {
            $this->processMaterialHeader($object, $form, $post);
        }

        $object->commit();

        // Автоматически подставляем недостающие поля даты/времени у материала
        if ($new && ($object instanceof Material)) {
            $this->processObjectDates($object, $post);
        }

        // Заполним кастомные поля
        $this->processObjectFields($object, $form, $post, $files);

        // Заполняем данные пользователя в полях материала
        if ($new) {
            if ($object instanceof Material) {
                $this->processObjectUserData($object, $server);
            }
            $this->processUTM($object, (array)$post, (array)$this->session);
        }
    }


    /**
     * Обрабатывает название и описание материала
     * @param Material $material Материал для заполнения
     * @param Form $form Текущая форма
     * @param array $post Данные $_POST-полей
     */
    public function processMaterialHeader(Material $material, Form $form, array $post = [])
    {
        if (isset($post['_name_']) && trim((string)$post['_name_'])) {
            $material->name = trim((string)$post['_name_']);
        } elseif (!$material->id) {
            $material->name = $form->Material_Type->name . ': ' . date(RAASViewWeb::i()->_('DATETIMEFORMAT'));
        }
        if (isset($post['_description_']) && trim((string)$post['_description_'])) {
            $material->description = trim((string)$post['_description_']);
        }
    }


    /**
     * Подставляет даты в объект
     * @param SOME $object Объект для заполнения
     * @param Feedback $feedback Уведомление формы обратной связи
     */
    public function processObjectDates(SOME $object, array $post = [])
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
    public function processObjectUserData(SOME $object, array $server = [])
    {
        foreach (['ip' => 'REMOTE_ADDR', 'user_agent' => 'HTTP_USER_AGENT'] as $key => $val) {
            if ($field = ($object->fields[$key] ?? null)) {
                $field->deleteValues();
                $field->addValue(trim((string)($server[$val] ?? '')));
            }
        }
    }


    /**
     * Подставляет UTM-метки в объект
     * @param SOME $object Объект для заполнения
     * @param array $post Данные $_POST-полей
     * @param array $session Данные $_SESSION-полей
     */
    public function processUTM(SOME $object, array $post = [], array $session = [])
    {
        foreach ($object->fields as $fieldURN => $field) {
            if (stristr($fieldURN, 'utm_') && !($post[$fieldURN] ?? null) && trim((string)($session[$fieldURN] ?? ''))) {
                $field->deleteValues();
                $field->addValue(trim((string)$session[$fieldURN]));
            }
        }
    }


    /**
     * Обрабатывает кастомные поля объекта
     * @param Material|Feedback $object Объект для заполнения
     * @param Form $form Форма обратной связи
     * @param array $post Данные $_POST-полей
     * @param array $files Данные $_FILES-полей
     * @param bool $debug Режим отладки
     */
    public function processObjectFields(SOME $object, Form $form, array $post = [], array $files = [], $debug = false)
    {
        foreach ($form->fields as $fieldURN => $temp) {
            if ($field = ($object->fields[$fieldURN] ?? null)) {
                if ($field->datatypeStrategy->isMedia()) {
                    $this->processFileField($field, $post, $files, $debug);
                    $field->clearLostAttachments();
                } else {
                    $this->processRegularField($field, $post);
                }
            }
        }
    }


    /**
     * Обрабатывает регулярное поле
     * @param Field $field Поле для заполнения (у материала или уведомления)
     * @param array $post Данные $_POST-полей
     */
    public function processRegularField(Field $field, array $post = [])
    {
        $field->deleteValues();
        $postData = $field->datatypeStrategy->getPostData($field, true, $post);
        foreach ($postData as $key => $value) {
            $value = trim(strip_tags((string)$value));
            $value = $field->datatypeStrategy->export($value);
            if ($value != null) {
                $field->addValue($value);
            }
        }
    }


    /**
     * Обрабатывает файловое поле
     * @param Field $field Поле для заполнения (у материала или уведомления)
     * @param array $post Данные $_POST-полей
     * @param array $files Данные $_FILES-полей
     * @param bool $debug Режим отладки
     */
    public function processFileField(Field $field, array $post = [], array $files = [], $debug = false)
    {
        // 2024-09-30, AVS: добавил условие, что если поле формы, но форма создает материал, то файлы копируются
        // (иначе при создании feedback'а файлы перемещаются и в материал уже не загружаются)
        $copyFiles = false;
        if (($field instanceof Form_Field) && ($field->parent->material_type)) {
            $copyFiles = true;
        }
        $field->deleteValues();
        $filesData = $field->datatypeStrategy->getFilesData($field, true, true, $files, $post);

        foreach ($filesData as $key => $fileData) {
            // 2017-09-05, AVS: убрал создание attachment'а по ID#, чтобы не было конфликтов
            // в случае дублирования материалов с одним attachment'ом
            // с текущего момента каждый новый загруженный файл - это новый attachment
            // 2024-09-30, AVS: добавил условие, что если поле формы, но форма создает материал, то файлы копируются
            // (иначе при создании feedback'а файлы перемещаются и в материал уже не загружаются)
            if ($copyFiles) {
                $fileData['copy'] = true;
            }
            $attachment = $field->processAttachment($fileData);
            $oldAttachmentId = (int)($fileData['meta']['attachment'] ?? null);
            if (!$attachment && $oldAttachmentId) {
                $attachment = new Attachment($oldAttachmentId);
            }
            if ($attachment && $attachment->id) {
                $attachment->vis = (bool)($fileData['meta']['vis'] ?? true);
                $attachment->name = trim((string)($fileData['meta']['name'] ?? ''));
                $attachment->description = trim((string)($fileData['meta']['description'] ?? ''));
                $value = $field->datatypeStrategy->export($attachment);
                if ($value !== null) {
                    $field->addValue($value);
                }
            }
        }
    }


    /**
     * Уведомление администратора о заполненной форме
     * @param Feedback $feedback Уведомление формы обратной связи
     * @param ?Material $material Созданный материал
     * @param bool $forAdmin Уведомление для администратора
     *                       (если нет, то для пользователя)
     * @param bool $debug Режим отладки
     * @return array|null <pre><code>array<
     *    ('emails'|'smsEmails')[] => [
     *        'emails' => array<string> e-mail адреса,
     *        'subject' => string Тема письма,
     *        'message' => string Тело письма,
     *        'from' => string Поле "от",
     *        'fromEmail' => string Обратный адрес,
     *        'attachments' => array<[
     *            'tmp_name' => string Путь к реальному файлу,
     *            'type' => string MIME-тип файла,
     *            'name' => string Имя файла
     *        ]> вложения,
     *        'embedded' => array<[
     *            'tmp_name' => string Путь к реальному файлу,
     *            'type' => string MIME-тип файла,
     *            'name' => string Имя файла
     *        ]> встроенные файлы,
     *    ],
     *    'smsPhones' => array<string URL SMS-шлюза>
     * >|null</code></pre> Набор отправляемых писем либо URL SMS-шлюза (только в режиме отладки)
     */
    public function notify(Feedback $feedback, ?Material $material = null, $forAdmin = true, $debug = false)
    {
        if (!$feedback->parent->Interface->id) {
            return;
        }
        $form = $feedback->parent;
        if ($forAdmin) {
            $addresses = $this->parseFormAddresses($form);
        } else {
            $addresses = $this->parseUserAddresses($feedback);
        }
        $template = $form->Interface;

        $notificationData = [
            'Item' => $feedback,
            'Material' => $material,
            'formInterface' => $this,
            'ADMIN' => $forAdmin,
            'forUser' => !$forAdmin,
        ];

        $subject = $this->getEmailSubject($feedback, $forAdmin);
        $message = $this->getMessageBody($template, array_merge($notificationData, ['SMS' => false]));
        $smsMessage = $this->getMessageBody($template, array_merge($notificationData, ['SMS' => true]));
        $fromName = $this->getFromName();
        $fromEmail = $this->getFromEmail();
        $debugMessages = [];
        $attachments = $this->getAttachments($feedback, $material, $forAdmin);

        $processEmbedded = $this->processEmbedded($message);
        $message = Text::inlineCSS($processEmbedded['message']);
        $embedded = (array)$processEmbedded['embedded'];

        if ($emails = ($addresses['emails'] ?? null)) {
            if ($debug) {
                $debugMessages['emails'] = [
                    'emails' => $emails,
                    'subject' => $subject,
                    'message' => $message,
                    'from' => $fromName,
                    'fromEmail' => $fromEmail,
                    'attachments' => $attachments,
                    'embedded' => $embedded,
                ];
            } else {
                // @codeCoverageIgnoreStart
                Application::i()->sendmail(
                    $emails,
                    $subject,
                    $message,
                    $fromName,
                    $fromEmail,
                    true,
                    $attachments,
                    $embedded
                );
                // @codeCoverageIgnoreEnd
            }
        }

        if ($smsEmails = ($addresses['smsEmails'] ?? null)) {
            if ($debug) {
                $debugMessages['smsEmails'] = [
                    'emails' => $smsEmails,
                    'subject' => $subject,
                    'message' => $smsMessage,
                    'from' => $fromName,
                    'fromEmail' => $fromEmail,
                ];
            } else {
                // @codeCoverageIgnoreStart
                Application::i()->sendmail(
                    $smsEmails,
                    $subject,
                    $smsMessage,
                    $fromName,
                    $fromEmail,
                    false
                );
                // @codeCoverageIgnoreEnd
            }
        }

        if (($smsPhones = ($addresses['smsPhones'] ?? null)) &&
            ($urlTemplate = Package::i()->registryGet('sms_gate'))
        ) {
            foreach ($smsPhones as $phone) {
                $url = Text::renderTemplate($urlTemplate, [
                    'PHONE' => urlencode($phone),
                    'TEXT' => urlencode($smsMessage)
                ]);
                if ($debug || !Application::i()->prod) {
                    $debugMessages['smsPhones'][] = $url;
                // @codeCoverageIgnoreStart
                } elseif (!$debug && Application::i()->prod) {
                    $result = file_get_contents($url);
                }
                // @codeCoverageIgnoreEnd
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
    public function parseFormAddresses(Form $form)
    {
        $addresses = preg_split('/( |;|,)/', trim((string)$form->email));
        $addresses = array_map('trim', $addresses);
        $addresses = array_values(array_filter($addresses));
        $result = [];
        foreach ($addresses as $address) {
            if (($address[0] == '[') &&
                ($address[strlen($address) - 1] == ']')
            ) {
                $address = trim(substr((string)$address, 1, -1));
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
     * Получает список адресов пользователя
     * @param SOME $object Объект, из которого берем данные
     * @return [
     *             'emails' => array<string> Список настоящих e-mail,
     *             'smsPhones' => array<string> Список телефонов
     *                                          для SMS-уведомлений
     *                                          в формате +79990000000
     *                                          или 79990000000
     *         ]
     */
    public function parseUserAddresses(SOME $object)
    {
        $result = ['emails' => [], 'smsPhones' => []];
        if ($object->email) {
            $result['emails'][] = $object->email;
        } else {
            foreach ($object->fields as $field) {
                if ($field->datatype == 'email') {
                    $result['emails'] = $field->getValues(true);
                }
            }
        }
        $phonesRaw = [];
        if ($object->phone) {
            $phonesRaw[] = $object->phone;
        } else {
            foreach ($object->fields as $field) {
                if ($field->datatype == 'tel') {
                    $phonesRaw = array_merge($phonesRaw, $field->getValues(true));
                }
            }
        }
        foreach ($phonesRaw as $phoneRaw) {
            if (preg_match('/(\\+)?\\d+/umi', $phoneRaw)) {
                $result['smsPhones'][] = '+7' . Text::beautifyPhone($phoneRaw);
            }
        }
        $result['smsPhones'] = array_values(array_unique((array)$result['smsPhones']));
        return $result;
    }


    /**
     * Получает заголовок e-mail сообщения
     * @param Feedback $feedback Уведомление обратной связи
     * @param bool $forAdmin Уведомление для администратора
     *                       (если нет, то для пользователя)
     * @return string
     */
    public function getEmailSubject(Feedback $feedback, $forAdmin = true)
    {
        $host = $this->server['HTTP_HOST'] ?? '';
        if ($host && function_exists('idn_to_utf8')) {
            $host = idn_to_utf8($host);
        }
        $host = mb_strtoupper((string)$host);
        $subject = date(RAASViewWeb::i()->_('DATETIMEFORMAT')) . ' ' . sprintf(
            View_Web::i()->_('FEEDBACK_STANDARD_HEADER'),
            $feedback->parent->name,
            $feedback->page->name,
            $host
        );
        return $subject;
    }


    /**
     * Получает тело сообщения
     * @param Snippet $template Шаблон уведомления
     * @param array $data Данные для отработки шаблона
     * @return string
     */
    public function getMessageBody(Snippet $template, array $data = [])
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
    public function getFromName()
    {
        $host = $this->server['HTTP_HOST'] ?? '';
        if ($host && function_exists('idn_to_utf8')) {
            $host = idn_to_utf8($host);
        }
        return View_Web::i()->_('ADMINISTRATION_OF_SITE') . ' ' . $host;
    }


    /**
     * Получает значение обратного адреса
     * @return string
     */
    public function getFromEmail()
    {
        $host = $this->server['HTTP_HOST'] ?? '';
        return 'info@' . $host;
    }


    /**
     * Получает вложения для письма
     * @param Feedback $feedback Уведомление формы обратной связи
     * @param ?Material $material Созданный материал
     * @param bool $forAdmin Уведомление для администратора
     *                       (если нет, то для пользователя)
     * @return array <pre>array<[
     *     'tmp_name' => string Путь к реальному файлу,
     *     'type' => string MIME-тип файла,
     *     'name' => string Имя файла
     * ]></pre>
     */
    public function getAttachments(Feedback $feedback, ?Material $material = null, $forAdmin = true)
    {
        return [];
    }


    /**
     * Преобразует строку и выбирает встроенные файлы
     * @param string $text Исходный текст письма
     * @return array <pre>[
     *     'message' => string Преобразованный текст письма,
     *     'embedded' => array<[
     *         'tmp_name' => string Путь к реальному файлу,
     *         'type' => string MIME-тип файла,
     *         'name' => string Имя файла
     *     ]> Встроенные файлы
     * ]</pre>
     */
    public function processEmbedded($text)
    {
        $embedded = [];
        $rxes = ['src="(.*?)"', 'url\\((.*?)\\)'];
        foreach ($rxes as $rx) {
            preg_match_all('/' . $rx . '/umis', $text, $regs);
            foreach ($regs[0] as $i => $oldSrc) {
                $newSrc = $oldSrc;
                $attrValue = trim((string)$regs[1][$i], '"\'');
                if (!preg_match('/^(cid):/umis', $attrValue)) {
                    $basename = $this->getBasename($attrValue);
                    if (!isset($embedded[$basename]) &&
                        ($embeddedEntry = $this->getEmbedded($attrValue))
                    ) {
                        $embedded[$basename] = $embeddedEntry;
                    }
                    $newAttrValue = 'cid:' . $embedded[$basename]['name'];
                    $newSrc = str_replace($attrValue, $newAttrValue, $newSrc);
                    $text = str_replace($oldSrc, $newSrc, $text);
                }
            }
        }
        return ['message' => $text, 'embedded' => array_values($embedded)];
    }


    /**
     * Получает имя встроенного файла по оригинальному адресу
     * @param string $src Оригинальный адрес
     * @return string
     */
    public function getBasename($src)
    {
        $result = dechex(crc32($src));
        if (!stristr($src, 'data:')) {
            $result .= '-' . basename(parse_url($src, PHP_URL_PATH));
        }
        return $result;
    }


    /**
     * Получает встроенный файл по ссылке
     * @param string $src Ссылка
     * @return array <pre>[
     *     'tmp_name' => string Путь к реальному файлу,
     *     'type' => string MIME-тип файла,
     *     'name' => string Имя файла
     * ]</pre>
     */
    public function getEmbedded($src)
    {
        $name = $this->getBasename($src);
        $tmpname = tempnam(sys_get_temp_dir(), 'raas');
        $mime = 'application/octet-stream';
        $text = '';
        if (preg_match('/^(http(s)?:)?\\/\\//umis', $src, $regs)) {
            // Внешний файл
            if (!($regs[1] ?? null)) {
                $src = 'http' . ($_SERVER['HTTPS'] ? 's' : '') . ':' . $src;
            }
            $text = @file_get_contents($src);
        } elseif ($src[0] == '/') {
            // Файл на данном сайте
            $text = @file_get_contents(Application::i()->baseDir . $src);
        } elseif (preg_match('/^data:.*?;base64,(.*?)$/umis', $src, $regs)) {
            // Data URI
            $text = base64_decode($regs[1]);
            $name = basename($tmpname);
            // 2022-07-27, AVS: убрал - непонятно зачем сделано, но глючит с добавлением
            // return $src;
        }
        file_put_contents($tmpname, $text);

        if ($type = @getimagesize($tmpname)) {
            $ext = image_type_to_extension($type[2]);
            $name = pathinfo($name, PATHINFO_FILENAME) . $ext;
            $mime = image_type_to_mime_type($type[2]);
            if (($type[0] > static::MAIL_SIZE) || ($type[1] > static::MAIL_SIZE)) {
                $tmpname2 = tempnam(sys_get_temp_dir(), 'raas')
                          . image_type_to_extension($type[2]);
                Thumbnail::make($tmpname, $tmpname2, static::MAIL_SIZE, static::MAIL_SIZE);
                unlink($tmpname);
                $tmpname = $tmpname2;
            }
        } elseif ($type = mime_content_type($tmpname)) {
            $mime = $type;
        }

        $result = [
            'tmp_name' => $tmpname,
            'type' => $mime,
            'name' => $name,
        ];
        return $result;
    }
}
