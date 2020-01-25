<?

	// 外部ファイルのインクルード
	require("/home/kids2/ListOutput/libs/conf.php");
	require(CLS_DB_FILE);
	require(CLS_LO_FILE);
	require("./functions.php");
	
	mb_internal_encoding("UTF-8");
	mb_http_output("UTF-8");

	// ListOutputオブジェクト設定用、初期設定ファイルの取得
	if( !isset($_POST["conf"]) )
	{
		echo fncGetPages("_%PAGE_HEADER%_");
		echo fncGetPages("_%PAGE_ERROR%_", "conf is not set!<br />本ページは直接表示出来ません。<a href=\"/lo\">ここのページ</a>から条件設定して呼び出して下さい。");
		echo fncGetPages("_%PAGE_FOOTER%_");
		return;
	}
	
	$aryPOST = $_POST;
	
	$strConfigFileName = $aryPOST["conf"];
	

//var_dump($_POST);


	// オブジェクトの作成
	$objDB          = new clsDB;
	$objListOutput  = new CListOutput;
	

	// DB 接続
	if( $objDB->open($DB_LOGIN_USERNAME, $DB_LOGIN_PASSWORD, $POSTGRESQL_HOSTNAME, '') == false )
	{
		echo "db login failed";
		return;
	}
//	else
//	{
//		echo "login successed!";
//	}


	// ListOutput 設定
	$objListOutput->SetReplaceMode(CLISTOUTPUT_REPLACE_ALL);
	$objListOutput->SetConfigDir(CHKLIST_CONFIG_DIR);
	$objListOutput->SetTemplateDir(CHKLIST_TEMPLATE_DIR);
	$objListOutput->SetEvalMode(CLISTOUTPUT_EVAL_CLASS);

	// 置換え設定
	$aryReplaceList["template"] = $aryPOST["template"];
	$aryReplaceList[strtolower("lngGroupCode")] = $aryPOST["lngGroupCode"];
	$aryReplaceList[strtolower("lngUserCode")] = $aryPOST["lngUserCode"];
	$aryReplaceList["productno_in"] = $aryPOST["productno_in"];
	if( strlen(trim($aryPOST["productno_in"])) > 0 )
	{
		// SQL用
		$aryReplaceList[strtolower("column_lngProductNo_in_enable")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable")] = "--";

		// HTML用
		$aryReplaceList[strtolower("column_lngProductNo_in_enable_html_start")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_enable_html_end")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable_html_start")] = "<!--";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable_html_end")] = "-->";
	}
	else
	{
		// SQL用
		$aryReplaceList[strtolower("column_lngProductNo_in_enable")] = "--";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable")] = "";

		// HTML用
		$aryReplaceList[strtolower("column_lngProductNo_in_enable_html_start")] = "<!--";
		$aryReplaceList[strtolower("column_lngProductNo_in_enable_html_end")] = "-->";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable_html_start")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable_html_end")] = "";
	}
	$aryReplaceList["date_from"] = $aryPOST["date_from"];
	$aryReplaceList["date_to"]   = $aryPOST["date_to"];
	$aryReplaceList["cal_date_from"] = $aryPOST["cal_date_from"];
	$aryReplaceList["cal_date_to"]   = $aryPOST["cal_date_to"];
	$aryReplaceList[strtolower("lngOrderStatusCode")]   = $aryPOST["lngOrderStatusCode"];
	
	// ユーザーコードの設定
	if( $aryPOST["lngUserCode"] == "0" )
	{
		$aryReplaceList[strtolower("column_lngUserCode_enable")] = "--";
		$aryReplaceList[strtolower("column_lngUserCode_flag")] = "FALSE";
	}
	else
	{
		$aryReplaceList[strtolower("column_lngUserCode_enable")] = "";
		$aryReplaceList[strtolower("column_lngUserCode_flag")] = "TRUE";
	}

	// 発注状態の設定
	if( $aryPOST["lngOrderStatusCode"] == "0" )
	{
		$aryReplaceList[strtolower("column_lngOrderStatusCode_enable")] = "--";
		$aryReplaceList[strtolower("column_strOrderStatusName_flag")] = "FALSE";
	}
	else
	{
		$aryReplaceList[strtolower("column_lngOrderStatusCode_enable")] = "";
		$aryReplaceList[strtolower("column_strOrderStatusName_flag")] = "TRUE";
	}


	if( empty($aryPOST["import_first"]) == false) {
		// 置換えのインポート
		$objListOutput->ImportReplaceList($aryReplaceList);
	}

	// confファイルの設定
	if( $objListOutput->SetConfigFile($strConfigFileName) )
	{
		if( empty($aryPOST["import_first"]) == true) {
			// 置換えのインポート
			$objListOutput->ImportReplaceList($aryReplaceList);
		}

		// 実行
		if( $objListOutput->ListExecute($objDB, $strPage) == false )
		{
			echo "ListExecute Error!<br>";
			echo $objListOutput->GetErrorMessage();
			return;
		}
	}
	else
	{
		echo $strConfigFileName . " Not found!";
		return;
	}

//	echo $objListOutput->GetErrorMessage();
	
	// 各結果の取り出し(必要に応じて)
//	$objListOutput->GetResult($aryResult);
//	var_dump($aryResult);
	
	
	$objDB->close();
	unset($objDB);
	unset($objListOutput);
	
	
	echo fncGetPages("_%PAGE_HEADER%_");
	echo $strPage;
	if( $strPage == "" )
	{
		echo fncGetPages("_%PAGE_ERROR%_", "data is not found!<br />選択された条件でのデータが見つかりません。<a href=\"javascript:history.back()\">戻る</a>");
	}
	echo fncGetPages("_%PAGE_FOOTER%_");
	//var_dump($aryResult);
	//echo "<br>============<br>";
	
?>
