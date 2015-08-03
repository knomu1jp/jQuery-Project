<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <title>MySQLへの接続テスト</title>
    </head>

    <body>

<?php
$dsn = 'mysql:dbname=jqGrid;host=192.168.11.105';
$user = 'jqGrid';
$password = 'jqGrid';

try{
    $dbh = new PDO($dsn, $user, $password);

    $sql = 'select * from table_php';
    foreach ($dbh->query($sql) as $row) {
        print($row['id'].',');
        print($row['情報']);
        print($row['テキスト']);
        print($row['コメント']);
        print('<br />');
    }
}
catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$dbh = null;
?>

    </body>
</html>