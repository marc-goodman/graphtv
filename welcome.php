<?php
/**
 * Created by PhpStorm.
 * User: Marc
 * Date: 3/14/2018
 * Time: 2:55 PM
 */

require_once('includes/login_constants.php');
require_once('includes/utilities.php');

require_secure();
session_start();
require_login();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to GraphTV!</title>
    <style>
        th {
            background-color: DarkBlue;
            color: White;
            padding: 10px;
            cursor: pointer;
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                              supported by Chrome and Opera */
        }
        tr:nth-child(even) {
            background-color: LightBlue;
        }
        td {
            border: 0;
            padding: 5px 15px 5px 15px;
        }
        table {
            border-spacing: 0px;
        }
        .center {
            text-align: center;
        }
    </style>
    <script>
        function search() {
            var key = document.getElementById('title').value;

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                    var titles = JSON.parse(xhr.response);
                    var table = '<table>\n';
                    table += '<tr><th>Title</th><th class="center">Type</th><th class="center">Start Year</th></tr>\n';
                    titles.forEach(function(title) {
                        table += '<tr><td><a href="http://imdb.com/title/' + title.tconst + '" target="_blank">' + title.primaryTitle + '</a></td>';
                        table += '<td class="center">' + title.titleType + '</td><td class="center">' + title.startYear + '</td></th></tr>';
                    });
                    table += '</table>';
                    document.getElementById('results').innerHTML = table;
                }
            }
            xhr.open('GET', 'title_search.php?title=' + key);
            xhr.send();
            console.log(key);
        }
    </script>
</head>
<body>
    <h1>Welcome to GraphTV!</h1>
    <p>Hello <?php echo $_SESSION[SESSION_USERNAME_KEY]; ?>, welcome to GraphTV!<p>
    <p><a href="logout.php">Click here to logout.</a></p>
    <?php
        if ($_SESSION[SESSION_ROLE_KEY] == 'admin') {
            ?>
            <h2>Admin Functions!</h2>
            <p>Administrator interface includes function for:</p>
            <ul>
                <li><a href="haha.php">Drop the IMDB database.</a></li>
                <li><a href="haha.php">Add a new title to the database, along with ratings.</a></li>
                <li><a href="haha.php">Edit review, cast, and other information.</a></li>
                <li><a href="haha.php">View account information for registered users.</a></li>
            </ul>
            <?php
        }
    ?>
    <h2>Search for a Title</h2>
    <p>Enter your title in the box below, and click the OK button. Note: results are limited to the first 50 matches.</p>
    <p><label>Enter title:</label> <input type="text" id="title"> <button onclick="search();">OK</button></p>
    <div id="results"></div>
</body>
</html>