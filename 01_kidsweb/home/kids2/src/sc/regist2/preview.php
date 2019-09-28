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
		//�ǡ�����Ͽ���˥��顼�ˤʤ뤿�ᡢDB��������EUC-JP���Ѵ�����
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
		// �ƥ�ץ졼�Ȥ��鹽�ۤ���HTML�����
		$aryData["PREVIEW_STYLE"] = $aryPreview["PreviewStyle"];
		$aryData["PREVIEW_DATA"] = $aryPreview["PreviewData"];
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		echo $objTemplate->strTemplate;

		// DB����
		$objDB->close();
		// ������λ
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
		// TODO:��ӥ�����ֹ��������褦�ˤ���
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
		// TODO:ary��ʣ�������˼����ѹ�
		$dtmInsertDate = fncGetSlipInsertDate($aryRegResult["strSlipCode"][0], $objDB);
		// TODO:ʣ�����б���TABLE��TR����Ϥ���function���ɲá�Ǽ�ʽ�NO�ȥ�ӥ�����ֹ��ʻ���������ߡ�
		// ������������
		$aryData["dtmInsertDate"] = $dtmInsertDate;
		// Ǽ�ʽ�NO������
		$aryData["strSlipCode"] = $aryRegResult["strSlipCode"][0];

		// �ƥ�ץ졼�Ȥ��鹽�ۤ���HTML�����
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();
		echo $objTemplate->strTemplate;

		// DB����
		$objDB->close();
		// ������λ
		return true;
	}

	if ($strMode == "download"){
		//TODO:Ģɼ��������ɤμ�����ajax POST�Ǽ���
		//�ѥ�᡼���Ȥ���Ǽ�ʽ�NO�ȥ�ӥ�����ֹ��������
		$strDownloadSlipCode = $_POST["strdownloadslipcode"];
		$lngDownloadRevisionNo = $_POST["lngdownloadrevisionno"];

		//TODO:Ģɼ�����ǡ�����DB������
		//$aryDownloadData = fncGetSlipDownloadData($strDownloadSlipCode, $lngDownloadRevisionNo);

		//TODO:��Ͽ�ǡ�����Excel�ƥ�ץ졼�ȤȤ����������ɤ���Excel���֥������Ȥ��������
		//fncDownloadReportExcel($aryHeader, $aryDetail, $objDB, $objAuth);

		// TODO:MIME�����פ򥻥åȤ��ƥ��������
		//   //MIME�����ס�https://technet.microsoft.com/ja-jp/ee309278.aspx
		//   header("Content-Description: File Transfer");
		//   header('Content-Disposition: attachment; filename="weather.xlsx"');
		//   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//   header('Content-Transfer-Encoding: binary');
		//   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		//   header('Expires: 0');
		//   ob_end_clean(); //�Хåե��õ�
		   
		//   $writer = new XlsxWriter($spreadsheet);
		//   $writer->save('php://output');
		
		// TODO:���곫��

		// ������λ
		return true;

	}

	// �̾盧������뤳�Ȥ�̵���������ʥ⡼�ɤ�POST������礳��������
	echo "�����ʥ⡼�ɤ�POST����ޤ���";
	return true;

	// �إ�Ѵؿ���json���󥳡��ɸ��base64���󥳡���
	// base64�Ѵ�����Τ� HTML��hidden�ե�����ɤ˰����ʷ��ǳ�Ǽ���뤿�ᡣ
	function EncodeToJson($object){
		$json = base64_encode(json_encode($object));
		return $json;
	}

	// �إ�Ѵؿ���base64�ǥ����ɸ��json�ǥ�����
	function DecodeFromJson($json){
		$object = json_decode(base64_decode($json), true);
		return $object;
	}

	// �إ�Ѵؿ������顼���̤ؤ�����
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