
<?php
ini_set('display_errors', 1);
//POST引数受け取り
$item_id=$_POST["item_id"];

//POST引数 item_id なしの場合、登録モード
if($item_id==""){
    list($item_id, $fw) = new_data($_POST);
    $mode = "登録";
}else {
//POST引数ありの場合、編集・削除モード

    if($_POST["_method"]=="delete"){
        //削除モード
        $mode = "削除";
        $fw = delete_data("items", $item_id);
    }else{
        //更新モード
        $mode = "更新";
        $fw = update_data("items", $_POST);
    }
}
//---------------------
//登録関数(登録モード：引数：POSTオブジェクト、戻り値: #1:item_id、#2: 実行結果Bool)
function new_data($in_data){
    //ID自動採番
    global $item_id; //
    require_once "./common/fn_common.php";  //自動採番関数を含む共通関数群読み込み
    $prefix = "AA";
    $item_id = auto_num($prefix, $length=8); //#1: プレフィックス, #2: 文字列長
    //}
    
    //画像保存処理
    $filename = $_FILES['upload_image']['name'];

    if($_FILES["upload_image"]["error"]==4){
        //登録ファイルがない場合
        $up_path="";
        $MSG = '画像なし';
    }else{
        //登録ファイルがある場合
        $filename = $_FILES['upload_image']['name'];
        $up_path = './images/'.$item_id."_".$filename;
        $MSG = img_upload($_FILES, $up_path); //画像登録関数で登録 (#1: ファイルオブジェクト, #2保存先パス)
        if(!strpos($MSG,"成功")){
            $up_path = '';
        }
    }

    //=====
    //DB登録処理
    // ０. DB 接続
    require_once "./common/db_connect.php"; // DB接続関数読み込み(DB名:"kadai_db", 文字コード:"utf8"; ホスト名:localhost)
    $pdo = get_pdo("root","root"); // DB接続関数呼び出し(#1[DB User]: "root", #2[DB Pass]: "root"
    
    // 1. SQL文を用意
    $stmt = $pdo->prepare("INSERT INTO 
                            items(
                            id,
                            name,
                            description,
                            image_path,
                            date_created,
                            last_updated
                            )
                            VALUES(
                            :item_id,
                            :item_name,
                            :item_desc,
                            :image_path,
                            sysdate(),
                            sysdate()
                            );");

    //  2. バインド変数を用意
    // Integer 数値の場合 PDO::PARAM_INT
    // String文字列の場合 PDO::PARAM_STR

    $item_name = $in_data["item_name"];
    $item_desc = $in_data["item_desc"];

    $stmt->bindValue(':item_id', $item_id , PDO::PARAM_STR);  //自動採番済
    $stmt->bindValue(':item_name', $item_name, PDO::PARAM_STR);
    $stmt->bindValue(':item_desc', $item_desc, PDO::PARAM_STR);
    $stmt->bindValue(':image_path', $up_path, PDO::PARAM_STR); //画像保存時取得

    //  3. 実行
    $status = $stmt->execute();
    
    //４．データ登録処理後
    if($status === false){
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $error = $stmt->errorInfo();
        exit('ErrorMessage:'.$error[2]);
    }else{
        return array($item_id,true);
        //５．index.phpへリダイレクト =>なし
        //header("location: item_read.php");
    }

}

//---------------------
//更新関数(更新モード：引数 #1：更新先テーブル、#2: 更新対象POSTオブジェクト、　戻り値:実行結果Bool)

function update_data($tbl, $in_data){
    require_once "./common/fn_common.php";  //共通関数群（画像保存関数）読み込み

    //=====
    //DB登録処理

    // ０. DB 接続
    require_once "./common/db_connect.php"; // DB接続関数読み込み(DB名:"kadai_db", 文字コード:"utf8"; ホスト名:localhost)
    $pdo = get_pdo("root","root"); // DB接続関数呼び出し(#1[DB User]: "root", #2[DB Pass]: "root"
    
    // 1. SQL文を用意

    $sql = "UPDATE ${tbl} set
                name = :item_name,
                description = :item_desc,
                {:image_path}
                last_updated = sysdate()
            WHERE id=:item_id ; 
            ";
    $sql .= "";

    //  2. バインド変数を用意
    // Integer 数値の場合 PDO::PARAM_INT
    // String文字列の場合 PDO::PARAM_STR
    $item_id = $in_data["item_id"];
    $item_name = $in_data["item_name"];
    $item_desc = $in_data["item_desc"];

    //Fileが更新されていた場合エラーコード：0 (Fileあり)
    if($_FILES["upload_image"]["error"]==0){
        //すでに保存されているファイルを削除
        //$sql = str_replace("{:image_path}", '東京都港区', $sql);
        
        $result = select_db($pdo, $tbl, $item_id);

        if(file_exists($result["image_path"])){
            unlink($result["image_path"]);
        }
        //新たに選択したファイルをアップロード
        //$filename = $img_file['upload_image']['name'];
        $filename = $_FILES['upload_image']['name'];
        $up_path = './images/'.$item_id."_".$filename;
        $sql = str_replace("{:image_path}", "image_path = '${up_path}',", $sql);

        $MSG = img_upload($_FILES, $up_path);

    }else{
        //File更新なし エラーコード：4
        $sql = str_replace("{:image_path}", '', $sql);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':item_id', $item_id , PDO::PARAM_STR);
    $stmt->bindValue(':item_name', $item_name, PDO::PARAM_STR);
    $stmt->bindValue(':item_desc', $item_desc, PDO::PARAM_STR);

    //  3. 実行
    //DB更新
    $status = $stmt->execute();
 
    //４．データ登録処理後
    if($status === false){
        //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
        $fw=false;
        $error = $stmt->errorInfo();
        exit('ErrorMessage:'.$error[2]);
    }else{
        $fw=true;
        //５．index.phpへリダイレクト =>なし
        //header("location: item_read.php");
    }
    return $fw;

}

//---------------------
//削除関数(削除モード：引数 #1：更新先テーブル、#2: 削除対象 item_id、　戻り値:実行結果Bool)
function delete_data($tbl, $item_id){
    require_once "./common/db_connect.php"; // DB接続関数読み込み(DB名:"kadai_db", 文字コード:"utf8"; ホスト名:localhost)
    $pdo = get_pdo("root","root"); // DB接続関数呼び出し(#1[DB User]: "root", #2[DB Pass]: "root"

    $result = select_db($pdo, $tbl, $item_id);
    if(file_exists($result["image_path"])){
        unlink($result["image_path"]);
    }
    $fw = delete_db($pdo, $tbl, $item_id);

    return $fw;    

}

?>


<html>

<head>
    <meta charset="utf-8">
    <title>DB登録</title>
</head>

<body>
    <!-- データ更新メッセージ （更新成功/失敗） -->
    <?php if($fw){;?>
        <h1><?php echo $_POST["item_name"]; ?> を<?= $mode ?>しました。</h1>
        <h2>Item ID: <?php echo $item_id; ?></h2>
    <?php } else { ?>
        <h1><?php echo $_POST["item_name"]; ?> の<?= $mode ?>に失敗しました。</h1>
    <?php } ;?>

    <!--  画像登録結果メッセージ -->
    <p><?php if(!empty($MSG)) echo $MSG;?></p>
    
    <!-- 登録画像 -->
    <?php if(!empty($up_path)){;?>
        <img src="<?php echo $up_path;?>" alt="">
    <?php } ;?>

    <ul>
        <li><a href="item_read.php">データ一覧を確認する</a></li>
        <li><a href="item_add.php">戻る</a></li>
    </ul>
</body>

</html>
<?php


?>