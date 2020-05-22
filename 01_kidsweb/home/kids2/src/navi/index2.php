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

include_once 'conf.inc';
require LIB_FILE;
require LIB_DEBUGFILE;

$objDB = new clsDB();
$objAuth = new clsAuth();

$objDB->open("", "", "", "");

if ($_GET) {
    $aryData = $_GET;
} else if ($_POST) {
    $aryData = $_POST;
}
// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

$strNaviCode = $aryData["strNaviCode"];

// 権限チェック
// 見積原価検索
if (fncCheckAuthority(DEF_FUNCTION_E2, $objAuth)) {
    $aryData["Estimate_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Estimate_Search_visibility"] = 'style="visibility: hidden"';
}

// アップロード
if (fncCheckAuthority(DEF_FUNCTION_UP0, $objAuth)) {
    $aryData["Upload_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Upload_visibility"] = 'style="visibility: hidden"';
}
// 302　商品検索
if (fncCheckAuthority(DEF_FUNCTION_P2, $objAuth)) {
    $aryData["P_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["P_Search_visibility"] = 'style="visibility: hidden"';
}

// 402 受注管理（受注検索）
if (fncCheckAuthority(DEF_FUNCTION_SO2, $objAuth)) {
    $aryData["So_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["So_Search_visibility"] = 'style="visibility: hidden"';
}

// 502 発注管理（発注検索）
if (fncCheckAuthority(DEF_FUNCTION_PO2, $objAuth)) {
    $aryData["Po_Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Po_Regist_visibility"] = 'style="visibility: hidden"';
}

// 510 発注管理（発注書検索）
if (fncCheckAuthority(DEF_FUNCTION_PO10, $objAuth)) {
    $aryData["Po_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Po_Search_visibility"] = 'style="visibility: hidden"';
}

// 601 売上管理（ 売上登録）
if (fncCheckAuthority(DEF_FUNCTION_SC1, $objAuth)) {
    $aryData["Sc_Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sc_Regist_visibility"] = 'style="visibility: hidden"';
}

// 602 売上管理（ 売上検索）
if (fncCheckAuthority(DEF_FUNCTION_SC2, $objAuth)) {
    $aryData["Sc_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sc_Search_visibility"] = 'style="visibility: hidden"';
}

// 602 売上管理（ 納品書検索）
if (fncCheckAuthority(DEF_FUNCTION_SC2, $objAuth)) {
    $aryData["Sc_Search2_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sc_Search2_visibility"] = 'style="visibility: hidden"';
}

// 701 仕入管理（ 仕入登録）
if (fncCheckAuthority(DEF_FUNCTION_PC1, $objAuth)) {
    $aryData["Pc_Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Pc_Regist_visibility"] = 'style="visibility: hidden"';
}

// 702 仕入管理（ 仕入検索）
if (fncCheckAuthority(DEF_FUNCTION_PC2, $objAuth)) {
    $aryData["Pc_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Pc_Search_visibility"] = 'style="visibility: hidden"';
}
// 1901 金型帳票登録
if (fncCheckAuthority(DEF_FUNCTION_MR1, $objAuth)) {
    $aryData["Mr_Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Mr_Regist_visibility"] = 'style="visibility: hidden"';
}

// 1902 金型帳票検索
if (fncCheckAuthority(DEF_FUNCTION_MR2, $objAuth)) {
    $aryData["Mr_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Mr_Search_visibility"] = 'style="visibility: hidden"';
}

// 1801 金型履歴登録
if (fncCheckAuthority(DEF_FUNCTION_MM1, $objAuth)) {
    $aryData["Mm_Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Mm_Regist_visibility"] = 'style="visibility: hidden"';
}

// 1802 金型履歴検索
if (fncCheckAuthority(DEF_FUNCTION_MM2, $objAuth)) {
    $aryData["Mm_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Mm_Search_visibility"] = 'style="visibility: hidden"';
}

// 1806 金型一覧検索
if (fncCheckAuthority(DEF_FUNCTION_MM6, $objAuth)) {
    $aryData["Mm_List_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Mm_List_visibility"] = 'style="visibility: hidden"';
}

// 2201 請求書登録
if (fncCheckAuthority(DEF_FUNCTION_INV1, $objAuth)) {
    $aryData["Inv_Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Inv_Regist_visibility"] = 'style="visibility: hidden"';
}
// 2202 請求書検索
if (fncCheckAuthority(DEF_FUNCTION_INV2, $objAuth)) {
    $aryData["Inv_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Inv_Search_visibility"] = 'style="visibility: hidden"';
}
// 2204 請求集計
if (fncCheckAuthority(DEF_FUNCTION_INV4, $objAuth)) {
    $aryData["Inv_Aggregate_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Inv_Aggregate_visibility"] = 'style="visibility: hidden"';
}
if (fncCheckAuthority(DEF_FUNCTION_UC2, $objAuth)) {
    $aryData["lngFunctionCode2"] = DEF_FUNCTION_UC2;
    $aryData["Regist_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Regist_visibility"] = 'style="visibility: hidden"';
}

if (fncCheckAuthority(DEF_FUNCTION_UC3, $objAuth)) {
    $aryData["lngFunctionCode3"] = DEF_FUNCTION_UC3;
    $aryData["Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Search_visibility"] = 'style="visibility: hidden"';
}
// 帳票出力
if (fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    $aryData["List_Search_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["List_Search_visibility"] = 'style="visibility: hidden"';
}

if (fncCheckAuthority(DEF_FUNCTION_SYS1, $objAuth)) {
    $aryData["Sysc_Info_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sysc_Info_visibility"] = 'style="visibility: hidden"';
}
if (fncCheckAuthority(DEF_FUNCTION_SYS2, $objAuth)) {
    $aryData["Sysc_Server_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sysc_Server_visibility"] = 'style="visibility: hidden"';
}
if (fncCheckAuthority(DEF_FUNCTION_SYS3, $objAuth)) {
    $aryData["Sysc_Mail_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sysc_Mail_visibility"] = 'style="visibility: hidden"';
}
if (fncCheckAuthority(DEF_FUNCTION_SYS4, $objAuth)) {
    $aryData["Sysc_Session_visibility"] = 'style="visibility: visible"';
} else {
    $aryData["Sysc_Session_visibility"] = 'style="visibility: hidden"';
}
switch ($strNaviCode) {
    // 見積原価検索
    case "estimate-search":
        $strTemplatePath = "/search/cmn/navigate/estimate_navi.html";
        break;
    // アップロード
    case "upload":
        $strTemplatePath = '/upload2/cmn/navigate/upload2_navi.html';
        break;
    // 商品検索
    case "p-search":
        $strTemplatePath = '/search/cmn/navigate/p_navi.html';
        break;
    // 受注検索
    case "so-search":
        $strTemplatePath = '/search/cmn/navigate/so_navi.html';
        break;
    // 発注検索
    case "po-search":
        $strTemplatePath = '/search/cmn/navigate/po_navi.html';
        break;
    // 発注書検索
    case "po-search2":
        $strTemplatePath = '/search/cmn/navigate/po2_navi.html';
        break;
    // 売上納品書登録
    case "sc-regist":
        $strTemplatePath = '/sc/cmn/navigate/sc_navi.html';
        break;
    // 売上検索
    case "sc-search":
        $strTemplatePath = '/search/cmn/navigate/sc_navi.html';
        break;
    // 納品書検索
    case "sc-search2":
        $strTemplatePath = '/search/cmn/navigate/sc2_navi.html';
        break;
    // 仕入登録
    case "pc-regist":
        $strTemplatePath = '/pc/cmn/navigate/pc_navi.html';
        break;
    // 仕入検索
    case "pc-search":
        $strTemplatePath = '/search/cmn/navigate/pc_navi.html';
        break;
    // 請求登録
    case "inv-regist":
        $strTemplatePath = '/inv/cmn/navigate/inv_navi.html';
        break;
    // 請求検索
    case "inv-search":
        $strTemplatePath = '/search/cmn/navigate/inv_navi.html';
        break;
    // 請求集計
    case "inv-aggregate":
        $strTemplatePath = '/inv/cmn/navigate/inv_agg_navi.html';
        break;
    case "list-es":
        $strTemplatePath = '/search/cmn/navigate/list_es_navi.html';
        break;
    case "list-p":
        $strTemplatePath = '/search/cmn/navigate/list_p_navi.html';
        break;
    case "list-po":
        $strTemplatePath = '/search/cmn/navigate/list_po_navi.html';
        break;
    case "uc-search":
        $strTemplatePath = '/search/cmn/navigate/uc_navi.html';
        break;
    case "uc-info":
        $strTemplatePath = '/uc/cmn/navigate/uc_info_navi.html';
        break;
    case "uc-regist":
        $strTemplatePath = '/uc/cmn/navigate/uc_navi.html';
        break;
    case "mm-regist":
        $strTemplatePath = '/mm/cmn/navigate/mm_navi.html';
        break;
    case "mm-search":
        $strTemplatePath = '/search/cmn/navigate/mm_navi.html';
        break;
    case "mm-list":
        $strTemplatePath = '/search/cmn/navigate/mm_list_navi.html';
        break;
    case "mr-regist":
        $strTemplatePath = '/mr/cmn/navigate/mr_navi.html';
        break;
    case "mr-search":
        $strTemplatePath = '/search/cmn/navigate/mr_navi.html';
        break;
    case "sys-info":
        $strTemplatePath = '/sysc/cmn/navigate/sysc_info_navi.html';
        break;
    case "sys-session":
        $strTemplatePath = '/sysc/cmn/navigate/sysc_session_navi.html';
        break;
    case "sys-sev":
        $strTemplatePath = '/sysc/cmn/navigate/sysc_server_navi.html';
        break;
    case "sys-mail":
        $strTemplatePath = '/sysc/cmn/navigate/sysc_mail_navi.html';
        break;
    case "dataex":
        $strTemplatePath = '/search/cmn/navigate/dataex_navi.html';
        break;
    case "closed":
        $strTemplatePath = '/search/cmn/navigate/closed_navi.html';
        break;
    case "m":
        $strTemplatePath = '/search/cmn/navigate/m_navi.html';
        break;

}

// DBクローズ
$objDB->close();
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->strTemplateRoot = SRC_ROOT;
$objTemplate->getTemplate($strTemplatePath);

// テンプレート生成
$objTemplate->replace($aryData);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
//-------------------------------------------------------------------------

return true;
