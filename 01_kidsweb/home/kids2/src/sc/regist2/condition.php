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
	//   フォーム初期値設定
	// --------------------------
	// 売上区分プルダウン
	$optSalesClass .= fncGetPulldown("m_salesclass", "lngsalesclasscode","strsalesclassname", "", "", $objDB);
	$aryData["optSalesClass"] = $optSalesClass;

	echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "sc/regist2/condition.tmpl", $aryData ,$objAuth );

	//-------------------------------------------------------------------------
	// DB切断
	//-------------------------------------------------------------------------
	$objDB->close();

	return true;
?>