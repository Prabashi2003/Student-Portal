<?php
$host = 'sql102.infinityfree.com';
$dbname = 'if0_39670566_student_db';
$username = 'if0_39670566';
$password = 'lIHaiyvVnRkG';

$mysql = new mysqli($host, $username, $password, $dbname);

if ($mysql->connect_error) {
    die("Connection failed: " . $mysql->connect_error);
}

?>