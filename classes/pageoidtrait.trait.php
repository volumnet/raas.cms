<?php
/**
 * Файл трейта странице-образных сущностей
 */
namespace RAAS\CMS;

/**
 * Трейт странице-образных сущностей
 */
trait PageoidTrait
{
    /**
     * Получить заголовок H1
     * @return string
     */
    public function getH1()
    {
        return trim($this->h1) ?: trim($this->name);
    }


    /**
     * Получить имя для пункта меню
     * @return string
     */
    public function getMenuName()
    {
        return trim($this->menu_name) ?: trim($this->name);
    }


    /**
     * Получить имя для хлебных крошек
     * @return string
     */
    public function getBreadcrumbsName()
    {
        return trim($this->breadcrumbs_name) ?: trim($this->name);
    }


    /**
     * Увеличивает счетчик просмотра на 1
     */
    public function visit()
    {
        $this->visit_counter++;
        // 2017-09-07, AVS: сделал через базу, чтобы не сохранялось изменение,
        // если взят из кэша
        static::_SQL()->update(
            static::_tablename(),
            "id = " . (int)$this->id,
            ['visit_counter' => (int)$this->visit_counter]
        );
    }


    /**
     * Помечает сущность как измененную и увеличивает
     * счетчик редактирования на 1
     * @param bool $commit Сохранить сущность
     */
    public function modify($commit = true)
    {
        $d0 = time();
        $d1 = strtotime($this->modify_date);
        $d2 = strtotime($this->last_modified);
        $arr = [];
        if (($d0 - $d1 >= 3600) && ($d0 - $d2 >= 3600)) {
            $arr['last_modified'] = $this->last_modified = date('Y-m-d H:i:s');
            $arr['modify_counter'] = $this->modify_counter++;
            if ($commit) {
                self::$SQL->update(
                    self::_tablename(),
                    "id = " . (int)$this->id,
                    $arr
                );
            }
        }
    }
}
