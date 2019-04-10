<?php

	$strEnMark = "\\";

	$strEnMark = ( $strEnMark == "\\" ) ? "\\\\" : $strEnMark;

	if( $strEnMark == "\\\\" )
	{
		echo "OK! " . $strEnMark;
	}
	else
	{
		echo "NG!" . $strEnMark;
	}

?>
