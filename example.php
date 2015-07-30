<?php 
//MySQLデータベースサーバーへの接続に必要な情報をインクルードする。
// こには、ユーザー名、データベース、パスワードを格納。
include("dbconfig.php");

// urlパラメーターに対し、colModelで解説されている4つのパラメーターを追加。
// 必要なクエリを構成するために、これらのパラメーターを取得する。
// Since we specify in the options of the grid that we will use a GET method 
// GETメソッドを使用する旨を、グリッドのオプションで指定してため、パラメーターを取得する適切なコマンドを使用するべきである。
// このケースでは、 $_GETとしている。$_POSTを使用する。最良の方法は、
// GET変数およびPOST変数を持つ$_REQUESTを使用することかもしれない。
// 詳細は、PHP に関する文書を参照のこと。
// リクエストしたページを取得。デフォルトでは、グリッドは、これを1に設定している。
$page = $_GET['page']; 

// グリッドにいくつ行を設定するか取得 -- グリッドに、rowNum パラメーター
$limit = $_GET['rows']; 

// インデックス行を取得。例: ユーザーがクリックしてソート。最初は、sortnameパラメーター
// その後colModelからインデックスを取得。 
$sidx = $_GET['sidx']; 

// ソートの順番 －－ 最初は、sortorder
$sord = $_GET['sord']; 

// はじめに渡さない場合は、インデックスは、最初の列を使用するか指定したインデックスを使用する。
if(!$sidx) $sidx =1; 

// MySQLデータベースサーバーに接続
$db = mysql_connect($dbhost, $dbuser, $dbpassword) or die("Connection Error: " . mysql_error()); 

// データベースを選択
mysql_select_db($database) or die("Error connecting to db."); 

// クエリの行数を計算。結果をページするために必要。
$result = mysql_query("SELECT COUNT(*) AS count FROM invheader"); 
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count']; 

// クエリの総ページ数を計算
if( $count > 0 ) { 
    $total_pages = ceil($count/$limit); 
} else { 
    $total_pages = 0; 
} 

// 何らかの理由で、要求したページが総数よりも大きい場合、
// 要求したページ数を総ページ数に設定する。
if ($page > $total_pages) $page=$total_pages;

// 行の開始位置を計算
$start = $limit*$page - $limit;

// 何らかの理由で、開始位置が負の数字の場合は、0に設定。
// よくあるケースでは、ユーザーが要求ページを０とタイプ。
if($start <0) $start = 0; 

// グリッドデータの実際のクエリ
$SQL = "SELECT invid, invdate, amount, tax,total, note FROM invheader ORDER BY $sidx $sord LIMIT $start , $limit"; 
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 

// 適切なヘッダー情報を設定するべき
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
    header("Content-type: application/xhtml+xml;charset=utf-8"); 
} else {
    header("Content-type: text/xml;charset=utf-8");
}
echo "<?xml version='1.0' encoding='utf-8'?>";
echo "<rows>";
echo "<page>".$page."</page>";
echo "<total>".$total_pages."</total>";
echo "<records>".$count."</records>";

// CDATAにテキストデータを必ず置く。
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    echo "<row id='". $row[invid]."'>";            
    echo "<cell>". $row[invid]."</cell>";
    echo "<cell>". $row[invdate]."</cell>";
    echo "<cell>". $row[amount]."</cell>";
    echo "<cell>". $row[tax]."</cell>";
    echo "<cell>". $row[total]."</cell>";
    echo "<cell><![CDATA[". $row[note]."]]></cell>";
    echo "</row>";
}
echo "</rows>"; 