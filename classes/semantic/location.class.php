<?php
/**
 * Размещение
 */
namespace RAAS\CMS;

/**
 * Класс размещения
 * @property-read Template $parent Родительский шаблон
 * @property-read string $urn URN размещения
 * @property-read int $x Смещение относительно левого края шаблона
 *                       по горизонтали в px
 * @property-read int $y Смещение относительно верхнего края шаблона
 *                       по вертикали в px
 * @property-read int $width Ширина в px
 * @property-read int $height Высота в px
 * @property-read bool $horizontal Считать ли размещение горизонтальным
 * @property-read array<
 *                    string[] CSS-свойство => string значение свойства
 *                > $style Набор CSS-стилей размещения
 */
class Location
{
    /**
     * Минимальная ширина, px
     */
    const min_width = 140;

    /**
     * Минимальная высота, px
     */
    const min_height = 50;

    /**
     * Минимальная высота, px, при которой размещение считается вертикальным
     */
    const vertical_min_height = 90;

    /**
     * Родительский шаблон
     * @var Template
     */
    private $parent;

    /**
     * URN размещения
     * @var string
     */
    private $urn;

    /**
     * Смещение относительно левого края шаблона по горизонтали в px
     * @var int
     */
    private $x;

    /**
     * Смещение относительно верхнего края шаблона по вертикали в px
     * @var int
     */
    private $y;

    /**
     * Ширина в px
     * @var int
     */
    private $width;

    /**
     * Высота в px
     * @var int
     */
    private $height;

    public function __get($var)
    {
        switch ($var) {
            case 'parent':
            case 'urn':
            case 'x':
            case 'y':
            case 'width':
            case 'height':
                return $this->$var;
                break;
            case 'horizontal':
                return $this->urn &&
                       ($this->height < self::vertical_min_height);
            case 'style':
                $style = [];
                $style['left'] = $this->x . 'px';
                $style['top'] = $this->y . 'px';
                $style['width'] = $this->width . 'px';
                $style['min-height'] = $this->height . 'px';
                foreach ($style as $key => $val) {
                    $style[$key] = $key . ': ' . $val . ';';
                }
                return implode(' ', $style);
                break;
        }
    }


    /**
     * Конструктор класса
     * @param Template $template Родительский шаблон
     * @param string $urn URN размещения
     * @param [
     *            'x' => int Смещение относительно левого края шаблона
     *                       по горизонтали в px
     *            'y' => int Смещение относительно верхнего края шаблона
     *                       по вертикали в px
     *            'width' => int Ширина в px
     *            'height' => int Высота в px
     *        ] $params Параметры размещения
     */
    public function __construct(
        Template $template = null,
        $urn = '',
        array $params = []
    ) {
        $this->parent = $template;
        $this->urn = $urn;
        $temp = (array)json_decode($this->parent->locations_info, true);
        $locs = [];
        foreach ($temp as $row) {
            $locs[$row['urn']] = $row;
        }
        unset($temp);

        foreach (['x', 'y', 'width', 'height'] as $key) {
            if (isset($locs[$urn][$key])) {
                $this->$key = isset($locs[$urn][$key])
                            ? (int)$locs[$urn][$key]
                            : 0;
            }
            if (isset($params[$key])) {
                $this->$key = (int)$params[$key];
            }
        }
        $this->width = max($this->width, self::min_width);
        $this->height = max($this->height, self::min_height);
        $this->x = max(0, min($this->parent->width - $this->width, $this->x));
        $this->y = max(0, min($this->parent->height - $this->height, $this->y));
    }
}
