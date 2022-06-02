<?php
    //検索パラメータ取得
    $srch_id = $_GET["id"];
    $srch_name = $_GET["name"];
    $srch_desc = $_GET["desc"];
    $srch_date_fr = $_GET["date_from"]; //対象データ(自)
    $srch_date_to = $_GET["date_to"]; //対象データ(至)　※データ作成日ベース
?>

<h1>登録データ一覧</h1>

<form action="./item_read.php" method="get">
    <table>
        <tr><th>ID:</th><td><input id="id" name="id" value="<?= $srch_id ?>"></td></tr>
        <tr><th>Name</th><td><input id="name" name="name" value="<?= $srch_name ?>"></td></tr>
        <tr><th>Description</th><td><input id="desc" name="desc" value="<?= $srch_desc ?>"></td></tr>
        <tr><th>Date</th><td><input id="date_from" type="date" name="date_from" value="<?= $srch_date_fr ?>"> - <input id="date_to" type="date" name="date_to" value="<?= $srch_date_to ?>"></td></tr>    
    </table>
    <input type="submit" value="Search" id="search_btn">
</form>

<?php
//-----------------
//データ表示部生成処理

//=====
//DB読み込み処理

// ０. DB 接続
require_once "./common/db_connect.php"; // DB接続関数読み込み(DB名:"kadai_db", 文字コード:"utf8"; ホスト名:localhost)
$pdo = get_pdo("root","root"); // DB接続関数呼び出し(#1[DB User]: "root", #2[DB Pass]: "root"

// 1. SQL文を用意

$ref = new DateTime($_GET["date_to"]);
$srch_date_to = $ref->modify('+1 day')->format('Y-m-d H:i:s'); //当日日付を含めるため検索値をインクリメント

$sql = "SELECT * FROM items WHERE";
$sql .= " id LIKE '%${srch_id}%'";

$sql .= "AND name LIKE '%${srch_name}%'";
$sql .= "AND description LIKE '%${srch_desc}%'";
$sql .= "AND date_created >= '${srch_date_fr}'";
$sql .= "AND date_created < '${srch_date_to}'";
$sql .= ";";
$stmt = $pdo->prepare($sql);


// 2. 条件Bind
$status = $stmt->execute();

//３．データ表示
require_once "./common/fn_common.php"; //XSS対策関数を含む共通関数群読み込み

$view="";
if ($status==false) {
    //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("ErrorQuery:".$error[2]);
}else{
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
  $view .= "<table>";
  $view .= "<th>Item ID</th>";
  $view .= "<th>Item Name</th>";
  $view .= "<th>Description</th>";
  $view .= "<th>Image</th>";
  $view .= "<th>Date created</th>";
  $view .= "<th>Date Updated</th>";
  
  while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
    //select.php の $view処理部分にXSS対策をする。
    $id     = h($result["id"]);
    $name   = h($result["name"]);
    $desc   = h($result["description"]);
    $impth  = h($result["image_path"]);
    $created  = h($result["date_created"]);
    $updated  = h($result["last_updated"]);

    $view .= "
            <tr>
                <td><a href='item_add.php?id=${id}'>${id}</a></td>
                <td>${name}</td><td>${desc}</td>
                <td><img src='${impth}' alt='${impth}'></td>
                <td>${created}</td><td>${updated}</td>
                </tr>
            
            ";
  }
  $view .= "</table>";


}

?>
<!-- Main[Start] -->
<div>
    <div class="container jumbotron">
        <?= $view ?>
    </div>
</div>
<!-- Main[End] -->


<ul>
    <li><a href="item_read.php">確認する</a></li>
    <li><a href="item_add.php">戻る</a></li>
</ul>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    //フォーム検索パラメータ 日付エラーチェック
    $("#search_btn").on("click", function(){
        const $in_date_fr = $("#date_from").val();
        const $in_date_to = $("#date_to").val();

        if ($in_date_to != "" && $in_date_fr > $in_date_to) {
            alert("検索範囲の指定が不正です");
        }
    });

</script>