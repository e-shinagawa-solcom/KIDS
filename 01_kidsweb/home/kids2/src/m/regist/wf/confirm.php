<?
/** 
*	マスタ管理 ワークフロー順序マスタ 確認画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 新規登録
// edit.php -> strSessionID              -> confirm.php
// edit.php -> lngActionCode             -> confirm.php
// edit.php -> strWorkflowOrderName      -> confirm.php
// edit.php -> lngWorkflowOrderGroupCode -> confirm.php
// edit.php -> strOrderData              -> confirm.php
//
// 削除
// index.php -> strSessionID         -> confirm.php
// index.php -> lngActionCode        -> confirm.php
// index.php -> lngWorkflowOrderCode -> confirm.php
//
// 実行
// confirm.php -> strSessionID              -> action.php
// confirm.php -> lngActionCode             -> action.php
// confirm.php -> lngWorkflowOrderCode      -> action.php
// confirm.php -> strWorkflowOrderName      -> action.php
// confirm.php -> lngWorkflowOrderGroupCode -> action.php
// confirm.php -> strOrderData              -> action.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GETデータ取得
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

if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	$aryCheck["strWorkflowOrderName"]      = "null:length(0,100)";
	$aryCheck["lngWorkflowOrderGroupCode"] = "null:number(0,2147483647)";
	$aryCheck["strOrderData"]              = "null:ascii(1,100)";
}
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	$aryCheck["lngWorkflowOrderCode"]      = "null:number(0,2147483647)";
}

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// 登録 かつ エラーがない 場合、
// 新規登録、修正チェック実行
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && !join ( $aryCheckResult ) )
{
	// グループデータ取得
	$strQuery = "SELECT * " .
                "FROM m_GroupRelation gr, m_AuthorityGroup ag, m_User u " .
                "WHERE gr.lngGroupCode = " . $aryData["lngWorkflowOrderGroupCode"] .
                " AND u.lngUserCode = gr.lngUserCode" .
                " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 結果件数がない場合、エラー
	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 9056, DEF_WARNING, "ユーザーがいません。", TRUE, "", $objDB );
	}

	// ユーザーコードをキーとする連想配列にユーザー権限レベルをセット
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryAuthorityLevel[$objResult->lngusercode] = $objResult->lngauthoritylevel;
		$aryUserName[$objResult->lngusercode] = $objResult->struserdisplayname;
	}

	$objDB->freeResult( $lngResultID );

	// ユーザーの順番が有効かどうかのチェック
	// A.グループ所属チェック
	// B.重複チェック
	// C.並び順の有効性(権限チェック)

	// 順番登録データ を '&' で分解
	$aryOrderData = explode ( "&", $aryData["strOrderData"] );
	$lngOrderDataLength = count ( $aryOrderData ) - 1;

	// '=' で分解し、ユーザーコード、期限日数を配列にセット
	for ( $i = 0; $i < $lngOrderDataLength; $i++ )
	{
		$aryOrderSubData = explode ( "=", $aryOrderData[$i] );

		// A.グループ所属チェック
		// 入力されたユーザーコードがグループに属していなかった場合エラー
		if ( $aryAuthorityLevel[$aryOrderSubData[0]] == "" )
		{
			fncOutputError ( 9056, DEF_WARNING, "指定ユーザーは指定グループに含まれていません。", TRUE, "", $objDB );
		}

		// B.重複チェック
		// より以前の配列値に同じユーザーコードが存在した場合重複エラー
		$count = count ( $aryUserCode );
		for ( $j = 0; $j < $count; $j++ )
		{
			if ( $aryUserCode[$j] == $aryOrderSubData[0] )
			{
				fncOutputError ( 9056, DEF_WARNING, "ユーザーが重複しています。", TRUE, "", $objDB );
			}
		}

		$aryUserCode[$i]  = $aryOrderSubData[0];
		$aryLimitDays[$i] = $aryOrderSubData[1];
	}

	// C.並び順の有効性(権限チェック)
	$count = count ( $aryUserCode ) - 1;
	for ( $i = 0; $i < $count; $i++ )
	{
		if ( $aryAuthorityLevel[$aryUserCode[$i]] < $aryAuthorityLevel[$aryUserCode[$i + 1]] )
		{
			fncOutputError ( 9056, DEF_WARNING, "権限の低いユーザーが高いユーザーよりも前に承認者として登録されています。", TRUE, "", $objDB );
		}
	}
}

// 削除 かつ エラーがない 場合、
// 削除チェック実行
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( $aryCheckResult ) )
{
	$strQuery = "SELECT * " .
                "FROM t_Workflow t, m_Workflow w " .
                "WHERE w.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"] .
                " AND t.lngWorkflowSubCode =" .
                "(" .
                "  SELECT MAX(t2.lngWorkflowSubCode)" .
                "  FROM t_Workflow t2, m_Workflow w2" .
                "  WHERE t.lngWorkflowCode = t2.lngWorkflowCode" .
                "   AND t2.lngWorkflowCode = w2.lngWorkflowCode" .
                ")" .
                " AND t.lngWorkflowStatusCode = 1" .
                " AND t.lngWorkflowCode = w.lngWorkflowCode";


	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 結果が1件でもあった場合、削除不可能とし、エラー出力
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
	}

	// 削除対象表示のためのデータを取得
	$strQuery = "SELECT * " .
                "FROM m_WorkflowOrder wo, m_User u " .
                "WHERE wo.lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"] .
                " AND wo.lngInChargeCode = u.lngUserCode " .
                "ORDER BY wo.lngWorkflowOrderNo";

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	// ワークフロー順番名取得
	$aryData["strWorkflowOrderName"] = $objMaster->aryData[0]["strworkflowordername"];

	// ワークフローグループコード取得
	$aryData["lngWorkflowOrderGroupCode"] = $objMaster->aryData[0]["lngworkflowordergroupcode"];

	$count = count ( $objMaster->aryData );

	for ( $i = 0; $i < $count; $i++ )
	{
		$aryData["strOrderData"] .= $objMaster->aryData[$i]["lngusercode"] . "=" . $objMaster->aryData[$i]["lnglimitdays"] . "&";
		$aryUserName[$objMaster->aryData[$i]["lngusercode"]] = $objMaster->aryData[$i]["struserdisplayname"];
	}

	$objMaster = new clsMaster();

	// 順番登録データ を '&' で分解
	$aryOrderData = explode ( "&", $aryData["strOrderData"] );
	$lngOrderDataLength = count ( $aryOrderData ) - 1;
}

// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["lngActionCode"]   =& $aryData["lngActionCode"];
$aryParts["strTableName"]    =  "m_WorkflowOrder";
$aryParts["strKeyName"]      =  "lngWorkflowOrderCode";
$aryParts["lngKeyCode"]      =& $aryData["lngWorkflowOrderCode"];
$aryParts["strSessionID"]    =& $aryData["strSessionID"];


// lngWorkflowOrderGroupCode の(CODE+NAME)取得
$aryGroupCode = fncGetMasterValue( "m_Group", "lngGroupCode", "strGroupDisplayCode || ':' || strGroupDisplayName", "Array", "", $objDB );

list ( $lngUserCode, $lngLimitDays ) = explode ( "=", $aryOrderData[0] );

$aryParts["MASTER"]  = "				<tr><td id=\"Column0\" class=\"SegColumn\"></td><td class=\"Segs\" align=\"left\">" . fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] ) . "</td></tr>";
$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\"></td><td class=\"Segs\" align=\"left\">" . fncHTMLSpecialChars( $aryGroupCode[$aryData["lngWorkflowOrderGroupCode"]] ) . "</td></tr>\n";

$aryParts["HIDDEN"]  = "<input type=\"hidden\" name=\"strWorkflowOrderName\" value=\"" . fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] ) . "\">\n";

$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngWorkflowOrderGroupCode\" value=\"" . $aryData["lngWorkflowOrderGroupCode"] . "\">\n";

for ( $i = 0; $i < $lngOrderDataLength; $i++ )
{
	list ( $lngUserCode, $lngLimitDays ) = explode ( "=", $aryOrderData[$i] );

	$aryParts["MASTER"] .= "				<tr><td class=\"SegColumn\">" . ( $i + 1 ) . "</td><td class=\"Segs\" align=\"left\">$aryUserName[$lngUserCode] : $lngLimitDays 日間</td></tr>\n";
}

$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strOrderData\" value=\"" . $aryData["strOrderData"] . "\">\n";

$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngWorkflowOrderCode\" value=\"" . $aryData["lngWorkflowOrderCode"] . "\">\n";




if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">";
	echo "<form action=/m/regist/wf/edit.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/wf/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
/*
	echo "<form><table border>";
	echo "<input type=hidden name=strSessionID value=" . $aryData["strSessionID"] . ">";
	echo "<input type=hidden name=lngActionCode value=" . $aryData["lngActionCode"] . ">";
	echo $aryParts["HIDDEN"];
	echo $aryParts["MASTER"];
	echo "</table><input type=button value=BACK onClick=\"document.forms[0].action='edit.php';document.forms[0].submit();\"><input type=button value=SUBMIT onClick=\"document.forms[0].action='action.php';document.forms[0].submit();\"></form>";
*/
}


$objDB->close();


return TRUE;
?>


