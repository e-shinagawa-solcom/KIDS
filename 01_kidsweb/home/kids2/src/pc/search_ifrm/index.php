<?php

	// ----------------------------------------------------------------------------
	/**
	*       仕入管理  検索項目画面 ( Inline Frame )
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
	require(SRC_ROOT."pc/cmn/lib_pc.php");

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

	// 文字列チェック
	$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限チェック
	// 702 仕入管理（仕入検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 703 仕入管理（仕入検索　管理モード）
	if ( fncCheckAuthority( DEF_FUNCTION_PC3, $objAuth ) )
	{
		$aryData["AdminSet_visibility"] = "visible";
		// 707 仕入管理（無効化）
		if ( fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) )
		{
			$aryData["btnInvalid_visibility"] = "visible";
			$aryData["btnInvalidVisible"] = "disabled";
		}
		else
		{
			$aryData["btnInvalid_visibility"] = "hidden";
			$aryData["btnInvalidVisible"] = "disabled";
		}
	}
	else
	{
		$aryData["AdminSet_visibility"] = "hidden";
		$aryData["btnInvalid_visibility"] = "hidden";
		$aryData["btnInvalidVisible"] = "";
	}
	// 704 仕入管理（詳細表示）
	if ( fncCheckAuthority( DEF_FUNCTION_PC4, $objAuth ) )
	{
		$aryData["btnDetail_visibility"] = "visible";
		$aryData["btnDetailVisible"] = "checked";
	}
	else
	{
		$aryData["btnDetail_visibility"] = "hidden";
		$aryData["btnDetailVisible"] = "";
	}
	// 705 仕入管理（修正）
	if ( fncCheckAuthority( DEF_FUNCTION_PC5, $objAuth ) )
	{
		$aryData["btnFix_visibility"] = "visible";
		$aryData["btnFixVisible"] = "checked";
	}
	else
	{
		$aryData["btnFix_visibility"] = "hidden";
		$aryData["btnFixVisible"] = "";
	}
	// 706 仕入管理（削除）
	if ( fncCheckAuthority( DEF_FUNCTION_PC6, $objAuth ) )
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
	// 2004.04.14 suzukaze update start
	// 仕入状態
	//$aryData["lngStockStatusCode"] 		= fncGetMultiplePulldown( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", DEF_STOCK_DELIVER, '', $objDB );
	$aryData["lngStockStatusCode"] 		= fncGetCheckBoxObject( "m_stockstatus", "lngstockstatuscode", "strstockstatusname", "lngStockStatusCode[]", 'where lngStockStatusCode not in (1)', $objDB );
	// ワークフロー状態
//	$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );



	// 2004.04.14 suzukaze update start
	// 支払条件
	$aryData["lngPayConditionCode"] 	= fncGetPulldown( "m_paycondition", "lngpayconditioncode", "strpayconditionname", 0, '', $objDB );
	// 仕入科目
	$aryData["lngStockSubjectCode"]		= fncGetPulldown( "m_stocksubject", "lngstocksubjectcode", "lngstocksubjectcode, strstocksubjectname", 1, '', $objDB );
	// 仕入部品
	$aryData["lngStockItemCode"] 		= fncGetPulldown( "m_stockitem", "lngstocksubjectcode || '-' || lngstockitemcode", "lngstockitemcode, strstockitemname", 0, '', $objDB );
	// 運搬方法
	$aryData["lngDeliveryMethodCode"] 	= fncGetPulldown( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", 0, '', $objDB );

	//　プルダウンリストの取得に失敗した場合エラー表示
	if ( !$aryData["lngStockStatusCode"] or !$aryData["lngPayConditionCode"] or !$aryData["lngStockSubjectCode"] or !$aryData["lngStockItemCode"] )
	{
		fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
	}

	// クッキーの設定
	if( $_COOKIE["PurchaseControlSearch"] )
	{
		$aryCookie = fncStringToArray ( $_COOKIE["PurchaseControlSearch"], "&", ":" );
		while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
		{
			$aryData[$strKeys] = $strValues;
		}
	}

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "pc/search_ifrm/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

?>
