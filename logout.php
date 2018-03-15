<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 11/3/2015
 * Time: 10:59 AM
 */

require_once('includes/utilities.php');
require_once('includes/login_constants.php');

session_start();
destroy_session();
header('Location: ' . LOGIN_PAGE);
