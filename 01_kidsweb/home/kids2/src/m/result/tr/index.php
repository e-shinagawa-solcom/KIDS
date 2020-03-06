<?
/** 
*	マスタ管理 想定レートマスタ マスターテーブル結果一覧画面
*
*	@package   KIDS
*	@license   http://www.solcom.co.jp/ 
*	@copyright Copyright &copy; 2019, Solcom 
*	@author    solcom rin
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID        -> index.php
// index.php -> lngmonetaryunitcode -> index.php
// index.php -> now                 -> index.php
//
// 登録画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
//
// 修正画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngmonetaryunitcode   -> edit.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;
// $aryData = $_GET;

// 文字列チェック
$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngmonetaryunitcode"] = "number(0,2147483647)";
$aryCheck["dtmapplystartdate"]   = "date(/)";
$aryCheck["dtmapplyenddate"]   = "date(/)";


$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->strTableName = "m_TemporaryRate";

// 検索クエリ生成
$strQuery = "SELECT * FROM m_TemporaryRate";

// 通貨単位コード条件生成
if ( $aryData["lngmonetaryunitcode"] )
{
	$aryWhereString[] = " lngmonetaryunitcode = " . $aryData["lngmonetaryunitcode"];
}

// 適用開始月条件生成
if ( $aryData["dtmapplystartdate"] )
{
	$aryWhereString[] = " dtmapplystartdate >= " . $aryData["dtmapplystartdate"];
}

// 適用終了月条件生成
if ( $aryData["dtmapplyenddate"] )
{
	$aryWhereString[] = "dtmapplyenddate <= " . $aryData["dtmapplystartdate"];
}

// 条件分を生成、クエリに追加
if ( $aryWhereString && count ( $aryWhereString ) )
{
	$strWhereString = join ( " AND", $aryWhereString );
	$strQuery .= " WHERE " . $strWhereString;
}

$strQuery .= " ORDER BY lngmonetaryunitcode, dtmapplystartdate DESC";

// データの取得とオブジェクトへのセット
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// カラム関連データの取得とセット
$lngColumnNum = $objDB->getFieldsCount ( $lngResultID );

for ( $i = 0; $i < $lngColumnNum; $i++ )
{
	// カラム名の読み込みとセット
	$objMaster->aryColumnName[$i] = pg_field_name ( $lngResultID, $i);

	// 型の読み込みとセット
	$objMaster->aryType[$i]       = pg_field_type ( $lngResultID, $i);
}

if ( $lngResultNum )
{
	// データの読み込みとセット
	$objMaster->aryData = pg_fetch_all ( $lngResultID );

	///////////////////////////////////////////////////////////////////
	// テーブル生成
	///////////////////////////////////////////////////////////////////
	// 結果行表示
	$count = 0;

	// lngmonetaryunitcode のプルダウンメニュー(CODE+NAME)取得
	$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngmonetaryunitcode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );

	$dtmNowDate = date ( "Y-m-d" );

	// レコード表示処理
	foreach ( $objMaster->aryData as $record )
	{
		$count++;

		// 現在の年月日が含まれるレコードの場合、背景色を変えて表示
		if ( $record[$objMaster->aryColumnName[2]] <= $dtmNowDate && $record[$objMaster->aryColumnName[3]] >= $dtmNowDate )
		{
			$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#99CCff;\">\n";
		}
		else
		{
			$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";
		}
		// カラム生成
		$aryParts["strResultHtml"] .= "		<th>$count</th>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryMonetaryUnitCode[$record["lngmonetaryunitcode"]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["curconversionrate"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["dtmapplystartdate"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["dtmapplyenddate"] . "</td>\n";

		// GETで渡す文字列生成
		$getUrl = "strSessionID=" .$aryData["strSessionID"]
				. "&lngmonetaryunitcode=" . $record["lngmonetaryunitcode"]
				. "&dtmapplystartdate=" . $record["dtmapplystartdate"];
// echo $lngResultNum;
// return;

		// 過去のレートの場合、修正ボタンを非表示
		if ( $record[$objMaster->aryColumnName[3]] < $dtmNowDate )
		{
			// 修正ボタン非表示
			$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap></td>\n";
		}

		else
		{
			// 修正ボタン生成
			// $aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/tr/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";
			$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"/m/regist/tr/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&" . $getUrl ."\" name=\"fix\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";
		}


		$aryParts["strResultHtml"] .= "	</tr>\n";

	}
}

// 結果がなかった場合、結果無しの表示
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . 6 . ">結果無し。</th></tr>";
}




$objDB->close();



$aryParts["strSessionID"] = $aryData["strSessionID"];
$aryParts["lngmonetaryunitcode"] = $aryData["lngmonetaryunitcode"];
$aryParts["dtmapplystartdate"] = $aryData["dtmapplystartdate"];
$aryParts["dtmapplyenddate"]  = $aryData["dtmapplyenddate"];
$aryParts["strTableName"]   = $objMaster->strTableName;
$aryParts["strEditURL"]     = "/m/regist/tr/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/result/tr/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
