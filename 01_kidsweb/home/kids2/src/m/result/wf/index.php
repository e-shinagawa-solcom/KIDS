<?
/** 
*	マスタ管理 ワークフロー順序マスタ マスターテーブル結果一覧画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID              -> index.php
// index.php -> lngWorkflowOrderCode      -> index.php
// index.php -> lngWorkflowOrderGroupCode -> index.php
// index.php -> lngInChargeCode           -> index.php
//
// 登録画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
//
// 修正画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngmonetaryratecode   -> edit.php
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

// 文字列チェック
$aryCheck["strSessionID"]              = "null:numenglish(32,32)";
$aryCheck["lngWorkflowOrderCode"]      = "number(0,2147483647)";
$aryCheck["lngWorkflowOrderGroupCode"] = "number(0,2147483647)";
$aryCheck["lngInChargeCode"]           = "number(0,2147483647)";

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
$objMaster->strTableName = "m_WorkflowOrder";

// 検索クエリ生成
$strQuery = "SELECT * FROM m_WorkflowOrder o, m_User u, m_Group g";

// ワークフロー順番コード条件生成
if ( $aryData["lngWorkflowOrderCode"] )
{
	$aryWhereString[] = " o.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"];
}

// ワークフロー順序グループコード条件生成
if ( $aryData["lngWorkflowOrderGroupCode"] )
{
	$aryWhereString[] = " o.lngWorkflowOrderGroupCode = " . $aryData["lngWorkflowOrderGroupCode"];
}

// 担当者コード条件生成
if ( $aryData["lngInChargeCode"] )
{
	$aryWhereString[] = " o.lngWorkflowOrderCode = ( SELECT o2.lngWorkflowOrderCode FROM m_WorkflowOrder o2 WHERE o2.lngInChargeCode = " . $aryData["lngInChargeCode"] . " AND o.lngWorkflowOrderCode = o2.lngWorkflowOrderCode )";
}

// 表示フラグがTRUEのものだけ表示
$aryWhereString[] = " o.bytWorkflowOrderDisplayFlag = TRUE";

// 属性テーブルとの結合
$aryWhereString[] = " o.lngInChargeCode = u.lngUserCode";
$aryWhereString[] = " o.lngWorkflowOrderGroupCode = g.lngGroupCode";

// 条件分を生成、クエリに追加
$strWhereString = join ( " AND", $aryWhereString );
$strQuery .= " WHERE " . $strWhereString . " ORDER BY o.lngWorkflowOrderCode, o.lngWorkflowOrderNo";


// データの取得とオブジェクトへのセット
$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );

if ( $lngResultNum )
{
	///////////////////////////////////////////////////////////////////
	// テーブル生成
	///////////////////////////////////////////////////////////////////
	// 結果行表示
	$count = 0;

	// bytGroupDisplayFlag 取得
	$aryGroupDisplayFlag = Array ( "t" => "表示", "f" => "非表示" );


	// lngWorkflowOrderCode 数、初期化
	$codeCount = -1;

	// レコード表示処理
	foreach ( $objMaster->aryData as $record )
	{
		// lngWorkflowOrderNo 数(まとめる行数)、インクリメント
		$noCount++;

		// 最初のワークフロー順序番号の場合、ワークフロー名とグループ名を表示
		if ( $record["lngworkfloworderno"] == 1 )
		{
			// lngWorkflowOrderCode 数、インクリメント
			$codeCount++;

			// lngWorkflowOrderNo 数(まとめる行数)、初期化
			$noCount = 0;

			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";

			// カラム生成
			$aryParts["strResultHtml"] .= "<th nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . ( $codeCount + 1 ) . "</th>\n";
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strworkflowordername"] ) . "</td>\n";
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strgroupdisplayname"] ) . "</td>\n";
		}

		// それ以外の場合、<tr>のみを表示
		else
		{
			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";
		}

		// カラムの表示
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lngworkfloworderno"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["struserdisplayname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lnglimitdays"] . "日間</td>\n";

		// 最初のワークフロー順序番号の場合、削除ボタンの表示
		if ( $record["lngworkfloworderno"] == 1 )
		{
			// GETで渡す文字列生成
			$getUrl = "strSessionID=" .$aryData["strSessionID"]. "&lngWorkflowOrderCode=" . $record["lngworkflowordercode"] . "&lngActionCode=" . DEF_ACTION_DELETE;

			// 削除ボタン生成
			$aryParts["strResultHtml"] .= "		<th bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/wf/confirm.php?$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'delete' );\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></th>\n";
		}

		$aryParts["strResultHtml"] .= "	</tr>\n";
		$aryCount[$record["lngworkflowordercode"]] = $record["lngworkfloworderno"];
	}


	$aryKeys = array_keys ( $aryCount );

	// ROWSPAN 埋め込み
	foreach ( $aryKeys as $lngworkflowordercode )
	{
		$aryParts["strResultHtml"] = preg_replace ( "/_%count$lngworkflowordercode%_/", $aryCount[$lngworkflowordercode], $aryParts["strResultHtml"] );
	}

}
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . ( count ( $objMaster->aryColumnName ) + 1 ) . ">結果無し。</th></tr>";
}


// カラム行HTML取得
$aryParts["strColumnHtml"] = $objMaster->getColumnHtmlTable( 5 );


$objDB->close();



$aryParts["HIDDEN"]           = "<input type=hidden name=strSessionID value=" .$aryData["strSessionID"] . ">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngWorkflowOrderCode value=" .$aryData["lngWorkflowOrderCode"]. ">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngWorkflowOrderGroupCode value=" .$aryData["lngWorkflowOrderGroupCode"]. ">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngInChargeCode value=" .$aryData["lngInChargeCode"]. ">\n";
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["lngColumnNum"]    = 5;
$aryParts["strEditURL"]      = "/m/regist/wf/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];

// HTML出力
$objTemplate = new clsTemplate();
//echo getArrayTable( $aryParts, "TABLE" );exit;
$objTemplate->getTemplate( "m/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
