<?php

/**
 * Created by PhpStorm.
 * User: pater
 * Date: 22.05.2017
 * Time: 23:42
 */
interface DatabaseOperation
{

    public function userExists($user_login, $user_password);
    public function isAdmin($user_login, $user_password);
    public function registerUser($user_login, $user_password);
    public function isLoginBusy($user_login);

}