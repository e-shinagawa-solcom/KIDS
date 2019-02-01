<?
error_reporting(E_ALL);


	$strTo = "kids@kuwagata.co.jp,kou@kuwagata.co.jp,japan060718@yahoo.co.jp";
	$strSubject = "斎藤テスト";
	$strMessage = "テストです。";
	$strHeader = "From: kids@kuwagata.co.jp\n";
	
	//$strHeader .= 'Content-Type: text/plain; charset="iso-2022-jp"';
	
	// 
	mb_language("Japanese");
	
	// 
	$strRet = mail( $strTo, $strSubject, $strMessage, $strHeader );
	
	if( !$strRet )
	{
		echo "送信失敗";
	}

	$aryErr = error_get_last();
	var_dump($aryErr);


?>
