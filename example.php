<?php 
//MySQL�f�[�^�x�[�X�T�[�o�[�ւ̐ڑ��ɕK�v�ȏ����C���N���[�h����B
// ���ɂ́A���[�U�[���A�f�[�^�x�[�X�A�p�X���[�h���i�[�B
include("dbconfig.php");

// url�p�����[�^�[�ɑ΂��AcolModel�ŉ������Ă���4�̃p�����[�^�[��ǉ��B
// �K�v�ȃN�G�����\�����邽�߂ɁA�����̃p�����[�^�[���擾����B
// Since we specify in the options of the grid that we will use a GET method 
// GET���\�b�h���g�p����|���A�O���b�h�̃I�v�V�����Ŏw�肵�Ă��߁A�p�����[�^�[���擾����K�؂ȃR�}���h���g�p����ׂ��ł���B
// ���̃P�[�X�ł́A $_GET�Ƃ��Ă���B$_POST���g�p����B�ŗǂ̕��@�́A
// GET�ϐ������POST�ϐ�������$_REQUEST���g�p���邱�Ƃ�������Ȃ��B
// �ڍׂ́APHP �Ɋւ��镶�����Q�Ƃ̂��ƁB
// ���N�G�X�g�����y�[�W���擾�B�f�t�H���g�ł́A�O���b�h�́A�����1�ɐݒ肵�Ă���B
$page = $_GET['page']; 

// �O���b�h�ɂ����s��ݒ肷�邩�擾 -- �O���b�h�ɁArowNum �p�����[�^�[
$limit = $_GET['rows']; 

// �C���f�b�N�X�s���擾�B��: ���[�U�[���N���b�N���ă\�[�g�B�ŏ��́Asortname�p�����[�^�[
// ���̌�colModel����C���f�b�N�X���擾�B 
$sidx = $_GET['sidx']; 

// �\�[�g�̏��� �|�| �ŏ��́Asortorder
$sord = $_GET['sord']; 

// �͂��߂ɓn���Ȃ��ꍇ�́A�C���f�b�N�X�́A�ŏ��̗���g�p���邩�w�肵���C���f�b�N�X���g�p����B
if(!$sidx) $sidx =1; 

// MySQL�f�[�^�x�[�X�T�[�o�[�ɐڑ�
$db = mysql_connect($dbhost, $dbuser, $dbpassword) or die("Connection Error: " . mysql_error()); 

// �f�[�^�x�[�X��I��
mysql_select_db($database) or die("Error connecting to db."); 

// �N�G���̍s�����v�Z�B���ʂ��y�[�W���邽�߂ɕK�v�B
$result = mysql_query("SELECT COUNT(*) AS count FROM invheader"); 
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count']; 

// �N�G���̑��y�[�W�����v�Z
if( $count > 0 ) { 
    $total_pages = ceil($count/$limit); 
} else { 
    $total_pages = 0; 
} 

// ���炩�̗��R�ŁA�v�������y�[�W�����������傫���ꍇ�A
// �v�������y�[�W���𑍃y�[�W���ɐݒ肷��B
if ($page > $total_pages) $page=$total_pages;

// �s�̊J�n�ʒu���v�Z
$start = $limit*$page - $limit;

// ���炩�̗��R�ŁA�J�n�ʒu�����̐����̏ꍇ�́A0�ɐݒ�B
// �悭����P�[�X�ł́A���[�U�[���v���y�[�W���O�ƃ^�C�v�B
if($start <0) $start = 0; 

// �O���b�h�f�[�^�̎��ۂ̃N�G��
$SQL = "SELECT invid, invdate, amount, tax,total, note FROM invheader ORDER BY $sidx $sord LIMIT $start , $limit"; 
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 

// �K�؂ȃw�b�_�[����ݒ肷��ׂ�
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

// CDATA�Ƀe�L�X�g�f�[�^��K���u���B
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