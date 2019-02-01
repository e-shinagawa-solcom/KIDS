<?

// 概要：クエリー文からデータを取得する
// 引数：
//		$objDB	DBオブジェクト
//		$strQuery	SQLクエリー文
// 戻り値：
//		True:成功 ／ True以外：失敗、エラーメッセージ含む
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
