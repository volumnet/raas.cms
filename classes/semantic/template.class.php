<?php
/**
 * Шаблон
 */
declare(strict_types=1);

namespace RAAS\CMS;

use Error;
use phpDocumentor\Reflection\DocBlockFactory;
use SOME\SOME;
use RAAS\Application;
use RAAS\Attachment as Attachment;
use RAAS\User as RAASUser;

/**
 * Класс шаблона
 * @property-read array<
 *                    string[] URN размещения => Location
 *                > $locations Размещения
 * @property-read Attachment $Background Фоновое изображение
 * @property-read RAASUser $author Автор страницы
 * @property-read RAASUser $editor Редактор страницы
 * @property-read array<
 *                    string[] CSS-свойство => string значение свойства
 *                > $style Набор CSS-стилей шаблона
 * @property-read string $filename Имя файла кэша для сохранения
 * @property-read string $name Наименование сниппета
 */
class Template extends SOME
{
    use ImportByURNTrait;
    use CodeTrait;

    protected static $tablename = 'cms_templates';

    protected static $defaultOrderBy = "urn";

    protected static $cognizableVars = [
        'name',
        'locations',
    ];

    protected static $references = [
        'Background' => [
            'FK' => 'background',
            'classname' => Attachment::class,
            'cascade' => false
        ],
        'author' => [
            'FK' => 'author_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
        'editor' => [
            'FK' => 'editor_id',
            'classname' => RAASUser::class,
            'cascade' => false
        ],
    ];

    public function __get($var)
    {
        switch ($var) {
            case 'style':
                $style = [];
                if ($this->Background->id) {
                    $style['background-image'] = 'url(\''
                                               .     $this->Background->fileURL
                                               . '\')';
                }
                $style['width'] = $this->width . 'px';
                $style['height'] = $this->height . 'px';
                foreach ($style as $key => $val) {
                    $style[$key] = $key . ': ' . $val . ';';
                }
                return implode(' ', $style);
                break;
            case 'filename':
                // Здесь именно ...properties... , поскольку при сохранении
                // нужно удалять старый файл
                // Обращение к новому файлу идёт только в случае реального commit'а
                // Шунтирование ...updates... идёт на случай, когда сниппет
                // генерируется динамически
                $filename = Package::i()->cacheDir . '/system/templates/'
                    . (($this->properties['urn'] ?? null) ?: ($this->updates['urn'] ?? null))
                    . '.tmp.php';
                return $filename;
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        if (!$this->urn && $this->name) {
            $this->urn = $this->name;
        }
        Package::i()->getUniqueURN($this);
        $datetime = date('Y-m-d H:i:s');
        $uid = (int)Application::i()->user->id;
        if (!$this->id) {
            $this->post_date = $datetime;
            $this->author_id = $uid;
        }
        $this->modify_date = $datetime;
        $this->editor_id = $uid;
        $this->width = min($this->width, 680);
        if ($this->locs) {
            $this->locations_info = json_encode((array)$this->locs);
            unset($this->locs);
        }
        if ($this->id && (($this->updates['urn'] ?? null) != ($this->properties['urn'] ?? null))) {
            $this->deleteFile();
        }
        parent::commit();
        $this->saveFile();
    }


    /**
     * Удаляет фоновое изображение
     */
    public function deleteBackground()
    {
        if ($this->Background->id) {
            Attachment::delete($this->Background);
        }
        $this->background = 0;
        $this->commit();
    }


    /**
     * Отрабатывает шаблон
     * @param array $data Данные, передаваемые в шаблон
     */
    public function process(array $data = [])
    {
        if (!is_file($this->filename)) {
            $this->saveFile();
        }
        $st = microtime(true);
        extract($data);
        try {
            $result = @include $this->filename;
        } catch (Error $e) {
            $result = null;
        }
        return $result;
    }


    /**
     * Размещения
     * @return array<string[] URN размещения => Location>
     */
    public function _locations()
    {
        $temp = (array)json_decode($this->locations_info ?: '', true);
        $locs = [];
        foreach ($temp as $row) {
            $locs[$row['urn']] = $row;
        }
        unset($temp);

        preg_match_all('/\\$Page-\\>location\\(("|\')(.*?)("|\')\\)/i', $this->description ?: '', $regs);
        preg_match_all('/\\$Page-\\>locationBlocksText\\[("|\')(.*?)("|\')\\]/i', $this->description ?: '', $regs2);
        $newLocs = array_values(array_unique(array_merge((array)$regs[2], (array)$regs2[2])));

        $locations = [];
        $min_y = 0;
        if ($newLocs) {
            foreach ($newLocs as $l) {
                $locations[$l] = new Location(
                    $this,
                    $l,
                    (
                        isset($locs[$l]) ?
                        [] :
                        [
                            'name' => $l,
                            'x' => 0,
                            'y' => $min_y,
                            'width' => Location::min_width,
                            'height' => Location::min_height
                        ]
                    )
                );
                if (!isset($locs[$l])) {
                    if ($min_y < ($this->height - Location::min_height)) {
                        $min_y = max($locations[$l]->y, $min_y)
                               + max($locations[$l]->height, 50);
                    } else {
                        $min_y = 0;
                    }
                }
            }
        }
        //ksort($locations);
        return $locations;
    }


    /**
     * Возвращает наименование сниппета
     * @return string
     */
    protected function _name()
    {
        if ($description = $this->description) {
            $tokens = token_get_all($description);
            $docBlockTexts = array_values(array_filter($tokens, function ($item) {
                return $item[0] == T_DOC_COMMENT;
            }));
            if ($docBlockTexts) {
                $docBlockText = $docBlockTexts[0][1];
                $docBlockFactory  = DocBlockFactory::createInstance();
                try {
                    $docBlock = $docBlockFactory->create($docBlockText);
                    $result = $docBlock->getSummary();
                    return $result;
                } catch (Exception $e) {
                }
            }
        }
        if ($this->urn) {
            return $this->urn;
        }
        return '';
    }


    public static function delete(SOME $item)
    {
        $item->deleteBackground();
        $item->deleteFile();
        parent::delete($item);
    }
}
