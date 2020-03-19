<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  登録
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
*         ・登録処理
*         ・エラーチェック
*         ・登録処理完了後、登録完了画面へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// ライブラリファイル読込
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
	
	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB		= new clsDB();
	$objDB->open("", "", "", "");

	// --------------------------
	//  フォーム初期値設定
	// --------------------------
	// 顧客コード
	$aryData["strDefaultCompanyDisplayCode"] = $_POST["strcompanydisplaycode"];
	$aryData["strDefaultCompanyDisplayName"] = $_POST["strcompanydisplayname"];

	// 通貨コードの初期値。画面にも保存しておく
	$lngDefaultMonetaryUnitCode = $_POST["lngmonetaryunitcode"];
	$aryData["lngDefaultMonetaryUnitCode"] = $lngDefaultMonetaryUnitCode;

	// 売上区分プルダウン（親画面の出力明細一覧エリアの1行目の売上区分コードを初期値選択）
	// $optSalesClass .= fncGetPulldown("m_salesclass", "lngsalesclasscode","strsalesclassname", $lngDefaultSalesClassCode, "", $objDB);
	$optSalesClass .= fncGetPulldown("m_salesclass", "lngsalesclasscode","strsalesclassname", "", "", $objDB);
	$aryData["optSalesClass"] = $optSalesClass;

	// 通貨単位プルダウン
	$optMonetaryUnitCode = "<option value=''> </option>"; 
	$optMonetaryUnitCode .= fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode","strmonetaryunitname", $_POST["lngmonetaryunitcode"], "", $objDB);
	$aryData["optMonetaryUnitCode"] = $optMonetaryUnitCode;
	
	$aryData["strSessionID"] = $_POST["strSessionID"];
	// --------------------------
	//  画面表示
	// --------------------------
	// 納品書明細検索条件入力画面の表示
	echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "sc/regist2/condition.tmpl", $aryData ,$objAuth );

	// DB切断
	$objDB->close();
	
	// 処理終了
	return true;

?>