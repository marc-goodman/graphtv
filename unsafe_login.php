<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 11/2/2015
 * Time: 12:46 PM
 */

/*
require_once('includes/page_constants.php');
*/

require_once('includes/login_constants.php');
require_once('includes/utilities.php');
require_once('includes/login_code.php');
require_once('includes/login_db_constants.php');
require_once('includes/login_db_code.php');

require_secure();
session_start();

$login_username = get_post_value(LOGIN_USERNAME_KEY);
$login_password = get_post_value(LOGIN_PASSWORD_KEY);
$register_username = get_post_value(REGISTER_USERNAME_KEY);
$register_password = get_post_value(REGISTER_PASSWORD_KEY);
$register_confirm_password = get_post_value(REGISTER_CONFIRM_PASSWORD_KEY);
$login_pressed = get_post_value(LOGIN_BUTTON_VALUE);
$register_pressed = get_post_value(REGISTER_BUTTON_VALUE);

$error_message = unsafe_login_or_register(
    $login_pressed,
    $register_pressed,
    $login_username,
    $login_password,
    $register_username,
    $register_password,
    $register_confirm_password
);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Or Register</title>
    <link rel="stylesheet" type="text/css" href="includes/login.css.php">
</head>
<body>
<div id="login_content">
    <?php echo $error_message; ?>
    <form method="POST" action="unsafe_login.php">
        <table class="login_table">
            <tr><th colspan="2"><div class="login_header">Login to Your Account</div></th></tr>
            <tr>
                <th class="login_label">Username:</th>
                <td><input type="text" name="<?php echo LOGIN_USERNAME_KEY; ?>"
                        value="<?php echo $login_username; ?>"></td>
            </tr>
            <tr>
                <th class="login_label">Password:</th>
                <td><input type="password" name="<?php echo LOGIN_PASSWORD_KEY; ?>"
                           value="<?php echo $login_password; ?>"></td>
            </tr>
            <tr><td colspan="2"><div class="login_submit"><input type="submit" value="LOGIN" name="<?php echo LOGIN_BUTTON_VALUE; ?>"></div></td></tr>
        </table>
        <div class="login_or">OR</div>
        <table class="login_table">
            <tr><th colspan="2"><div class="login_header">Register a New Account</div></th></tr>
            <tr><th class="login_label">Username:</th>
                <td><input type="text" name="<?php echo REGISTER_USERNAME_KEY; ?>"
                           value="<?php echo $register_username; ?>"></td>
            </tr>
            <tr>
                <th class="login_label">Password:</th>
                <td><input type="password" name="<?php echo REGISTER_PASSWORD_KEY; ?>"
                           value="<?php echo $register_password; ?>"></td>
            </tr>
            <tr>
                <th class="login_label">Confirm Password:</th>
                <td><input type="password" name="<?php echo REGISTER_CONFIRM_PASSWORD_KEY; ?>"
                           value="<?php echo $register_confirm_password; ?>"></td>
            </tr>
            <tr><td colspan="2"><div class="login_submit"><input type="submit" value="REGISTER" name="<?php echo REGISTER_BUTTON_VALUE; ?>"></div></td></tr>
        </table>
    </form>
</div>
</body>
</html>
