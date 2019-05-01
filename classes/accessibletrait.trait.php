<?php
/**
 * Файл трейта сущности, имеющей доступы для пользователей
 */
namespace RAAS\CMS;

/**
 * Трейт сущности, имеющей доступы для пользователей
 */
trait AccessibleTrait
{
    /**
     * Имеет ли пользователь доступ к сущности
     * @param User $user Пользователь для проверки
     * @return bool
     */
    public function userHasAccess(User $user)
    {
        $a = CMSAccess::userHasCascadeAccess($this, $user);
        return ($a >= 0);
    }

    /**
     * Имеет ли текущий пользователь доступ к сущности
     * @return bool
     */
    public function currentUserHasAccess()
    {
        return $this->userHasAccess(Controller_Frontend::i()->user);
    }
}
