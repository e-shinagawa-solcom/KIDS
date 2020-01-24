<?php

// ライブラリ読み込み
include_once('conf.inc');
require_once(LIB_FILE);
require_once(LIB_DEBUGFILE);

set_time_limit(0);

$objDB   = new clsDB();
$objDB->open( "postgres", "", "", "" );


$strQuery = "VACUUM FULL VERBOSE;";
$lngResultID = $objDB->execute($strQuery);

var_dump($lngResultID);

//	$objResult = $objDB->fetchObject( $lngResultID, 0 );
//var_dump($objResult);


?>
