<?php
// ----------------------------------------------------------------------------
/**
*       締め処理　実行処理
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
*         ・締め処理、および締め処理の戻し処理の実行
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "closed/cmn/lib_closed.php");
require (LIB_DEBUGFILE);

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}

// 文字列チェック
if ( $aryData["lngActionCode"] == "" or $aryData["lngActionCode"] < 2 or $aryData["lngActionCode"] > 3 )
{
	echo "情報の取得に失敗しました。<BR>";
	return true;
}
if ( fncCheckString( $aryData["dtmUpdateFrom"], "null:date" ) != FALSE )
{
	echo "開始計上日が指定されていません。<BR>";
	return true;
}
if ( fncCheckString( $aryData["dtmUpdateTo"], "null:date" ) != FALSE )
{
	echo "終了計上日が指定されていません。<BR>";
	return true;
}

if(  !is_array($aryData["lngTargetData"]) )
{
	echo "対象が指定されていません。<BR>";
	return true;
}

$aryTargetFlag = array();
while( list($strKey, $strValue) = each($aryData["lngTargetData"]) )
{
	switch($strValue)
	{
		case DEF_FUNCTION_SC0:
			$aryTargetFlag[DEF_FUNCTION_SC0] = true;
		break;
		case DEF_FUNCTION_PC0:
			$aryTargetFlag[DEF_FUNCTION_PC0] = true;
		break;
	}
}

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 1400 締め処理
if ( !fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 日付の確認
if ( $aryData["dtmUpdateFrom"] > $aryData["dtmUpdateTo"] )
{
	$aryData["dtmUpdateTo"] = $aryData["dtmUpdateFrom"];
}
else
{
	$dtmUpdateFrom = $aryData["dtmUpdateFrom"];
	$dtmUpdateTo   = $aryData["dtmUpdateTo"];
}

$aryData["strMessageDetail"] = "";

////////////////////////////////////////////////////////////////////
/////////////////////////////締め処理///////////////////////////////
////////////////////////////////////////////////////////////////////
if ( $aryData["lngActionCode"] == DEF_CLOSED_RUN )
{
	$lngReceiveCount = 0;
	$lngOrderCount   = 0;
	$lngSalesCount   = 0;
	$lngStockCount   = 0;
		
	////////////////////////////////////
	//////////////売上処理//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_SC0] )
	{
		// トランザクション開始
		$objDB->transactionBegin();

		// 更新行の行レベルロック（売上、受注を同時にロック）
		$strQuery = "SELECT ms.lngSalesNo, ms.lngrevisionno as lngsalesrevisionno, ms.strSalesCode, mr.lngReceiveNo, mr.lngrevisionno as lngreceiverevisionno " 
			. " FROM m_Sales ms"
			. " INNER JOIN  t_salesdetail tsd on tsd.lngSalesNo = ms.lngSalesNo and tsd.lngrevisionno = ms.lngrevisionno"
			. " INNER JOIN  t_receivedetail trd on trd.lngReceiveNo = tsd.lngReceiveNo and trd.lngrevisionno = tsd.lngreceiverevisionno"
			. " INNER JOIN  m_receive mr on mr.lngReceiveNo = trd.lngReceiveNo and mr.lngrevisionno = trd.lngrevisionno"
			. " WHERE ms.lngSalesStatusCode = " . DEF_SALES_END
			. " AND mr.lngReceiveStatusCode = " . DEF_RECEIVE_END 
			. " AND ms.bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. "ORDER BY ms.lngSalesNo, ms.lngrevisionno DESC FOR UPDATE";
fncDebug("close.log", $strQuery, __FILE__, __LINE__, "a");
		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "売上データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngSalesCount = $lngResultNum;
			for ( $i = 0; $i < $lngSalesCount; $i++ )
			{
				$arySalesResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );
		
        $lastSalesNo = 0;
        $lastRevisionNo = 0;
        $salesList = array();
		// ロックした売上マスタ、受注マスタのステータスを更新
		for ( $i = 0; $i < $lngSalesCount; $i++ )
		{
		    if( $arySalesResult[$i]["lngsalesno"] != $lastSalesNo )
		    {
				// 売上マスタ
				$strQuery = "UPDATE m_sales SET lngsalesstatuscode = " . DEF_SALES_CLOSED 
				. " WHERE lngsalesno = " . $arySalesResult[$i]["lngsalesno"] . " AND lngrevisionno = " . $arySalesResult[$i]["lngsalesrevisionno"];
				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					fncOutputError ( 9061, DEF_ERROR, "売上分の受注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}
				$objDB->freeResult( $lngResultID );
				$lastSalesNo = $arySalesResult[$i]["lngsalesno"];
				$lastRevisionNo = $arySalesResult[$i]["lngsalesrevisionno"];
				$salesList[] = $arySalesResult[$i]["strsalescode"];
			}
			// 受注マスタ
			$strQuery = "UPDATE m_receive SET lngreceivestatuscode = " . DEF_RECEIVE_CLOSED 
			. " WHERE lngreceiveno = " . $arySalesResult[$i]["lngreceiveno"] . " AND lngrevisionno = " . $arySalesResult[$i]["lngreceiverevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "売上分の受注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}
		// コミット処理
//		$objDB->transactionRollback();
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < count($salesList); $i++ )
		{
			$aryDetailData["strFuncType"] = "売上管理";
			$aryDetailData["strCode"] = $salesList[$i];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( $lngSalesCount > 0 )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////仕入処理//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_PC0] )
	{
		// トランザクション開始
		$objDB->transactionBegin();

		// 更新行の行レベルロック（仕入、発注を同時にロック）
		$strQuery = "SELECT ms.lngstockNo, ms.lngrevisionno as lngstockrevisionno, ms.strstockCode, mo.lngorderNo, mo.lngrevisionno as lngorderrevisionno " 
			. " FROM m_stock ms"
			. " INNER JOIN  t_stockdetail tsd on tsd.lngstockNo = ms.lngstockNo and tsd.lngrevisionno = ms.lngrevisionno"
			. " INNER JOIN  t_orderdetail tod on tod.lngorderNo = tsd.lngorderNo and tod.lngrevisionno = tsd.lngorderrevisionno"
			. " INNER JOIN  m_order mo on mo.lngorderNo = tod.lngorderNo and mo.lngrevisionno = tod.lngrevisionno"
			. " WHERE ms.lngstockStatusCode = " . DEF_STOCK_END
			. " AND mo.lngorderStatusCode = " . DEF_ORDER_END 
			. " AND ms.bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. "ORDER BY ms.lngstockNo, ms.lngrevisionno DESC FOR UPDATE";

fncDebug("close.log", $strQuery, __FILE__, __LINE__, "a");
		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "仕入データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngStockCount = $lngResultNum;
			for ( $i = 0; $i < $lngStockCount; $i++ )
			{
				$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

        $lastStockNo = 0;
        $lastRevisionNo = 0;
        $stockList = array();
		// ロックした発注マスタのステータスを更新
		for ( $i = 0; $i < $lngStockCount; $i++ )
		{
			// 仕入マスタのUPDATE
		    if( $lastStockNo != $aryStockResult[$i]["lngstockno"])
		    {
				$strQuery = "UPDATE m_stock SET lngstockstatuscode = " . DEF_STOCK_CLOSED 
				. "WHERE lngstockno = " . $aryStockResult[$i]["lngstockno"] . " AND lngrevisionno = " . $aryStockResult[$i]["lngstockrevisionno"];

				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}
				$objDB->freeResult( $lngResultID );

				$lastStockNo = $aryStockResult[$i]["lngstockno"];
				$lastRevisionNo = $aryStockResult[$i]["lngstockrevisionno"];
				$stockList[] = $aryStockResult[$i]["strstockcode"];
			}

			// 発注マスタのUPDATE
			$strQuery = "UPDATE m_order SET lngorderstatuscode = " . DEF_ORDER_CLOSED 
			. "WHERE lngorderno = " . $aryStockResult[$i]["lngorderno"] . " AND lngrevisionno = " . $aryStockResult[$i]["lngorderrevisionno"];

			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );

		}

		// コミット処理
//		$objDB->transactionRollback();
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < count($stockList); $i++ )
		{
			$aryDetailData["strFuncType"] = "仕入管理";
			$aryDetailData["strCode"] = $stockList[$i];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( $lngStockCount > 0 )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////結果処理//////////////
	////////////////////////////////////

	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	$aryData["strProcessMessage"] = "下記の「納品済」のデータに対して締め処理を行いました。";
	$aryData["strAction"] = "/closed/closed.php?strSessionID=";

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "closed/finish.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}
////////////////////////////////////////////////////////////////////
///////////////////////////締め戻し処理/////////////////////////////
////////////////////////////////////////////////////////////////////
else if ( $aryData["lngActionCode"] == DEF_CLOSED_RETURN )
{
	$lngReceiveCount = 0;
	$lngOrderCount   = 0;
	$lngSalesCount   = 0;
	$lngStockCount   = 0;
		
	////////////////////////////////////
	//////////////売上処理//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_SC0] )
	{
		// トランザクション開始
		$objDB->transactionBegin();

		// 更新行の行レベルロック（売上、受注を同時にロック）
		$strQuery = "SELECT ms.lngSalesNo, ms.lngrevisionno as lngsalesrevisionno, ms.strSalesCode, mr.lngReceiveNo, mr.lngrevisionno as lngreceiverevisionno " 
			. " FROM m_Sales ms"
			. " INNER JOIN  t_salesdetail tsd on tsd.lngSalesNo = ms.lngSalesNo and tsd.lngrevisionno = ms.lngrevisionno"
			. " INNER JOIN  t_receivedetail trd on trd.lngReceiveNo = tsd.lngReceiveNo and trd.lngrevisionno = tsd.lngreceiverevisionno"
			. " INNER JOIN  m_receive mr on mr.lngReceiveNo = trd.lngReceiveNo and mr.lngrevisionno = trd.lngrevisionno"
			. " WHERE ms.lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND mr.lngReceiveStatusCode = " . DEF_RECEIVE_CLOSED 
			. " AND ms.bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. "ORDER BY ms.lngSalesNo, ms.lngrevisionno DESC FOR UPDATE";
fncDebug("close.log", $strQuery, __FILE__, __LINE__, "a");
		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "売上データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngSalesCount = $lngResultNum;
			for ( $i = 0; $i < $lngSalesCount; $i++ )
			{
				$arySalesResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );
		
        $lastSalesNo = 0;
        $lastRevisionNo = 0;
        $salesList = array();
		// ロックした売上マスタ、受注マスタのステータスを更新
		for ( $i = 0; $i < $lngSalesCount; $i++ )
		{
		    if( $arySalesResult[$i]["lngsalesno"] != $lastSalesNo)
		    {
				// 売上マスタ
				$strQuery = "UPDATE m_sales SET lngsalesstatuscode = " . DEF_SALES_END 
				. " WHERE lngsalesno = " . $arySalesResult[$i]["lngsalesno"] . " AND lngrevisionno = " . $arySalesResult[$i]["lngsalesrevisionno"];
				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					fncOutputError ( 9061, DEF_ERROR, "売上分の受注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}
				$objDB->freeResult( $lngResultID );
				$lastSalesNo = $arySalesResult[$i]["lngsalesno"];
				$lastRevisionNo = $arySalesResult[$i]["lngsalesrevisionno"];
				$salesList[] = $arySalesResult[$i]["strsalescode"];
			}
			// 受注マスタ
			$strQuery = "UPDATE m_receive SET lngreceivestatuscode = " . DEF_RECEIVE_END 
			. " WHERE lngreceiveno = " . $arySalesResult[$i]["lngreceiveno"] . " AND lngrevisionno = " . $arySalesResult[$i]["lngreceiverevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "売上分の受注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}
		// コミット処理
//		$objDB->transactionRollback();
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < count($salesList); $i++ )
		{
			$aryDetailData["strFuncType"] = "売上管理";
			$aryDetailData["strCode"] = $salesList[$i];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( $lngSalesCount > 0 )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////仕入処理//////////////
	////////////////////////////////////
	if( $aryTargetFlag[DEF_FUNCTION_PC0] )
	{
		// トランザクション開始
		$objDB->transactionBegin();

		// 更新行の行レベルロック（仕入、発注を同時にロック）
		$strQuery = "SELECT ms.lngstockNo, ms.lngrevisionno as lngstockrevisionno, ms.strstockCode, mo.lngorderNo, mo.lngrevisionno as lngorderrevisionno " 
			. " FROM m_stock ms"
			. " INNER JOIN  t_stockdetail tsd on tsd.lngstockNo = ms.lngstockNo and tsd.lngrevisionno = ms.lngrevisionno"
			. " INNER JOIN  t_orderdetail tod on tod.lngorderNo = tsd.lngorderNo and tod.lngrevisionno = tsd.lngorderrevisionno"
			. " INNER JOIN  m_order mo on mo.lngorderNo = tod.lngorderNo and mo.lngrevisionno = tod.lngrevisionno"
			. " WHERE ms.lngstockStatusCode = " . DEF_STOCK_CLOSED
			. " AND mo.lngorderStatusCode = " . DEF_ORDER_CLOSED 
			. " AND ms.bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', ms.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. "ORDER BY ms.lngstockNo, ms.lngrevisionno DESC FOR UPDATE";

fncDebug("close.log", $strQuery, __FILE__, __LINE__, "a");
		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "仕入データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngStockCount = $lngResultNum;
			for ( $i = 0; $i < $lngStockCount; $i++ )
			{
				$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		$objDB->freeResult( $lngResultID );

        $lastStockNo = 0;
        $lastRevisionNo = 0;
        $stockList = array();
		// ロックした発注マスタのステータスを更新
		for ( $i = 0; $i < $lngStockCount; $i++ )
		{
			// 仕入マスタのUPDATE
		    if( $lastStockNo != $aryStockResult[$i]["lngstockno"] )
		    {
				$strQuery = "UPDATE m_stock SET lngstockstatuscode = " . DEF_STOCK_END 
				. "WHERE lngstockno = " . $aryStockResult[$i]["lngstockno"] . " AND lngrevisionno = " . $aryStockResult[$i]["lngstockrevisionno"];

				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}
				$objDB->freeResult( $lngResultID );

				$lastStockNo = $aryStockResult[$i]["lngstockno"];
				$lastRevisionNo = $aryStockResult[$i]["lngstockrevisionno"];
				$stockList[] = $aryStockResult[$i]["strstockcode"];
			}

			// 発注マスタのUPDATE
			$strQuery = "UPDATE m_order SET lngorderstatuscode = " . DEF_ORDER_END 
			. "WHERE lngorderno = " . $aryStockResult[$i]["lngorderno"] . " AND lngrevisionno = " . $aryStockResult[$i]["lngorderrevisionno"];

			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );

		}

		// コミット処理
//		$objDB->transactionRollback();
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < count($stockList); $i++ )
		{
			$aryDetailData["strFuncType"] = "仕入管理";
			$aryDetailData["strCode"] = $stockList[$i];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( $lngStockCount > 0 )
		{
			$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
		}
	}
	
	////////////////////////////////////
	//////////////結果処理//////////////
	////////////////////////////////////

	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	$aryData["strProcessMessage"] = "下記の「締め済」のデータに対して締め処理の戻し処理を行いました。";
	$aryData["strAction"] = "/closed/closed.php?strSessionID=";

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "closed/finish.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}
?>