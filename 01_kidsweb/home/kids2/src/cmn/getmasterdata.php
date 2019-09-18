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

	// GET����μ���
	$lngProcessID  = $_GET["lngProcessID"];  // �¹Ԥ���SQL��ID
	$strFormValue  = $_GET["strFormValue"];   // �ե������


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

	$fp = fopen ( SQLLOG_ROOT . "$lngProcessID.txt", "w" );
	fwrite ( $fp, $strQuery . "\n" );
	
	// �ǡ���ʸ�������(***,***\n)
	if ( $strMasterData = fncGetMasterData( $strQuery ) )
	{
		// �ޥ������ǡ�������
		echo $strMasterData;
	}

	fwrite ( $fp, $strQuery . "\n" . $strMasterData );
	fclose ( $fp );


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

	if ( $lngResultRows = pg_Num_Rows( $result ) )
	{

		$aryFieldNum = $objDB->getFieldsCount( $result );

		// ���������(,name1,name2������)
		for ( $i = 1; $i < $aryFieldNum; $i++ )
		{
			$strMasterHeader .= "\tname$i";
		}

		// ��̤�����
		for ( $i = 0; $i < $lngResultRows; $i++ )
		{
			$array = $objDB->fetchArray ( $result, $i );

			// �裱�ե�����ɤ�ID�Ȥ���
			$strMasterData .= "$array[0]";

			// �裲�ե�����ɰʹߤ�NAME�Ȥ���
			for ( $j = 1; $j < $aryFieldNum; $j++ )
			{
				$array[$j] = preg_replace ( "/\s+?$/", "", $array[$j] );// ������
				$strMasterData   .= "\t$array[$j]";
			}

			$strMasterData .= "\n";
		}
	}
	else
	{
		$strMasterData .= "";
	}
	return $strMasterHeader . "\n" . $strMasterData;

}
?>
