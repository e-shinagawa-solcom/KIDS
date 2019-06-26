<?
/** 
*	ユーザー管理 実行画面
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.04.14	ユーザーが顧客であった場合にユーザーの表示・非表示を切り替えられないバグの修正
*
*/
// confirm.php -> strSessionID           -> action.php
// confirm.php -> lngFunctionCode        -> action.php
// confirm.php -> bytInvalidFlag         -> action.php
// confirm.php -> lngUserCode            -> action.php
// confirm.php -> strUserID              -> action.php
// confirm.php -> strPassword            -> action.php
// confirm.php -> strPasswordCheck       -> action.php
// confirm.php -> strMailAddress         -> action.php
// confirm.php -> bytMailTransmitFlag    -> action.php
// confirm.php -> strUserDisplayCode     -> action.php
// confirm.php -> strUserDisplayName     -> action.php
// confirm.php -> strUserFullName        -> action.php
// confirm.php -> lngAttributeCode       -> action.php
// confirm.php -> lngCompanyCode         -> action.php
// confirm.php -> lngGroupCode           -> action.php
// confirm.php -> lngAuthorityGroupCode  -> action.php
// confirm.php -> lngAccessIPAddressCode -> action.php
// confirm.php -> strNote                -> action.php
// confirm.php -> strUserImageFileName   -> action.php

include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_POST;
//echo getArrayTable( $aryData, "TABLE" );exit;

// bytInvalidFlag の Boolean 化
if ( $aryData["bytInvalidFlag"] == "checked" )
{
	$aryData["bytInvalidFlag"] = "FALSE";
}
else
{
	$aryData["bytInvalidFlag"] = "TRUE";
}

// bytMailTransmitFlag の Boolean 化
if ( $aryData["bytMailTransmitFlag"] == "checked" )
{
	$aryData["bytMailTransmitFlag"] = "TRUE";
}
else
{
	$aryData["bytMailTransmitFlag"] = "FALSE";
}

// bytUserDisplayFlag の Boolean 化
if ( $aryData["bytUserDisplayFlag"] == "checked" )
{
	$aryData["bytUserDisplayFlag"] = "TRUE";
}
else
{
	$aryData["bytUserDisplayFlag"] = "FALSE";
}



$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC5 . ")";
$aryCheck["bytInvalidFlag"]         = "null:english(4,5)";
$aryCheck["strUserID"]              = "null:numenglish(0,32767)";
$aryCheck["bytMailTransmitFlag"]    = "null:english(4,5)";
$aryCheck["bytUserDisplayFlag"]     = "null:english(4,5)";
$aryCheck["strUserDisplayCode"]     = "null:numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "null:length(0,120)";
$aryCheck["strUserFullName"]        = "null:length(0,120)";
$aryCheck["lngCompanyCode"]         = "null:number(0,32767)";
$aryCheck["lngGroupCode"]           = "null:ascii(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "null:number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "null:number(-1,32767)";
$aryCheck["strNote"]                = "length(0,1000)";

// メール配信許可フラグがたっている場合メール入力必須
if ( $aryData["bytMailTransmitFlag"] == "TRUE" )
{
	$aryCheck["strMailAddress"] = "null:mail";
}
else
{
	$aryCheck["strMailAddress"] = "mail";
}


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
//////////////////////////////////////////////////////////////////////////
// ユーザー設定の場合
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 && fncCheckAuthority( DEF_FUNCTION_UC1, $objAuth ) )
{
	$aryData["lngUserCode"]           = $objAuth->UserCode;
	$aryData["lngUserCodeConditions"] = 1;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["strUserIDDisabled"]             = "disabled";
	$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
}

//////////////////////////////////////////////////////////////////////////
// ユーザー登録の場合
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
{
	$aryCheck["strPassword"]        = "null:ascii(0,32767)";
	$aryCheck["strPasswordCheck"]   = "null:ascii(0,32767)";

	if ( $aryData["strPassword"] != $aryData["strPasswordCheck"] )
	{
		fncOutputError ( 1102, DEF_WARNING, "パスワードがミスマッチしています。", TRUE, "", $objDB );
	}
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayID"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayID"], "", $objDB );

	$aryData["lngUserCode_Error"]         = $aryError["lngUserCode"];
	$aryData["strUserID_Error"]           = $aryError["strUserID"];
	$aryData["strUserDisplayCode_Error"]  = $aryError["strUserDisplayCode"];
	$aryData["lngUserCode_Error_Message"] = $aryErrorMessage["lngUserCode"];
	$aryData["strUserID_Error_Message"]   = $aryErrorMessage["strUserID"];
	$aryData["strUserDisplayCode_Error_Message"] = $aryErrorMessage["strUserDisplayCode"];
}

//////////////////////////////////////////////////////////////////////////
// ユーザー修正の場合
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && fncCheckAuthority( DEF_FUNCTION_UC5, $objAuth ) )
{
	$aryData["lngUserCodeConditions"] = 1;
}

//////////////////////////////////////////////////////////////////////////
// それ以外(権限ERROR)
//////////////////////////////////////////////////////////////////////////
else
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


//////////////////////////////////////////////////////////////////////////
// 属性が「顧客」だった場合の特殊処理
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngAttributeCode"] > 0 )
{
	// 強制設定
	$aryData["bytInvalidFlag"]                = "TRUE";
	$aryData["bytMailTransmitFlag"]           = "FALSE";
// 2004.04.14 suzukaze update start
//	$aryData["bytUserDisplayFlag"]            = "TRUE";
// 2004.04.14 suzukaze update end
	$aryData["lngAuthorityGroupCode"]         = 6;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["bytMailTransmitFlagDisabled"]   = "disabled";
// 2004.04.14 suzukaze update start
//	$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
// 2004.04.14 suzukaze update end
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	if ( !$aryData["lngGroupCode"] )
	{
		$aryData["lngGroupCode"]              = ":0";
	}
}

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );
//exit;

$objDB->transactionBegin();

// ユーザー設定、ユーザー修正の場合、現状のユーザーデータ取得
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// ユーザー管理
	// 案件読み込み、検索、詳細情報取得クエリ関数
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 1107, DEF_WARNING, "", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryUserData["bytInvalidFlag"]         = $objResult->bytinvalidflag;
	$aryUserData["lngUserCode"]            = $objResult->lngusercode;
	$aryUserData["strUserID"]              = $objResult->struserid;
	$aryUserData["bytMailTransmitFlag"]    = $objResult->bytmailtransmitflag;
	$aryUserData["strMailAddress"]         = $objResult->strmailaddress;
	$aryUserData["bytUserDisplayFlag"]     = $objResult->bytuserdisplayflag;
	$aryUserData["strUserDisplayCode"]     = $objResult->struserdisplaycode;
	$aryUserData["strUserDisplayName"]     = $objResult->struserdisplayname;
	$aryUserData["strUserFullName"]        = $objResult->struserfullname;
	$aryUserData["lngCompanyCode"]         = $objResult->lngcompanycode;
	$aryUserData["strCompanyName"]         = $objResult->strcompanyname;
	$aryUserData["lngGroupCode"]           = $objResult->lnggroupcode;
	$aryUserData["strGroupName"]           = $objResult->strgroupname;
	$aryUserData["lngAuthorityGroupCode"]  = $objResult->lngauthoritygroupcode;
	$aryUserData["strAuthorityGroupName"]  = $objResult->strauthoritygroupname;
	$aryUserData["lngAccessIPAddressCode"] = $objResult->lngaccessipaddresscode;
	$aryUserData["strAccessIPAddress"]     = $objResult->straccessipaddress;
	$aryUserData["strUserImageFileName"]   = $objResult->struserimagefilename;
	$aryUserData["strNote"]                = $objResult->strnote;

	// フラグの変換
	if ( $aryUserData["bytInvalidFlag"] == "t" )
	{
		$aryUserData["bytInvalidFlag"] = "TRUE";
	}
	else
	{
		$aryUserData["bytInvalidFlag"] = "FALSE";
	}

	if ( $aryUserData["bytMailTransmitFlag"] == "t" )
	{
		$aryUserData["bytMailTransmitFlag"] = "TRUE";
	}
	else
	{
		$aryUserData["bytMailTransmitFlag"] = "FALSE";
	}

	if ( $aryUserData["bytUserDisplayFlag"] == "t" )
	{
		$aryUserData["bytUserDisplayFlag"] = "TRUE";
	}
	else
	{
		$aryUserData["bytUserDisplayFlag"] = "FALSE";
	}

	$strConpanySelectWhere = "AND lngCompanyCode = " . $aryData["lngCompanyCode"];

	$objDB->freeResult( $lngResultID );

	// ユーザー設定の場合、変更不可項目を強制設定
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 )
	{
		$aryData["strUserID"]             = $aryUserData["strUserID"];
		$aryData["bytInvalidFlag"]        = $aryUserData["bytInvalidFlag"];
		$aryData["bytUserDisplayFlag"]    = $aryUserData["bytUserDisplayFlag"];
		$aryData["lngAuthorityGroupCode"] = $aryUserData["lngAuthorityGroupCode"];
	}

	// ユーザー重複チェック
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( $aryData["lngUserCode"], $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], $aryUserData["lngUserCode"], $aryUserData["strUserID"], $aryUserData["lngCompanyCode"], $aryUserData["strUserDisplayCode"], "UPDATE", $objDB );
	if ( $bytErrorFlag )
	{
		$aryData["lngUserCode_Error"]        = $aryError["lngUserCode"];
		$aryData["strUserID_Error"]          = $aryError["strUserID"];
		$aryData["strUserDisplayCode_Error"] = $aryError["strUserDisplayCode"];
		$aryData["lngUserCode_Error_Message"] = $aryErrorMessage["lngUserCode"];
		$aryData["strUserID_Error_Message"]   = $aryErrorMessage["strUserID"];
		$aryData["strUserDisplayCode_Error_Message"] = $aryErrorMessage["strUserDisplayCode"];

		$lngErrorCount += $bytErrorFlag;
	}

	////////////////////////////////////////////////////////////////
	// グループ変更チェック
	////////////////////////////////////////////////////////////////
	// 入力されたグループコードの配列を生成
	$aryGroupCode = explode ( ":", $aryData["lngGroupCode"] );
	array_shift ( $aryGroupCode );
	$lngGroupCodeNum = count ( $aryGroupCode );

	// DBに登録されているグループコードを取得し、配列にセット
	$strQuery = "SELECT lngGroupCode FROM m_GroupRelation WHERE lngUserCode = " . $aryUserData["lngUserCode"] . " ORDER BY lngGroupCode\n";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryGroupCodeOriginal[$i] = $objResult->lnggroupcode;
	}

	$objDB->freeResult( $lngResultID );
	$lngGroupCodeOriginalNum = count ( $aryGroupCodeOriginal );

	// 所属を解除したグループコードを取得し、配列にセット
	// もともとの所属グループ分、ループ
	for ( $i = 0; $i < $lngGroupCodeOriginalNum; $i++ )
	{
		// 今回入力されたグループ分、ループ
		for ( $j = 0; $j < $lngGroupCodeNum; $j++ )
		{
			// 入力されたグループが、もともと所属していたかどうかを比較
			if ( $aryGroupCode[$j] == $aryGroupCodeOriginal[$i] )
			{
				$flgDeleteArray = 1;
				break 1;
			}
		}

		// 存在していなかった場合、
		// ワークフローチェック対象としてクエリWHERE句生成
		if ( !$flgDeleteArray )
		{
			$aryGroupCodeDelete[] = " lngWorkflowOrderGroupCode = $aryGroupCodeOriginal[$i]";
			$flgUpdate = 1; // 変更フラグ
		}
		// 存在フラグ初期化
		$flgDeleteArray = 0;
	}

	// 所属を離れたグループがワークフローオーダーに存在していた場合、エラー
	/*
	if ( $flgUpdate )
	{
		$strQuery = "SELECT lngWorkflowOrderGroupCode FROM m_WorkflowOrder WHERE lngInChargeCode = " . $aryUserData["lngUserCode"] . " AND (" . join ( " OR", $aryGroupCodeDelete ) . ") AND bytWorkflowOrderDisplayFlag = TRUE\n";
		list ( $lngResultId, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum )
		{
			$objDB->freeResult( $lngResultID );
			fncOutputError ( 1103, DEF_WARNING, "", TRUE, "", $objDB );
		}
		else
		{
			$aryQuery[0] = "DELETE FROM m_GroupRelation WHERE lngUserCode = " . $aryUserData["lngUserCode"] . " AND (" . join ( " OR", $aryGroupCodeDelete ) . ")\n";
			$aryQuery[0] = preg_replace ( "/lngWorkflowOrderGroupCode/", "lngGroupCode", $aryQuery[0] );
		}
	}
	*/

	// ユーザー修正の場合、ログイン許可・権限グループ変更チェック
	// ログイン許可を外し、ワークフロー順番に含まれていてた場合
	// または
	// 権限グループを変更し、ワークフロー順番に含まれていた場合
	// エラーとする
	/*
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && ( ( $aryData["bytInvalidFlag"] != $aryUserData["bytInvalidFlag"] && $aryData["bytInvalidFlag"] == "TRUE" ) || ( $aryData["lngAuthorityGroupCode"] != $aryUserData["lngAuthorityGroupCode"] && ( $aryData["lngAuthorityGroupCode"] > 2 && $aryData["lngAuthorityGroupCode"] < 6 ) ) ) )
	{
		$strQuery = "SELECT lngWorkflowOrderCode FROM m_WorkflowOrder WHERE lngInChargeCode = " . $aryData["lngUserCode"];
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$aryData["bytInvalidFlag_Error"] = "visibility:visible;";
			$objDB->freeResult( $lngResultID );
			$bytErrorFlag = TRUE;
		}
	}
	*/

	// ユーザー修正の場合、ユーザーID変更チェック
	// ユーザーIDが変更された場合、ログイン状態にあった場合、エラー
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && $aryData["strUserID"] != $aryUserData["strUserID"] )
	{
		$strQuery = "SELECT date_trunc('second', l.dtmLoginTime ) AS remaining," .
	                " c.strValue AS timeout " .
	                "FROM t_LoginSession l, m_CommonFunction c " .
	                "WHERE l.strLoginUserID = '" . $aryUserData["strUserID"] . "'" .
	                " AND l.bytSuccessfulFlag = true" .
	                " AND c.strClass = 'timeout'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			// セッションを保持するユーザーのIDを取得
			$objResult = $objDB->fetchObject( $lngResultID, 0 );

			if ( time() - strtotime ( $objResult->remaining ) > $objResult->timeout * 60 )
			{
				$aryData["strUserID_Error"] = "visibility:visible;";
				$bytErrorFlag = TRUE;
			}
			$objDB->freeResult( $lngResultID );
		}
	}


	// 追加グループのクエリ生成
	for ( $i = 0; $i < $lngGroupCodeNum; $i++ )
	{
		for ( $j = 0; $j < $lngGroupCodeOriginalNum; $j++ )
		{
			if ( $aryGroupCode[$i] == $aryGroupCodeOriginal[$j] )
			{
				$flgInsertArray = 1;
				break 1;
			}
		}
		if ( !$flgInsertArray )
		{
			$lngGroupRelationCode = fncGetSequence( "m_GroupRelation.lngGroupRelationCode", $objDB );
			$aryQuery[] = "INSERT INTO m_GroupRelation VALUES ( $lngGroupRelationCode, " . $aryData["lngUserCode"] . ", $aryGroupCode[$i], FALSE )";
			$flgUpdate = 1; // 変更フラグ
		}
		$flgInsertArray = 0;
	}

	// デフォルトグループの設定
	//if ( $flgUpdate )
	//{
		// グループ関連マスタのロック
		$aryQuery[] = "SELECT * FROM m_GroupRelation WHERE lngUserCode = " . $aryData["lngUserCode"] . " FOR UPDATE";

		// ユーザーの属するグループをすべてFALSEに
		$aryQuery[] = "UPDATE m_GroupRelation SET bytdefaultflag = FALSE WHERE lngUserCode = " . $aryData["lngUserCode"];
		// $aryGroupCode[0] のグループをTRUEに
		$aryQuery[] = "UPDATE m_GroupRelation SET bytdefaultflag = TRUE WHERE lngUserCode = " . $aryData["lngUserCode"] . " AND lngGroupCode = $aryGroupCode[0]";
	//}

	///////////////////////////////////////////////////////////////////////
	// 一般パラメーター変更処理
	///////////////////////////////////////////////////////////////////////
	// ログイン許可フラグ
	if ( $aryData["bytInvalidFlag"] != $aryUserData["bytInvalidFlag"] )
	{
		$aryUpdate[] = "bytInvalidFlag = " . $aryData["bytInvalidFlag"];
	}

	// ユーザーID
	if ( $aryData["strUserID"] != $aryUserData["strUserID"] )
	{
		$aryUpdate[] = "strUserID = '" . $aryData["strUserID"] . "'";
	}

	// パスワード
	if ( $aryData["strPassword"] )
	{
		$aryUpdate[] = "strPasswordHash = '" . $aryData["strPassword"] . "'";
	}

	// メールアドレス
	if ( $aryData["strMailAddress"] != $aryUserData["strMailAddress"] )
	{
		$aryUpdate[] = "strMailAddress = '" . $aryData["strMailAddress"] . "'";
	}

	// メール配信許可フラグ
	if ( $aryData["bytMailTransmitFlag"] != $aryUserData["bytMailTransmitFlag"] )
	{
		$aryUpdate[] = "bytMailTransmitFlag = " . $aryData["bytMailTransmitFlag"];
	}

	// ユーザー表示フラグ
	if ( $aryData["bytUserDisplayFlag"] != $aryUserData["bytUserDisplayFlag"] )
	{
		$aryUpdate[] = "bytUserDisplayFlag = '" . $aryData["bytUserDisplayFlag"] . "'";
	}

	// ユーザー表示コード
	if ( $aryData["strUserDisplayCode"] != $aryUserData["strUserDisplayCode"] )
	{
		$aryUpdate[] = "strUserDisplayCode = '" . $aryData["strUserDisplayCode"] . "'";
	}

	// ユーザー表示名
	if ( $aryData["strUserDisplayName"] != $aryUserData["strUserDisplayName"] )
	{
		$aryUpdate[] = "strUserDisplayName = '" . $aryData["strUserDisplayName"] . "'";
	}

	// ユーザーフルネーム
	if ( $aryData["strUserFullName"] != $aryUserData["strUserFullName"] )
	{
		$aryUpdate[] = "strUserFullName = '" . $aryData["strUserFullName"] . "'";
	}

	// 会社名
	if ( $aryData["lngCompanyCode"] != $aryUserData["lngCompanyCode"] )
	{
		$aryUpdate[] = "lngCompanyCode = " . $aryData["lngCompanyCode"];
	}

	// 権限コード
	if ( $aryData["lngAuthorityGroupCode"] != $aryUserData["lngAuthorityGroupCode"] )
	{
		$aryUpdate[] = "lngAuthorityGroupCode = " . $aryData["lngAuthorityGroupCode"];
	}

	// アクセスIPアドレスコード
	if ( $aryData["lngAccessIPAddressCode"] != $aryUserData["lngAccessIPAddressCode"] )
	{
		$aryUpdate[] = "lngAccessIPAddressCode = " . $aryData["lngAccessIPAddressCode"];
	}

	// ユーザーイメージファイル
	if ( $aryData["strUserImageFileName"] && $aryData["strUserImageFileName"] != $aryUserData["strUserImageFileName"] )
	{
		$aryUpdate[] = "strUserImageFileName = '" . $aryData["strUserImageFileName"] . "'";
	}

	// 備考
	if ( $aryData["strNote"] != $aryUserData["strNote"] )
	{
		$aryUpdate[] = "strNote = '" . $aryData["strNote"] . "'";
	}

	// m_User UPDATE クエリ生成
	if ( is_array($aryUpdate) && count ( $aryUpdate ) )
	{
		// ユーザーマスタロック
		$aryQuery[] = "SELECT * FROM m_User WHERE lngUserCode = " . $aryData["lngUserCode"] . " FOR UPDATE";
		$aryQuery[] = "UPDATE m_User SET " . join ( ", ", $aryUpdate ) . " WHERE lngUserCode = " . $aryData["lngUserCode"];
	}

//	echo "<h1>UPDATE</h1>";
}

// ユーザー登録の場合、INSERT
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 )
{
	// ユーザー重複チェック(登録されるユーザーIDのチェック)
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], "", $objDB );

	if ( $bytErrorFlag )
	{
		fncOutputError ( 1101, DEF_ERROR, "重複", TRUE, "", $objDB );
	}

	// ユーザーコード、登録クエリ生成
	$aryData["lngUserCode"] = fncGetSequence( "m_User.lngUserCode", $objDB );
	$aryQuery[0] = "INSERT INTO m_User VALUES (" .
                   "  $aryData[lngUserCode]," .
                   "  $aryData[lngCompanyCode]," .
                   "  $aryData[lngAuthorityGroupCode]," .
                   " '$aryData[strUserID]'," .
                   " '$aryData[strPassword]'," .
                   " '$aryData[strUserFullName]'," .
                   "  $aryData[bytMailTransmitFlag]," .
                   " '$aryData[strMailAddress]'," .
                   "  $aryData[bytUserDisplayFlag]," .
                   " '$aryData[strUserDisplayCode]'," .
                   " '$aryData[strUserDisplayName]'," .
                   "  $aryData[bytInvalidFlag]," .
                   "  $aryData[lngAccessIPAddressCode]," .
                   " '$aryData[strUserImageFileName]'," .
                   " '$aryData[strMyPageInfo]'," .
                   " '$aryData[strNote]' )";

	$aryGroupCode = explode ( ":", $aryData["lngGroupCode"] );

	// デフォルトグループ登録
	$lngGroupRelationCode = fncGetSequence( "m_GroupRelation.lngGroupRelationCode", $objDB );
	$aryQuery[1] = "INSERT INTO m_GroupRelation VALUES ( $lngGroupRelationCode, $aryData[lngUserCode], $aryGroupCode[1], TRUE )";

	// その他のグループ登録
	for ( $i = 2; $i < count ( $aryGroupCode ); $i++ )
	{
		$lngGroupRelationCode = fncGetSequence( "m_GroupRelation.lngGroupRelationCode", $objDB );
		$aryQuery[$i] = "INSERT INTO m_GroupRelation VALUES ( $lngGroupRelationCode, $aryData[lngUserCode], $aryGroupCode[$i], FALSE )";
	}

//	echo "<h1>INSERT</h1>";
}


//////////////////////////////////////////////////////////////////////////
// 画像UPLOAD
//////////////////////////////////////////////////////////////////////////
if ( $aryData["strUserImageFileName"] )
{
	if ( !copy ( USER_IMAGE_TMPDIR . $aryData["strUserImageFileName"], USER_IMAGE_DIR . $aryData["strUserImageFileName"] ) )
	{
		fncOutputError ( 1106, DEF_FATAL, "", TRUE, "", $objDB );
	}
	if ( !unlink ( USER_IMAGE_TMPDIR . $aryData["strUserImageFileName"] ) )
	{
		fncOutputError ( 1106, DEF_FATAL, "", TRUE, "", $objDB );
	}
}



//////////////////////////////////////////////////////////////////////////
// クエリ実行(ユーザー追加)
//////////////////////////////////////////////////////////////////////////
for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
//	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();

// echo "<a href=\"javascript:opener.window.location.reload();window.close();\">CLOSE</a>";
// echo "<a href=\"javascript:location='/menu/menu.php?strSessionID=$aryData[strSessionID]';\">MENU</a>";
// echo getArrayTable( $aryData, "TABLE" );


//////////////////////////////////////////////////////////////////////////
// メール送信(ユーザー追加)
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && $aryData["lngAttributeCode"] < 1 && $aryData["bytInvalidFlag"] == "FALSE" && $aryData["bytMailTransmitFlag"] == "TRUE" )
{
	list ( $strSubject, $strBody ) = fncGetMailMessage( 1102, $aryData, $objDB );
	$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );
	if ( !$aryData["strMailAddress"] || !mail ( $aryData["strMailAddress"], $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" ) )
	{
		fncOutputError ( 9053, DEF_WARNING, "メール送信失敗。", TRUE, "", $objDB );
	}
//	echo "メール送信:$aryData[strMailAddress]";
}

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////


// ユーザー情報の場合
if( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 )
{
	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/uc/regist/edit.php?strSessionID=".$aryData["strSessionID"]."&lngFunctionCode=1101";

	echo fncGetReplacedHtml( "uc/regist/finish1.tmpl", $aryData, $objAuth );
}
// ユーザー登録の場合
elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 )
{

	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/uc/regist/edit.php?strSessionID=".$aryData["strSessionID"]."&lngFunctionCode=1102";

	echo fncGetReplacedHtml( "uc/regist/finish1.tmpl", $aryData, $objAuth );
}
// ユーザー修正の場合
elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// 成功時戻り先のアドレス指定 （意味無し。削除予定）
	$aryData["strAction"] = "/uc/search/index.php?strSessionID=";
	$aryData["strSessionID"] = $aryData["strSessionID"];


	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "uc/regist/finish.tmpl" );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}

$objDB->close();


return TRUE;
?>
