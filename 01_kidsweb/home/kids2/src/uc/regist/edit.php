<?
/** 
*	ユーザー管理 データ入力画面
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
// index.php -> strSessionID           -> edit.php
// index.php -> lngFunctionCode        -> edit.php
// index.php -> lngUserCode            -> edit.php
//
// 案件処理実行へ
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

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GETデータ取得
if ( $_GET )
{
	$aryData = $_GET;
}
else
{
	$aryData = $_POST;
}

$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC5 . ")";
$aryCheck["lngUserCode"]            = "number(0,32767)";


// 入力ミスによる戻り以外の場合、エラー表示を非表示に設定
if ( !preg_match ( "/confirm\.php/", $_SERVER["HTTP_REFERER"] ) )
{
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
}


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// ユーザー設定の場合
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 && fncCheckAuthority( DEF_FUNCTION_UC1, $objAuth ) )
{
	$aryData["lngUserCode"]           = $objAuth->UserCode;
	$aryData["lngUserCodeConditions"] = 1;
	$aryData["bytInvalidFlagDisabled"]         = "disabled";
	$aryData["strUserIDDisabled"]              = "disabled";
	$aryData["bytUserDisplayFlagDisabled"]     = "disabled";
	$aryData["lngAuthorityGroupCodeDisabled"]  = "disabled";
	$aryData["lngAccessIPAddressCodeDisabled"] = "disabled";
	$aryData["strNaviCode"] = "uc-info";
}

// ユーザー登録の場合
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC2 && fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
{
	$aryData["bytUserDisplayFlag"] = "t";
	$aryData["bytInvalidFlag"] = "f";
	$aryData["strNaviCode"] = "uc-regist";
}

// ユーザー修正の場合
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 && fncCheckAuthority( DEF_FUNCTION_UC5, $objAuth ) )
{
	$aryData["lngUserCodeConditions"]  = 1;
	$aryData["lngGroupCodeConditions"] = 0;
	$aryData["strNaviCode"] = "uc-modify";
}

// それ以外
else
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// ユーザー情報、ユーザー修正の場合
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// ユーザー管理
	// 案件読み込み、検索、詳細情報取得クエリ関数
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 1107, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// 所属グループの数だけループ
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// デフォルトグループの場合、ユーザーデータの読み込み
		if ( $objResult->bytdefaultflag == 't' )
		{
			$aryData["bytInvalidFlag"]         = $objResult->bytinvalidflag;
			$aryData["lngUserCode"]            = $objResult->lngusercode;
			$aryData["strUserID"]              = $objResult->struserid;
			$aryData["bytMailTransmitFlag"]    = $objResult->bytmailtransmitflag;
			$aryData["strMailAddress"]         = $objResult->strmailaddress;
			$aryData["bytUserDisplayFlag"]     = $objResult->bytuserdisplayflag;
			$aryData["strUserDisplayCode"]     = $objResult->struserdisplaycode;
			$aryData["strUserDisplayName"]     = $objResult->struserdisplayname;
			$aryData["strUserFullName"]        = $objResult->struserfullname;
			$aryData["lngCompanyCode"]         = $objResult->lngcompanycode;
			$aryData["strCompanyDisplayCode"]  = $objResult->strcompanydisplaycode;
			$aryData["strCompanyName"]         = $objResult->strcompanyname;
			$aryData["lngGroupCode"]           = ":" . $objResult->lnggroupcode;
			$aryData["strGroupDisplayCode"]    = $objResult->strgroupdisplaycode;
			$aryData["strGroupName"]           = $objResult->strgroupname;
			$aryData["lngAuthorityGroupCode"]  = $objResult->lngauthoritygroupcode;
			$aryData["strAuthorityGroupName"]  = $objResult->strauthoritygroupname;
			$aryData["lngAccessIPAddressCode"] = $objResult->lngaccessipaddresscode;
			$aryData["strAccessIPAddress"]     = $objResult->straccessipaddress;
			$aryData["strUserImageFileName"]   = $objResult->struserimagefilename;
			$aryData["strNote"]                = $objResult->strnote;
		}

		// デフォルトグループ外の場合、グループコードを連結
		else
		{
			$lngGroupCodeSub .= ":" . $objResult->lnggroupcode;
		}
	}

	// デフォルトを先頭とした、連結グループコード文字列の生成
	$aryData["lngGroupCode"] .= $lngGroupCodeSub;

	// 結果開放
	$objDB->freeResult( $lngResultID );


	// 属性が「顧客」だった場合
	list ( $lngResultID, $lngResultNum ) = fncQuery ( "SELECT * FROM m_AttributeRelation WHERE lngCompanyCode = $aryData[lngCompanyCode] AND lngAttributeCode = 2", $objDB );
	if ( $lngResultNum > 0 )
	{
		$aryData["bytInvalidFlag"]                = "";
		$aryData["bytMailTransmitFlag"]           = "";
// 2004.04.14 suzukaze update start
//		$aryData["bytUserDisplayFlag"]            = "checked";
// 2004.04.14 suzukaze update end
		$aryData["lngAuthorityGroupCode"]         = 6;
		$aryData["lngAttributeCode"]              = 1;
		$aryData["bytInvalidFlagDisabled"]        = "disabled";
		$aryData["bytMailTransmitFlagDisabled"]   = "disabled";
// 2004.04.14 suzukaze update start
//		$aryData["bytUserDisplayFlagDisabled"]    = "disabled";
// 2004.04.14 suzukaze update end
		$aryData["lngAuthorityGroupCodeDisabled"] = "disabled";
	}
}

// HTMLエンコード
$aryData = fncToHTMLString( $aryData );

// 権限グループSELECTメニュー
$aryData["lngAuthorityGroupCode"]  = fncGetPulldown( "m_AuthorityGroup", "lngAuthorityGroupCode", "strAuthorityGroupName", $aryData["lngAuthorityGroupCode"], "", $objDB );
// アクセスIPSELECTメニュー
$aryData["lngAccessIPAddressCode"]  = fncGetPulldown( "m_AccessIPAddress", "lngAccessIPAddressCode", "strAccessIPAddress, strNote", $aryData["lngAccessIPAddressCode"], "", $objDB );

// HIDDEN 値セット
$aryData["strSessionID"]           = $aryData["strSessionID"];
$aryData["lngFunctionCode"]        = $aryData["lngFunctionCode"];

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


// ユーザー画像処理
if ( $aryData["strUserImageFileName"] )
{
	$aryData["strUserImageFileName"] = USER_IMAGE_URL . $aryData["strUserImageFileName"];
}
else
{
	$aryData["strUserImageFileName"] = USER_IMAGE_DEFAULT_URL;
}


// URLセット
$aryData["filename"] = "confirm.php";




//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	$aryData["RENEW"] = TRUE;
}

//echo getArrayTable( $aryData, "TABLE" );exit;
echo fncGetReplacedHtml( "uc/regist/parts.tmpl", $aryData, $objAuth );


$objDB->close();


return TRUE;
?>
