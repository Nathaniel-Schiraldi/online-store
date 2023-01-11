<?php

/*Author: Nathaniel Schiraldi
Student Number: 000855552
Last Modified: November 18, 2022
Description: Basic database connection script.*/

try {
    $dbh = new PDO("mysql:host=localhost;dbname=sa000855552",
            "sa000855552", "Sa_20030623");
} catch (Exception $e) {
    die("ERROR: Couldn't connect. {$e->getMessage()}");
}

?>