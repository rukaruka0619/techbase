<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mission_5-1</title>
</head>
<!--ここまではお決まりのやつですね-->
<body>
<?php

// DB接続設定
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//m4-2 テーブルが無かったら作成
$createTableSql = <<<EOT
CREATE TABLE  IF NOT EXISTS board (
    id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32),
    comment TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    post_pass VARCHAR(255) NOT NULL
) DEFAULT CHARACTER SET=utf8mb4;
EOT;

$result = $pdo->query($createTableSql);

//m4-3　テーブルを表示
//$sql ='SHOW TABLES';
//$result = $pdo -> query($sql);
//foreach ($result as $row){
   // echo $row[0];
    //echo '<br>';
//}
//echo "<hr>";
    
//m4-4　
//$sql ='SHOW CREATE TABLE tbtest';
//$result = $pdo -> query($sql);
//foreach ($result as $row){
   // echo $row[1];
//}
//echo "<hr>";
    
//m4-5　投稿処理
if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["post_pass"]) && empty($_POST["flag"])) {
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $post_pass = password_hash($_POST["post_pass"], PASSWORD_DEFAULT);

    $sql = $pdo->prepare("INSERT INTO board (name, comment, post_pass) VALUES (:name, :comment, :post_pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':post_pass', $post_pass, PDO::PARAM_STR);
    $sql -> execute();
    
    echo "☆投稿できました☆";

//投稿処理終わり
    
// m4-6,m4-8 削除機能
}elseif(!empty($_POST["delNum"]) && !empty($_POST["delPass"])){
    $delNum = $_POST["delNum"];
    $delPass = $_POST["delPass"];

    //m4-6
    $selectRecordSql = "SELECT * FROM board";
    $result = $pdo -> query($selectRecordSql);
    $results = $result -> fetchAll();

    foreach($results as $row) {
        
    //m4-8
        if($row["id"] === $delNum && password_verify($delPass, $row['post_pass']) === true) {
        $deleteRecordSql = "delete from board where id=:id";
        $result = $pdo->prepare($deleteRecordSql);
        $result->bindParam(":id", $delNum, PDO::PARAM_INT);
        $result->execute();
        echo "☆削除できました☆";
        }
    }

//削除機能終わり

//m4-6,m4-7 編集機能
//ediにする
}elseif(!empty($_POST["ediNum"]) && !empty($_POST["ediPass"])) {
    $ediNum = $_POST["ediNum"];
    $ediPass = $_POST["ediPass"];

    $selectRecordSql = "SELECT * FROM board";
    $result = $pdo -> query($selectRecordSql);
    $results = $result -> fetchAll();

    foreach($results as $row) {
        if($row["id"] === $ediNum && password_verify($ediPass, $row["post_pass"]) === true) {
            $ediNum = $row["id"];
            $ediName = $row["name"];
            $ediComment = $row["comment"];
            break;
        }
    }

//newにする
}elseif(!empty($_POST["flag"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["post_pass"])) {
    $newNum = $_POST["flag"];
    $newName = $_POST["name"];
    $newComment = $_POST["comment"];
    $newPass = $_POST["post_pass"];
    
    $updateRecordSql = "UPDATE board SET name=:name, comment=:comment, post_pass=:post_pass WHERE id=:id";
    $result = $pdo->prepare($updateRecordSql);
    $result->bindParam(":name", $newName, PDO::PARAM_STR);
    $result->bindParam(":comment", $newComment, PDO::PARAM_STR);
    $result->bindParam(":id", $newNum, PDO::PARAM_INT);
    $result->bindParam(":post_pass", $newPass, PDO::PARAM_STR);
    $result->execute();
    
    echo "☆編集できました☆";
}
?>

<p>【投稿はこちら】</p>
<form action="" method="post">
    <input type="hidden" name="flag" value="<?= $ediNum ?? ''; ?>">
    名前 : <input type="text" name="name" placeholder="名前" value="<?= $ediName ?? ''; ?>"><br>
    コメント : <input type="text" name="comment" placeholder="コメント" value="<?= $ediComment ?? ''; ?>"><br>
    パスワード : <input type="password" name="post_pass" placeholder="パスワード"><br>
    <input type="submit" name="submit">
</form>
<p>【削除はこちら（パスワードが必要です）】</p>
<form action="" method="post">
    投稿番号 : <input type="number" name="delNum" placeholder="削除する番号"><br>
    パスワード : <input type="password" name="delPass" placeholder="パスワード"><br>
    <input type="submit" name="delete" value="削除"> 
</form>
<p>【編集はこちら（パスワードが必要です）】</p>
<form action="" method="post">
    投稿番号 : <input type="number" name="ediNum" placeholder="編集する番号"><br>
    パスワード : <input type="password" name="ediPass" placeholder="パスワード"><br>
    <input type="submit" name="edit" value="編集">
</form>

<?php 
$sql = "SELECT * FROM board";
$result = $pdo -> query($sql);
$results = $result -> fetchAll();
foreach($results as $row) {
    echo $row['id'] . ",";
    echo $row['name'] . ",";
    echo $row['comment'] . ",";
    echo $row['created_at'];
    echo "<hr>";
}
?>

</body>
</html>
