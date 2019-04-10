<?

// ----------------------------------------------------------------------------
/**
*       データエクスポート 検索画面
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・メニュー画面を表示
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------


	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "dataex/cmn/lib_dataex.php");

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// POSTデータ取得
	$aryData = $_GET;


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


	// 権限確認のための出力対象の機能コードを取得
	$lngFunctionCode = getFunctionCode( $aryData["lngExportData"] );



	// 権限確認
	if ( !fncCheckAuthority( $lngFunctionCode, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
	$aryCheck["lngExportData"] = "null:number(DEF_EXPORT_SALES,DEF_EXPORT_ESTIMATE)";


	// L／C設定日(前日)
	$strDefaultSDate = date( "Y/m/d", strtotime( "-1 day" ) );
	$strDefaultEDate = $strDefaultSDate;

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	//echo getArrayTable( $aryCheckResult, "TABLE" );
	//echo getArrayTable( $aryData, "TABLE" );
	//exit;
	fncPutStringCheckError( $aryCheckResult, $objDB );


	// マスタデータの生成
	switch( (int)$aryData["lngExportData"] )
	{
		case 6:	// stat01
		case 7:	// stat02
			// 部門
			$aryData["lngGroupCode"]	= fncGetPulldown( "m_Group", "lngGroupCode", "strgroupdisplayname", 0,'where lngcompanycode in (0, 1)', $objDB );
			// 売上区分
			$aryData["lngSalesClassCode"] 	= fncGetCheckBoxObject( "m_SalesClass", "lngsalesclasscode", "strsalesclassname", "lngSalesClassCode[]", 'where lngsalesclasscode <> 0', $objDB );

			// クッキーの設定
			if( $_COOKIE["DataExport_stat01"] )
			{
				$aryCookie = fncStringToArray ( $_COOKIE["DataExport_stat01"], "&", ":" );
				while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
				{
					$aryData[$strKeys] = $strValues;
				}
			}
			break;
	}

	// From To 日付の生成
	$year = date("Y");
	$month = date("m");
	$day = date("d");
	switch( (int)$aryData["lngExportData"] )
	{
		case 6:	// stat01
			$day = 1;
			$varMktime = mktime( 0,0,0,$month, $day , $year );
			$strDefaultSDate = date( "Y/m/d", $varMktime );
			
			$month = $month + "3";
			$day = 0;
			$varMktime = mktime( 0,0,0,$month, $day , $year );
			$strDefaultEDate = date( "Y/m/d", $varMktime );
			break;
			
		case 7:	// stat02
			$month = $month - "1";
			$day = 1;
			$varMktime = mktime( 0,0,0,$month, $day , $year );
			$strDefaultSDate = date( "Y/m/d", $varMktime );
			
 			$month = $month + "1";
			$day = 0;
			$varMktime = mktime( 0,0,0,$month, $day , $year );
			$strDefaultEDate = date( "Y/m/d", $varMktime );
			break;
	}


	$aryData["lcdatestart"] = $strDefaultSDate;
	$aryData["lcdateend"]   = $strDefaultEDate;


	// HTML出力
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "dataex/search/" . $aryDirName[$aryData["lngExportData"]] . "/parts.tmpl" );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;

?>
