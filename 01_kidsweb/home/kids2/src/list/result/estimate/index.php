<?
/** 
*	帳票出力 見積原価計算 検索結果画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// 検索結果画面( * は指定帳票のファイル名 )
// *.php -> strSessionID       -> index.php

// 印刷画面へ
// index.php -> strSessionID       -> index.php
// index.php -> lngReportCode      -> index.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

// 検索条件項目取得
if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
{
	$aryColumn = $aryData["SearchColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryColumn[$i]] = 1;
	}
	unset ( $aryData["SearchColumn"] );
	unset ( $aryColumn );
}


// 文字列チェック
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 見積原価帳票出力
// コピーファイル取得クエリ生成
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_ESTIMATE;

// 見積原価書取得クエリ生成
	// SELECT
	$aryQuery[] = "SELECT";

	$aryQuerySelect[] = " e.lngestimatestatuscode";
	$aryQuerySelect[] = " e.lngrevisionno";

	$aryQuerySelect[] = " e.lngEstimateNo AS strReportKeyCode";
	$aryQuerySelect[] = " e.strProductCode";
	$aryQuerySelect[] = " e.lngInputUserCode";
	$aryQuerySelect[] = " p.strProductName";
	$aryQuerySelect[] = " g.strGroupDisplayCode AS strInchargeGroupDisplayCode";
	$aryQuerySelect[] = " g.strGroupDisplayName AS strInchargeGroupDisplayName";
	$aryQuerySelect[] = " u1.strUserDisplayCode AS strInchargeUserDisplayCode";
	$aryQuerySelect[] = " u1.strUserDisplayName AS strInchargeUserDisplayName";
	$aryQuerySelect[] = " u2.strUserDisplayCode AS strInputUserDisplayCode";
	$aryQuerySelect[] = " u2.strUserDisplayName AS strInputUserDisplayName";

	$aryQuery[] = join ( ", ", $aryQuerySelect );
	unset ( $aryQuerySelect );

	// FROM
	$aryQuery[] = "FROM m_Estimate e";
	$aryQuery[] = " INNER JOIN m_Product p ON p.strProductCode       = e.strProductCode";
	$aryQuery[] = "  AND p.bytInvalidFlag = FALSE";
	$aryQuery[] = " LEFT OUTER JOIN m_Group g   ON p.lngInChargeGroupCode = g.lngGroupCode";
	$aryQuery[] = " LEFT OUTER JOIN m_User u1   ON p.lngInChargeUserCode  = u1.lngUserCode";
//	$aryQuery[] = "  AND u1.bytInvalidFlag = FALSE";
	$aryQuery[] = " INNER JOIN m_User u2   ON e.lngInputUserCode     = u2.lngUserCode";
//	$aryQuery[] = "  AND u2.bytInvalidFlag = FALSE";
	//$aryQuery[] = " LEFT OUTER JOIN m_Workflow w  ON w.strWorkflowKeyCode = e.lngEstimateNo";
	//$aryQuery[] = "  AND w.bytInvalidFlag = FALSE";
	//$aryQuery[] = " LEFT OUTER JOIN t_Workflow tw ON w.lngWorkflowCode = tw.lngWorkflowCode";
	//$aryQuery[] = "  AND lngWorkflowSubCode =";
	//$aryQuery[] = "   ( SELECT MAX(tw2.lngWorkflowSubCode) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode )";



/////////////////////////////////////////////////////////////////
// 検索条件
/////////////////////////////////////////////////////////////////
// 作成日時
if ( $aryData["dtmInsertDateConditions"] )
{
	if ( $aryData["dtmInsertDateFrom"] )
	{
		$aryQueryWhere[] = "date_trunc('day', e.dtmInsertDate ) >= '" . $aryData["dtmInsertDateFrom"] . "'";
	}
	if ( $aryData["dtmInsertDateTO"] )
	{
		$aryQueryWhere[] = "date_trunc('day', e.dtmInsertDate ) <= '" . $aryData["dtmInsertDateTo"] . "'";
	}
}

// 製品コード
if ( $aryData["strProductCodeConditions"] )
{
// 2004.10.09 suzukaze update start
	if ( $aryData["strProductCodeFrom"] )
	{
		$aryQueryWhere[] = "e.strProductCode >= '" . $aryData["strProductCodeFrom"] . "'";
	}
	if ( $aryData["strProductCodeTo"] )
	{
		$aryQueryWhere[] = "e.strProductCode <= '" . $aryData["strProductCodeTo"] . "'";
	}
// 2004.10.09 suzukaze update end
}

// 入力者
if ( $aryData["lngInputUserCodeConditions"] && $aryData["strInputUserDisplayCode"] )
{
	$aryQueryWhere[] = "u2.strUserDisplayCode = '" . $aryData["strInputUserDisplayCode"] . "'";
}

// 部門
if ( $aryData["lngInChargeGroupCodeConditions"] && $aryData["strInChargeGroupDisplayCode"] )
{
	$aryQueryWhere[] = "g.strGroupDisplayCode = '" . $aryData["strInChargeGroupDisplayCode"] . "'";
}
// 担当者
if ( $aryData["lngInChargeUserCodeConditions"] && $aryData["strInChargeUserDisplayCode"] )
{
	$aryQueryWhere[] = "u1.strUserDisplayCode = '" . $aryData["strInChargeUserDisplayCode"] . "'";
}


// WHERE
// $aryQueryWhere[] = "e.lngEstimateStatusCode = " . DEF_ESTIMATE_APPROVE;

$aryQueryWhere[] = " e.lngRevisionNo = ( SELECT MAX ( e2.lngRevisionNo ) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo )";

$aryQueryWhere[] = " 0 <= ( SELECT MIN ( e3.lngRevisionNo ) FROM m_Estimate e3 WHERE e.lngEstimateNo = e3.lngEstimateNo )";
//$aryQueryWhere[] = "w.lngFunctionCode = " . DEF_FUNCTION_E0;
//$aryQueryWhere[] = "tw.lngWorkflowStatusCode = " . DEF_STATUS_APPROVE;





// A:「発注」状態より大きい状態の発注データ
// B:「発注」状態のデータ
// C:ワークフローに存在しない(即認証案件)
// D:「承認」状態にある案件
// A OR ( B AND ( C OR D ) )
$aryQuery[] = " AND (";

// A:「承認」状態より大きい状態の発注データ
$aryQuery[] = "  e.lngestimatestatuscode > " . 0;

$aryQuery[] = "  OR";
$aryQuery[] = "  (";

// B:「承認」状態のデータ
$aryQuery[] = "    e.lngestimatestatuscode = " . 2;
$aryQuery[] = "     AND";
$aryQuery[] = "    (";

// C:ワークフローに存在しない(即認証案件)
$aryQuery[] = "      0 = ";
$aryQuery[] = "      (";
$aryQuery[] = "        SELECT COUNT ( mw.lngWorkflowCode ) ";
$aryQuery[] = "        FROM m_Workflow mw ";
$aryQuery[] = "        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = e.lngestimateno";
$aryQuery[] = "         AND mw.lngFunctionCode = " . DEF_FUNCTION_E1;
$aryQuery[] = "      )";

// D:「承認」状態にある案件
$aryQuery[] = "      OR " . DEF_STATUS_APPROVE . " = ";
$aryQuery[] = "      (";
$aryQuery[] = "        SELECT tw.lngWorkflowStatusCode";
$aryQuery[] = "        FROM m_Workflow mw2, t_Workflow tw";
$aryQuery[] = "        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = e.lngestimateno";
$aryQuery[] = "         AND mw2.lngFunctionCode = " . DEF_FUNCTION_E1;
$aryQuery[] = "         AND tw.lngWorkflowSubCode =";
$aryQuery[] = "        (";
$aryQuery[] = "          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode";
$aryQuery[] = "        )";
$aryQuery[] = "         AND mw2.lngWorkflowCode = tw.lngWorkflowCode";
$aryQuery[] = "      )";
$aryQuery[] = "    )";
$aryQuery[] = "  )";
$aryQuery[] = ")";


$aryQuery[] = "AND e.lngestimatestatuscode != " . DEF_ESTIMATE_DENIAL;



$aryQuery[] = " WHERE " . join ( " AND ", $aryQueryWhere );
unset ( $aryQueryWhere );
$aryQuery[] = "ORDER BY p.strProductCode DESC";

// ナンバーをキーとする連想配列に帳票コードを取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strCopyQuery, $objDB );

for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
}

if ( $lngResultNum > 0 )
{
	$objDB->freeResult( $lngResultID );
}



$strQuery = join ( "\n", $aryQuery );

//require( LIB_DEBUGFILE );
//fncDebug( 'lib_list_estimate.txt', $strQuery, __FILE__, __LINE__);

// 帳票データ取得クエリ実行・テーブル生成
list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
unset ( $aryQuery );

for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	$aryParts["strResult"] .= "<tr class=\"Segs\">\n";

	$aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";

	$aryParts["strResult"] .= "<td align=center>";

	// コピーファイルパスが存在している場合、コピー帳票出力ボタン表示
	if ( $aryReportCode[$objResult->strreportkeycode] != NULL )
	{
		// コピー帳票出力ボタン表示
		$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
	}

	$aryParts["strResult"] .= "</td>\n<td align=center>";

	// コピーファイルパスが存在しない または コピー解除権限がある場合、
	// 帳票出力ボタン表示
	if ( $aryReportCode[$objResult->strreportkeycode] == NULL || fncCheckAuthority( DEF_FUNCTION_LO4, $objAuth ) )
	{
		// 帳票出力ボタン表示
		$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
	}

	$aryParts["strResult"] .= "</td></tr>\n";

	unset ( $strCopyCheckboxObject );
}



$aryParts["strColumn"] = "
					<td id=\"Column0\" nowrap>製品コード</td>
					<td id=\"Column1\" nowrap>製品名称</td>
					<td id=\"Column2\" nowrap>入力者</td>
					<td id=\"Column3\" nowrap>部門</td>
					<td id=\"Column4\" nowrap>担当者</td>
					<td id=\"Column5\" nowrap>COPY プレビュー</td>
					<td id=\"Column6\" nowrap>プレビュー</td>
";

$aryParts["strListType"] = "estimate";
$aryParts["HIDDEN"] = getArrayTable( $aryData, "HIDDEN" );


$objDB->close();

$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "list/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
