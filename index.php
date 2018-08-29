<!-- ログインのチェック、ログインしているユーザーの判定、投稿一覧表示、ログアウト機能 -->
<?php
    session_start();
    // データベースに接続している文字列が書いてあるファイルを読み込む
    require('db_connect.php');

    echo '<br>';
    echo '<br>';
    // var_dump($_SESSION);

    // ログインチェック
    // $_SESSION['id']がない時 == ログインしていない時
    if (!isset($_SESSION['id'])) {
        // ログインしていない時にlogin.phpに遷移する
        header('Location: login.php');
        exit(); // それ以降の処理を行わない
    }

    // ログインしているユーザーの情報を取得するsql文
    $sql = 'SELECT * FROM `members` WHERE `member_id`=?';
    $data = array($_SESSION['id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    // ログインしているユーザーの情報を$login_memberに代入
    $login_member = $stmt->fetch(PDO::FETCH_ASSOC);

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
   <!--  <style>
        body {
            background-image: -webkit-linear-gradient( 90deg, #6a2c89 0%, #67ab9f 100%);
        }
    </style> -->
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
                    <li><a href="logout.php">ログアウト</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-4 content-margin-top">
                <legend>ようこそ<?php echo $login_member['nick_name']; ?>さん！</legend>

                <form method="post" action="" class="form-horizontal" role="form">

                    <!-- つぶやき -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">つぶやき</label>
                        <div class="col-sm-8">
                            <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                        </div>
                    </div>
                    <ul class="paging">
                        <input type="submit" class="btn btn-info" value="つぶやく">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                        <li><a href="index.html" class="btn btn-default">前</a></li>
                            &nbsp;&nbsp;|&nbsp;&nbsp;
                        <li><a href="index.html" class="btn btn-default">次</a></li>
                    </ul>
                </form>
            </div>

            <div class="col-md-8 content-margin-top">
                <div class="msg">
                    <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
                    <p>
                        つぶやき４<span class="name"> (Seed kun) </span>
                        [<a href="#">Re</a>]
                    </p>
                    <p class="day">
                        <a href="view.html">
                            2016-01-28 18:04
                        </a>
                        [<a href="#" style="color: #00994C;">編集</a>]
                        [<a href="#" style="color: #F33;">削除</a>]
                    </p>
                </div>
                <div class="msg">
                    <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
                    <p>
                        つぶやき３<span class="name"> (Seed kun) </span>
                        [<a href="#">Re</a>]
                    </p>
                    <p class="day">
                        <a href="view.html">
                            2016-01-28 18:03
                        </a>
                        [<a href="#" style="color: #00994C;">編集</a>]
                        [<a href="#" style="color: #F33;">削除</a>]
                    </p>
                </div>
                <div class="msg">
                    <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
                    <p>
                        つぶやき２<span class="name"> (Seed kun) </span>
                        [<a href="#">Re</a>]
                    </p>
                    <p class="day">
                        <a href="view.html">
                            2016-01-28 18:02
                        </a>
                        [<a href="#" style="color: #00994C;">編集</a>]
                        [<a href="#" style="color: #F33;">削除</a>]
                    </p>
                </div>
                <div class="msg">
                    <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
                    <p>
                        つぶやき１<span class="name"> (Seed kun) </span>
                        [<a href="#">Re</a>]
                    </p>
                    <p class="day">
                        <a href="view.html">
                            2016-01-28 18:01
                        </a>
                        [<a href="#" style="color: #00994C;">編集</a>]
                        [<a href="#" style="color: #F33;">削除</a>]
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
