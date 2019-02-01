<?php
	
	require ( "../lib/excelparser_new/excelparser.php" );							// Excel Parser オブジェクト
	require ( "../lib/excelparser_new/lib_excelparser.php" );	// Excel Parser ライブラリ


	$excel			= new ExcelFileParser( "debug.log", ABC_NO_LOG );	// ABC_NO_LOG  ABC_VAR_DUMP;


	$filename = "../upload_tmp/948d90fc311678324ad865fa9a8a2615.tmp";

	$error_code = $excel->ParseFromFile($filename);

	//if( $exc->worksheet['unicode'][0] ) echo 'unicode';

	var_dump($exc->worksheet['unicode']);

//	echo "iconv:". iconv("UCS-2LE","UTF-8",$excel->fonts[1]['name']);

	var_dump(mb_convert_encoding( $excel->fonts[1]['name'], "UTF-8", "UCS-2LE" ));


/*
$fd = fopen( $filename, 'rb');
$content = fread ($fd, filesize ($name));
fclose($fd);
$error_code = $excel->ParseFromString($content);
unset( $content, $fd );

*/
	echo "error" .$error_code;
	
?>
