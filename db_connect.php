<?php

	// DB接続のみの処理。
	// データベース接続文字列。データベースに接続するために必要な文字列。
	// $dsn = '使用するDBの種類:dbname=DB名;host=ホスト名';  mysqlを使っているからmysql. これから作るDBの名前. mysqlを使う場合はlocalhost.
	$dsn = 'mysql:dbname=0618_seed_sns;host=localhost';
	$user = 'root';
	$password = '';
	$dbh = new PDO($dsn, $user, $password);
	$dbh->query('SET NAMES UTF8');

 ?>