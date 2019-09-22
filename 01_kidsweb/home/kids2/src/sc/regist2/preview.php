<?php
// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ�ץ�ӥ塼
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
	require PATH_HOME . "/vendor/autoload.php";

	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	//-------------------------------------------------------------------------
	// �ѥ�᡼������
	//-------------------------------------------------------------------------
	// ���å����ID
	if ($_POST["strSessionID"]){
		$aryData["strSessionID"] = $_POST["strSessionID"];
	}else{
		$aryData["strSessionID"] = $_REQUEST["strSessionID"];   
	}
	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// �����⡼��
	$strMode    = $_POST["strMode"];

	//-------------------------------------------------------------------------
	// DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;

	// 600 ������
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 601 �������������Ͽ��
	if( fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// 610 �������ʹ��ɲá��Ժ����
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}

	//-------------------------------------------------------------------------
	//  �ץ�ӥ塼����ɽ��
	//-------------------------------------------------------------------------
	if ($strMode == "display-preview"){
		// �ץ�ӥ塼ɽ�������Ͽ���뤿�����ϥǡ�����json���Ѵ��������򤹤�
		$aryHeader = $_POST["aryHeader"];
		$aryDetail = $_POST["aryDetail"];
		$aryData["aryHeaderJson"] = EncodeToJson($aryHeader);
		$aryData["aryDetailJson"] = EncodeToJson($aryDetail);

		//TODO:Excel�ƥ�ץ졼�Ȥ���ץ�ӥ塼HTML��������ưʲ����ѿ��˥��å�
		$aryData["PREVIEW_STYLE"] = "";
		$aryData["PREVIEW_DATA"] = "";

		// �ץ�ӥ塼����ɽ��
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		echo $objTemplate->strTemplate;

		return true;
	}

	if ($strMode == "regist-test"){
		// �ץ�ӥ塼ɽ���������򤷤���Ͽ�ǡ�����json������������
		$aryHeader = DecodeFromJson($_POST["aryHeaderJson"]);
		$aryDetail = DecodeFromJson($_POST["aryDetailJson"]);

		//TODO:��Ͽ����


		// ��Ͽ��̲��̤�ɽ������ѥ�᡼��������
		$aryData["dtmRegistDate"] = "2019/5/27 12:34:56";
		$aryData["lngSlipNo"] = "KWG19527-01-01";

		// ��Ͽ��̲���ɽ��
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();
		echo $objTemplate->strTemplate;

		return true;
	}


	// ------------------------
	//   Ģɼɽ��
	// ------------------------
	if($strMode == "chouhyou-sample"){
		// ���ܸ���б������硢����1�Ԥ�ɬ��
		ini_set('default_charset', 'UTF-8');

		// �ɤ߹���
		//use PhpOffice\PhpSpreadsheet\IOFactory;
		//use PhpOffice\PhpSpreadsheet\Writer\Html;

		$file = mb_convert_encoding('template\Ǽ�ʽ�temple_B��_Ϣ�����.xlsx', 'UTF-8','EUC-JP' );
		$sheetname = mb_convert_encoding('B������', 'UTF-8','EUC-JP' );
		$cellValue = mb_convert_encoding('���̤��ͤ򥻥å�', 'UTF-8','EUC-JP' );
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

		// �֥å����ͤ����ꤹ��
		$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
		//$outHeader = $writer->generateHTMLHeader();
		$outStyle = $writer->generateStyles(true);
		$outSheetData = $writer->generateSheetData();
		//$outFooter .= $writer->generateHTMLFooter();

		//TODO:���٤ο����������֤�
		$outStyle = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
		$outSheetData = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');
		$aryData["PREVIEW_STYLE"] = $outStyle;
		$aryData["PREVIEW_DATA"] = $outSheetData;

		//$out2 = mb_convert_encoding($output, 'EUC-JP', 'UTF-8');


		// �ץ�ӥ塼����ɽ��
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		//header("Content-Type: text/plain");
		echo $objTemplate->strTemplate;

		return true;
	}



	/*
	define ( "PATH_HOME",	"E:/Source/Repos/solcom-net/KIDS/01_kidsweb/home/kids2" );
	require (PATH_HOME . "/vendor/autoload.php");
	define ( "REPORT_TMPDIR",	PATH_HOME . "/report_tmp/" );
	$filepath = REPORT_TMPDIR . "Ǽ�ʽ�temple_B��_Ϣ�����.xls";

	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
	$spreadsheet = $reader->load($filepath);

	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
	$output = $writer->generateHTMLHeader();
	$output .= $writer->generateStyles(true);
	$output .= $writer->generateSheetData();
	$output .= $writer->generateHTMLFooter();
	echo mb_convert_encoding($output, 'EUC-JP', 'UTF-8');
	*/


	// --------------------------------
	//    ��Ͽ����
	// --------------------------------
	if($strMode == "regist"){
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// ���ޥ�����������٤���Ͽ
		/*
		if (!fncRegistSales())
		{
			fncOutputError ( 9051, DEF_FATAL, "���ޥ�����Ͽ����", TRUE, "", $objDB );
		}

		// ���������Ͽ
		if (!fncRegistSalesDetail($lngSlipNo, $objDB))
		{
			fncOutputError ( 9051, DEF_FATAL, "���������Ͽ����", TRUE, "", $objDB );
		}

		// Ǽ����ɼ�ޥ�����Ͽ
		if (!fncRegistSlip($strSlipCode, $objDB, $objAuth))	
		{
			fncOutputError ( 9051, DEF_FATAL, "Ǽ����ɼ�ޥ�����Ͽ����", TRUE, "", $objDB );
		}

		// Ǽ����ɼ������Ͽ
		if (!fncRegistSlipDetail($lngSlipNo, $objDB))
		{
			fncOutputError ( 9051, DEF_FATAL, "Ǽ����ɼ������Ͽ����", TRUE, "", $objDB );
		}
*/
		// �ȥ�󥶥�����󥳥ߥå�
		$objDB->transactionCommit();

		// ��Ͽ��λ���̤�ɽ��
		$aryDeleteData = $aryHeadResult;
		$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
		$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

		// ���쥳���ɡ����ܸ�
		$aryDeleteData["lngLanguageCode"] = 1;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

		// �ƥ�ץ졼������
		$objTemplate->replace( $aryDeleteData );
		$objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;

		$objDB->close();

		return true;
	}

	// --------------------------------
	//    �ץ�ӥ塼ɽ��
	// --------------------------------

	return true;

	function EncodeToJson($object){
		$json = base64_encode(json_encode($object));
		return $json;
	}

	function DecodeFromJson($json){
		$object = json_decode(base64_decode($json), true);
		return $object;
	}

	// ���顼���̤ؤ�����
	function MoveToErrorPage($strMessage){
		
		// ���쥳���ɡ����ܸ�
		$aryHtml["lngLanguageCode"] = 1;

		// ���顼��å�����������
		$aryHtml["strErrorMessage"] = $strMessage;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;

		exit;
	}


?>