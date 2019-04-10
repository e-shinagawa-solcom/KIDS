<?
/** 
*	ユーザー管理 詳細情報表示画面
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID           -> ditail.php
// index.php -> lngFunctionCode        -> ditail.php
// index.php -> bytInvalidFlag         -> ditail.php
// index.php -> lngUserCode            -> ditail.php
// index.php -> strUserID              -> ditail.php
// index.php -> strMailAddress         -> ditail.php
// index.php -> bytMailtransmitFlag    -> ditail.php
// index.php -> strUserDisplayCode     -> ditail.php
// index.php -> strUserDisplayName     -> ditail.php
// index.php -> strUserFullName        -> ditail.php
// index.php -> lngCompanyCode         -> ditail.php
// index.php -> lngGroupCode           -> ditail.php
// index.php -> lngAuthorityGroupCode  -> ditail.php
// index.php -> lngAccessIPAddressCode -> ditail.php
// index.php -> strNote                -> ditail.php

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
$aryData = $_GET;


$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC4 . "," . DEF_FUNCTION_UC4 . ")";
$aryCheck["bytInvalidFlag"]         = "english(4,5)";
$aryCheck["lngUserCode"]            = "null:number(0,32767)";
$aryCheck["strUserID"]              = "numenglish(0,32767)";
$aryCheck["strMailAddress"]         = "mail";
$aryCheck["strUserDisplayCode"]     = "numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "length(0,120)";
$aryCheck["strUserFullName"]        = "length(0,120)";
$aryCheck["lngCompanyCode"]         = "number(0,32767)";
$aryCheck["lngGroupCode"]           = "number(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "number(0,32767)";
$aryCheck["strNote"]                = "length(0,1000)";

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_UC4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
$strURL = fncGetURL( $aryData );

// ユーザーコードによる条件検索を有効化
$aryData["lngUserCodeConditions"] = 1;
// グループ条件無効化
$aryData["lngGroupCodeConditions"] = 0;


$aryInvalidFlag      = Array ("t" => "不許可", "f" => "許可" );
$aryMailTransmitFlag = Array ("t" => "許可",   "f" => "不許可" );
$aryUserDisplayFlag  = Array ("t" => "表示",   "f" => "非表示" );


// ユーザー管理
// 案件読み込み、検索、詳細情報取得クエリ関数
list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
$objResult = $objDB->fetchObject( $lngResultID, 0 );

$partsData["bytInvalidFlag"]        = $objResult->bytinvalidflag;
$partsData["lngUserCode"]           = $objResult->lngusercode;
$partsData["strUserID"]             = $objResult->struserid;
$partsData["bytMailTransmitFlag"]   = $objResult->bytmailtransmitflag;
$partsData["strMailAddress"]        = $objResult->strmailaddress;
$partsData["strUserDisplayCode"]    = $objResult->struserdisplaycode;
$partsData["strUserDisplayName"]    = $objResult->struserdisplayname;
$partsData["bytUserDisplayFlag"]    = $objResult->bytuserdisplayflag;
$partsData["strUserFullName"]       = $objResult->struserfullname;
$partsData["strCompanyDisplayCode"] = $objResult->strcompanydisplaycode;
$partsData["strCompanyName"]        = $objResult->strcompanyname;
$partsData["strAuthorityGroupName"] = $objResult->strauthoritygroupname;
$partsData["strAccessIPAddress"]    = $objResult->straccessipaddress;
$partsData["strUserImageFileName"]  = $objResult->struserimagefilename;
$partsData["strNote"]               = $objResult->strnote;

// 所属グループ表示文字列生成
$partsData["aryGroup"] = "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupname . "<br>\n";


// ログイン許可フラグ、表示変換
$partsData["bytInvalidFlag"] = $aryInvalidFlag[$partsData["bytInvalidFlag"]];

// メール配信許可フラグ、表示変換
$partsData["bytMailTransmitFlag"] = $aryMailTransmitFlag[$partsData["bytMailTransmitFlag"]];

// ユーザー表示フラグ、表示変換
$partsData["bytUserDisplayFlag"] = $aryUserDisplayFlag[$partsData["bytUserDisplayFlag"]];

// ユーザー表示画像、表示変換
if ( $partsData["strUserImageFileName"] )
{
	$partsData["strUserImageFileName"] = USER_IMAGE_URL . $partsData["strUserImageFileName"];
}
else
{
	$partsData["strUserImageFileName"] = USER_IMAGE_DEFAULT_URL;
}

// 所属グループ複数表示処理
for ( $i = 1; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	// 所属グループ表示文字列生成
	$partsData["aryGroup"] .= "[" . $objResult->strgroupdisplaycode . "] " . $objResult->strgroupname . "<br>\n";
}

$partsData["strMode"] = "detail";

$objDB->freeResult( $lngResultID );

// パーツテンプレート読み込み
$objTemplate = new clsTemplate();
//$objTemplate->getTemplate( "uc/result/detail.tmpl" );
$objTemplate->getTemplate( "uc/regist/confirm.tmpl" );
$objTemplate->replace( $partsData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;



$objDB->close();


return TRUE;
?>
