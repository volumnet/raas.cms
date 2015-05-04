<?php
namespace RAAS\CMS;

interface IAccessible
{
    public function userHasAccess(User $user);

    public function currentUserHasAccess();
}