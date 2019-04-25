<?php
/**
 * Файл трейта рекурсивной сущности
 */
namespace RAAS\CMS;

/**
 * Трейт рекурсивной сущности
 */
trait RecursiveTrait
{
    /**
     * Возвращает себя и все дочерние элементы всех уровней
     * @return array<self>
     */
    protected function _selfAndChildren()
    {
        return array_merge([$this], (array)$this->all_children);
    }


    /**
     * Возвращает себя и все родительские элементы
     * @return array<self>
     */
    protected function _selfAndParents()
    {
        return array_merge([$this], (array)$this->parents);
    }


    /**
     * Возвращает свой ID# и ID# всех дочерних элементов всех уровней
     * @return array<int>
     */
    protected function _selfAndChildrenIds()
    {
        return array_merge([$this->id], (array)$this->all_children_ids);
    }


    /**
     * Возвращает свой ID# и ID# всех родительских элементов
     * @return array<int>
     */
    protected function _selfAndParentsIds()
    {
        return array_merge([$this->id], (array)$this->parents_ids);
    }
}
