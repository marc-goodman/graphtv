<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 11/2/2015
 * Time: 3:04 PM
 */

function error_message($type, $detail)
{
    return '<div id="error_header">' . $type . '</div><div id="error_detail">' . $detail . '</div>';
}

function set_user($user)
{
    $_SESSION[SESSION_USERNAME_KEY] = $user[ACCOUNT_USERNAME_FIELD];
    $_SESSION[SESSION_ROLE_KEY] = $user[ACCOUNT_ROLE_FIELD];
    header('Location: ' . HOME_PAGE);
}

function login($username, $password)
{
    if (empty($username)) {
        return error_message(E_LOGIN, E_NO_USERNAME);
    }
    if (empty($password)) {
        return error_message(E_LOGIN, E_NO_PASSWORD);
    }
    // $user = lookup_key_value($username, USER_ACCOUNT_FILE);
    $user = lookup_user($username);
    if (!$user || !$user[0]) {
        return error_message(E_LOGIN, E_USERNAME_NOT_FOUND);
    }
    if (md5($password) != $user[0][ACCOUNT_PASSWORD_HASH_FIELD]) {
        return error_message(E_LOGIN, E_PASSWORD_INCORRECT);
    }
    set_user($user[0]);
}

function unsafe_login($username, $password)
{
    if (empty($username)) {
        return error_message(E_LOGIN, E_NO_USERNAME);
    }
    if (empty($password)) {
        return error_message(E_LOGIN, E_NO_PASSWORD);
    }
    // $user = lookup_key_value($username, USER_ACCOUNT_FILE);
    $user = unsafe_lookup_user($username, $password);
    if (!$user || !$user[0]) {
        return error_message(E_LOGIN, E_LOGIN_INVALID);
    }
    set_user($user[0]);
}

function register($username, $password, $confirm)
{
    if (empty($username)) {
        return error_message(E_REGISTER, E_NO_USERNAME);
    }
    if (empty($password)) {
        return error_message(E_REGISTER, E_NO_PASSWORD);
    }
    if (empty($confirm)) {
        return error_message(E_REGISTER, E_NO_CONFIRM);
    }
    if ($password !== $confirm) {
        return error_message(E_REGISTER, E_CONFIRM_MISMATCH);
    }
    // $user = lookup_key_value($username, USER_ACCOUNT_FILE);
    $user = lookup_user($username);
    if (!empty($user)) {
        return error_message(E_REGISTER, E_ACCOUNT_EXISTS);
    }
    // add_key_value($username, [$username, password_hash($password, PASSWORD_DEFAULT)], USER_ACCOUNT_FILE);
    add_user($username, md5($password));
    set_user(["username" => $username, "role" => 'user']);
}

function login_or_register(
    $login_pressed,
    $register_pressed,
    $login_username,
    $login_password,
    $register_username,
    $register_password,
    $register_confirm_password
)
{
    if (!empty($login_pressed)) {
        return login($login_username, $login_password);
    } elseif (!empty($register_pressed)) {
        return register($register_username, $register_password, $register_confirm_password);
    }
    return "";
}

function unsafe_login_or_register(
    $login_pressed,
    $register_pressed,
    $login_username,
    $login_password,
    $register_username,
    $register_password,
    $register_confirm_password
)
{
    if (!empty($login_pressed)) {
        return unsafe_login($login_username, $login_password);
    } elseif (!empty($register_pressed)) {
        return register($register_username, $register_password, $register_confirm_password);
    }
    return "";
}