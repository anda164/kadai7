<?php
    //-----------------------------------------
    // FB接続関数(#1: DBユーザー名、#2: DBパスワード、戻り値：PDO)
    //-----------------------------------------
    function get_pdo($db_user,$db_pass){

        //※DB名、ホスト名、文字コードは固定埋め込み
        $dbname = "kadai_db";
        $charset = "utf8";
        $hostname = "localhost";

        try {
            $pdo = new PDO("mysql:dbname=${dbname};charset=${charset};host=${hostname}",$db_user,$db_pass);

            //PDOの設定は固定（変更が必要であれば戻り値に対してsetAttributeを行うこと）
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            //接続成功の場合、PDOオブジェクトを返す
            return $pdo;

        } catch (PDOException $e) {
            exit('DBConnectError:'.$e->getMessage());
        }
    }

    //-----------------------------------------
    // Select抽出関数(#1: PDOオブジェクト、#2: テーブル名、#3: プライマリキー、戻り値：当該レコードの値配列)
    //　キーを元に他のデータ値を取得
    //-----------------------------------------
    function select_db($pdo, $tbl, $key){
        $sql = "SELECT * FROM ${tbl} WHERE id = :id;";
        $stmt = $pdo->prepare($sql);
 
        // 「:id」に対して引数のキー値をセット
        $stmt->bindValue(':id', $key);
        
        // SQL実行
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    //-----------------------------------------
    // Delete関数(#1: PDOオブジェクト、#2: テーブル名、#3: プライマリキー、戻り値：処理結果(bool))
    //　キーを元に当該レコードを削除
    //-----------------------------------------
    function delete_db($pdo, $tbl, $key){
        $sql = "DELETE FROM ${tbl} WHERE id = :id;";
        $stmt = $pdo->prepare($sql); 
        // 「:id」に対して引数のキー値をセット
        $stmt->bindValue(':id', $key);

        $result = $stmt->execute();
        return $result;
    }




?>