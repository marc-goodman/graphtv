<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 11/11/2015
 * Time: 12:04 PM
 */

/*
$query = <<<QUERY
SELECT		TB2.primaryTitle AS seriesTitle,
            title_episode.tconst,
            seasonNumber,
			episodeNumber,
			TB1.primaryTitle,
			averageRating,
			numVotes
FROM		title_episode
JOIN		title_basics AS TB1 ON title_episode.tconst = TB1.tconst
JOIN		title_ratings ON title_ratings.tconst = TB1.tconst
JOIN        title_basics AS TB2 ON title_episode.parentTconst = TB2.tconst
WHERE		TB2.primaryTitle = :title
ORDER BY    seasonNumber, episodeNumber;
QUERY;
$conn = new PDO("sqlsrv:Server=MARC-PC;Database=IMDB", "IMDB", "IMDB001!");

$stmt = $conn->prepare($query);
$stmt->execute(array(':title' => $_GET['title']));
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
*/

function unsafe_lookup_user($username, $password)
{
    $db = new PDO("sqlsrv:Server=" . DB_SERVER . ";Database=" . DB_DATABASE, DB_USER, DB_PASSWORD);
    $hash = md5($password);
    $query = "SELECT * FROM " . USERS_TABLE . " WHERE " . USERS_HASH_FIELD . " = '$hash' AND " . USERS_USERNAME_FIELD . " = '$username';"; // vulnerable to SQL injection attacks
    $result = $db->query($query);
    return $result->fetchAll(PDO::FETCH_NUM);
}

function lookup_user($username)
{
    $db = new PDO("sqlsrv:Server=" . DB_SERVER . ";Database=" . DB_DATABASE, DB_USER, DB_PASSWORD);
    $query = "SELECT * FROM " . USERS_TABLE . " WHERE " . USERS_USERNAME_FIELD . " = '$username';"; // vulnerable to SQL injection attacks
    $result = $db->query($query);
    return $result->fetchAll(PDO::FETCH_NUM);
}

function add_user($username, $hash)
{
    $db = new PDO("sqlsrv:Server=" . DB_SERVER . ";Database=" . DB_DATABASE, DB_USER, DB_PASSWORD);
    $query = "INSERT INTO";
    $query .= " " . USERS_TABLE . " (" . USERS_USERNAME_FIELD . ", " . USERS_HASH_FIELD . ", " . USERS_ROLE_FIELD . ")";
    $query .= " VALUES ('$username', '$hash', 'user');"; // vulnerable to SQL injection attacks
    $db->query($query);
}
