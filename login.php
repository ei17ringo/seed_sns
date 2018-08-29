<?php
    // $_SESSIONを使えるようにする
    session_start();
    // データベースに接続している文字列が書いてあるファイルを読み込む
    require('db_connect.php');

    // POST送信された時
    if (!empty($_POST)) {
        // POST送信されたメールアドレスとパスワードが一致するユーザーのデータを取得したい
        $sql = 'SELECT * FROM `members` WHERE `email`=? AND `password`=?';
        $data = array($_POST['email'], sha1($_POST['password']));
        $stmt = $dbh->prepare($sql); // 準備
        $stmt->execute($data); // 実行

        // 一致したレコードを使える形で取得する（フェッチする）
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        // echo '<br>';
        // echo '<br>';
        // var_dump($member);

        // ログインした情報が一致しなかった場合
        if ($member == false) {
            $error['login'] = 'failed';
        } else {
            // ログインした情報が一致した場合
            // 1.セッションにログインIDを保存
            $_SESSION['id'] = $member['member_id'];

            // 2.ログインした時間をセッションに保存
            $_SESSION['time'] = time();
            // time()
            // 現在時刻を取得する関数

            // 3.自動ログイン処理

            // 4.ログイン後の画面に遷移
            header('Location: index.php');
        }

    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 content-margin-top">
                <legend>ログイン</legend>

                <form method="post" action="" class="form-horizontal" role="form">
                    <!-- メールアドレス -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">メールアドレス</label>
                        <div class="col-sm-8">
                            <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
                        </div>
                    </div>

                    <!-- パスワード -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">パスワード</label>
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control" placeholder="">
                        </div>
                    </div>
                    <?php if(isset($error['login']) && $error['login'] == 'failed') { ?>
                        <p class="error">* ログインに失敗しました。もう一度入力してください。</p>
                    <?php } ?>
                    <input type="submit" class="btn btn-default" value="ログイン">
                </form>
            </div>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
