<?php
/**
 * Шаблон
 */
namespace RAAS\CMS;

use SOME\SOME;
use RAAS\Attachment as Attachment;

/**
 * Класс шаблона
 * @property-read array<
 *                    string[] URN размещения => Location
 *                > $locations Размещения
 * @property-read Attachment $Background Фоновое изображение
 * @property-read array<
 *                    string[] CSS-свойство => string значение свойства
 *                > $style Набор CSS-стилей шаблона
 */
class Template extends SOME
{
    use ImportByURNTrait;

    protected static $tablename = 'cms_templates';

    protected static $defaultOrderBy = "name";

    protected static $cognizableVars = ['locations'];

    protected static $references = [
        'Background' => [
            'FK' => 'background',
            'classname' => Attachment::class,
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
        $this->width = min($this->width, 680);
        if ($this->locs) {
            $this->locations_info = json_encode((array)$this->locs);
            unset($this->locs);
        }
        parent::commit();
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
     * Размещения
     * @return array<string[] URN размещения => Location>
     */
    public function _locations()
    {
        $temp = (array)json_decode($this->locations_info, true);
        $locs = [];
        foreach ($temp as $row) {
            $locs[$row['urn']] = $row;
        }
        unset($temp);

        preg_match_all(
            '/\\$Page-\\>location\\(("|\')(.*?)("|\')\\)/i',
            $this->description,
            $regs
        );
        preg_match_all(
            '/\\$Page-\\>locationBlocksText\\[("|\')(.*?)("|\')\\]/i',
            $this->description,
            $regs2
        );
        $newLocs = array_values(array_unique(array_merge(
            (array)$regs[2],
            (array)$regs2[2]
        )));

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


    public static function delete(SOME $Item)
    {
        $Item->deleteBackground();
        parent::delete($Item);
    }
}
