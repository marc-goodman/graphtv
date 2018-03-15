<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 11/2/2015
 * Time: 12:45 PM
 */

/**
 * Form field names
 */
const LOGIN_USERNAME_KEY = 'login_username';
const LOGIN_PASSWORD_KEY = 'login_password';
const LOGIN_BUTTON_VALUE = 'login';
const REGISTER_USERNAME_KEY = 'register_username';
const REGISTER_PASSWORD_KEY = 'register_password';
const REGISTER_CONFIRM_PASSWORD_KEY = 'register_confirm_password';
const REGISTER_BUTTON_VALUE = 'register';

/**
 * Session keys
 */
const SESSION_USERNAME_KEY = 'username';
const SESSION_ROLE_KEY = 'role';

/**
 * User account fields
 */
const ACCOUNT_USERNAME_FIELD = 0;
const ACCOUNT_PASSWORD_HASH_FIELD = 1;
const ACCOUNT_ROLE_FIELD = 2;
/**
 * Files and paths
 */
const USER_ACCOUNT_FILE = 'data/users.csv';

/**
 * Error messages
 */
const E_LOGIN = 'Error Logging In!';
const E_REGISTER = 'Error Registering!';

const E_NO_USERNAME = 'Username must be supplied.';
const E_NO_PASSWORD = 'Password must be supplied.';
const E_NO_CONFIRM = 'Password confirmation must be supplied.';
const E_CONFIRM_MISMATCH = 'Password and confirmation must match.';
const E_ACCOUNT_EXISTS = 'Username already exists. Please try a different username.';
const E_USERNAME_NOT_FOUND = 'Username does not exist.';
const E_PASSWORD_INCORRECT = 'Password is incorrect.';
const E_LOGIN_INVALID = 'Username or password is incorrect.';

const HOME_PAGE = 'welcome.php';
const LOGIN_PAGE = 'login.php';