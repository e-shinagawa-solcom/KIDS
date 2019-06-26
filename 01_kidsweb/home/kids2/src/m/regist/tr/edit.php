<?
/** 
*	マスタ管理 想定レートマスタ データ入力画面
*
*	@package   KIDS
*	@license   http://www.solcom.co.jp/ 
*	@copyright Copyright &copy; 2019, Solcom 
*	@author    solcom rin 
*	@access    public
*	@version   1.00
*
*/
// 登録画面
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
//
// 修正画面
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
// index.php -> lngmonetaryunitcode -> edit.php
// index.php -> dtmapplystartdate   -> edit.php
//
// 確認画面へ
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";

if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
	$aryCheck["dtmapplystartdate"]   = "null:date(/)";
}

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// $aryDataKeys = array_keys ( $aryData );
// foreach ( $aryDataKeys as $key ) {
// 	if(strstr($key, 'ERROR_MESSAGE')) {
// 		fncOutputError ( 9052, DEF_WARNING, $aryData[$key], TRUE, "", $objDB );
// 	}
// }


// エラーがない場合、マスターオブジェクト生成、文字列チェック実行
if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
//////////////////////////////////////////////////////////////////////////
// キーコードの表示処理
//////////////////////////////////////////////////////////////////////////
// 新規の場合、キー入力項目生成
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// 想定レートマスタから行データとカラム名取得
	$objMaster->setMasterTable( "m_TemporaryRate", "lngmonetaryunitcode", NULL, Array (), $objDB );
	// 通貨単位
	$aryParts["lngmonetaryunitname"] = fncGetPulldown( "m_MonetaryUnit", "lngmonetaryunitcode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", $aryData["lngmonetaryunitcode"], "WHERE lngmonetaryunitcode > 1", $objDB );
	$aryParts["lngmonetaryunitcode_disabled"] = "";
	$aryParts["dtmapplystartdate_disabled"] = "";
}

// 登録以外の場合、キー入力項目にスモークを掛ける
elseif ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	// 想定レートマスタから指定行データとカラム名取得
	$objMaster->setMasterTable( "m_TemporaryRate", "lngmonetaryunitcode", $aryData["lngmonetaryunitcode"], Array ( "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
	// 通貨単位
	$aryParts["lngmonetaryunitname"] = fncGetPulldown( "m_MonetaryUnit", "lngmonetaryunitcode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", $objMaster->aryData[0][$objMaster->aryColumnName[0]], "", $objDB );
	// 適用開始月
	$aryParts["dtmapplystartdate"] = $objMaster->aryData[0][$objMaster->aryColumnName[2]];
	// 換算レート
	$aryParts["curconversionrate"] = $objMaster->aryData[0][$objMaster->aryColumnName[1]];
	// 適用終了月
	$aryParts["dtmapplyenddate"] = $objMaster->aryData[0][$objMaster->aryColumnName[3]];

	$aryParts["lngmonetaryunitcode_disabled"] = "disabled";
	$aryParts["dtmapplystartdate_disabled"] = "disabled";
	$aryParts["HIDDEN"]  = "<input type=\"hidden\" name=\"lngmonetaryunitcode\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";
	$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"dtmapplystartdate\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[2]] . "\">\n";

}

if (isset($aryData["dtmapplystartdate"]) && isset($aryData["dtmapplyenddate"]) && isset($aryData["curconversionrate"])) {
	$aryParts["dtmapplystartdate"] = $aryData["dtmapplystartdate"];
	$aryParts["dtmapplyenddate"] = $aryData["dtmapplyenddate"];
	$aryParts["curconversionrate"] = $aryData["curconversionrate"];
}

// 検索クエリ生成
$strQuery = "SELECT lngmonetaryunitcode, curconversionrate, dtmapplystartdate, dtmapplyenddate FROM m_MonetaryRate";
$strQuery .= " WHERE lngMonetaryRateCode = '" . DEF_MONETARYCLASS_SHANAI . "'";
$strQuery .= " AND lngmonetaryunitcode = '" . DEF_MONETARY_USD . "'";
$strQuery .= " ORDER BY dtmapplystartdate DESC, dtmapplyenddate DESC";

// データの取得とオブジェクトへのセット
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
    // 結果行表示
    $count = 0;
    // 取得
    for( $i = 0; $i < $lngResultNum; $i++ )
    {
        $count++;
        $objFetch	= $objDB->fetchObject( $lngResultID, $i );
        $aryParts["monetaryRateHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );applyMonetaryRate( this );\" style=\"background:#ffffff;\">\n";
        $aryParts["monetaryRateHtml"] .= "		<td nowrap>" . $objFetch->dtmapplystartdate . "</td>\n";
        $aryParts["monetaryRateHtml"] .= "		<td nowrap>" . $objFetch->dtmapplyenddate . "</td>\n";
        $aryParts["monetaryRateHtml"] .= "		<td nowrap>" . $objFetch->curconversionrate . "</td>\n";
        $aryParts["monetaryRateHtml"] .= "	</tr>\n";
        if ($count == 6) {
        	break;
		}
    }
} else {
    fncOutputError(9061, DEF_ERROR, "通貨レートマスタの取得に失敗しました。", TRUE, "", $objDB);
}

// cookieセット
setcookie("strSessionID", $aryData["strSessionID"]);

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////

$objDB->close();

$aryParts["strTableName"] = $objMaster->strTableName;
$aryParts["strSessionID"] = $aryData["strSessionID"];
$aryParts["lngActionCode"] = $aryData["lngActionCode"];
$aryParts["lngmonetaryunitcode"] = $aryData["lngmonetaryunitcode"];
// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/tr/edit.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
