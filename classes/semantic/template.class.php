<?php
namespace RAAS\CMS;
use \RAAS\Attachment as Attachment;

class Template extends \SOME\SOME
{
    protected static $tablename = 'cms_templates';
    protected static $defaultOrderBy = "name";
    protected static $cognizableVars = array('locations');

    protected static $references = array(
        'Background' => array('FK' => 'background', 'classname' => 'RAAS\\Attachment', 'cascade' => false),
    );
    
    public function __get($var)
    {
        switch ($var) {
            case 'style':
                $style = array();
                if ($this->Background->id) {
                    $style['background-image'] = 'url(\'' . $this->Background->fileURL . '\')'; 
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
        //$this->height = min($this->height, 1024);
        if ($this->locs) {
            $this->locations_info = json_encode((array)$this->locs);
            unset($this->locs);
        }
        parent::commit();
        /*if ($this->background_attachment) {
            $this->background_attachment->parent = $this;
            $size = getimagesize($this->background_attachment->upload);
            $this->background_attachment->commit();
            
            if ((int)$size[0] > $this->width) {
                $this->width = (int)$size[0];
                
            }
            if ((int)$size[1] > $this->height) {
                $this->height = (int)$size[1];
            }
            $this->background = $this->background_attachment->id;
            unset($this->background_attachment);
            $this->commit();
        } */
    }
    
    public function deleteBackground()
    {
        if ($this->Background->id) {
            Attachment::delete($this->Background);
        }
        $this->background = 0;
        $this->commit();
    }
    
    
    public static function importByURN($urn = '')
    {
        $SQL_query = "SELECT * FROM " . self::_tablename() . " WHERE urn = ?";
        if ($SQL_result = self::$SQL->getline(array($SQL_query, $urn))) {
            return new self($SQL_result);
        }
        return null;
    }
    
    
    public function _locations()
    {
        $temp = (array)json_decode($this->locations_info, true);
        $locs = array();
        foreach ($temp as $row) {
            $locs[$row['urn']] = $row;
        }
        unset($temp);
        
        preg_match_all('/\\$Page-\\>location\\(("|\')(.*?)("|\')\\)/i', $this->description, $regs);
        preg_match_all('/\\$Page-\\>locationBlocksText\\[("|\')(.*?)("|\')\\]/i', $this->description, $regs2);
        $newLocs = array_values(array_unique(array_merge((array)$regs[2], (array)$regs2[2])));

        $locations = array();
        $min_y = 0;
        if ($newLocs) {
            foreach ($newLocs as $l) {
                $locations[$l] = new Location($this, $l, isset($locs[$l]) ? array() : array('name' => $l, 'x' => 0, 'y' => $min_y, 'width' => Location::min_width, 'height' => Location::min_height));
                if (!isset($locs[$l])) {
                    if ($min_y < ($this->height - Location::min_height)) {
                        $min_y = max($locations[$l]->y, $min_y) + max($locations[$l]->height, 50);
                    } else {
                        $min_y = 0;
                    }
                }
            }
        }
        //ksort($locations);
        return $locations;
    }
    
    public static function delete(self $Item)
    {
        $Item->deleteBackground();
        parent::delete($Item);
    }
}