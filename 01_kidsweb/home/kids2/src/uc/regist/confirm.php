<?
/** 
*	ユーザー管理 確認画面
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.04.14	ユーザーが顧客であった場合に、表示・非表示を切り替えられないバグの修正
*
*/
// edit.php -> strSessionID           -> confirm.php
// edit.php -> lngFunctionCode        -> confirm.php
// edit.php -> bytInvalidFlag         -> confirm.php
// edit.php -> lngUserCode            -> confirm.php
// edit.php -> strUserID              -> confirm.php
// edit.php -> strPassword            -> confirm.php
// edit.php -> strPasswordCheck       -> confirm.php
// edit.php -> strMailAddress         -> confirm.php
// edit.php -> bytMailTransmitFlag    -> confirm.php
// edit.php -> strUserDisplayCode     -> confirm.php
// edit.php -> strUserDisplayName     -> confirm.php
// edit.php -> strUserFullName        -> confirm.php
// edit.php -> lngAttributeCode       -> confirm.php
// edit.php -> lngCompanyCode         -> confirm.php
// edit.php -> lngGroupCode           -> confirm.php
// edit.php -> lngAuthorityGroupCode  -> confirm.php
// edit.php -> lngAccessIPAddressCode -> confirm.php
// edit.php -> strNote                -> confirm.php
// edit.php -> strUserImageFileName   -> confirm.php

// 実行へ
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


// 設定読み込み
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


$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC5 . ")";
$aryCheck["bytInvalidFlag"]         = "english(1,7)";
$aryCheck["strUserID"]              = "null:numenglish(0,32767)";
$aryCheck["strUserDisplayCode"]     = "null:numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "null:length(0,120)";
$aryCheck["strUserFullName"]        = "null:length(0,120)";
$aryCheck["bytUserDisplayFlag"]     = "english(1,7)";
$aryCheck["bytMailTransmitFlag"]    = "english(1,7)";
$aryCheck["lngGroupCode"]           = "null:ascii(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "null:number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "null:number(-1,32767)";
$aryCheck["strNote"]                = "length(0,1000)";

// 会社のみエラーメッセージを変える特殊処理
$aryCheck["lngCompanyCode"]     = "null:number(0,32767,必須項目が入力されていません)";

$aryData["bytInvalidFlag_Error"]     = "visibility:hidden;";
$aryData["lngUserCode_Error"]        = "visibility:hidden;";
$aryData["strUserID_Error"]          = "visibility:hidden;";
$aryData["strPassword_Error"]        = "visibility:hidden;";
$aryData["strPasswordCheck_Error"]   = "visibility:hidden;";
$aryData["strMailAddress_Error"]     = "visibility:hidden;";
$aryData["strUserDisplayCode_Error"] = "visibility:hidden;";
$aryData["strUserDisplayName_Error"] = "visibility:hidden;";
$aryData["strUserFullName_Error"]    = "visibility:hidden;";
$aryData["lngCompanyCode_Error"]     = "visibility:hidden;";
$aryData["lngGroupCode_Error"]       = "visibility:hidden;";
$aryData["strNote_Error"]            = "visibility:hidden;";


// bytMailTransmitFlag の Boolean 化
if ( $aryData["bytMailTransmitFlag"] == "t" )
{
	$aryCheck["strMailAddress"]      = "null:mail";
}
else
{
	$aryCheck["strMailAddress"]      = "mail";
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
	$aryCheck["lngUserCode"]          = "null:number(0,32767)";
	$aryData["lngUserCodeConditions"] = 1;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["strUserIDDisabled"]             = "disabled";
	$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	unset ( $aryCheck["bytInvalidFlag"] );
	unset ( $aryCheck["strUserID"] );
	unset ( $aryCheck["bytUserDisplayFlag"] );
	unset ( $aryCheck["lngAuthorityGroupCode"] );
	unset ( $aryCheck["lngAccessIPAddressCode"] );
}

//////////////////////////////////////////////////////////////////////////
// ユーザー登録の場合
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
{
	// 顧客でなければパスワード必須
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && $aryData["lngAttributeCode"] < 1 )
	{
		$aryCheck["strPassword"]        = "null:password(0,64)";
		$aryCheck["strPasswordCheck"]   = "null:password(0,64)";
	}
	else
	{
		$aryCheck["strPassword"]        = "password(0,64)";
		$aryCheck["strPasswordCheck"]   = "password(0,64)";
	}

	// ユーザーコードはなくて良い
	$aryData["lngUserCode_Error"] = "visibility:hidden;";
}

//////////////////////////////////////////////////////////////////////////
// ユーザー修正の場合
//////////////////////////////////////////////////////////////////////////
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && fncCheckAuthority( DEF_FUNCTION_UC5, $objAuth ) )
{
	$aryData["lngUserCodeConditions"]  = 1;
	$aryData["lngGroupCodeConditions"] = 0;
	$aryCheck["lngUserCode"]          = "null:number(0,32767)";

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
	unset ( $aryData["bytInvalidFlag"] );
	unset ( $aryData["bytMailTransmitFlag"] );
	$aryData["lngAuthorityGroupCode"]         = 6;
	$aryData["bytInvalidFlagDisabled"]        = "disabled";
	$aryData["bytMailTransmitFlagDisabled"]   = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	if ( !$aryData["lngGroupCode"] )
	{
		$aryData["lngGroupCode"]              = ":0";
	}

	// 入力状況チェック(なければ自動生成処理へ)
	if ( ( !$aryData["strUserID"] || !$aryData["strUserDisplayCode"] || !$aryData["strPassword"] ) && $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 )
	{
		// ID自動生成処理(無限ループを避けるため20固定)
		for ( $i = 0; $i  < 20; $i++ )
		{
			// ユーザーID入力がなかったら自動生成
			if ( !$aryData["strUserID"] )
			{
				$aryData["strUserID"]          = "guest" . sprintf ( "%05d", fncGetSequence( "m_User.strUserID", $objDB ) );
			}

			// 表示ユーザーコード入力がなかったら自動生成
			if ( !$aryData["strUserDisplayCode"] )
			{
				$aryData["strUserDisplayCode"] = sprintf ( "%03x", fncGetSequence( "m_User.strUserDisplayCode", $objDB ) );
			}

			// パスワード入力がなかったら自動生成
			if ( !$aryData["strPassword"] )
			{
				$aryData["strPassword"]        = sprintf ( "%.6s", MD5 ( $aryData["strUserID"] ) );
				$aryData["strPasswordCheck"]   = $aryData["strPassword"];
			}

			// 重複チェック
			list ( $bytErrorFlag, $a, $a ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], "", $objDB );

			if ( !$bytErrorFlag )
			{
				break;
			}
		}
	}
}

$lngErrorCount += $bytErrorFlag;

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );


//////////////////////////////////////////////////////////////////////////
// ユーザー登録の場合のユーザーチェック
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && $aryData["lngCompanyCode"] )
{
	list ( $bytErrorFlag, $aryError, $aryErrorMessage ) = checkUniqueUser( 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], 0, $aryData["strUserID"], $aryData["lngCompanyCode"], $aryData["strUserDisplayCode"], "", $objDB );
	$aryData["lngUserCode_Error"]         = $aryError["lngUserCode"];
	$aryData["strUserID_Error"]           = $aryError["strUserID"];
	$aryData["strUserDisplayCode_Error"]  = $aryError["strUserDisplayCode"];
	$aryData["lngUserCode_Error_Message"] = $aryErrorMessage["lngUserCode"];
	$aryData["strUserID_Error_Message"]   = $aryErrorMessage["strUserID"];
	$aryData["strUserDisplayCode_Error_Message"] = $aryErrorMessage["strUserDisplayCode"];

	$lngErrorCount += $bytErrorFlag;
}


// パスワード暗号化
if ( $aryData["strPassword"] )
{
	$aryData["strPassword"]      = MD5 ( $aryData["strPassword"] );
	$aryData["strPasswordCheck"] = MD5 ( $aryData["strPasswordCheck"] );
}

//////////////////////////////////////////////////////////////////////////
// ユーザー設定、ユーザー修正の場合、現状のユーザーデータ取得
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// ユーザー管理
	// 案件読み込み、検索、詳細情報取得クエリ関数
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 1101, DEF_WARNING, "ユーザーがいません。", TRUE, "", $objDB );
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
	$aryUserData["strNote"]                = $objResult->strnote;
	$aryUserData["strUserImageFileName"]   = $objResult->struserimagefilename;

	$strConpanySelectWhere = "AND lngCompanyCode = " . $aryData["lngCompanyCode"];

	$objDB->freeResult( $lngResultID );

	// ログ認許フラグの変換
	if ( $aryUserData["bytInvalidFlag"] == "f" )
	{
		$aryUserData["bytInvalidFlag"] = "checked";
	}
	else
	{
		$aryUserData["bytInvalidFlag"] = "";
	}

	// ユーザー設定の場合、変更不可項目を強制設定
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 )
	{
		$aryData["strUserID"]              = $aryUserData["strUserID"];
		$aryData["bytInvalidFlag"]         = $aryUserData["bytInvalidFlag"];
		$aryData["bytUserDisplayFlag"]     = $aryUserData["bytUserDisplayFlag"];
		$aryData["lngAuthorityGroupCode"]  = $aryUserData["lngAuthorityGroupCode"];
		$aryData["lngAccessIPAddressCode"] = $aryUserData["lngAccessIPAddressCode"];
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

	// グループ変更チェック
	// 入力されたグループコードの配列を生成
	$aryGroupCode = explode ( ":", $aryData["lngGroupCode"] );
	array_shift ( $aryGroupCode );
	$lngGroupCodeNum = count ( $aryGroupCode );

	// DBに登録されているグループコードを取得
	$strQuery = "SELECT lngGroupCode FROM m_GroupRelation WHERE lngUserCode = " . $aryUserData["lngUserCode"] . " ORDER BY lngGroupCode\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryGroupCodeOriginal[$i] = $objResult->lnggroupcode;
	}
	$lngGroupCodeOriginalNum = count ( $aryGroupCodeOriginal );

	// 所属を解除したグループコードを取得
	// もともとの所属グループ分、ループ
	for ( $i = 0; $i < $lngGroupCodeOriginalNum; $i++ )
	{
		// 今回入力されたグループ分、ループ
		for ( $j = 0; $j < $lngGroupCodeNum; $j++ )
		{
			// 入力されたグループが、もともと所属していたかどうかを比較
			if ( $aryGroupCode[$j] == $aryGroupCodeOriginal[$i] )
			{
				$flgMatchDelete = 1;
				break 1;
			}
		}

		// 存在していなかった場合、
		// ワークフローチェック対象としてクエリWHERE句生成
		if ( !$flgMatchDelete )
		{
			$aryGroupCodeDelete[] = " lngWorkflowOrderGroupCode = $aryGroupCodeOriginal[$i]";
			$flgMatchArray  = 1;
		}

		// 存在フラグ初期化
		$flgMatchDelete = 0;
	}
	$objDB->freeResult( $lngResultID );
}


// パスワードチェック
if ( $aryData["strPassword"] && $aryData["strPasswordCheck"] && $aryData["strPassword"] != $aryData["strPasswordCheck"] )
{
	$lngErrorCount++;
	$aryData["strPassword_Error"]              = "visibility:visible;";
	$aryData["strPassword_Error_Message"]      = "確認パスワードが違っています。";
	$aryData["strPasswordCheck_Error"]         = "visibility:visible;";
	$aryData["strPasswordCheck_Error_Message"] = "確認パスワードが違っています。";
}
else
{
	$aryData["strPassword_Error"]      = "visibility:hidden;";
	$aryData["strPasswordCheck_Error"] = "visibility:hidden;";
}


//////////////////////////////////////////////////////////////////////////
// 表示のための文字列成形関連処理
//////////////////////////////////////////////////////////////////////////
// ユーザー修正の場合、特殊レイアウト
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	$aryData["RENEW"] = TRUE;
}

// ログイン許可チェックボックスのチェック
if ( $aryData["bytInvalidFlag"] == "f" )
{
	$aryData["bytInvalidFlag"] = "checked";
}
// メール送信許可チェックボックスのチェック
if ( $aryData["bytMailTransmitFlag"] == "t" )
{
	$aryData["bytMailTransmitFlag"] = "checked";
}
// ユーザー表示チェックボックスのチェック
if ( $aryData["bytUserDisplayFlag"] == "t" )
{
	$aryData["bytUserDisplayFlag"] = "checked";
}

// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
$lngErrorCount += $bytErrorFlag;

//////////////////////////////////////////////////////////////////////////
// 画像UPLOAD
//////////////////////////////////////////////////////////////////////////
if ( $_FILES['binUserPic']['name'] != "" && preg_match ( "/image\/(" . USER_IMAGE_TYPE . ")$/i", $_FILES['binUserPic']['type'], $aryFileType ) && $_FILES['binUserPic']['size'] < IMAGE_LIMIT )
{
	$aryData["strUserPicName"]       = $aryData["strUserID"];
	$aryData["strUserImageFileName"] = MD5 ( $aryData["strUserPicName"] ) . "." . $aryFileType[1];

	if ( !move_uploaded_file( $_FILES['binUserPic']['tmp_name'], USER_IMAGE_TMPDIR . $aryData["strUserImageFileName"] ) )
	{
		$aryData["strUserImageFile_Error"]         = "visibility:visible;";
		$aryData["strUserImageFile_Error_Message"] = "画像アップロードに失敗しました。";
		$lngErrorCount++;
	}
}
//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
// 文字列チェックにエラーがある場合、入力画面に戻る


//エラーがあったら
if( $lngErrorCount > 0 )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">\n";
	echo "<form action=\"/uc/regist/edit.php\" method=\"POST\">\n";
	echo getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );
	echo "</form>\n";
	echo "<script language=\"javascript\">document.forms[0].submit();</script>";
}
//エラーがなかったら
else
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">\n";
	echo "<form action=\"/uc/regist/action.php\" method=\"POST\">\n";
	echo getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );
	echo "</form>\n";
	echo "<script language=\"javascript\">document.forms[0].submit();</script>";

}

$objDB->close();


return TRUE;
?>


