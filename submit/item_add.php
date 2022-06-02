
<?php
    //PHP 追加/更新機能

    //GETパラメータの値：
    //　設定なし： 追加モード
    //　設定なし： 更新・削除モード

    $initial_id=$_GET["id"];

    //更新モード時の処理（DBから値を抽出しフォームに設定）
    if ($initial_id){
        //DB読み込み処理
        //0.DB 接続
        require_once "./common/db_connect.php"; // DB接続関数読み込み(DB名:"kadai_db", 文字コード:"utf8"; ホスト名:localhost)
        $pdo = get_pdo("root","root"); // DB接続関数呼び出し(#1[DB User]: "root", #2[DB Pass]: "root"
        //1.値抽出
        $result = select_db($pdo, "items", $initial_id);
        //2.値設定 => HTML埋め込み
        $initial_name = $result["name"];
        $initial_desc = $result["description"];
        $initial_img = $result["image_path"];
    };
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <form action="?" method="post" enctype="multipart/form-data">
        <table>
            <tr><th><label for="item_id">Item ID</label></th><td><input type="text" id="item_id" name="item_id" value="<?= $initial_id ?>" readonly style="background-color: lightgray;"></td></tr>
            <tr><th>Item Name</th><td><input type="text" id="item_name" name="item_name" value="<?= $initial_name ?>"></td></tr>
            <tr><th>Description</th><td><input type="text" id="item_desc"  name="item_desc" value="<?= $initial_desc ?>"></td></tr>
            <tr><th>Image</th>
                <td>
                    <input type="file" name="upload_image" id="item_img_input" value="<?= $initial_img ?>">
                </td>
            </tr>
            <tr><th></th>
                <td>
                    <img src='<?= $initial_img ?>' alt='<?= $initial_img ?>' id="item_img">
                </td>
            </tr>
        </table>
        <br>
        <button type="submit" name="_method" value="post" id="submit_btn" onclick="return checkMyForm();" formaction="item_write.php">Submit</button>
        <button type="submit" name="_method" value="delete" id="delete_btn" onclick="return checkMyForm();" formaction="item_write.php">Delete</button>
    </form>
</body>

</html>



<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
//-------------------------
//画面動作設定処理(Javascript)
//-------------------------

    //ボタンラベル変更（”更新”）
    let url = new URL(window.location.href);
    let params = url.searchParams;
    if(params.get('id')){
        //　※更新モード（GETパラメータが存在する)の場合、ボタンのラベルを”Update”に変更する　
        $("#submit_btn").text("Update");
    }else{
        //　※追加モード（GETパラメータが存在しない)の場合、削除ボタンを非表示
        $("#delete_btn").hide();
    }

    //画像ファイル設定時のプレビュー表示処理
    $('#item_img_input').on('change', function (e) {
        var fileset = $(this).val();
        if (fileset === '') {
            alert("canceled");
            $("#item_img").attr('src', "");
            $("#item_img").attr('alt', "");
        } else {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#item_img").attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    //Item名の入力がない場合にJavascriptの処理を止めるための関数
    function checkMyForm() {
        if($("#item_name").val()=="") {
            alert("Item Nameの入力がありません");
            return false; // myFormのsubmitを止める
        } else {
            return true; // myFormをsubmitする
        }
    }



</script>
