<?php

/**
*	ファイル概要	: 見積原価管理・マッピング設定
*
*
*	仕入科目明細行 HIDDEN構成
*
*		aryDetail[仕入科目キー][明細行番号][lngStockSubjectCode]	// 仕入科目コード
*		aryDetail[仕入科目キー][明細行番号][lngStockItemCode]		// 仕入部品コード
*		aryDetail[仕入科目キー][明細行番号][lngCustomerCode]		// 仕入先コード
*		aryDetail[仕入科目キー][明細行番号][bytPayOffTargetFlag]	// 償却対象フラグ
*		aryDetail[仕入科目キー][明細行番号][lngProductQuantity]		// 計画個数
*		aryDetail[仕入科目キー][明細行番号][lngMonetaryUnitCode]	// 通貨コード
*		aryDetail[仕入科目キー][明細行番号][curProductPrice]		// 単価
*		aryDetail[仕入科目キー][明細行番号][curSubTotalPrice]		// 計画原価
*		aryDetail[仕入科目キー][明細行番号][strNote]			// 備考
*
*		aryDetail[仕入科目キー][明細行番号][bytPercentInputFlag]	// %入力フラグ
*		aryDetail[仕入科目キー][明細行番号][curProductRate]		// 計画率
*		aryDetail[仕入科目キー][明細行番号][curConversionRate]		// 換算レート
*		aryDetail[仕入科目キー][明細行番号][curSubTotalPriceJP]		// 日本円で計画原価小計を出すための準備に使用
*		aryDetail[仕入科目キー][明細行番号][strCompanyDisplayName]	// 仕入先名称
*
*
*	@charset	: utf-8
*/



	// マッピング対象機能コード
	define ( "MAP_FUNCTION",	DEF_FUNCTION_E0 );	// 見積原価管理

	// 処理モード
	define ( "PROC_SALES",	"saleskey" );		// 売上分類（区分）キー
	define ( "PROC_STOCK",	"stockkey" );		// 仕入科目キー
	define ( "PROC_DIFF",	"diff" );		// 差分
	define ( "PROC_NORMAL",	"normal" );		// 通常
	define ( "PROC_DETAIL01",	"detail01" );		// 明細行
	define ( "PROC_DETAIL",	"detail" );		// 明細行
	define ( "PROC_JUMP",	"jump" );		// 小計・合計他
	define ( "PROC_END",	"end" );		// 終了
	define ( "PROC_COUNT",	"count" );		// 明細行カウンタ

	// ---------------------------------------------------------------------------
	// 売上分類名称
	define("SALESCLS_1",			"1:固定費売上");


	// 仕入科目名称
	define ( "STOCK_431",			"431:金型償却高" );
	define ( "STOCK_433",			"433:金型海外償却" );
	define ( "STOCK_403",			"403:材料ツール仕入高" );
	define ( "STOCK_402",			"402:輸入パーツ仕入高" );
	define ( "STOCK_401",			"401:材料パーツ仕入高" );
	define ( "STOCK_420",			"420:外注加工費" );
	define ( "STOCK_1224",			"1224:チャージ" );
	define ( "STOCK_1230",			"1230:経費" );

	// 初期値
	define ( "INIT_COMPANY_CODE",		"0000" );	// 仕入先・顧客先コード
	define ( "INIT_COMPANY_NAME",		"その他" );	// 仕入先・顧客先名称
	define ( "INIT_SALES_ITEM_CODE",	"99" );		// 売上区分コード
	define ( "INIT_SALES_ITEM_NAME",	"-" );		// 売上区分名称
	define ( "INIT_STOCK_ITEM_CODE",	"99" );		// 仕入部品コード
	define ( "INIT_STOCK_ITEM_NAME",	"-" );		// 仕入部品名称


	// ジャンプキー
	define ( "REMARK",			"コメント" );
	define ( "STANDARD_RATE",		"標準割合" );


	// 日本円換算レート
	define ( "JP_RATE",	1.000000 );	// 日本円換算レート

	// 明細行HIDDENハッシュ名
	define ( "DETAIL_HASH",	"aryDetail" );	// 明細行HIDDENハッシュ名

	// バッファリング NAME属性
	define ( "NAME_USER_DISPLAY_NAME",		"strUserDisplayName" );			// 担当者名称
	define ( "NAME_SALES_KEYNAME",			"saleskeyname" );			// 売上分類名称
	define ( "NAME_STOCK_KEYNAME",			"stockkeyname" );			// 仕入科目名称
	define ( "NAME_DETAIL_ROW",			"detailrow" );				// 明細行番号
	define ( "NAME_PRODUCTION_QUANTITY_HIDDEN",	"lngProductionQuantity_hidden" );	// 生産予定数補完
	define ( "NAME_PRODUCT_PRICE_HIDDEN",		"curProductPrice_hidden" );		// 納価補完
	define ( "NAME_CONVERSION_RATE",		"curConversionRate" );			// 換算レート
	define ( "NAME_STANDARD_RATE",			"curStandardRate" );			// 標準割合
	define ( "NAME_SYSTEM_CONVERSION_RATE",		"curConversionRate" );			// System換算レート
	define ( "NAME_SYSTEM_STANDARD_RATE",		"curStandardRate" );			// System標準割合
	define ( "NAME_PERCENT_INPUT_FLAG",		"bytPercentInputFlag" );		// %入力フラグ
	define ( "NAME_PRODUCT_RATE",			"curProductRate" );			// 計画率
	define ( "NAME_MONETARY_UNIT_CODE",		"lngMonetaryUnitCode" );		// 通貨コード
	define ( "NAME_PRODUCT_QUANTITY",		"lngProductQuantity" );			// 計画個数
	define ( "NAME_PRODUCT_PRICE",			"curProductPrice" );			// 単価
	define ( "NAME_SUBTOTAL_PRICE",			"curSubTotalPrice" );			// 計画原価小計
	define ( "NAME_SUBTOTAL_PRICE_JP",		"curSubTotalPriceJP" );			// 日本円計画原価小計
	define ( "NAME_COMPANY_DISPLAY_NAME",		"strCompanyDisplayName" );		// 仕入先名称
	define ( "NAME_REMARK",				"strRemark" );				// コメント
	define ( "NAME_JUMP_FLAG",			"lngJumpFlag" );			// ジャンプフラグ
	define ( "NAME_JUMP_KEY",			"jumpkey" );				// ジャンプキー

	// バッファリング 判定キー・行/列
	define ( "ROWCOL_PRODUCTION_QUANTITY",		"5H");	// 生産予定数判定行/列
	define ( "ROWCOL_PRODUCT_PRICE",		"5K");	// 納価判定行/列
	define ( "ROWCOL_CONVERSION_RATE",		"7K" );	// 換算レート判定行/列
	define ( "COL_SALES_DIVISION_CODE",		"A" );	// 売上分類コード判定列
	define ( "COL_SALES_CLASS_CODE",		"B" );	// 売上区分判定列
	define ( "COL_STOCK_SUBJECT_CODE",		"A" );	// 仕入科目コード判定列
	define ( "COL_STOCK_ITEM_CODE",			"B" );	// 仕入部品判定列
	define ( "COL_CUSTOMER_COMPANY_CODE",		"C" );	// 仕入先判定列
	define ( "COL_PERCENT_INPUT",			"C" );	// 計画率判定列
	define ( "COL_PAYOFF_TARGET_FLAG",		"D" );	// 償却対象フラグ判定列
	define ( "COL_PRODUCT_QUANTITY",		"E" );	// 計画個数判定列
	define ( "COL_MONETARY_UNIT_CODE",		"F" );	// 通貨コード判定列
	define ( "COL_PRODUCT_PRICE",			"G" );	// 単価判定列
	define ( "COL_SUBTOTAL_PRICE",			"H" );	// 計画原価判定列
	define ( "COL_NOTE",				"I" );	// 明細行備考判定列


	// 終端判定キー・行/列
	define ( "ROWCOL_LAST_HEADER",		"7K" );		// ヘッダー終端列 (old:5K)
	define ( "COL_LAST_DETAIL",		"I" );		// 明細行終端列
	define ( "COL_FINISH_DETAIL",		"J" );		// 明細行処理終了列

	// Cell値変換対象文字列
	define ( "CELL_MONETARY_JP",		"JP" );		// 通貨コード：日本円
	define ( "CELL_MONETARY_US",		"US" );		// 通貨コード：USドル
	define ( "CELL_MONETARY_HKD",		"HKD" );	// 通貨コード：HKDドル
	define ( "CELL_BOOL",			"○" );		// フラグ文字列
	define ( "CELL_PERCENT",		"%" );		// パーセント文字列

	// Cell値変換文字列
	define ( "CONV_BOOL_T",			"true" );	// TRUE
	define ( "CONV_BOOL_F",			"false" );	// FALSE
	define ( "CONV_MONETARY_JP",		"\\\\" );	// 日本円
	define ( "CONV_MONETARY_US",		"$" );		// USドル
	define ( "CONV_MONETARY_HKD",		"HKD" );	// HKDドル





	$aryMapping	= array();

	// ---------------------------------------------------------------------------
	// 売上分類
	$aryMapping[PROC_SALES][SALESCLS_1]	= 11;	// 1:固定費売上


	// 仕入科目をhidden配列の数値キーに対応させるための配列
	$aryMapping[PROC_STOCK][STOCK_431]	= 0;	// 431:金型償却高
	$aryMapping[PROC_STOCK][STOCK_433]	= 1;	// 433:金型海外償却
	$aryMapping[PROC_STOCK][STOCK_403]	= 2;	// 403:材料ツール仕入高
	$aryMapping[PROC_STOCK][STOCK_402]	= 3;	// 402:輸入パーツ仕入高
	$aryMapping[PROC_STOCK][STOCK_401]	= 4;	// 401:材料パーツ仕入高
	$aryMapping[PROC_STOCK][STOCK_420]	= 5;	// 420:外注加工費
	$aryMapping[PROC_STOCK][STOCK_1224]	= 6;	// 1224:チャージ
	$aryMapping[PROC_STOCK][STOCK_1230]	= 7;	// 1230:経費

	// 差分項目 マッピング配列
	$aryMapping[PROC_DIFF]["3B"]				= "strproductcode";							// 製品コード
	$aryMapping[PROC_DIFF]["3D"]				= "strproductname";							// 製品名称
	$aryMapping[PROC_DIFF]["3K"]				= "dtmdeliverylimitdate"	.SPLIT_TYPE_CHAR.	"date";		// 納期
	$aryMapping[PROC_DIFF]["4B"]				= "lnginchargegroupcode"	.SPLIT_TYPE_CHAR.	"code";		// 部門コード
	$aryMapping[PROC_DIFF]["4D"]				= "lnginchargeusercode"		.SPLIT_TYPE_CHAR.	"code";		// 担当者コード
	$aryMapping[PROC_DIFF]["4K"]				= "curretailprice";							// 上代
	$aryMapping[PROC_DIFF]["5B"]				= "lngcartonquantity";							// カートン入数
	$aryMapping[PROC_DIFF]["5D"]				= "lngplancartonproduction";						// 計画C/T
	$aryMapping[PROC_DIFF][ROWCOL_PRODUCTION_QUANTITY]	= "lngproductionquantity";						// 生産予定数
	$aryMapping[PROC_DIFF][ROWCOL_PRODUCT_PRICE]		= "curproductprice";							// 納価

	// Header・固定項目 マッピング配列
	$aryMapping[PROC_NORMAL]["3B"]				= "strProductCode";							// 製品コード
	$aryMapping[PROC_NORMAL]["3D"]				= "strProductName";							// 製品名称
	$aryMapping[PROC_NORMAL]["3K"]				= "dtmDeliveryLimitDate"	.SPLIT_TYPE_CHAR.	"date";		// 納期
	$aryMapping[PROC_NORMAL]["4B"]				= "strGroupDisplayCode"		.SPLIT_TYPE_CHAR.	"code";		// 部門コード
	$aryMapping[PROC_NORMAL]["4D"]				= "strUserDiplayCode"		.SPLIT_TYPE_CHAR.	"code_name";	// 担当者コード
	$aryMapping[PROC_NORMAL]["4K"]				= "curRetailPrice";							// 上代
	$aryMapping[PROC_NORMAL]["5B"]				= "lngCartonQuantity";							// カートン入数
	$aryMapping[PROC_NORMAL]["5D"]				= "lngPlanCartonProduction";						// 計画C/T
	$aryMapping[PROC_NORMAL][ROWCOL_PRODUCTION_QUANTITY]	= "lngProductionQuantity";						// 生産予定数
	$aryMapping[PROC_NORMAL][ROWCOL_PRODUCT_PRICE]		= "curProductPrice";							// 納価
	$aryMapping[PROC_NORMAL][ROWCOL_CONVERSION_RATE]	= NAME_CONVERSION_RATE;							// 換算レート

	// 売上分類・区分（明細行）マッピング配列
	$aryMapping[PROC_DETAIL01][COL_SALES_DIVISION_CODE]	= "lngSalesDivisionCode"	.SPLIT_TYPE_CHAR.	"code";		// 売上分類コード
	$aryMapping[PROC_DETAIL01][COL_SALES_CLASS_CODE]	= "lngSalesClassCode"		.SPLIT_TYPE_CHAR.	"code";		// 売上区分コード
	$aryMapping[PROC_DETAIL01][COL_CUSTOMER_COMPANY_CODE]	= "lngCustomerCompanyCode"	.SPLIT_TYPE_CHAR.	"code";		// 仕入先コード
	$aryMapping[PROC_DETAIL01][COL_PAYOFF_TARGET_FLAG]	= "bytPayOffTargetFlag"		.SPLIT_TYPE_CHAR.	"bool";		// 償却対象フラグ
	$aryMapping[PROC_DETAIL01][COL_PRODUCT_QUANTITY]	= NAME_PRODUCT_QUANTITY;						// 計画個数
	$aryMapping[PROC_DETAIL01][COL_MONETARY_UNIT_CODE]	= NAME_MONETARY_UNIT_CODE	.SPLIT_TYPE_CHAR.	"money";	// 通貨コード
	$aryMapping[PROC_DETAIL01][COL_PRODUCT_PRICE]		= NAME_PRODUCT_PRICE;							// 単価
	$aryMapping[PROC_DETAIL01][COL_SUBTOTAL_PRICE]		= NAME_SUBTOTAL_PRICE;							// 計画原価
	$aryMapping[PROC_DETAIL01][COL_NOTE]			= "strNote";								// 備考

	// 仕入科目（明細行）マッピング配列
	$aryMapping[PROC_DETAIL][COL_STOCK_SUBJECT_CODE]	= "lngStockSubjectCode"		.SPLIT_TYPE_CHAR.	"code";		// 仕入科目コード
	$aryMapping[PROC_DETAIL][COL_STOCK_ITEM_CODE]		= "lngStockItemCode"		.SPLIT_TYPE_CHAR.	"code";		// 仕入部品コード
	$aryMapping[PROC_DETAIL][COL_CUSTOMER_COMPANY_CODE]	= "lngCustomerCompanyCode"	.SPLIT_TYPE_CHAR.	"code_percent";	// 仕入先コード
	$aryMapping[PROC_DETAIL][COL_PAYOFF_TARGET_FLAG]	= "bytPayOffTargetFlag"		.SPLIT_TYPE_CHAR.	"bool";		// 償却対象フラグ
	$aryMapping[PROC_DETAIL][COL_PRODUCT_QUANTITY]		= NAME_PRODUCT_QUANTITY;						// 計画個数
	$aryMapping[PROC_DETAIL][COL_MONETARY_UNIT_CODE]	= NAME_MONETARY_UNIT_CODE	.SPLIT_TYPE_CHAR.	"money";	// 通貨コード
	$aryMapping[PROC_DETAIL][COL_PRODUCT_PRICE]		= NAME_PRODUCT_PRICE;							// 単価
	$aryMapping[PROC_DETAIL][COL_SUBTOTAL_PRICE]		= NAME_SUBTOTAL_PRICE;							// 計画原価
	$aryMapping[PROC_DETAIL][COL_NOTE]			= "strNote";								// 備考

	// 小計・合計他キー配列
	$aryMapping[PROC_JUMP][STANDARD_RATE]	= STANDARD_RATE;	// 標準割合
	$aryMapping[PROC_JUMP][REMARK]		= REMARK;		// コメント

	// 小計・合計他配列
	$aryMapping[STANDARD_RATE]["E"]		= NAME_STANDARD_RATE	.SPLIT_TYPE_CHAR.	"percent";	// 標準割合
	$aryMapping[REMARK]["F"]		= NAME_REMARK;	// コメント

	// 売上科目毎カウンタ配列（初期値）
	$aryMapping[PROC_COUNT][SALESCLS_1]	= 0;	//
/*	$aryMapping[PROC_COUNT][SALESCLS_2]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_3]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_4]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_5]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_6]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_7]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_8]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_9]	= 0;	//
	$aryMapping[PROC_COUNT][SALESCLS_10]	= 0;	//
*/

	// 仕入科目毎カウンタ配列（初期値）
	$aryMapping[PROC_COUNT][STOCK_431]	= 0;	// 431:金型償却高
	$aryMapping[PROC_COUNT][STOCK_433]	= 0;	// 433:金型海外償却
	$aryMapping[PROC_COUNT][STOCK_403]	= 0;	// 403:材料ツール仕入高
	$aryMapping[PROC_COUNT][STOCK_402]	= 0;	// 402:輸入パーツ仕入高
	$aryMapping[PROC_COUNT][STOCK_401]	= 0;	// 401:材料パーツ仕入高
	$aryMapping[PROC_COUNT][STOCK_420]	= 0;	// 420:外注加工費
	$aryMapping[PROC_COUNT][STOCK_1224]	= 0;	// 1224:チャージ
	$aryMapping[PROC_COUNT][STOCK_1230]	= 0;	// 1230:経費



?>
