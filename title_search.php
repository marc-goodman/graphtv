<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 3/14/2018
 * Time: 4:02 PM
 */
require_once('includes/login_constants.php');
require_once('includes/login_db_constants.php');
require_once('includes/login_db_code.php');

session_start();

if (!isset($_SESSION) || empty($_SESSION[SESSION_USERNAME_KEY])) {
    echo '{ error: "Not Logged In" }';
    return;
}

$key = $_GET['title'];

$query = <<<QUERY
SELECT		TOP 50
            tconst,
            titleType,
            primaryTitle,
            startYear
FROM        title_basics
WHERE       primaryTitle LIKE '%$key%';
QUERY;
$conn = new PDO("sqlsrv:Server=MARC-PC;Database=IMDB", "IMDB", "IMDB001!");

$result = $conn->query($query);
echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));