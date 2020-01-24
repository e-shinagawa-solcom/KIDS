<?
/** 
*	パスワードリマインド　処理
*
*	メールアドレスからパスワード情報ページへのメール送信
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	パスワードリマインダーのリンクよりメールアドレス入力画面を表示し
*	その画面より、メールアドレスの正当性をチェックする
*	この処理中ではエラー画面は表示せず、成功しても失敗しても
*	同じ画面を表示する
*	チェックがOKであれば、パスワード情報アドレスを設定した
*	メールを送信する
*
*	更新履歴
*	2004.02.26	メールに記載するアドレス部分を一部修正
*	2004.06.01	DB登録処理時の値なし部分の判断処理を修正
*
*/

// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );
require ( SRC_ROOT . "remind/reminder.php" );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータの取得
$aryBase = $_POST;

// 成功フラグの設定
$bytSuccessFlag = TRUE;

// 設定内容の確認　および　メール送信情報の取得
if ( !$aryBase["strMailAddress"] )
{
	// エラーでも正常メッセージの表示
	require ( SRC_ROOT . 'remind/index.html' );
	$objDB->close();
	exit;
}

// 文字列チェック
$aryCheck["strMailAddress"]      = "null:email(0,50)";

// 変数名となるキーを取得
$aryKey = array_keys( $aryCheck );
$flag = TRUE;
// キーの数だけチェック
foreach ( $aryKey as $strKey )
{
	// $aryData[$strKey]  : チェック対象データ
	// $aryCheck[$strKey] : チェック内容(数値、英数字、アスキー等)
	$strResult = fncCheckString( $aryBase[$strKey], $aryCheck[$strKey] );
	if ( $strResult ) 
	{
		list ( $lngErrorNo, $strErrorMessage ) = explode ( ":", $strResult );
//			fncOutputError ( $lngErrorNo, DEF_ERROR, $strErrorMessage, FALSE, "", $objDB );
		$flag = FALSE;
	}
}

// 文字列チェックエラー
if ( !$flag )
{
	$bytSuccessFlag = FALSE;
}

// ユーザー情報の取得
if ( $bytSuccessFlag )
{
	if ( !$aryBase["strMailAddress"] or !$aryData = getMailAddressToInfo( $aryBase["strMailAddress"], $objDB ) )
	{
		$bytSuccessFlag = FALSE;
		$aryData["lngUserCode"] = 0;
	}
}

// メール配信許可、メールアドレスのチェック
if ( $bytSuccessFlag )
{
	if ( !$aryData["bytMailTransmitFlag"] || $aryData["strMailAddress"] == "" || $aryData["bytInvalidFlag"] )
	{
		$bytSuccessFlag = FALSE;
	}
}

// 管理ユーザーのアドレスの取得
if ( $bytSuccessFlag ) 
{
	$aryData["strAdminAddress"] = fncGetAdminFunction( "adminmailaddress", $objDB );
	if ( !$aryData["strAdminAddress"] )
	{
		$bytSuccessFlag = FALSE;
	}
}

// リマインダー処理のためのconf.incにて設定されている時間内有効なセッションの作成
// セッションID作成
$strSessionID = md5 ( uniqid ( rand(), 1 ) );

// アクセスIPアドレスチェック
if ( $bytSuccessFlag )
{
	if ( !checkAccessIPSimple( $objDB, $objAuth ) )
	{
		$bytSuccessFlag = FALSE;
	}
}

if ( $bytSuccessFlag )
{
	$SuccessFlag = "TRUE";
}
else
{
	$SuccessFlag = "FALSE";
}

// 2004.06.01 suzukaze update start
if ( $aryData["lngUserCode"] == "" )
{
	$aryData["lngUserCode"] = "null";
}
// 2004.06.01 suzukaze update end

// ログインセッション管理テーブルに書き込み
$strQuery = "INSERT INTO t_LoginSession VALUES (" .
            " '" . $strSessionID . "', " . $aryData["lngUserCode"] . ", '" . $aryData["strUserID"] . "', '" . $aryBase["strMailAddress"] .
			"', now(), '" . $objAuth->AccessIP . "', " . $SuccessFlag . ")";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( !$objDB->freeResult( $lngResultID ) )
{
	$bytSuccessFlag = FALSE;
}

// リマインダー処理アドレスの作成
if ( $bytSuccessFlag )
{
// 2004.02.26 suzukaze update start
	$aryData["strURL"] = TOP_URL . 'remind/passwdinfo.php?strInfo=' . $strSessionID;
// 2004.02.26 suzukaze update end
	// メール雛型の取得、置換
	if ( list( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_LOGIN2, $aryData, $objDB ) )
	{
		// 設定内容がＯＫならばメールの送信
		$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );
		mail( $aryData["strMailAddress"], $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
	}
}

// エラーでも正常でも”正常”メッセージの表示
require ( SRC_ROOT . 'remind/confirm.html' );

$objDB->close();

return TRUE;
?>
