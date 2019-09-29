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
		//  ��Ͽ/�����ǡ�������
		// --------------------------
		// �����оݤ�ɳ�Ť��ǡ����ʽ������˥��åȡ���Ͽ���϶���
		$lngRenewTargetSlipNo = $_POST["lngRenewTargetSlipNo"];
		$strRenewTargetSlipCode = $_POST["strRenewTargetSlipCode"];
		$lngRenewTargetSalesNo = $_POST["lngRenewTargetSalesNo"];
		$strRenewTargetSalesCode = $_POST["strRenewTargetSalesCode"];
		$aryData["lngRenewTargetSlipNo"] = $lngRenewTargetSlipNo;
		$aryData["strRenewTargetSlipCode"] = $strRenewTargetSlipCode;
		$aryData["lngRenewTargetSalesNo"] = $lngRenewTargetSalesNo;
		$aryData["strRenewTargetSalesCode"] = $strRenewTargetSalesCode;

		// �ץ�ӥ塼ɽ�������Ͽ/����������Ԥ����ᡢ���ϥǡ�����json���Ѵ��������򤹤�
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
	//  ��Ͽ/��������
	//-------------------------------------------------------------------------
	if ($strMode == "regist-or-renew"){
		// --------------------------
		//  ��Ͽ/�����ǡ�������
		// --------------------------
		// �����оݤ�ɳ�Ť�Ǽ����ɼ�ֹ����Ͽ�ξ��϶���
		$lngRenewTargetSlipNo = $_POST["lngRenewTargetSlipNo"];
		// �����оݤ�ɳ�Ť�Ǽ�ʥ����ɡ���Ͽ�ξ��϶���
		$strRenewTargetSlipCode = $_POST["strRenewTargetSlipCode"];
		// �����оݤ�ɳ�Ť�����ֹ����Ͽ�ξ��϶���
		$lngRenewTargetSalesNo = $_POST["lngRenewTargetSalesNo"];
		// �����оݤ�ɳ�Ť���女���ɡ���Ͽ�ξ��϶���
		$strRenewTargetSalesCode = $_POST["strRenewTargetSalesCode"];

		// ��Ͽ����������true:��Ͽ��false:������
		$isCreateNew = strlen($lngRenewTargetSlipNo) == 0;
		
		// �ץ�ӥ塼ɽ���������򤷤���Ͽ/�����ǡ�����json������������
		$aryHeader = DecodeFromJson($_POST["aryHeaderJson"]);
		$aryDetail = DecodeFromJson($_POST["aryDetailJson"]);

		// --------------------------------
		//  ʸ���������Ѵ���UTF-8->EUC-JP��
		// --------------------------------
		// json�Ѵ�����ʸ�������ɤ� UTF-8 �ˤʤä�
		// �ǡ�����Ͽ���˥��顼�ˤʤ뤿�ᡢEUC-JP���᤹
		$aryHeader = fncConvertArrayHeaderToEucjp($aryHeader);
		$aryDetail = fncConvertArrayDetailToEucjp($aryDetail);

		//DBG:��������ȥ�����
		// --------------------------
		//  ��Ͽ/�������Х�ǡ������
		// --------------------------
		// ������֥����ɤ�2�ʳ������٤�¸�ߤ���ʤ饨�顼�Ȥ���
		// if(fncNotReceivedDetailExists($aryDetail, $objDB))
		// {
		// 	MoveToErrorPage("Ǽ�ʽ�ȯ�ԤǤ��ʤ����֤����٤����򤵤�Ƥ��ޤ���");
		// }

		//DBG:��������ȥ�����
		// --------------------------
		//  �ǡ����١�������
		// --------------------------
		// // �ȥ�󥶥�����󳫻�
		// $objDB->transactionBegin();

		// // ����ޥ�������
		// $updResult = fncUpdateReceiveMaster($aryDetail, $objDB);
		// if (!$updResult){
		// 	MoveToErrorPage("����ǡ����ι����˼��Ԥ��ޤ�����");
		// }

		// // ���ޥ��������ܺ١�Ǽ����ɼ�ޥ�����Ǽ����ɼ���٤ؤΥ쥳�����ɲá�
		// // Ǽ����ɼ�ֹ椬���ʤ���Ͽ�����Ǥʤ��ʤ齤����Ԥ�
		// $aryRegResult = fncRegisterSalesAndSlip(
		// 	$lngRenewTargetSlipNo, $strRenewTargetSlipCode, $lngRenewTargetSalesNo, $strRenewTargetSalesCode,
		// 	$aryHeader, $aryDetail, $objDB, $objAuth);

		// if (!$aryRegResult["result"]){
		// 	MoveToErrorPage("��塦Ǽ����ɼ�ǡ�������Ͽ�ޤ��Ͻ����˼��Ԥ��ޤ�����");
		// }

		// // ���ߥå�
		// $objDB->transactionCommit();

		// --------------------------
		//  �����оݥǡ����Υ�å����
		// --------------------------
		// �����ξ�硢�����оݥǡ����˥�å��������äƤ���Τǲ������
		if (!$isCreateNew)
		{
			$unlocked = fncReleaseExclusveLock(EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW, $strSlipCode, $objAuth, $objDB);
			if(!$unlocked)
			{
				MoveToErrorPage("Ǽ�ʽ�ǡ����ν������������ޤ���������å�����˼��Ԥ��ޤ���");
			}
		}

		// --------------------------
		//  ��Ͽ��̲���ɽ��
		// --------------------------

		//DBG:TESTCODE
		$aryPage1 = array();
		$aryPage1["strSlipCode"] = "02000307";
		$aryPage1["lngRevisionNo"] = "REV1";
		$aryPage2 = array();
		$aryPage2["strSlipCode"] = "02030554";
		$aryPage2["lngRevisionNo"] = "REV2";
		$aryPerPage = array();
		$aryPerPage[] = $aryPage1;
		$aryPerPage[] = $aryPage2;

		// ������̡ʥơ��֥���ϡ�
		$strHtml = fncGetRegisterResultTableBodyHtml($aryPerPage, $objDB);
		$aryData["tbodyResiterResult"] = $strHtml;

		// ��Ͽ��λ��å�����
		$aryData["strMessage"] = "��Ͽ����λ���ޤ���";

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
		$strSlipCode = $_POST["strSlipCode"];
		$lngRevisionNo = $_POST["lngRevisionNo"];

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
		echo "��������ɤ����Ĥ�ꡣslip=".$strSlipCode.", rev=".$lngRevisionNo;

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