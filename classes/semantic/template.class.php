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
 * @property-read array<string[] URN размещения => Location> $locations Размещения
 * @property-read RAASUser $author Автор страницы
 * @property-read RAASUser $editor Редактор страницы
 * @property-read array<string[] CSS-свойство => string значение свойства> $style Набор CSS-стилей шаблона
 * @property-read string $filename Имя файла кэша для сохранения
 * @property-read string $post_date Дата создания файла
 * @property-read string $modify_date Дата обновления файла
 * @property-read string $description Код шаблона
 * @property-read string $name Наименование шаблона
 */
class Template extends SOME
{
    use CodeTrait;

    protected static $tablename = 'cms_templates';

    protected static $defaultOrderBy = "id";

    protected static $cognizableVars = [
        'description',
        'name',
        'locations',
    ];

    protected static $references = [
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
                $style['width'] = $this->width . 'px';
                $style['height'] = $this->height . 'px';
                foreach ($style as $key => $val) {
                    $style[$key] = $key . ': ' . $val . ';';
                }
                return implode(' ', $style);
                break;
            case 'filename':
                if (!$this->id) {
                    return null;
                }
                $filename = static::getDirName() . '/template' . (int)$this->id . '.tmp.php';
                return $filename;
                break;
            case 'post_date':
                if (!$this->filename || !is_file($this->filename)) {
                    return '0000-00-00 00:00:00';
                }
                return date('Y-m-d H:i:s', filectime($this->filename));
                break;
            case 'modify_date':
                if (!$this->filename || !is_file($this->filename)) {
                    return '0000-00-00 00:00:00';
                }
                return date('Y-m-d H:i:s', filemtime($this->filename));
                break;
            default:
                return parent::__get($var);
                break;
        }
    }


    public function commit()
    {
        $datetime = date('Y-m-d H:i:s');
        $uid = 0;
        if (Application::i()->user) {
            $uid = (int)Application::i()->user->id;
        }
        if (!$this->id) {
            $this->author_id = $uid;
        }
        $this->editor_id = $uid;
        $this->width = min($this->width, 680);
        if ($this->locs) {
            $this->locations_info = json_encode((array)$this->locs);
            unset($this->locs);
        }
        parent::commit();
        $this->saveFile();
    }


    /**
     * Отрабатывает шаблон
     * @param array $data Данные, передаваемые в шаблон
     */
    public function process(array $data = [])
    {
        $st = microtime(true);
        extract($data);
        $result = @include $this->filename;
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
                            'width' => Location::MIN_WIDTH,
                            'height' => Location::MIN_HEIGHT
                        ]
                    )
                );
                if (!isset($locs[$l])) {
                    if ($min_y < ($this->height - Location::MIN_HEIGHT)) {
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


    public static function delete(SOME $item)
    {
        $item->deleteFile();
        parent::delete($item);
    }
}
