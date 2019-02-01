<?php

	function getmicrotime()
	{
		list( $usec, $sec ) = explode( " ", microtime() );
		return ( (float)$usec + (float)$sec );
	}

	function uc2html($str)
	{
		$ret = '';

		$ret = mb_convert_encoding( $str, "UTF-8", "UCS-2LE" );

//echo "<br>======================<br>";
//echo mb_detect_encoding($str);
/*
		if ( function_exists("iconv") )
		{
			$ret = iconv("UCS-2LE","UTF-8",$str);
echo "<br>SRC =". $str;
echo "<br>DST =". $ret;
		}
		else
		{
			for( $i=0; $i<strlen($str)/2; $i++ )
			{
				$charcode = ord($str[$i*2]) + 256*ord($str[$i*2+1]);
				$ret .= '&#'.$charcode;
			}
		}
*/

		return $ret;
	}

	function show_time()
	{
		global $time_start,$time_end;

		$time = $time_end - $time_start;
		echo "Parsing done in $time seconds<hr size=1><br>";
	}

	function fatal($msg = '')
	{
		echo '[Fatal error]';
		if( strlen($msg) > 0 )
			echo ": $msg";
		echo "<br>\nScript terminated<br>\n";
		if( $f_opened) @fclose($fh);
		exit();
	};

?>
