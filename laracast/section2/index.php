<?php

require 'functions.php';
#dd($_SERVER["REQUEST_URI"]);
#require 'router.php';

//connect to our MySQL database
$dsn = "mysql:host=localhost;port=3306;dbname=myapp;user=root;charset=utf8mb4";
$pdo = new PDO($dsn);
$statement = $pdo->prepare("select * from posts");
$statement-> execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);
dd($posts);