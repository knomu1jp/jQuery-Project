<?php

mb_language("uni");
mb_internal_encoding("utf-8"); //内部文字コードを変更
mb_http_input("auto");
mb_http_output("utf-8");

$link = mysql_connect('192.168.11.105', 'jqGrid', 'jqGrid'); //user名とパスワードは各自
if (!$link) {
    die('接続できません: ' . mysql_error());
}

$db_name = "jqGrid"; //利用するデータベース名
mysql_select_db($db_name,$link);
$sql = 'SELECT * FROM table_php';

$query = mysql_query($sql);

//取得した結果を取り出して連想配列に入れていく
$arData= array();
while ($row = mysql_fetch_object($query)) {
    $arData[] = array(
       'id'=> $row->id
       ,'情報' => $row->情報
       ,'テキスト' => $row->テキストt
       ,'コメント' => $row->コメント
       );
}

//jsonとして出力
header('Content-type: application/json');
echo json_encode($arData);

?>