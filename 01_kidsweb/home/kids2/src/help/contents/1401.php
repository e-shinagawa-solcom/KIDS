<?
/** 
*	HELP�䤤��碌����
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


// POST�ǡ����������硢�᡼���ۿ�������
if ( array_count_values ( $_POST ) )
{
	// �����ɤ߹���
	include_once('conf.inc');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);

	// DB��³
	$objDB   = new clsDB();
	$objDB->open( "", "", "", "" );

	$aryData = $_POST;

	// ʸ��������å�
	$aryCheck["strSendMailUserName"] = "null:length(1,100)";
	$aryCheck["strContents"]         = "null:length(1,200)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	//fncPutStringCheckError( $aryResult, $objDB );

	// ʸ��������å���̥��顼ʸ��������
	$strError = join ( "", $aryResult );
	if ( $strError )
	{
		$strMessage = "���ܤ����Ϥ���Ƥ��ޤ���";
	}
	else
	{
		$strMailBody = $aryData["strSendMailUserName"] . " ���󤫤�μ���\n" . $aryData["strContents"];

		// ʸ���������Ѵ�(EUC->JIS)
		$strMailBody = mb_convert_encoding( $strMailBody, "JIS", "EUC-JP" );
		$strSubject  = mb_convert_encoding( "K.I.D.S HELP MAIL", "JIS", "EUC-JP" );
		$strSubject  = mb_encode_mimeheader ( $strSubject , "iso-2022-jp", "B" );

		$strMessage = "�䤤��碌�᡼����������ޤ�����";

		// �᡼������
		$strAdminMailAddress = fncGetAdminFunction( "adminmailaddress", $objDB );
		if ( !mail ( $strAdminMailAddress, $strSubject, $strMailBody, "From: $strAdminMailAddress\nReturn-Path: " . ERROR_MAIL_TO . "\n" ) )
		{
			$strMessage = "�䤤��碌�᡼�������˼��Ԥ��ޤ�����";
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


<span class="indexContents">����礻�ե������</span>

<div align="center">
<b><font color="#FF0000"><? echo $strMessage; ?> </font></b>

<table cellpadding="10" cellspacing="0" border="0">
	<tr>
		<td>

			<table cellpadding="5" cellspacing="1" border="0" bgcolor="#555555">
				<tr>
					<td class="doc1">
						K.I.D.S�����ƥ�����Ѥ�����˺��ä����Ȥ�ͭ�ä��餳�Υե���������Ѥ��Ƥ���������<BR>
						</td>
				</tr>

				<tr class="doc2">
					<td>
						�����ϥե�����
					</td>
				</tr>

				<tr class="doc3">
					<td>
	<form action="<? echo $_SERVER["PHP_SELF"]; ?>" method="POST">
<font size=2>

<B>̾����</B><p>
<input type="text" name="strSendMailUserName"><p>

<B>���ơ�</B><p>
<textarea rows=6 cols=50 wrap="hard" name="strContents"></textarea><p>
<input type="submit"value="��   ��">
<input type="reset" value="�ꥻ�å�">

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