<?
/** 
*	HELP問い合わせ画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 1401.php -> strSendMailUserName -> 1401.php
// 1401.php -> strContents         -> 1401.php


// POSTデータがある場合、メール配信処理へ
if ( array_count_values ( $_POST ) )
{
	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);

	// DB接続
	$objDB   = new clsDB();
	$objDB->open( "", "", "", "" );

	$aryData = $_POST;

	// 文字列チェック
	$aryCheck["strSendMailUserName"] = "null:length(1,100)";
	$aryCheck["strContents"]         = "null:length(1,200)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	//fncPutStringCheckError( $aryResult, $objDB );

	// 文字列チェック結果エラー文字列生成
	$strError = join ( "", $aryResult );
	if ( $strError )
	{
		$strMessage = "項目が入力されていません。";
	}
	else
	{
		$strMailBody = $aryData["strSendMailUserName"] . " さんからの質問\n" . $aryData["strContents"];

		// 文字コード変換(EUC->JIS)
		$strMailBody = mb_convert_encoding( $strMailBody, "JIS", "EUC-JP" );
		$strSubject  = mb_convert_encoding( "K.I.D.S HELP MAIL", "JIS", "EUC-JP" );
		$strSubject  = mb_encode_mimeheader ( $strSubject , "iso-2022-jp", "B" );

		$strMessage = "問い合わせメールを送信しました。";

		// メール送信
		$strAdminMailAddress = fncGetAdminFunction( "adminmailaddress", $objDB );
		if ( !mail ( $strAdminMailAddress, $strSubject, $strMailBody, "From: $strAdminMailAddress\nReturn-Path: " . ERROR_MAIL_TO . "\n" ) )
		{
			$strMessage = "問い合わせメール送信に失敗しました。";
		}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="ja">
<head>
<title>K.I.D.S. - Online Help</title>
<meta http-equiv="content-type" content="text/html; charset=euc-jp">

<link rel="stylesheet" type="text/css" media="screen" href="../cmn/styles.css">
</head>
<body id="ContentsBody">


<span class="indexContents">〜問合せフォーム〜</span>

<div align="center">
<b><font color="#FF0000"><? echo $strMessage; ?> </font></b>

<table cellpadding="10" cellspacing="0" border="0">
	<tr>
		<td>

			<table cellpadding="5" cellspacing="1" border="0" bgcolor="#555555">
				<tr>
					<td class="doc1">
						K.I.D.Sシステムを利用する時に困ったことが有ったらこのフォームを利用してください。<BR>
						</td>
				</tr>

				<tr class="doc2">
					<td>
						■入力フォーム
					</td>
				</tr>

				<tr class="doc3">
					<td>
	<form action="<? echo $_SERVER["PHP_SELF"]; ?>" method="POST">
<font size=2>

<B>名前：</B><p>
<input type="text" name="strSendMailUserName"><p>

<B>内容：</B><p>
<textarea rows=6 cols=50 wrap="hard" name="strContents"></textarea><p>
<input type="submit"value="送   信">
<input type="reset" value="リセット">

</form>
</font>
					</td>
				</tr>
			</table>


		</td>
	</tr>
</table>
</div>

</body>
</html>