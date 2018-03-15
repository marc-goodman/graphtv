<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 11/2/2015
 * Time: 2:17 PM
 */

function require_secure()
{
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit();
    }
}

function require_login()
{
    if (!isset($_SESSION) || empty($_SESSION[SESSION_USERNAME_KEY])) {
        header('Location: ' . LOGIN_PAGE);
        exit();
    }
}

function get_post_value($key)
{
    if (isset($_POST[$key])) {
        return htmlentities($_POST[$key]);
    }
    return '';
}

function destroy_session()
{
    $session_info = session_get_cookie_params();
    $_SESSION = [];
    setcookie(session_name(), '', 0, $session_info['path'], $session_info['domain'], $session_info['secure'], $session_info['httponly']);
    session_destroy();
}
