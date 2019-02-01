<?
	
// DB設定
define ( "POSTGRESQL_HOSTNAME", "192.168.10.229" );
define ( "POSTGRESQL_HOSTPORT", "5432" );
define ( "DB_LOGIN_USERNAME", "kids" );
define ( "DB_LOGIN_PASSWORD", "" );
// define ( "DB_NAME", "kids" );
define ( "DB_NAME", "kidscore2" );

// エラーメール送信先
define ( "ERROR_MAIL_TO", "saito@toretate.com" );


// パス
define ( "PROJECT_ROOT", "/home/kids2/" );
define ( "LIB_ROOT", "/home/kids2/ListOutput/libs/" );
define ( "FNC_LIBS_FILE", LIB_ROOT . "libs.php" );
define ( "CLS_DB_FILE", LIB_ROOT . "clsdb.php" );
define ( "CLS_LO_FILE", LIB_ROOT . "listoutput.php" );


define ( "CHKLIST_CONFIG_DIR", PROJECT_ROOT . "ListOutput/config/" );
define ( "CHKLIST_TEMPLATE_DIR", PROJECT_ROOT . "ListOutput/template/" );



?>
