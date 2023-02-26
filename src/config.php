<?php
if( !defined('DB_SERVER')) define('DB_SERVER', 'dijkstra.ug.bcc.bilkent.edu.tr');
if( !defined('DB_USERNAME')) define('DB_USERNAME', 'lara.fenercioglu');
if( !defined('DB_PASSWORD')) define('DB_PASSWORD', 'YAfSK1bW');
if( !defined('DB_NAME')) define('DB_NAME', 'lara_fenercioglu');

$mysqli = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
    die("ERROR: Connection couldn't be established to database. " . mysqli_connect_error());
}
?>
