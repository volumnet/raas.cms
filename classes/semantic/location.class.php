<?php
namespace RAAS\CMS;

class Location
{
    const min_width = 140;
    const min_height = 50;
    const vertical_min_height = 90;
    
    private $parent;
    private $urn;
    private $x;
    private $y;
    private $width;
    private $height;
    
    public function __get($var)
    {
        switch ($var) {
            case 'parent': case 'urn': case 'x': case 'y': case 'width': case 'height':
                return $this->$var;
                break;
            case 'horizontal':
                return $this->urn && ($this->height < self::vertical_min_height);
            case 'style':
                $style = array();
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
    
    public function __construct(Template $template = null, $urn = '', array $params = array())
    {
        $this->parent = $template;
        $this->urn = $urn;
        $temp = (array)json_decode($this->parent->locations_info, true);
        $locs = array();
        foreach ($temp as $row) {
            $locs[$row['urn']] = $row;
        }
        unset($temp);
        
        foreach (array('x', 'y', 'width', 'height') as $key) {
            if (isset($locs[$urn][$key])) {
                $this->$key = isset($locs[$urn][$key]) ? (int)$locs[$urn][$key] : 0;
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