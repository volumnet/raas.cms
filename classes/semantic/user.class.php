<?php
namespace RAAS\CMS;

class User extends \SOME\SOME
{
    protected static $tablename = 'cms_users';
    protected static $defaultOrderBy = "login";
    
    protected static $links = array('social' => array('tablename' => 'cms_users_social', 'field_from' => 'uid', 'field_to' => 'url'));
}