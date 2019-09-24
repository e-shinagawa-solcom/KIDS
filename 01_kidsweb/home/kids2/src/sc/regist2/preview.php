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
		// --------------------------
		//  ��Ͽ�ǡ�������
		// --------------------------
		// �ץ�ӥ塼ɽ�������Ͽ������Ԥ����ᡢ���ϥǡ�����json���Ѵ��������򤹤�
		$aryHeader = $_POST["aryHeader"];
		$aryDetail = $_POST["aryDetail"];
		$aryData["aryHeaderJson"] = EncodeToJson($aryHeader);
		$aryData["aryDetailJson"] = EncodeToJson($aryDetail);

		// --------------------------------
		//  ʸ���������Ѵ���UTF-8->EUC-JP��
		// --------------------------------
		//jQuery��ajax��POST�����ʸ�������ɤ� UTF-8 �ˤʤä�
		//�ǡ�����Ͽ���˥��顼�ˤʤ뤿�ᡢEUC-JP���Ѵ�����
		$aryHeader = fncConvertArrayHeaderToEucjp($aryHeader);
		$aryDetail = fncConvertArrayDetailToEucjp($aryDetail);

		// --------------------------
		//  �ץ�ӥ塼����
		// --------------------------
		//��Ͽ�ǡ�����Excel�ƥ�ץ졼�ȤȤ���ץ�ӥ塼HTML����������
		$aryPreview = fncGenerateReportPreview($aryHeader, $aryDetail, $objDB, $objAuth);

		// --------------------------
		//  �ץ�ӥ塼����ɽ��
		// --------------------------
		$aryData["PREVIEW_STYLE"] = $aryPreview["PreviewStyle"];
		$aryData["PREVIEW_DATA"] = $aryPreview["PreviewData"];
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		echo $objTemplate->strTemplate;

		return true;
	}

	//-------------------------------------------------------------------------
	//  ��Ͽ����
	//-------------------------------------------------------------------------
	if ($strMode == "regist"){
		// --------------------------
		//  ��Ͽ�ǡ�������
		// --------------------------
		// �ץ�ӥ塼ɽ���������򤷤���Ͽ�ǡ�����json������������
		$aryHeader = DecodeFromJson($_POST["aryHeaderJson"]);
		$aryDetail = DecodeFromJson($_POST["aryDetailJson"]);

		// --------------------------------
		//  ʸ���������Ѵ���UTF-8->EUC-JP��
		// --------------------------------
		// json�Ѵ�����ʸ�������ɤ� UTF-8 �ˤʤä�
		// �ǡ�����Ͽ���˥��顼�ˤʤ뤿�ᡢEUC-JP���᤹
		$aryHeader = fncConvertArrayHeaderToEucjp($aryHeader);
		$aryDetail = fncConvertArrayDetailToEucjp($aryDetail);

		// --------------------------
		//  ��Ͽ���Х�ǡ������
		// --------------------------
		// ������֥����ɤ�2�ʳ������٤�¸�ߤ���ʤ饨�顼�Ȥ���
		if(fncNotReceivedDetailExists($aryDetail, $objDB))
		{
			MoveToErrorPage("Ǽ�ʽ�ȯ�ԤǤ��ʤ����֤����٤����򤵤�Ƥ��ޤ���");
		}

		// --------------------------
		//  �ǡ����١�����Ͽ
		// --------------------------
		// �ȥ�󥶥�����󳫻�
		$objDB->transactionBegin();

		// ����ޥ�������
		$updResult = fncUpdateReceiveMaster($aryDetail, $objDB);
		if (!$updResult){
			MoveToErrorPage("����ǡ����ι����˼��Ԥ��ޤ�����");
		}

		// ���ޥ�����Ͽ�����ܺ���Ͽ��Ǽ����ɼ�ޥ�����Ͽ��Ǽ����ɼ������Ͽ
		$aryRegResult = fncRegisterSalesAndSlip($aryHeader, $aryDetail, $objDB, $objAuth);
		if (!$aryRegResult["result"]){
			MoveToErrorPage("��塦Ǽ����ɼ�ǡ�������Ͽ�˼��Ԥ��ޤ�����");
		}

		// ���ߥå�
		$objDB->transactionCommit();

		// --------------------------
		//  ��Ͽ��̲���ɽ��
		// --------------------------
		// ���̤�ɽ������ѥ�᡼��������
		// Ǽ�ʽ�NO��ɳ�Ť��������μ���
		$dtmInsertDate = fncGetSlipInsertDate($aryRegResult["strSlipCode"][0], $objDB);
		// ������������
		$aryData["dtmInsertDate"] = $dtmInsertDate;
		// Ǽ�ʽ�NO������
		$aryData["strSlipCode"] = implode(",", $aryRegResult["strSlipCode"]);

		// �ƥ�ץ졼�Ȥ��鹽�ۤ���HTML�����
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();
		echo $objTemplate->strTemplate;

		return true;
	}


	// -----------------------------------
	//   Ģɼɽ������ץ�
	// -----------------------------------
	// if($strMode == "chouhyou-sample"){
	// 	// ���ܸ���б������硢����1�Ԥ�ɬ��
	// 	ini_set('default_charset', 'UTF-8');

	// 	// �ɤ߹���
	// 	$file = mb_convert_encoding('template\Ǽ�ʽ�temple_B��_Ϣ�����.xlsx', 'UTF-8','EUC-JP' );
	// 	$sheetname = mb_convert_encoding('B������', 'UTF-8','EUC-JP' );
	// 	$cellValue = mb_convert_encoding('���̤��ͤ򥻥å�', 'UTF-8','EUC-JP' );
	// 	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
	// 	$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);
	// 	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
	// 	$outStyle = $writer->generateStyles(true);
	// 	$outSheetData = $writer->generateSheetData();
	// 	$outStyle = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
	// 	$outSheetData = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');

	// 	$aryData["PREVIEW_STYLE"] = $outStyle;
	// 	$aryData["PREVIEW_DATA"] = $outSheetData;

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