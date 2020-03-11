<?php

// ----------------------------------------------------------------------------
/**
 *       レフトナビゲーション生成
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
 *         ・パラメータより、ボタンオブジェクトを設定
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ライブラリ読み込み
//-------------------------------------------------------------------------
include_once 'conf.inc';
require LIB_FILE;
require LIB_DEBUGFILE;
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// パラメータ初期化
//-------------------------------------------------------------------------
$aryData = array();
$aryData = $_REQUEST;
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// オブジェクト生成
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();
$objTemplate = new clsTemplate();
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// セッション確認
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// DBクローズ
$objDB->close();

$strNaviCode = $_REQUEST["strNaviCode"];

if ($strNaviCode == "uc-info") {
    $strTemplatePath = "/navi/uc_info.html";
} else if ($strNaviCode == "uc-regist") {
    $strTemplatePath = "/navi/uc_regist.html";
} else if ($strNaviCode == "uc-search") {
    $strTemplatePath = "/navi/uc_search.html";
}

$objTemplate->getTemplate($strTemplatePath);
// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();

$aryData["strButton"] = $objTemplate->strTemplate;

// $aryData["strButton"] = '<span id="UserInfoNaviBt1" onclick="top.location=\'/uc/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_UC1 . '\';" disabled></span>';
// $aryData["strButton"] .= '<span id="RegistNaviBt1" onclick="top.location=\'/uc/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_UC2 . '\';"></span>';
// $aryData["strButton"] .= '<span id="SearchNaviBt1" onclick="top.location=\'/uc/search/index.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_UC3 . '\';"></span>';

//-------------------------------------------------------------------------
// METAタグ生成
//-------------------------------------------------------------------------
$aryData["strMeta"] = '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/uc/images.js"></script>';
$aryData["strMeta"] .= '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/cmn/images.js"></script>';
$aryData["strMeta"] .= '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/uc/initlayout.js"></script>';
$aryData["strMeta"] .= '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/cmn/initlayoutnavi.js"></script>';
$aryData["strMeta"] .= '<script type="text/javascript" language="javascript" src="/navi/cmn/exstr.js"></script>';
$aryData["strMeta"] .= '<link rel="stylesheet" type="text/css" media="screen" href="/navi/uc/layout.css">';
// if ($strDirName == "uc") {
//     $aryData["strMeta"] .= '<link rel="stylesheet" type="text/css" media="screen" href="/navi/uc/layout.css">';
// } else {
//     $aryData["strMeta"] .= '<link rel="stylesheet" type="text/css" media="screen" href="/navi/cmn/layout.css">';
// }
//-------------------------------------------------------------------------
// テンプレート読み込み
$objTemplate->getTemplate("/navi/uc.html");

// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();
// HTML出力
echo $objTemplate->strTemplate;
//-------------------------------------------------------------------------

return true;
