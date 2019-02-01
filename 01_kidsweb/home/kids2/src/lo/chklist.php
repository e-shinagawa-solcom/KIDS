<?

	// �����ե�����Υ��󥯥롼��
	require("/home/kids2/ListOutput/libs/conf.php");
	require(CLS_DB_FILE);
	require(CLS_LO_FILE);
	require("./functions.php");
	
	mb_internal_encoding("EUC-JP");
	mb_http_output("EUC-JP");

	// ListOutput���֥������������ѡ��������ե�����μ���
	if( !isset($_POST["conf"]) )
	{
		echo fncGetPages("_%PAGE_HEADER%_");
		echo fncGetPages("_%PAGE_ERROR%_", "conf is not set!<br />�ܥڡ�����ľ��ɽ������ޤ���<a href=\"/lo\">�����Υڡ���</a>���������ꤷ�ƸƤӽФ��Ʋ�������");
		echo fncGetPages("_%PAGE_FOOTER%_");
		return;
	}
	
	$aryPOST = $_POST;
	
	$strConfigFileName = $aryPOST["conf"];
	

//var_dump($_POST);


	// ���֥������Ȥκ���
	$objDB          = new clsDB;
	$objListOutput  = new CListOutput;
	

	// DB ��³
	if( $objDB->open($DB_LOGIN_USERNAME, $DB_LOGIN_PASSWORD, $POSTGRESQL_HOSTNAME, '') == false )
	{
		echo "db login failed";
		return;
	}
//	else
//	{
//		echo "login successed!";
//	}


	// ListOutput ����
	$objListOutput->SetReplaceMode(CLISTOUTPUT_REPLACE_ALL);
	$objListOutput->SetConfigDir(CHKLIST_CONFIG_DIR);
	$objListOutput->SetTemplateDir(CHKLIST_TEMPLATE_DIR);
	$objListOutput->SetEvalMode(CLISTOUTPUT_EVAL_CLASS);

	// �ִ�������
	$aryReplaceList["template"] = $aryPOST["template"];
	$aryReplaceList[strtolower("lngGroupCode")] = $aryPOST["lngGroupCode"];
	$aryReplaceList[strtolower("lngUserCode")] = $aryPOST["lngUserCode"];
	$aryReplaceList["productno_in"] = $aryPOST["productno_in"];
	if( strlen(trim($aryPOST["productno_in"])) > 0 )
	{
		// SQL��
		$aryReplaceList[strtolower("column_lngProductNo_in_enable")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable")] = "--";

		// HTML��
		$aryReplaceList[strtolower("column_lngProductNo_in_enable_html_start")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_enable_html_end")] = "";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable_html_start")] = "<!--";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable_html_end")] = "-->";
	}
	else
	{
		// SQL��
		$aryReplaceList[strtolower("column_lngProductNo_in_enable")] = "--";
		$aryReplaceList[strtolower("column_lngProductNo_in_disable")] = "";

		// HTML��
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
	
	// �桼���������ɤ�����
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

	// ȯ����֤�����
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
		// �ִ����Υ���ݡ���
		$objListOutput->ImportReplaceList($aryReplaceList);
	}

	// conf�ե����������
	if( $objListOutput->SetConfigFile($strConfigFileName) )
	{
		if( empty($aryPOST["import_first"]) == true) {
			// �ִ����Υ���ݡ���
			$objListOutput->ImportReplaceList($aryReplaceList);
		}

		// �¹�
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
	
	// �Ʒ�̤μ��Ф�(ɬ�פ˱�����)
//	$objListOutput->GetResult($aryResult);
//	var_dump($aryResult);
	
	
	$objDB->close();
	unset($objDB);
	unset($objListOutput);
	
	
	echo fncGetPages("_%PAGE_HEADER%_");
	echo $strPage;
	if( $strPage == "" )
	{
		echo fncGetPages("_%PAGE_ERROR%_", "data is not found!<br />���򤵤줿���ǤΥǡ��������Ĥ���ޤ���<a href=\"javascript:history.back()\">���</a>");
	}
	echo fncGetPages("_%PAGE_FOOTER%_");
	//var_dump($aryResult);
	//echo "<br>============<br>";
	
?>
