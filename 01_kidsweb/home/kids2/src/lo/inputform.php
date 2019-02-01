<?

	// 外部ファイルのインクルード
	require("/home/kids2/ListOutput/libs/conf.php");
	require(CLS_DB_FILE);
	require(FNC_LIBS_FILE);
	require("./functions.php");

	mb_internal_encoding("EUC-JP");
	mb_http_output("EUC-JP");

	$aryGET = $_GET;

	if( !isset($aryGET["template"]) )
	{
		echo fncGetPages("_%PAGE_HEADER%_");
		echo fncGetPages("_%PAGE_ERROR%_", "template is not set!<br />本ページは直接表示出来ません。<a href=\"/lo\">ここのページ</a>から条件設定して呼び出して下さい。");
		echo fncGetPages("_%PAGE_FOOTER%_");
		return;
	}

	$strInputFormTemplate = CHKLIST_TEMPLATE_DIR . $aryGET["template"];



	// オブジェクトの作成
	$objDB          = new clsDB;

	// DB 接続
	if( $objDB->open($DB_LOGIN_USERNAME, $DB_LOGIN_PASSWORD, $POSTGRESQL_HOSTNAME, '') == false )
	{
		echo "db login failed";
		return;
	}


	// テンプレートファイルを読み込み
	$aryFile = file($strInputFormTemplate);
	if( !$aryFile )
	{
		echo fncGetPages("_%PAGE_HEADER%_");
		echo fncGetPages("_%PAGE_ERROR%_", "template is not found!<br />テンプレートがありません。設定ファイルを確認して下さい。<br />$strInputFormTemplate");
		echo fncGetPages("_%PAGE_FOOTER%_");
		return;
	}
	
	// テンプレートファイル内を一行づつチェックし、置き換えを行う
	/*
	while( list($strKey, $strValue) = each($aryFile))
	{
		// _%...%_ を検索
		if( preg_match( "/_%[A-Za-z0-9_]+%_/", $strValue, $aryMatch) )
		{

		}
		
		$aryHtml[] = $strValue;
   	}*/

	$strHTML = implode("", $aryFile);

	$aryPattern[] = "/_%PAGE_HEADER%_/";
	$aryPattern[] = "/_%PAGE_FOOTER%_/";
	$aryPattern[] = "/_%PAGE_TEMPLATENAME%_/";
	$aryPattern[] = "/_%M_GROUP%_/";
	$aryPattern[] = "/_%M_USER%_/";
	$aryPattern[] = "/_%DATE_FROM%_/";
	$aryPattern[] = "/_%DATE_TO%_/";
	$aryPattern[] = "/_%CAL_DATE_FROM%_/";
	$aryPattern[] = "/_%CAL_DATE_TO%_/";
	$aryPattern[] = "/_%M_ORDERSTATUS%_/";
	$aryPattern[] = "/_%[\w]+%_/";
	
	$aryReplace[] = fncGetPages("_%PAGE_HEADER%_");
	$aryReplace[] = fncGetPages("_%PAGE_FOOTER%_");
	$aryReplace[] = $aryGET["template"];
	$aryReplace[] = fncGetElements($objDB, "_%M_GROUP%_", $aryGET["lngGroupCode"]);
	$aryReplace[] = fncGetElements($objDB, "_%M_USER%_", $aryGET["lngUserCode"]);
	$aryReplace[] = fncGetElements($objDB, "_%DATE_FROM%_", $aryGET["date_from"]);
	$aryReplace[] = fncGetElements($objDB, "_%DATE_TO%_", $aryGET["date_to"]);
	$aryReplace[] = fncGetElements($objDB, "_%CAL_DATE_FROM%_", $aryGET["cal_date_from"]);
	$aryReplace[] = fncGetElements($objDB, "_%CAL_DATE_TO%_", $aryGET["cal_date_to"]);
	$aryReplace[] = fncGetElements($objDB, "_%M_ORDERSTATUS%_", $aryGET["lngOrderStatusCode"]);
	$aryReplace[] = "";


	$strHTML = preg_replace($aryPattern, $aryReplace, $strHTML );

	$objDB->close();
	unset($objDB);

	echo $strHTML;

?>


