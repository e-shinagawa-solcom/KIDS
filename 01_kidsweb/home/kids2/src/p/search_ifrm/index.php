<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  検索項目画面 ( Inline Frame )
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
*         ・検索項目画面表示処理 ( Inline Frame )
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 設定の読み込み
	include_once ( "conf.inc" );

	// ライブラリ読み込み
	require ( LIB_FILE );
	require(SRC_ROOT."po/cmn/lib_po.php");
	require( "libsql.php" );

	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
	//////////////////////////////////////////////////////////////////////////
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 304 商品管理（詳細表示）
	if ( fncCheckAuthority( DEF_FUNCTION_P4, $objAuth ) )
	{
		$aryData["btnDetail_visibility"] = "visible";
		$aryData["btnDetailVisible"] = "checked";
	}
	else
	{
		$aryData["btnDetail_visibility"] = "hidden";
		$aryData["btnDetailVisible"] = "";
	}
	// 306 商品管理（修正）
	if ( fncCheckAuthority( DEF_FUNCTION_P6, $objAuth ) )
	{
		$aryData["btnFix_visibility"] = "visible";
		$aryData["btnFixVisible"] = "checked";
	}
	else
	{
		$aryData["btnFix_visibility"] = "hidden";
		$aryData["btnFixVisible"] = "";
	}
	// 306 商品管理（削除）
	if ( fncCheckAuthority( DEF_FUNCTION_P7, $objAuth ) )
	{
		$aryData["btnDelete_visibility"] = "visible";
		$aryData["btnDeleteVisible"] = "checked";
	}
	else
	{
		$aryData["btnDelete_visibility"] = "hidden";
		$aryData["btnDeleteVisible"] = "";
	}

	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// プルダウンメニュー
	// 部門
	$aryData["lngInChargeGroupCodeSelect"]	= fncGetPulldown( "m_group", "lnggroupcode", "strgroupdisplaycode || ' ' || strgroupdisplayname as strgroupdisplayname", 0,'WHERE bytgroupdisplayflag = true and lngcompanycode in (0,1)', $objDB );
	// カテゴリー
	$lngUserCode = $objAuth->UserCode;
	$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory2(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB, 2);
	// 企画進行状況
	$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", 0,'', $objDB );
	// 証紙
	$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", 0, '', $objDB );
	// 版権元
	$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", 0, '', $objDB );

	// ワークフロー状態
	$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );

	//　プルダウンリストの取得に失敗した場合エラー表示
	if ( !$aryData["lngGoodsPlanProgressCode"] or !$aryData["lngCertificateClassCode"] or !$aryData["lngCopyrightCode"] )
	{
		fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
	}

	// クッキーの設定
	if( $_COOKIE["ProductSearch"] )
	{
		$aryCookie = fncStringToArray ( $_COOKIE["ProductSearch"], "&", ":" );
		while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
		{
			$aryData[$strKeys] = $strValues;
		}
	}

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/search_ifrm/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

?>
