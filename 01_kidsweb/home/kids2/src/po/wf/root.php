<?php

// ----------------------------------------------------------------------------
/**
*       共通  承認ルート
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
*         ・各登録および修正画面上の承認ルート一覧を生成
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

if ( $_POST )
{
	$aryData = $_POST;
}
else
{
	$aryData = $_GET;
}

// 文字列チェック
$aryCheck["strSessionID"]              = "null:numenglish(32,32)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->strTableName = "m_WorkflowOrder";

$lngUserCode = $objAuth->UserCode;

// ユーザーの属するグループを含むワークフロー順序
// かつ(EXCEPT)
// ユーザーが属するワークフロー順番 または ユーザー以上の権限をもつユーザーの属するワークフロー順序
// 以上の条件のワークフローコードを取得するクエリ生成
$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
$aryQuery[] = "FROM m_WorkflowOrder w, m_GroupRelation gr ";
$aryQuery[] = "WHERE gr.lngUserCode = $lngUserCode ";
$aryQuery[] = " AND w.lngWorkflowOrderGroupCode = gr.lngGroupCode ";
$aryQuery[] = "EXCEPT ";
$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
$aryQuery[] = "FROM m_WorkflowOrder w, m_User u, m_AuthorityGroup ag ";
$aryQuery[] = "WHERE w.lngInChargeCode = $lngUserCode ";
$aryQuery[] = " OR ag.lngAuthorityLevel > ";
$aryQuery[] = "(";
$aryQuery[] = "  SELECT ag2.lngAuthorityLevel";
$aryQuery[] = "  FROM m_User u2, m_AuthorityGroup ag2";
$aryQuery[] = "  WHERE u2.lngUserCode = $lngUserCode";
$aryQuery[] = "   AND u2.lngAuthorityGroupCode = ag2.lngAuthorityGroupCode";
$aryQuery[] = ")";
$aryQuery[] = " AND w.lngInChargeCode = u.lngUserCode";
$aryQuery[] = " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode ";
$aryQuery[] = "GROUP BY w.lngworkflowordercode ";

list ( $lngResultID, $lngResultNum ) = fncQuery( join ( "", $aryQuery ), $objDB );

if ( $lngResultNum > 0 )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryWhereQuery[] = "lngWorkflowOrderCode = " . $objResult->lngworkflowordercode;
	}

	unset ( $aryQuery );

	$aryQuery[] = "SELECT wo.lngWorkflowOrderCode, wo.strWorkflowOrderName, g.strGroupDisplayName, wo.lngWorkflowOrderNo, u3.strUserDisplayName, wo.lngLimitDays ";
	$aryQuery[] = "FROM m_WorkflowOrder wo, m_Group g, m_User u3 ";
	$aryQuery[] = "WHERE ";
	$aryQuery[] = "(";
	$aryQuery[] = join ( " OR ", $aryWhereQuery );
	$aryQuery[] = ")";
	$aryQuery[] = " AND wo.bytWorkflowOrderDisplayFlag = TRUE";
	$aryQuery[] = " AND wo.lngWorkflowOrderGroupCode = g.lngGroupCode";
	$aryQuery[] = " AND wo.lngInChargeCode = u3.lngUserCode ";
	$aryQuery[] = "ORDER BY wo.lngWorkflowOrderCode, wo.lngWorkflowOrderNo";

	unset ( $aryWhereQuery );

	$strQuery = join ( "", $aryQuery );

	// データの取得とオブジェクトへのセット
	$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );
}

unset ( $aryQuery );

if ( $lngResultNum > 0 )
{
	///////////////////////////////////////////////////////////////////
	// テーブル生成
	///////////////////////////////////////////////////////////////////
	// 結果行表示
	$count = 0;

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

			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\">\n";
			 //$aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";

			// カラム生成
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strworkflowordername"] ) . "</td>\n";
			$aryParts["strResultHtml"] .= "		<td nowrap rowspan=_%count" . $record["lngworkflowordercode"] . "%_>" . fncHTMLSpecialChars( $record["strgroupdisplayname"] ) . "</td>\n";
		}

		// それ以外の場合、<tr>のみを表示
		else
		{
			 $aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\">\n";
			 //$aryParts["strResultHtml"] .= "	<tr id=\"TD" . $codeCount . "_" . $noCount . "\" class=\"Segs\" onclick=\"fncSelectSomeTrColor( this , 'TD" . $codeCount . "_' , _%count" . $record["lngworkflowordercode"] . "%_ );\" style=\"background:" . $record["strgroupdisplaycolor"] . ";\">\n";
		}

		// カラムの表示
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lngworkfloworderno"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["struserdisplayname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["lnglimitdays"] . "日間</td>\n";

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
for ( $i = 0; $i < 5; $i++ )
{
	$aryParts["strColumnHtml"] .= "		<td id=\"Column$i\" nowrap>Column$i</td>\n";
}



$objDB->close();



$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["lngColumnNum"]    = 5;

// HTML出力
$objTemplate = new clsTemplate();
//echo getArrayTable( $aryParts, "TABLE" );exit;
$objTemplate->getTemplate( "/po/wf/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
