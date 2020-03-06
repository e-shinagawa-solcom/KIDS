<?
/**
 *    マスタ管理 共通マスタ データ入力画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// 登録画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
//
// 修正画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
// index.php -> lngKeyCode            -> edit.php
// index.php -> (lngStockSubjectCode) -> edit.php
//
// 確認画面へ
// edit.php -> strSessionID          -> confirm.php
// edit.php -> lngActionCode         -> confirm.php
// edit.php -> strMasterTableName    -> confirm.php
// edit.php -> strKeyName            -> confirm.php
// edit.php -> lngKeyCode            -> confirm.php
// edit.php -> (lngStockSubjectCode) -> confirm.php

// 設定読み込み
include_once 'conf.inc';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "m/cmn/lib_m.php";

// DB接続
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

// POSTデータ取得
$aryData = $_GET;

// 仕入部品の場合に使用するlngStockSubjectCodeの成形
list($aryData["lngstocksubjectcode"], $i) = mb_split(":", $aryData["lngstocksubjectcode"]);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_M0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
$aryCheck["strKeyName"] = "ascii(1,32)";

// 文字列チェック
$aryCheckResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryCheckResult, $objDB);

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable($aryData["strMasterTableName"], $aryData["strKeyName"], $aryData[$aryData["strKeyName"]], array("lngstocksubjectcode" => $aryData["lngstocksubjectcode"]), $objDB);
$objMaster->setAryMasterInfo($aryData[$aryData["strKeyName"]], $aryData["lngstocksubjectcode"]);
// カラム数取得
$lngColumnNum = count($objMaster->aryColumnName);

//////////////////////////////////////////////////////////////////////////
// キーコードの表示処理
//////////////////////////////////////////////////////////////////////////
// 新規の場合、キーコードのシリアル取得
if ($aryData["lngActionCode"] == DEF_ACTION_INSERT) {
    // 仕入科目マスタ、仕入部品マスタ、国マスタ、組織マスタ以外は
    // シリアルにて新規コード発行
    if ($objMaster->strTableName != "m_StockSubject" && $objMaster->strTableName != "m_StockItem" && $objMaster->strTableName != "m_Country" && $objMaster->strTableName != "m_Organization") {
        // 戻ってきた際の処理
        if ($aryData[$aryData["strKeyName"]]) {
            $seq = $aryData[$aryData["strKeyName"]];
        } else {
            $seq = $objMaster->lngRecordRow;
        }

        $aryParts["MASTER"][0] = "<span class=\"InputSegs\"><input id=\"Input0\" type=\"text\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100 disabled></span>\n";

        $aryData["HIDDEN"] = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $seq . "\">\n";

    }

    // 仕入科目マスタ、仕入部品マスタ、国マスタは直接入力にて新規コード発行
    else {
        $aryParts["MASTER"][0] = "<span class=\"InputSegs\"><input id=\"Input0\" type=\"text\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $aryData[$objMaster->aryColumnName[0]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100></span>\n";
    }
}

// 登録以外の場合、キー入力項目にスモークを掛け、表示する
else {
    // キーコード表示
    $aryParts["MASTER"][0] = "<span class=\"InputSegs\"><input id=\"Input0\" type=\"text\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100 disabled></span>\n";
    $aryData["HIDDEN"] = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";
}

$aryData["COLUMN"] = "<span id=\"Column0\" class=\"ColumnSegs\"></span>\n";

// if ($objMaster->strTableName == "m_StockItem") {
//     $objMaster->aryColumnName = ['lngstockitemcode', 'lngstockclasscode', 'lngstocksubjectcode', 'strstockitemname', 'bytdisplayflag', 'bytinvalidflag', 'bytdisplayestimateflag', 'lngestimateareaclassno'];
// }
// 残りのカラム表示
for ($i = 1; $i < $lngColumnNum; $i++) {
    // 新規登録の場合
    if ($aryData["lngActionCode"] == DEF_ACTION_INSERT) {
        $aryParts["MASTER"][$i] = "<span class=\"InputSegs\"><input id=\"Input$i\" type=\"text\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . fncHTMLSpecialChars($aryData[$objMaster->aryColumnName[$i]]) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100></span>\n";
    }

    // 修正の場合
    else {
        $aryParts["MASTER"][$i] = "<span class=\"InputSegs\"><input id=\"Input$i\" type=\"text\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . fncHTMLSpecialChars($objMaster->aryData[0][$objMaster->aryColumnName[$i]]) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100></span>\n";
    }

    $aryData["COLUMN"] .= "<span id=\"Column$i\" class=\"ColumnSegs\"></span>\n";
}
/////////////////////////////////////////////////////////////////////////
// 仕入科目マスタ、または仕入部品マスタの場合、
// キーが2つのための特殊処理を行う
/////////////////////////////////////////////////////////////////////////
if ($objMaster->strTableName == "m_StockSubject" || $objMaster->strTableName == "m_StockItem") {
    // プルダウンメニュー取得
    list($aryParts["MASTER"][1], $hidden) = fncSpecialTableManage($aryData["lngActionCode"], $objMaster, $aryData, $objDB);
    $aryData["HIDDEN"] .= $hidden;
    // 仕入部品マスタだった場合、IDをずらす処理を行う
    if ($objMaster->strTableName == "m_StockItem") {
        $aryParts["MASTER"][2] = preg_replace("/Input2/", "Input3", $aryParts["MASTER"][2]);
        $aryParts["MASTER"][3] = preg_replace("/Input3/", "Input4", $aryParts["MASTER"][3]);
        $aryParts["MASTER"][4] = preg_replace("/Input4/", "Input5", $aryParts["MASTER"][4]);
        $aryParts["MASTER"][5] = preg_replace("/Input5/", "Input6", $aryParts["MASTER"][5]);
        $aryParts["MASTER"][6] = fncStockItemTableManage($aryData["lngActionCode"], $objMaster, $aryData, $objDB);
        // $aryParts["MASTER"][6] = preg_replace("/Input5/", "Input7", $aryParts["MASTER"][6]);
        $aryData["COLUMN"] .= "<span id=\"Column7\" class=\"ColumnSegs\"></span>\n";
    }
}

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );s
$count = count($aryParts["MASTER"]);
for ($i = 0; $i < $count; $i++) {
    $aryData["MASTER"] .= $aryParts["MASTER"][$i];
}

$objDB->close();

$aryData["lngLanguageCode"] = 1;
$aryData["strTableName"] = $objMaster->strTableName;

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("m/regist/c/edit.tmpl");
$objTemplate->replace($aryData);
$objTemplate->complete();
echo $objTemplate->strTemplate;

return true;
?>

<?

/////////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
 *    キーが2つのマスターのための特殊処理関数
 *
 *    @param  Long   $lngActionCode 処理コード
 *    @param  Object $objMaster     マスターテーブルオブジェクト
 *    @param  Array  $aryData       FORMデータ配列
 *    @param  Object $objDB         DBオブジェクト
 *    @return Array  $partsMaster   テンプレートデータ
 *    @access public
 */
// -----------------------------------------------------------------
function fncSpecialTableManage($lngActionCode, $objMaster, $aryData, $objDB)
{
    // メニュー初期化
    $strParts = "";
    $lngKeyNumber = 1;

    ////////////////////////////////////////////////////////////////
    // 第1プルダウン(仕入部品マスタのみ)
    ////////////////////////////////////////////////////////////////
    // 仕入部品の場合、仕入区分SELECT メニューを生成
    if ($objMaster->strTableName == "m_StockItem") {
        // 登録の場合、SELECTメニューの埋め込み
        if ($lngActionCode == DEF_ACTION_INSERT) {
            // $strParts = "<span class=\"InputSegs\"><select id=Input1 onChange=\"subLoadMasterOption( 'cnStockSubjectCode', this, document.forms[0]." . $objMaster->aryColumnName[1] . ", Array(this.value), objDataSourceSetting, 0 );\">\n";
			$strParts = "<span class=\"InputSegs\"><select id=Input1 onChange=\"fncGetStockSubject(document.forms[0]." . $objMaster->aryColumnName[1] . ", this.value);\">\n";
        }

        // 修正の場合、disabled のSELECTメニューの埋め込み
        elseif ($lngActionCode == DEF_ACTION_UPDATE) {
            $strParts = "<span class=\"InputSegs\"><select id=Input1 disabled>\n";
		}
        // 仕入科目マスタから区分コードを取得(直接フォームで持たないため逆引き)
        if ($aryData[$objMaster->aryColumnName[1]] != "") {
            $lngStockClassCode = fncGetMasterValue("m_StockSubject", "lngStockSubjectCode", "lngStockClassCode", $aryData[$objMaster->aryColumnName[1]], "", $objDB);
        }
        // 仕入区分マスタのSELECT メニューを生成
        $strParts .= "<option value=\"\"></option>\n";
        $strParts .= fncGetPulldown("m_StockClass", "lngStockClassCode", "lngStockClassCode || ':' || strStockClassName", $lngStockClassCode, "", $objDB);
        $strParts .= "</select></span>";

        $lngKeyNumber++;
    }

    ////////////////////////////////////////////////////////////////
    // 第2プルダウン
    ////////////////////////////////////////////////////////////////
    // 登録の場合、SELECTメニューの埋め込み
    if ($lngActionCode == DEF_ACTION_INSERT) {
        $strParts .= "<span class=\"InputSegs\"><select name=\"" . $objMaster->aryColumnName[1] . "\" id=Input$lngKeyNumber>\n";
    }

    // 修正の場合、HIDDEN と disabled のSELECTメニューの埋め込み
    elseif ($lngActionCode == DEF_ACTION_UPDATE) {
        $aryParts["HIDDEN"] = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[1]] . "\">\n";

        $strParts .= "<span class=\"InputSegs\"><select id=Input$lngKeyNumber disabled>\n";
    }

    // 仕入科目の場合の、SELECT メニュー
    if ($objMaster->strTableName == "m_StockSubject") {
        $strParts .= fncGetPulldown("m_StockClass", "lngStockClassCode", "lngStockClassCode || ':' || strStockClassName", $aryData[$objMaster->aryColumnName[1]], "", $objDB);
    }

    // 仕入部品の場合の、SELECT メニュー
    else {
        if ($lngStockClassCode > -1) {
            $lngStockClassCode = " WHERE lngStockClassCode = " . $lngStockClassCode;
        }
        $strParts .= fncGetPulldown("m_StockSubject", "lngStockSubjectCode", "lngStockSubjectCode || ':' || strStockSubjectName", $aryData[$objMaster->aryColumnName[1]], $lngStockClassCode, $objDB);
    }

    // SELECT メニューの閉じ
    $strParts .= "</select></span>\n";

    return array($strParts, $aryParts["HIDDEN"]);
}

/////////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
 *    仕入部品マスターのための特殊処理関数
 *
 *    @param  Object $objMaster     マスターテーブルオブジェクト
 *    @param  Array  $aryData       FORMデータ配列
 *    @param  Object $objDB         DBオブジェクト
 *    @return Array  $partsMaster   テンプレートデータ
 *    @access public
 */
// -----------------------------------------------------------------
function fncStockItemTableManage($lngActionCode, $objMaster, $aryData, $objDB)
{
	// 登録の場合、SELECTメニューの埋め込み
    if ($lngActionCode == DEF_ACTION_INSERT) {
        $lngestimateareaclassno = "";
    }

    // 修正の場合、HIDDEN と disabled のSELECTメニューの埋め込み
    elseif ($lngActionCode == DEF_ACTION_UPDATE) {
		$lngestimateareaclassno = $objMaster->aryData[0][$objMaster->aryColumnName[6]];
	}
    $strParts .= "<span class=\"InputSegs\"><select name=\"" . $objMaster->aryColumnName[6] . "\" id=Input7>\n";
    $strParts .= "<option value=\"\"></option>\n";
    $strParts .= fncGetPulldown("m_estimateareaclass", "lngestimateareaclassno", "lngestimateareaclassno || ':' || strestimateareaclassname", $lngestimateareaclassno, "", $objDB);
    $strParts .= "</select></span>";
    return $strParts;
}

?>
