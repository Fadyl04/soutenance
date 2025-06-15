<?php
$dbhost='localhost';
$dbname='adjarra';
$dbuser='root';
$dbpass='Fadyl111';

try {
    $db= new PDO('mysql:host=localhost;dbname=adjarra','root','Fadyl111');


    
} catch (PDOException $e) {
    //throw $th;
    die('erreur');

    print'erreur'.$e->getMessage();
}





?>