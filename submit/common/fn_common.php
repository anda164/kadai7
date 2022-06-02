<?php
//----------------------------------------
//自動採番関数 (第一引数：プレフィックス、第二引数：文字列長（裁定８）、戻り値：キー文字列)
//----------------------------------------
function auto_num($prefix, $length=8){
    if($length<8){
        $length = 8;
    }
    $json=file_get_contents('./data/num.json'); //num.json: プレフィックス単位で最終値を記録
    $jsondata = json_decode($json,true);
    $jsondata[$prefix] =(int)$jsondata[$prefix]+1; //記録されていた最終値をインクリメント
    $jsonstr =  json_encode($jsondata, JSON_UNESCAPED_UNICODE); //
    file_put_contents("./data/num.json", $jsonstr);//インクリメントされた値で最終値を更新

    return $prefix.str_pad($jsondata[$prefix], $length-strlen($prefix), '0', STR_PAD_LEFT);
}

//----------------------------------------
//セキュリティ対策 XSS -1
//XSS対応（ echoする場所で使用！それ以外はNG ）
//----------------------------------------
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//----------------------------------------
//画像登録処理 (#1：画像ファイルオブジェクト、#2：アップロードのパス、戻り値：処理結果のメッセージ)
//　※#1で指定したローカルファイルオブジェクトを#2のパスに保存
//----------------------------------------
function img_upload($img_file, $up_path){

    if(!empty($img_file)){
        
        $result = move_uploaded_file($img_file['upload_image']['tmp_name'],$up_path);

        if($result){
            $MSG = "画像アップロード成功！：'${up_path}'";
        }else{
            if($img_file['upload_image']['error']==4){
                $MSG = '画像の登録がありません。';
            }else{
                $MSG = '画像アップロード失敗！エラーコード：'.$img_file['upload_image']['error'];
            }
        }
    } else {
        $MSG = '画像を選択してください';
    }
    return $MSG;
}

?>