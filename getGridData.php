<?php

    $total_pages = 0;
    $count = 0;
    $datatype = '';

    // リターンするデータの形式
    if (isset($_GET['datatype']) && $_GET['datatype'] != '')
    {
        $datatype = $_GET['datatype'];
    }

    // 要求されたページ
    if (isset($_GET['page']))
    {
        $page = (int)$_GET['page'];
    }
    else
    {
        $page = 1;  // デフォルト 1 ページ目
    }

    // グリッドへ表示する行数
    if (isset($_GET['rows']))
    {
        $limit = (int)$_GET['rows'];
    }
    else
    {
        $limit = 10;    // デフォルト 10 行
    }
    // ソートする列
    if (isset($_GET['sidx']))
    {
        $sidx = (string)$_GET['sidx'];
        $sidx = "id";   // デフォルト ID 列
    }
    else
    {
        $sidx = "id";   // デフォルト ID 列
    }

    // ソートの順序
    if (isset($_GET['sord']))
    {
        $sord = (string)$_GET['sord'];
    }
    else
    {
        $sord = ''; // デフォルト 指定なし
    }

    // データベースから取得し、行数、ページ数など算出する
    $users = db_grid_getusers($page, $limit, $sidx, $sord, $total_pages, $count);

    if (is_array($users)){
        if ($datatype == "json"){
            header("Content-Type: application/json; charset=utf-8");
            echo '{'.PHP_EOL;
            echo '"total":"'.$total_pages.'",'.PHP_EOL;
            echo '"page":"'.$page.'",'.PHP_EOL;
            echo '"records":"'.$count.'",'.PHP_EOL;
            echo '"rows":['.PHP_EOL;
            $i = 0;
            $rowNum = min($count, $limit);
            foreach($users as $user)
            {
                echo '{';
                echo '"id":"'.$user['id'].'",';
                echo '"cell":["'.$user['id'].
                '","'.$user['情報'].
                '","'.$user['テキスト'].
                '","'.$user['コメント'].
                '"]';
                echo '}';
                if ($i < $rowNum - 1){
                    echo ',';
                }
                echo PHP_EOL;
                $i++;
            }
            echo ']'.PHP_EOL;
            echo '}'.PHP_EOL;
        }
    }

     /*
     * db_grid_getusers: データを取得して、データ並べ替え、ページ数算出
     */
    function db_grid_getusers(&$page, $limit, $sidx, $sord, &$total_pages, &$count)
    {

        $dsn = 'mysql:dbname=jqGrid;host=192.168.11.105';
        $user = 'jqGrid';
        $password = 'jqGrid';

        // データベースへ接続
        $dbh = new PDO($dsn, $user, $password);

            // table_php テーブルのデータ数を算出
        $result = $dbh->query("SELECT COUNT(*) as count FROM table_php")->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $row){
            $count = $row['count'];
        }

        // グリッドの表示行からページ数を算出
        if ($count > 0){
            $total_pages = ceil($count / $limit);
        }
        else{
            $total_pages = 0;
        }
        
        // 指定のページがページ数より大きければページ数を指定ページに設定
        if ($page > $total_pages){
            $page = $total_pages;
        }

        // 指定ページ数の開始行を算出
        // 表示行数($limit)が10行で、指定ページが2ページの場合、
        // 開始行は 10 x 2 - 10 = 10行目
        $start = $limit * $page - $limit;

        if ($start < 0){
            $start = 0;
        }

        $SQL = "SELECT id, 情報, テキスト, コメント FROM table_php ORDER BY ".$sidx." ".$sord." LIMIT ".$start." , ".$limit.";";
        //$SQL = "SELECT * FROM table_php ORDER BY id $sord LIMIT $start , 10"; 
        $result2 = $dbh->query($SQL)->fetchAll(PDO::FETCH_ASSOC);

        if(is_bool($result2)){
            $return = mysql_error();
        } else {        
            $rows = array();

            //while ($row = mysql_fetch_assoc($result)){
            foreach($result2 as $row){
                array_push($rows, $row);
            }

            $return = $rows;
        }

        return $return;
    }
?>