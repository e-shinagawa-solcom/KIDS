<?
/** 
*	マスタ管理 ワークフロー順序マスタ 完了画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
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
fncPutStringCheckError( $aryCheckResult, $objDB );



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

	// 登録処理(INSERT)

	$lngWorkflowOrderCode = fncGetSequence( "m_WorkflowOrder.lngWorkflowOrderCode", $objDB );

	// 状態コード デフォルト1 に設定
	$lngWorkflowStatusCode = 1;

	// 登録の数だけINSERTクエリ生成
	for ( $i = 0; $i < $lngOrderDataLength; $i++ )
	{
		// ユーザーコード、期限を分解
		list ( $lngUserCode, $lngLimitDays ) = explode ( "=", $aryOrderData[$i] );

		// 最終承認者の場合、状態コードを2に設定
		if ( $i == ( $lngOrderDataLength - 1 ) )
		{
			$lngWorkflowStatusCode = 2;
		}

		$aryQuery[] = "INSERT INTO m_WorkflowOrder VALUES ( $lngWorkflowOrderCode, " . ( $i + 1 ) . ", '" . $aryData["strWorkflowOrderName"] . "', $lngWorkflowStatusCode, $lngUserCode, $lngLimitDays, " . $aryData["lngWorkflowOrderGroupCode"] . ", TRUE )";
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

	// 削除処理(bytWorkflowDisplayFlag を FALSE)
	$aryQuery[] = "UPDATE m_WorkflowOrder SET bytWorkflowOrderDisplayFlag = FALSE WHERE lngWorkflowOrderCode = " . $aryData["lngWorkflowOrderCode"];
}




////////////////////////////////////////////////////////////////////////////
// クエリ実行
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();


$objDB->close();



//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
echo getArrayTable( $aryData, "HIDDEN" );
echo "<script language=javascript>window.returnValue=true;window.close();</script>";



return TRUE;
?>


