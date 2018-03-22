<?php

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

session_start(); // 1

if (isset($_POST['isSend'])) {
    if (!isset($_POST['name']) || $_POST['name'] === '') {
        $errors['name'] = '名前が入力されていません';
    }
    if (!isset($_POST['text']) || $_POST['text'] === '') {
        $errors['text'] = '本文が入力されていません';
    }
}

$fp = fopen('data.csv', 'a+b');

if($_POST['name'] && $_POST['text']) {
    $name = (string)filter_input(INPUT_POST, 'name');
    $text = (string)filter_input(INPUT_POST, 'text');
    $token = (string)filter_input(INPUT_POST, 'token'); // 3

    // $fp = fopen('data.csv', 'a+b');
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && sha1(session_id()) === $token) { // 3
        flock($fp, LOCK_EX);
        fputcsv($fp, [$name, $text]);
        rewind($fp);

    }
    unset($_POST['name']);
    unset($_POST['text']);
}
flock($fp, LOCK_SH);
while ($row = fgetcsv($fp)) {
    $rows[] = $row;
}
flock($fp, LOCK_UN);
fclose($fp);
?>
<!DOCTYPE html>
<meta charset="UTF-8">
<title>掲示板</title>
<h1>掲示板</h1>
<section>
    <h2>新規投稿</h2>
    <form action="" method="post">
        名前: <input type="text" name="name" value="<?= $_POST['name']?>"><p class="error"><?= $errors['name'] ?></p><br>
        本文: <textarea name="text" cols="50" rows="8"><?= $_POST['text']?></textarea><p class="error"><?= $errors['text'] ?></p><br>
        <button type="submit">投稿</button>
        <input type="hidden" name="token" value="<?=h(sha1(session_id())) /*2*/ ?>">
        <input type="hidden" name="isSend" value="true">
    </form>
</section>
<section>
    <h2>投稿一覧</h2>
<?php if (!empty($rows)) :?>
    <ul>
<?php foreach ($rows as $row): ?>
        <li>名前： <?=h($row[0])?><br>
        本文： <?=h($row[1])?></li>
        <br>
<?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>投稿はまだありません</p>
<?php endif; ?>
</section>

<style>
    ul {
        list-style: none;
        
    }
    li {
        border:1px solid #000;
        color: #0a1;
        width: 500px;
    }
    textarea {
        resize:none;
    }
    input {
        
    } 
    .error {
        color:red;
    }
</style>

<script>

</script>