<?
error_reporting(E_ALL);


	$strTo = "kids@kuwagata.co.jp,kou@kuwagata.co.jp,japan060718@yahoo.co.jp";
	$strSubject = "��ƣ�ƥ���";
	$strMessage = "�ƥ��ȤǤ���";
	$strHeader = "From: kids@kuwagata.co.jp\n";
	
	//$strHeader .= 'Content-Type: text/plain; charset="iso-2022-jp"';
	
	// 
	mb_language("Japanese");
	
	// 
	$strRet = mail( $strTo, $strSubject, $strMessage, $strHeader );
	
	if( !$strRet )
	{
		echo "��������";
	}

	$aryErr = error_get_last();
	var_dump($aryErr);


?>
