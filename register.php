<?
$pdo = new PDO('mysql:host=[::1];dbname=test','root');
try{
	$pdo->prepare('INSERT INTO user(account, password, name) VALUES (:account, :password, :name)')->execute($_REQUEST);
	echo '註冊成功';
}
catch(Exception $e){
	echo '註冊失敗';
}