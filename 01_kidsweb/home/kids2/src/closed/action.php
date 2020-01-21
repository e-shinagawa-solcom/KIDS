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
		
		// 締めた売上に紐づく受注マスタの行レベルロック
		$strQuery = "select m_receive.lngreceiveno, m_receive.lngrevisionno "
			. "from m_sales "
//			. "inner join ( "
//			. "select  "
//			. "    strsalescode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_sales "
//			. "group by m_sales.strsalescode "
//			. ") A "
//			. "on A.strsalescode = m_sales.strsalescode "
//			. "and A.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_salesdetail "
			. "on t_salesdetail.lngsalesno = m_sales.lngsalesno "
			. "and t_salesdetail.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_receivedetail "
			. "on t_receivedetail.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and t_receivedetail.lngreceivedetailno = t_salesdetail.lngreceivedetailno "
			. "and t_receivedetail.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "inner join m_receive "
			. "on m_receive.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and m_receive.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "where m_sales.lngSalesStatusCode = " . DEF_SALES_END
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom ."' "
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "売上分の受注データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngReceiveCount = $lngResultNum;
			for ( $i = 0; $i < $lngReceiveCount; $i++ )
			{
				$aryReceiveResult[] = $objDB->fetchArray( $lngResultID, $i );
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
		// ロックした受注マスタのステータスを更新
		for ( $i = 0; $i < $lngReceiveCount; $i++ )
		{
			$strQuery = "UPDATE m_receive SET lngreceivestatuscode = " . DEF_RECEIVE_CLOSED 
			. "WHERE lngreceiveno = " . $aryReceiveResult[$i]["lngreceiveno"] . " AND lngrevisionno = " . $aryReceiveResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "売上分の受注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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

		// 締めた売上に紐づく発注マスタの行レベルロック
		$strQuery = "select "
			. "m_order.lngorderno, m_order.lngrevisionno "
			. "from m_stock "
//			. "inner join ( "
//			. "select  "
//			. "    strstockcode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_stock "
//			. "group by m_stock.strstockcode "
//			. ") A "
//			. "on A.strstockcode = m_stock.strstockcode "
//			. "and A.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_stockdetail "
			. "on t_stockdetail.lngstockno = m_stock.lngstockno "
			. "and t_stockdetail.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_orderdetail "
			. "on t_orderdetail.lngorderno = t_stockdetail.lngorderno "
			. "and t_orderdetail.lngorderdetailno = t_stockdetail.lngorderdetailno "
			. "and t_orderdetail.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "inner join m_order "
			. "on m_order.lngorderno = t_stockdetail.lngorderno "
			. "and m_order.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "where m_stock.lngStockStatusCode = " . DEF_STOCK_END
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom . "'"
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngOrderCount = $lngResultNum;
			for ( $i = 0; $i < $lngOrderCount; $i++ )
			{
				$aryOrderResult[] = $objDB->fetchArray( $lngResultID, $i );
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

		// ロックした発注マスタのステータスを更新
		for ( $i = 0; $i < $lngOrderCount; $i++ )
		{
			$strQuery = "UPDATE m_order SET lngorderstatuscode = " . DEF_ORDER_CLOSED 
			. "WHERE lngorderno = " . $aryOrderResult[$i]["lngorderno"] . " AND lngrevisionno = " . $aryOrderResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データの締め処理状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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

		// 締めた売上に紐づく受注マスタの行レベルロック
		$strQuery = "select m_receive.lngreceiveno, m_receive.lngrevisionno "
			. "from m_sales "
//			. "inner join ( "
//			. "select  "
//			. "    strsalescode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_sales "
//			. "group by m_sales.strsalescode "
//			. ") A "
//			. "on A.strsalescode = m_sales.strsalescode "
//			. "and A.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_salesdetail "
			. "on t_salesdetail.lngsalesno = m_sales.lngsalesno "
			. "and t_salesdetail.lngrevisionno = m_sales.lngrevisionno "
			. "inner join t_receivedetail "
			. "on t_receivedetail.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and t_receivedetail.lngreceivedetailno = t_salesdetail.lngreceivedetailno "
			. "and t_receivedetail.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "inner join m_receive "
			. "on m_receive.lngreceiveno = t_salesdetail.lngreceiveno "
			. "and m_receive.lngrevisionno = t_salesdetail.lngreceiverevisionno "
			. "where m_sales.lngSalesStatusCode = " . DEF_SALES_CLOSED
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom ."' "
			. " AND to_char( date_trunc( 'month', m_sales.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "売上分の受注データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngReceiveCount = $lngResultNum;
			for ( $i = 0; $i < $lngReceiveCount; $i++ )
			{
				$aryReceiveResult[] = $objDB->fetchArray( $lngResultID, $i );
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

		// ロックした受注マスタのステータスを更新
		for ( $i = 0; $i < $lngReceiveCount; $i++ )
		{
			$strQuery = "UPDATE m_receive SET lngreceivestatuscode = " . DEF_RECEIVE_END 
			. "WHERE lngreceiveno = " . $aryReceiveResult[$i]["lngreceiveno"] . " AND lngrevisionno = " . $aryReceiveResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "売上分の受注データの締め処理戻し状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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

		// 締めた売上に紐づく発注マスタの行レベルロック
		$strQuery = "select "
			. "m_order.lngorderno, m_order.lngrevisionno "
			. "from m_stock "
//			. "inner join ( "
//			. "select  "
//			. "    strstockcode "
//			. "   ,MAX(lngrevisionno) as lngrevisionno "
//			. "from m_stock "
//			. "group by m_stock.strstockcode "
//			. ") A "
//			. "on A.strstockcode = m_stock.strstockcode "
//			. "and A.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_stockdetail "
			. "on t_stockdetail.lngstockno = m_stock.lngstockno "
			. "and t_stockdetail.lngrevisionno = m_stock.lngrevisionno "
			. "inner join t_orderdetail "
			. "on t_orderdetail.lngorderno = t_stockdetail.lngorderno "
			. "and t_orderdetail.lngorderdetailno = t_stockdetail.lngorderdetailno "
			. "and t_orderdetail.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "inner join m_order "
			. "on m_order.lngorderno = t_stockdetail.lngorderno "
			. "and m_order.lngrevisionno = t_stockdetail.lngorderrevisionno "
			. "where m_stock.lngStockStatusCode = " . DEF_STOCK_CLOSED
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) >= '" . $dtmUpdateFrom . "'"
			. " AND to_char( date_trunc( 'month', m_stock.dtmAppropriationDate ), 'YYYY/MM' ) <= '" . $dtmUpdateTo . "'"
			. " FOR UPDATE";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データのロック処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		if ( $lngResultNum )
		{
			$lngOrderCount = $lngResultNum;
			for ( $i = 0; $i < $lngOrderCount; $i++ )
			{
				$aryOrderResult[] = $objDB->fetchArray( $lngResultID, $i );
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

		// ロックした発注マスタのステータスを更新
		for ( $i = 0; $i < $lngOrderCount; $i++ )
		{
			$strQuery = "UPDATE m_order SET lngorderstatuscode = " . DEF_ORDER_END 
			. "WHERE lngorderno = " . $aryOrderResult[$i]["lngorderno"] . " AND lngrevisionno = " . $aryOrderResult[$i]["lngrevisionno"];
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 9061, DEF_ERROR, "仕入分の発注データの締め処理戻し状態への更新処理に失敗しました。", TRUE, "../closed/closed.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			$objDB->freeResult( $lngResultID );
		}

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