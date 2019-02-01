<?php
// *********************************
// ***** Please save as UTF-8. *****
// *********************************

// ----------------------------------------------------------------------------
/** 
*	データエクスポート用ライブラリ
*
*	データエクスポート用関数ライブラリ
*
*	@package    K.I.D.S.
*	@license    http://www.kuwagata.co.jp/
*	@copyright  KUWAGATA CO., LTD.
*	@author     K.I.D.S. Groups <info@kids-groups.com>
*	@access     public
*	@version 	2.00
*
*
*	更新履歴
*	2004.04.07	LC予定表の表示項目を追加
*	2004.05.13	LC予定表の表示項目を追加 納品場所の追加
*	2004.05.13	タイトルの追加
*	**********
*	2014.11.08	商品計画書に関する定義追加 k.saito
*/
// ----------------------------------------------------------------------------

	//////////////////////////////////////////////////////////////////////////////
	// 設定
	//////////////////////////////////////////////////////////////////////////////
	// 処理ID定義
	define ( "DEF_QUERY_ROOT", SRC_ROOT . "dataex/cmn/" ); // クエリパス

	define ( "DEF_EXPORT_SALES",    1 ); // 売上レシピ
	define ( "DEF_EXPORT_PURCHASE", 2 ); // Purchase Recipe
	define ( "DEF_EXPORT_LC",       3 ); // L/C予定表情報
	define ( "DEF_EXPORT_STOCK",    4 ); // 仕入一覧表
	define ( "DEF_EXPORT_ESTIMATE", 5 ); // 見積原価書
	define ( "DEF_EXPORT_STAT01",   6 ); // 社内統計データ（売上見込）
	define ( "DEF_EXPORT_STAT02",   7 ); // 社内統計データ（概算売上）
	define ( "DEF_EXPORT_PPLAN",	8 ); // 商品計画書


	// lngExportData を添字とするテンプレートファイルディレクトリの設定
	$aryDirName = array (
		DEF_EXPORT_SALES    => "sales",
		DEF_EXPORT_PURCHASE => "purchase",
		DEF_EXPORT_LC       => "lc",
		DEF_EXPORT_STOCK    => "stock",
		DEF_EXPORT_ESTIMATE => "estimate",
		DEF_EXPORT_STAT01	=> "stat01",
		DEF_EXPORT_STAT02	=> "stat02",
		DEF_EXPORT_PPLAN	=> "pplan"
	);


	// 売上レシピ
	$aryTitleName[1][1] = "売上レシピ_部門・顧客別";
	$aryTitleName[1][2] = "売上レシピ_部門・製品別";
	// Purchase Recipe
	$aryTitleName[2][1] = "Ｐｕｒｃｈａｓｅ　Ｒｅｃｉｐｅ　（ＬＣ）";
	$aryTitleName[2][2] = "Ｐｕｒｃｈａｓｅ　Ｒｅｃｉｐｅ　（ＴＴ）";
	$aryTitleName[2][3] = "Ｐｕｒｃｈａｓｅ　Ｒｅｃｉｐｅ　（ＯｎＢｏａｒｄ）";
	// LC予定表
	$aryTitleName[3][1] = "Ｌ／Ｃ予定表（新規）";
	$aryTitleName[3][2] = "Ｌ／Ｃ予定表（リバイズ）";
	// 仕入一覧表
	$aryTitleName[4][1] = "仕入一覧表_仕入科目・仕入先別";
	$aryTitleName[4][2] = "仕入一覧表_仕入科目・部門・製品別";

	// 見積原価書
	$aryTitleName[5][1] = "見積原価書";

//	$aryTitleName[6] = "社内統計データ01（売上見込）";
	$aryTitleName[6] = "STAT01";
	$aryTitleName[7] = "社内統計データ02（概算売上）";

	// 商品計画書	
	$aryTitleName[8] = "PPLAN";


	// カラム名定義
	$aryColumnName[1] = Array ( "売上計上日", "売上No", "受注No", "顧客コード", "顧客名称", "部門コード", "部門名称", "カテゴリ", "伝票No", "売上区分コード", "製品コード", "製品名称", "顧客品番","通貨名称", "単価", "単位", "数量", "税抜金額", "税額", "合計金額", "明細備考" );

	$aryColumnName[2] = Array ( "仕入計上日", "仕入No", "発注No", "仕入先コード", "仕入先名称", "部門コード", "部門名称", "カテゴリ", "担当者コード", "担当者名称", "伝票コード", "通貨名称", "レートタイプ", "通貨レート", "支払条件", "製品到着日", "仕入科目コード", "仕入科目名称", "仕入部品コード", "仕入部品名称", "製品コード", "製品名称", "単価", "単位", "数量", "税抜金額", "明細備考" );

	$aryColumnName[3] = Array ( "P.O.No", "行番号", "リバイズ", "状態", "支払条件", "POチェック", "Beneeficiary", "LC月", "商品CD", "商品名", "数量", "単位", "単価", "金額", "船積開始 予定日", "船積終了 予定日", "計上日", "更新日", "納品場所", "船積期限", "有効期限", "発行銀行", "銀行依頼日", "L/C No", "LC・AM Opening date", "通貨", "備考" );

	$aryColumnName[4] = Array ( "仕入計上日", "仕入No", "発注No", "仕入先コード", "仕入先名称", "部門コード", "部門名称", "カテゴリ", "伝票コード", "仕入科目コード", "仕入科目名称", "仕入部品コード", "仕入部品名称", "製品コード", "製品名称", "顧客品番", "単価", "単位", "数量", "税区分", "通貨名称" , "税抜金額", "税額", "合計金額", "合計金額TTM" );

	$aryColumnName[5] = Array ( "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N");


	function getFunctionCode( $lngExportData )
	{
		if ( $lngExportData == DEF_EXPORT_SALES )
		{
			$lngFunctionCode = DEF_FUNCTION_DE1;
		}
		elseif ( $lngExportData == DEF_EXPORT_PURCHASE )
		{
			$lngFunctionCode = DEF_FUNCTION_DE2;
		}
		elseif ( $lngExportData == DEF_EXPORT_LC )
		{
			$lngFunctionCode = DEF_FUNCTION_DE3;
		}
		elseif ( $lngExportData == DEF_EXPORT_STOCK )
		{
			$lngFunctionCode = DEF_FUNCTION_DE4;
		}
		elseif ( $lngExportData == DEF_EXPORT_ESTIMATE )
		{
			$lngFunctionCode = DEF_FUNCTION_DE5;
		}
		elseif ( $lngExportData == DEF_EXPORT_STAT01 )
		{
			$lngFunctionCode = DEF_FUNCTION_DE6;
		}
		elseif ( $lngExportData == DEF_EXPORT_STAT02 )
		{
			$lngFunctionCode = DEF_FUNCTION_DE7;
		}
		elseif ( $lngExportData == DEF_EXPORT_PPLAN )
		{
			$lngFunctionCode = DEF_FUNCTION_DE8;
		}

		return $lngFunctionCode;
	}

?>
