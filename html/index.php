<?php

session_start();

//ログインしていない場合、login.phpを表示
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('db.php');
require_once('functions.php');

/**
 * @param String $tweet_textarea
 * つぶやき投稿を行う。
 */
function newtweet($tweet_textarea)
{
    // 汎用ログインチェック処理をルータに作る。早期リターンで
    createTweet($tweet_textarea, $_SESSION['user_id']);
}

/**
 * ログアウト処理を行う。
 */
function logout()
{
    $_SESSION = [];
    $msg = 'ログアウトしました。';
}

if ($_POST) { /* POST Requests */
    if (isset($_POST['logout'])) { //ログアウト処理
        logout();
        header("Location: login.php");
    } elseif (isset($_POST['reply_post_id'])) { //返信処理
        newReplyTweet($_POST['tweet_textarea'], $_SESSION['user_id'], $_POST['reply_post_id']);
        header("Location: index.php");
    } elseif (isset($_POST['tweet_textarea'])) { //投稿処理
        newtweet($_POST['tweet_textarea']);
        header("Location: index.php");
    }
}

$tweets = getTweets();
$tweet_count = count($tweets);

?>

<!DOCTYPE html>
<html lang="ja">

<?php require_once('head.php'); ?>

<body>
  <div class="container">
    <h1 class="my-5">新規投稿</h1>
    <div class="card mb-3">
      <div class="card-body">
        <form method="POST">
    <?php 
    if (isset($_GET['reply'])) {
        ?>
          <textarea class="form-control" type=textarea name="tweet_textarea" ?><?= getUserReplyText($_GET['reply']) ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?= $_GET['reply'] ?>" />
        <?php
    } else {
        ?>
          <textarea class="form-control" type=textarea name="tweet_textarea" ?></textarea>
        <?php
    }
    ?>  
          
          <br>
          <input class="btn btn-primary" type=submit value="投稿">
        </form>
      </div>
    </div>
    <h1 class="my-5">コメント一覧</h1>
    <?php foreach ($tweets as $t) {
        ?>
      <div class="card mb-3">
        <div class="card-body">
          <p class="card-title"><b><?= "{$t['id']}" ?></b> <?= "{$t['name']}" ?> <small><?= "{$t['updated_at']}" ?></small></p>
          <p class="card-text"><?= "{$t['text']}" ?></p>
          
          <p><a href="index.php?reply=<?= "{$t['id']}" ?>">[返信する]</a>
          <?php if (isset($t['reply_id'])) {
            ?>
           <a href="view.php?id=<?= "{$t['reply_id']}" ?>">[返信元のメッセージ]</a>
          <?php
        } ?>
          </p>
          
        </div>
      </div>
    <?php
    } ?>
    <form method="POST">
      <input type="hidden" name="logout" value="dummy">
      <button class="btn btn-primary">ログアウト</button>
    </form>
    <br>
  </div>
</body>

</html>
