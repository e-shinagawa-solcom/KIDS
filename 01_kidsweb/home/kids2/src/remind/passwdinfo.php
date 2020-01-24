<?
/** 
*	パスワードリマインド　パスワード情報の表示、設定処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	パスワードリマインダー処理から送信されたメールより
*	このプログラムを呼び出し、情報が異常でなければ
*	新しいパスワードを発行する
*	情報が異常であった場合、異常内容によらず同じメッセージを
*	表示する
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

// GETデータの取得
$aryBase = $_GET;

// GETデータの確認
if ( isset($aryBase["strInfo"]) )
{
	$strSessionID = $aryBase["strInfo"];
	// セッションＩＤからユーザー情報の取得
	$aryData = getSessionIDToInfo( $strSessionID, $objDB );
	if ( !$aryData )
	{
		fncOutputError( 9052, DEF_ERROR, "パスワード発行できません。", TRUE, "", $objDB );
	}

	// 新しいパスワードの生成
	$strNewPassword = substr( md5( uniqid( rand(), 1 ) ), 0, 10);

	// パスワードの更新
	if ( !$aryData["lngUserCode"] )
	{
//		fncOutputError( 9051, DEF_ERROR, "ユーザー情報の更新に失敗しました。", TRUE, "", $objDB );
	}

	if ( !setNewPassword( $aryData["lngUserCode"], $strNewPassword, $objDB ) )
	{
		fncOutputError( 9051, DEF_ERROR, "ユーザー情報の更新に失敗しました。", TRUE, "", $objDB );
	}

	// 新しいパスワード情報画面の表示
	$fp = fopen ( TMP_ROOT . "remind/passwdinfo.html", "r" );

	while ( $strTemplLine = fgets ( $fp, 1000 ) )
	{
		$strTempl .= $strTemplLine;
	}
	// 置換
	$strTempl = preg_replace ( "/_%strNewPassword%_/i", $strNewPassword, $strTempl );
	// 出力
	echo $strTempl;

	// 使用したセッション情報の無効化
	if ( !setSessionOff( $strSessionID, $objDB ) )
	{
		fncOutputError( 9052, DEF_ERROR, "セッション異常。", TRUE, "", $objDB );
	}
}
else
{
	fncOutputError( 9052, DEF_ERROR, "指定されたアドレス情報が異常です。", TRUE, "", $objDB );
}

$objDB->close();


return TRUE;
?>
