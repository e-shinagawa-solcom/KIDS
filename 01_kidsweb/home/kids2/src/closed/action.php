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
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
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

		// 更新行の行レベルロック
		$strQuery = "SELECT lngSalesNo, strSalesCode FROM m_Sales WHERE lngSalesStatusCode = " . DEF_SALES_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

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

		// 更新行のUPDATE
		$strQuery = "UPDATE m_Sales SET lngSalesStatusCode = " . DEF_SALES_CLOSED . " WHERE lngSalesStatusCode = " . DEF_SALES_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "売上データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// コミット処理
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < $lngSalesCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "売上管理";
			$aryDetailData["strCode"] = $arySalesResult[$i]["strsalescode"];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
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

		// 更新行の行レベルロック
		$strQuery = "SELECT lngStockNo, strStockCode FROM m_Stock WHERE lngStockStatusCode = " . DEF_STOCK_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

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

		// 更新行のUPDATE
		$strQuery = "UPDATE m_Stock SET lngStockStatusCode = " . DEF_STOCK_CLOSED . " WHERE lngStockStatusCode = " . DEF_STOCK_END
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "仕入データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// コミット処理
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < $lngStockCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "仕入管理";
			$aryDetailData["strCode"] = $aryStockResult[$i]["strstockcode"];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
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

		// 更新行の行レベルロック
		$strQuery = "SELECT lngSalesNo, strSalesCode FROM m_Sales WHERE lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

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

		// 更新行のUPDATE
		$strQuery = "UPDATE m_Sales SET lngSalesStatusCode = " . DEF_SALES_END . " WHERE lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "売上データの締め処理戻し状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// コミット処理
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < $lngSalesCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "売上管理";
			$aryDetailData["strCode"] = $arySalesResult[$i]["strsalescode"];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
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

		// 更新行の行レベルロック
		$strQuery = "SELECT lngStockNo, strStockCode FROM m_Stock WHERE lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

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

		// 更新行のUPDATE
		$strQuery = "UPDATE m_Stock SET lngStockStatusCode = " . DEF_STOCK_END . " WHERE lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND bytInvalidFlag = FALSE " 
			. " AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom  
			. "' AND to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "仕入データの締め処理戻し状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		$objDB->freeResult( $lngResultID );

		// コミット処理
		$objDB->transactionCommit();

		unset ( $aryDetail );
		// 置換用文字列の設定
		for( $i = 0; $i < $lngStockCount; $i++ )
		{
			$aryDetailData["strFuncType"] = "仕入管理";
			$aryDetailData["strCode"] = $aryStockResult[$i]["strstockcode"];

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "closed/parts_detail.tmpl" );

			// テンプレート生成
			$objTemplate->replace( $aryDetailData );
			$objTemplate->complete();
			
			// HTML出力
			$aryDetail[] = $objTemplate->strTemplate;
		}

		if ( count($aryDetail) )
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