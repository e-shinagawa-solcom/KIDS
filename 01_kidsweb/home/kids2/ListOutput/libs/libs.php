<?

// ���ס������꡼ʸ����ǡ������������
// ������
//		$objDB	DB���֥�������
//		$strQuery	SQL�����꡼ʸ
// ����͡�
//		True:���� �� True�ʳ������ԡ����顼��å������ޤ�
//
function fncGetQyeryData($objDB, $strQuery, &$aryOutData)
{
	
	if ( !$lngResultID = $objDB->Execute( $strQuery ) )
	{
		return "error Execute!";
	}
	$lngResultNum = pg_num_rows ( $lngResultID );
	if ( !$lngResultNum )
	{
		return "not result";
	}
	
	
	while( $objDB->SafeFetch($lngResultID, $aryResult, $lngLine) )
	{
		$lngLine++;
		$aryOutData[] = $aryResult;
	}
	
	return true;
}


?>
