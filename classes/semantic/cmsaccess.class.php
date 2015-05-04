<?php 
namespace RAAS\CMS;

class CMSAccess extends \SOME\SOME
{
    const TO_ALL = 0;
    const TO_UNREGISTERED = 1;
    const TO_REGISTERED = 2;
    const TO_USER = 3;
    const TO_GROUP = 4;

    protected static $tablename = 'cms_access';
    protected static $defaultOrderBy = "priority";
    protected static $cognizableVars = array();

    protected static $references = array(
        'page' => array('FK' => 'page_id', 'classname' => 'RAAS\\CMS\\Page', 'cascade' => true),
        'material' => array('FK' => 'material_id', 'classname' => 'RAAS\\CMS\\Material', 'cascade' => true),
        'block_id' => array('FK' => 'block_id', 'classname' => 'RAAS\\CMS\\Block', 'cascade' => true),
        'user' => array('FK' => 'uid', 'classname' => 'RAAS\\CMS\\User', 'cascade' => true),
        'group' => array('FK' => 'gid', 'classname' => 'RAAS\\CMS\\Group', 'cascade' => true),
    );


    public function userHasAccess(User $user)
    {
        switch ($this->to_type) {
            case self::TO_ALL:
                $match = true;
                break;
            case self::TO_UNREGISTERED:
                $match = !$user->id;
                break;
            case self::TO_REGISTERED:
                $match = (bool)(int)$user->id;
                break;
            case self::TO_USER:
                $match = ($user->id == $this->uid);
                break;
            case self::TO_GROUP:
                $match = in_array($this->gid, $user->groups_ids);
                break;
        }
        if ($match) {
            return ($this->allow * 2) - 1;
        }
        return 0;
    }


    public function userHasCascadeAccess(\SOME\SOME $entity, User $user)
    {
        if (is_array($entity->access)) {
            $accessSet = array_reverse($entity->access);
            foreach ($accessSet as $access) {
                $a = $access->userHasAccess($user);
                if ($a) {
                    return $a;
                }
            }
        }
        return 0;
    }
}