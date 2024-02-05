<?php
/**
 * Файл трейта странице-образных сущностей
 */
declare(strict_types=1);

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
    public function getH1(): string
    {
        return trim((string)$this->h1) ?: trim((string)$this->name);
    }


    /**
     * Получить имя для пункта меню
     * @return string
     */
    public function getMenuName(): string
    {
        return trim((string)$this->menu_name) ?: trim((string)$this->name);
    }


    /**
     * Получить имя для хлебных крошек
     * @return string
     */
    public function getBreadcrumbsName(): string
    {
        return trim((string)$this->breadcrumbs_name) ?: trim((string)$this->name);
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
        $d1 = strtotime((string)$this->modify_date);
        $d2 = strtotime((string)$this->last_modified);
        $arr = [];
        // 2020-03-24, AVS: убрали условие
        // if (($d0 - $d1 >= 3600) && ($d0 - $d2 >= 3600)) { , т.к.
        // 1) оно ничем не обосновано,
        // 2) теряется информация о частых изменениях
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
