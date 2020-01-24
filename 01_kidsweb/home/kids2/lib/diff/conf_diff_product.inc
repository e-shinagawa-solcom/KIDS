<?php

/**
*	ファイル概要	: 製品マスタ・差分管理設定
*
*
*	@charset	: euc-jp
*/



	define ( "PRODUCT_MASTER_UPDATE_FLAG", 1 );	// 製品マスタ書き換え許可フラグ  1:許可  0:不許可


	$aryDiffProduct	= array();

	// Table・Key配列
	$aryDiffProduct["table"]	= "m_product";		// table
	$aryDiffProduct["key"]		= "strproductcode";	// key

	// 否認項目配列
	$aryDiffProduct["denial"]["strproductcode"]			= true;		// 製品コード
	$aryDiffProduct["denial"]["lnginchargeusercode"]	= true;		// 担当者コード

	// 差異チェック項目配列
	$aryDiffProduct["diff"]["strproductcode"]			= true;		// 製品コード
	$aryDiffProduct["diff"]["strproductname"]			= true;		// 製品名称（日本語）
	$aryDiffProduct["diff"]["lnginchargegroupcode"]		= true;		// 部門コード
	$aryDiffProduct["diff"]["lnginchargeusercode"]		= true;		// 担当者コード
	$aryDiffProduct["diff"]["lngcartonquantity"]		= true;		// カートン入り数
	$aryDiffProduct["diff"]["lngproductionquantity"]	= true;		// 生産予定数
	$aryDiffProduct["diff"]["dtmdeliverylimitdate"]		= true;		// 納期
	$aryDiffProduct["diff"]["curretailprice"]			= true;		// 上代
	$aryDiffProduct["diff"]["curproductprice"]			= true;		// 納価


	// 表示用項目配列
	$aryDiffProduct["display"]["strproductcode"]		= "製品コード";			// 製品コード
	$aryDiffProduct["display"]["strproductname"]		= "製品名称（日本語）";	// 製品名称（日本語）
	$aryDiffProduct["display"]["lnginchargegroupcode"]	= "部門コード";			// 部門コード
	$aryDiffProduct["display"]["lnginchargeusercode"]	= "担当者コード";		// 担当者コード
	$aryDiffProduct["display"]["lngcartonquantity"]		= "カートン入り数";		// カートン入り数
	$aryDiffProduct["display"]["lngproductionquantity"]	= "生産予定数";			// 生産予定数
	$aryDiffProduct["display"]["dtmdeliverylimitdate"]	= "納期";				// 納期
	$aryDiffProduct["display"]["curretailprice"]		= "上代";				// 上代
	$aryDiffProduct["display"]["curproductprice"]		= "納価";				// 納価
	$aryDiffProduct["display"]["curConversionRate"]		= "USドルレート";		// 換算レート
	$aryDiffProduct["display"]["curStandardRate"]		= "標準割合";			// 標準割合


	// 製品マスタ情報取得用SELECT句配列
	$aryDiffProduct["select"]["strproductcode"]			= true;												// 製品コード
	$aryDiffProduct["select"]["strproductname"]			= true;												// 製品名称（日本語）
	$aryDiffProduct["select"]["lnginchargegroupcode"]	= true;												// 部門コード
	$aryDiffProduct["select"]["lnginchargeusercode"]	= true;												// 担当者コード
	$aryDiffProduct["select"]["lngcartonquantity"]		= true;												// カートン入り数
	$aryDiffProduct["select"]["lngproductionquantity"]	= true;												// 生産予定数
	$aryDiffProduct["select"]["to_char( dtmdeliverylimitdate, 'YYYY/mm' ) as dtmdeliverylimitdate"]	= true;	// 納期
	$aryDiffProduct["select"]["curretailprice"]			= true;												// 上代
	$aryDiffProduct["select"]["curproductprice"]		= true;												// 納価

	// Tempテーブル登録用カラム配列
	$aryDiffProduct["tempdb"]["strProductCode"]				= true;		// 製品コード
	$aryDiffProduct["tempdb"]["strProductName"]				= true;		// 製品名称（日本語）
	$aryDiffProduct["tempdb"]["strGroupDisplayCode"]		= true;		// 部門コード
	$aryDiffProduct["tempdb"]["strUserDiplayCode"]			= false;	// 担当者コード
	$aryDiffProduct["tempdb"]["lngCartonQuantity"]			= true;		// カートン入り数
	$aryDiffProduct["tempdb"]["lngPlanCartonProduction"]	= true;		// 計画C/t
	$aryDiffProduct["tempdb"]["lngProductionQuantity"]		= true;		// 生産予定数
	$aryDiffProduct["tempdb"]["dtmDeliveryLimitDate"]		= true;		// 納期
	$aryDiffProduct["tempdb"]["curRetailPrice"]				= true;		// 上代
	$aryDiffProduct["tempdb"]["curProductPrice"]			= true;		// 納価
	$aryDiffProduct["tempdb"]["curConversionRate"]			= true;		// 換算レート
	$aryDiffProduct["tempdb"]["curStandardRate"]			= true;		// 標準割合

	// レート配列
	$aryDiffProduct["rate"]["curConversionRate"]	= true;		// 換算レート
	$aryDiffProduct["rate"]["curStandardRate"]		= true;		// 標準割合


	// 内部->表示コード変換項目
	define ( "DISP_INCHARGE_GROUP_CODE",	"lnginchargegroupcode" );	// 部門コード
	define ( "DISP_INCHARGE_USER_CODE",		"lnginchargeusercode" );	// 担当者コード

?>
