<?php

	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	
	echo fncGetReplacedHtml( "p/search_ifrm/parts.tmpl", $aryData );
	return true;
?>