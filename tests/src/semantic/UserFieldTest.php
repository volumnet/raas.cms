<?php
/**
 * Тест класса User_Field
 */
namespace RAAS\CMS;

use SOME\BaseTest;

/**
 * Тест класса User_Field
 * @covers \RAAS\CMS\User_Field
 */
class UserFieldTest extends BaseTest
{
    public static $tables = [
        'cms_access',
        'cms_access_blocks_cache',
        'cms_access_materials_cache',
        'cms_access_pages_cache',
        'cms_data',
        'cms_feedback',
        'cms_fields',
        'cms_users',
        'cms_users_groups_assoc',
        'cms_users_social',
    ];

    /**
     * Тест установки свойства Owner
     */
    public function testSetOwner()
    {
        $user = new User();
        $user->commit();

        $field = new User_Field();

        $this->assertNull($field->Owner);

        $field->Owner = $user;

        $this->assertEquals($user, $field->Owner);

        User::delete($user);
    }


    /**
     * Тест наследуемой установки свойств
     */
    public function testSetDefault()
    {
        $field = new User_Field();

        $this->assertNull($field->urn);

        $field->urn = 'test';

        $this->assertEquals('test', $field->urn);
    }


    /**
     * Тест метода commit()
     */
    public function testCommit()
    {
        $field = new User_Field([
            'classname' => 'aaa',
            'pid' => 1, // 1 - наши преимущества (только для проверки сохранения)
            'vis' => 1,
            'name' => 'Логин'
        ]);
        $field->commit();

        $this->assertEquals(User::class, $field->classname);
        $this->assertEquals(0, $field->pid);
        $this->assertEquals('_login_', $field->urn); // login - зарезервированное имя

        User_Field::delete($field);
    }


    /**
     * Тест метода getSet()
     */
    public function testGetSet()
    {
        $result = User_Field::getSet();
        $result = array_map(function ($x) {
            return (int)$x->id;
        }, $result);

        $this->assertContains(37, $result); // Телефон
        $this->assertNotContains(1, $result); // Описание к страницам
        $this->assertNotContains(12, $result); // Изображение у преимуществ
    }
}
