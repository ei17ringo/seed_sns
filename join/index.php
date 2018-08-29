<?php
    // $_SESSIONの値をファイル内で使用する時に使う
    session_start();
    /*
    ステップ① designからコピペ。拡張子を.phpにする
    ステップ② ブラウザで表示
    ステップ③ パス修正。各aタグのリンク先を.phpにする。
    ステップ④ db_connect.php作成
    ステップ⑤ データベース接続
    ステップ⑥ 入力チェック
    ステップ⑦ パスワード文字数チェック
    ステップ⑧ メールアドレス重複チェック

    ステップ⑨ enctype, input type="FILE"
    ステップ⑩ picture_pathフォルダ作成, パーミッション777
    ステップ⑪ ファイル選択、ファイルの拡張子チェック
    ステップ⑫ ファイルアップロード処理
    */



    // ステップ⑤ データベース接続
    require('../db_connect.php'); // ひとつ前の階層にあるため ../ を使う



    // ステップ⑥ 入力チェック
    if (!empty($_POST)) { // もしPOST送信されている時

        if ($_POST['nick_name'] == '') { // 空の時
            $error['nick_name'] = 'blank'; // $error['nick_name']という変数を自分で作り、blankという文字列を代入している。
        }

        if ($_POST['email'] == '') {
            $error['email'] = 'blank';
        }

        if ($_POST['password'] == '') {
            $error['password'] = 'blank';
        } elseif (mb_strlen($_POST['password']) < 4) { // ステップ⑦ パスワード文字数チェック
            /*
            mb_strlen() == 文字の長さの文字数を数字で返してくれる関数
            今回だと、POST送信されてきたpasswordの文字数を数え、文字数が4文字に達していなかったら次の処理を行う。
            */
            $error['password'] = 'length'; // passwordの文字数が4文字に達していなかったらlengthという文字列を$error['password']に代入
        }


        // ステップ⑧ メールアドレス重複チェック
        /*
        DBに同じemailがあるか確認したい。既に登録されているアドレスでは新規登録ができないようにしたい。

        ★★★ COUNT() ★★★
            sql分での関数。ヒットした件数を数字で取得してくれる。
            SELECT COUNT(*) FROM `members` // membersテーブルの中身全部のレコード数を取得

        ★★★ AS句 ★★★
            「●●として」sql文で使う。取得したデータに別の名前をつけて扱いやすいようにする。
            SELECT COUNT(*) AS `count` FROM `members` // membersテーブルの中身全部のレコード数をcountという名前で取得
        */
        if (!isset($error)) { // $errorが存在しない時
            $sql = 'SELECT COUNT(email) AS `count` FROM `members` WHERE `email`=?';
            // membersテーブルにあるemailが、POST送信されてきたemailと重複した回数を取得し、連想配列のkeyとしてcountという名前で保管する。値は回数が入る。
            $data = array($_POST['email']); // ?があったら必ず書く配列。
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            $email_count = $stmt->fetch(PDO::FETCH_ASSOC); // 重複回数をemail_countに代入

            echo '<pre>' . '<br>';
            // echo '↓ var_dump($email_count); ↓' . '<br>';
            var_dump($email_count); // 1以上の数字が出力されれば、そのアドレスは既に登録済みである、ということ。
            echo '</pre>';

            if ($email_count['count'] >= 1) { // もし重複するアドレスが1件以上既にDBに存在する場合
                $error['email'] = 'dublicated';
            }
        }

        // ステップ⑪ ファイル選択、ファイルの拡張子チェック
        // まずは$_FILESにどんな値が入っているか確認
        // $_FILES == 「enctype="multipart/form-data"」をformタグ内に入れることによって生成できるようになるスーパーグローバル変数。
        // $_FILESが持っている情報はファイルの名前、POST送信時に一時的にファイルが保存されるディレクトリ名、ファイルのサイズなどのファイルの詳細
        if (isset($_FILES)) {
            echo '<pre>' . '<br>' . '<br>';
            var_dump($_FILES);
            echo '</pre>';
        }
        /*
        これからどうしたいかと言うと、 .jpg, .gif, .png これら3つは登録できるようにしたい。それを実現するためい、POST送信されてきたファイル名の末尾にある拡張子を判定しなければならない。

        ★★★ substr() == substringの略。部分文字列の数を数える。 substr(元になる文字列, 文字のスタート位置からプラスマイナス何文字か) ★★★
        */
        // $hoge = '0123456789.php';
        // echo substr($hoge, 4);
        // echo substr($hoge, -4);

        if (!isset($error) && !empty($_FILES)) { // すべてのエラーがなければ
            if ($_FILES['picture_path']['name'] == '') {
                $error['picture_path'] = 'blank';
            } else {
                $ext = substr($_FILES['picture_path']['name'], -4); // ファイル名の最後から4文字を取得 ちなみにextension = 拡張子
                $ext = strtolower($ext);
                // strtolower() == ()内の文字列を小文字に変換する
                // strtoupper() == ()内の文字列を大文字に変換する
                if ($ext == '.jpg' || $ext == '.png' || $ext == '.gif') {
                    // ステップ⑫ ファイルアップロード処理
                    /*
                    ex) michy.jpgという画像を選択した場合、ファイル名に日付を加えたい。同じファイル名で違う画像でも重複しないように。日付を入れれば誰のどのデータか判別しやすいため。
                    */

                    $picture_name = date('YmdHis') . $_FILES['picture_path']['name'];
                        /*
                        $picture_nameには、submitした瞬間の日付とファイル名が連結された文字列が代入されている。
                        ex) 20180725114040michy.jpg

                        このファイル名としてDBに保存したい。
                        が、今は一時保管場所に保管されているので、永久保管場所に保存したい。
                        ★★★ move_uploaded_file() == ファイルをまずはseed_snsのフォルダにアップロードする関数。
                        move_uploaded_file(アップロードしたいファイル, フォルダのどこにどういう名前でアップロードするか指定) ★★★
                        */
                    move_uploaded_file($_FILES['picture_path']['tmp_name'], '../picture_path/' . $picture_name);

                        // $_SESSION, $_COOKIE
                        // セッションとクッキー
                        // セッションはサーバー上に一時的に保存する場所
                        // クッキーはブラウザ上に一時的に保存する場所
                        // $_SESSION == セッションから値を取得することができるスーパーグローバル変数
                        // $_COOKIE == クッキー(ブラウザ上)から値を取得することができるスーパーグローバル変数
                        // ポイント↓！！！
                        // $_SESSION(セッション)に値を保存してどのページからも取得できるようにする
                    $_SESSION['join'] = $_POST;
                    $_SESSION['join']['picture_path'] = $picture_name;
                    // var_dump($_SESSION);
                    header('Location: check.php');

                } else {
                    $error['picture_path'] = 'type';
                }
            }
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
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!-- designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！ -->

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
                <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
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

                <legend>会員登録</legend>
                <!-- ステップ⑨ enctype, input type="FILE" -->
                <form method="post" action="" class="form-horizontal" role="form" enctype="multipart/form-data"> <!-- enctype="multipart/form-data" ファイル送信時、formタグに記載する、魔法の言葉。記載しなければ、POST送信で画像を送りたくても画像のファイル名、つまりただの文字列しか送ることができない。 -->

                    <!-- ニックネーム -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">ニックネーム</label>
                        <div class="col-sm-8">
                            <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun">

                            <!-- ステップ⑥ 入力チェック -->
                            <?php if (isset($error['nick_name'])): ?> <!-- $error['nick_name']が存在するとき -->
                                <p class="error">* ニックネームを入力してください。</p>
                            <?php endif ?>

                        </div>
                    </div>

                    <!-- メールアドレス -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">メールアドレス</label>
                        <div class="col-sm-8">
                            <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">

                            <!-- ステップ⑥ 入力チェック -->
                            <?php if (isset($error['email']) && $error['email'] == 'blank'): ?> <!-- $error['email']が存在し、種類がblankのとき -->
                                <p class="error">* アドレスを入力してください。</p>

                            <!-- ステップ⑧ メールアドレス重複チェック -->
                            <?php elseif (isset($error['email']) && $error['email'] == 'dublicated'): ?>
                                <p class="error">* 既に登録済みのアドレスです。</p>

                            <?php endif ?>

                        </div>
                    </div>

                    <!-- パスワード -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">パスワード</label>
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control" placeholder="">

                            <!-- ステップ⑥ 入力チェック -->
                            <?php if (isset($error['password']) && $error['password'] == 'blank'): ?> <!-- $error['password']が存在するとき -->
                                <p class="error">* パスワードを入力してください。</p>

                                <!-- ステップ⑦ パスワード入力チェック -->
                            <?php elseif (isset($error['password']) && $error['password'] == 'length'): ?>
                                <p class="error">* パスワードは4文字以上入力してください。</p>
                            <?php endif ?>

                        </div>
                    </div>

                    <!-- プロフィール写真 -->
                    <div class="form-group">
                        <label class="col-sm-4 control-label">プロフィール写真</label>
                        <div class="col-sm-8">
                            <input type="file" name="picture_path" class="form-control">
                        </div>
                    </div>

                    <input type="submit" class="btn btn-default" value="確認画面へ">

                </form>

            </div>
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
</body>
</html>
