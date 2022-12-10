<?
header('content-type: text/json');
$pdo = new PDO('mysql:host=[::1];dbname=test','root');
$sql = $pdo->prepare('SELECT name FROM user WHERE account = :account and password = :password');
$sql->execute($_REQUEST);
echo json_encode(($sql->fetchAll()[0] ?? [null])[0]);