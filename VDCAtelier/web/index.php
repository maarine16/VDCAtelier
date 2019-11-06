<?php 

require __DIR__ . '/../vendor/autoload.php';

session_start();

$a = new Manager\Application; 
$a->run();