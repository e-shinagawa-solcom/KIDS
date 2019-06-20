<?
	// ---------------------------------------------------------------
	// �ץ����̾: getMasterData
	//
	// ����:   �ޥ������ǡ��������
	//
	// ����:
	//         $strFormValue1 : �ե��������1
	//         $strFormValue2 : �ե��������2
	//			�������ե������ͤ�¸�ߤ���������Ƽ���
	//
	//         $lngProcessID  : �¹Ԥ���SQL��ID
	//
	// �����:
	//         $strMasterData : �ޥ������ǡ���(***,***\n)
	// ---------------------------------------------------------------

	// �����ɤ߹���
	include ( "conf.inc" );
	require (LIB_FILE);
	
	//PHPɸ���JSON�Ѵ��᥽�åɤϥ��顼�ˤʤ�Τǳ����Υ饤�֥��(���餯���󥳡��ɤ�����)
	require_once 'JSON.php';

	// GET����μ���
	$lngProcessID  = $_GET["lngProcessID"];  // �¹Ԥ���SQL��ID
	$strFormValue  = $_GET["strFormValue"];   // �ե������

	//JSON���饹���󥹥��󥹲�
	$s = new Services_JSON();

	//////////////////////////////////////////////////////////////
	// �ᥤ��롼����
	//////////////////////////////////////////////////////////////
	for ( $i = 0; $i < count ( $strFormValue ); $i++ )
	{
		$strFormValue[$i] = mb_convert_encoding( $strFormValue[$i], "EUC-JP", "SJIS,EUC" );
	}

	//{
	//	echo ",����̵��";
	//	exit;
	//}
	// �����ե������ꥯ�������������
	if ( !$strQuery = file_get_contents ( LIB_ROOT . "sql/$lngProcessID.sql" ) )
	{
		echo ",�ե����륪���ץ�˼��Ԥ��ޤ�����";
		exit;
	}
	// ������(//������)�κ��
	$strQuery = preg_replace ( "/\/\/.+?\n/", "", $strQuery );
	// 2�ĤΥ��ڡ��������ԡ����֤򥹥ڡ���1�Ĥ��Ѵ�
	$strQuery = preg_replace ( "/(\s{2}|\n|\t)/", " ", $strQuery );
	// ������(/**/������)�κ��
	$strQuery = preg_replace ( "/\/\*.+?\*\//m", "", $strQuery );

	// �����ѿ����֤�����
	for ( $i = 0; $i < count ( $strFormValue ); $i++ )
	{
		$strQuery = preg_replace ( "/_%strFormValue$i%_/", $strFormValue[$i], $strQuery );
	}

	// �֤��������ʤ��ä��ѿ��� WHERE �硢����
	//$strQuery = preg_replace ( "/(AND|WHERE) [\w\._\(\)]+? (=||LIKE) [\w\(\)]*('%??%??'| )\)?/", "", $strQuery );
	$strQuery = preg_replace ( "/(AND|WHERE) [\w\._]+? ([<>]?=||LIKE) ('%??%??'| )/", "", $strQuery );

	// \ �ν���(DB �䤤��碌�Τ��� \ �� \\ �ˤ���)
	$strQuery = preg_replace ( "/\\\\/", "\\\\\\\\", $strQuery );

	// �ǡ�������
	$masterData = fncGetMasterData( $strQuery );
	mb_convert_variables('UTF-8' , 'EUC-JP' , $masterData );
	echo $s->encodeUnsafe($masterData);



// ---------------------------------------------------------------
// �ؿ�̾: fncGetMasterData
//
// ����:   �ޥ������ǡ��������������¹ԡ��ǡ���������CSV����������
//
// ����:
//         $strQuery      : ������
//
// �����:
//         $strMasterData : �ޥ������ǡ���(***,***\n)
// ---------------------------------------------------------------
function fncGetMasterData( $strQuery )
{
	// �ե������̾����
	$strMasterHeader = "id";

	// DB��³
	$objDB = new clsDB();
	if ( !$objDB->open( "", "", "", "" ) ) {
	    echo ",DB��³�˼��Ԥ��ޤ�����\n";
	    exit;
	}

	// �ޥ�������������¹�
	if ( !$result = $objDB->execute( $strQuery ) )
	{
		echo "id\tname1\n�ޥ������ǡ����η�̼����˼��Ԥ��ޤ�����\n";
	    exit;
	}
	
	$result = pg_fetch_all($result);

	//��¸�ν����ؤν����̤��θ���ơ����������ǡ����Υ������¸�ν����˹�碌��
	//���ե�����ɤ�ID������ʹߤ�NAME
	for($i=0;$i<count($result);$i++){
		$key = array_keys($result[$i]);
		for($i2=0;$i2<count($key);$i2++){
			if($i2==0){
				$result[$i]["id"] = $result[$i][$key[$i2]];
			} else {
				$result[$i]["name".$i2] = $result[$i][$key[$i2]];
			}
		}
	}

	return $result;

}
?>
