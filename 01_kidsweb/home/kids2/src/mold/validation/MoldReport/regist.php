<?php
// ----------------------------------------------------------------------------
/**
*       �ⷿĢɼ����  ��Ͽ �ե�����ǡ�������
*/
// ----------------------------------------------------------------------------
require_once('conf.inc');
require_once(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');

// ���֥�����������
$objDB   = new clsDB();
$objAuth = new clsAuth();

// DB�����ץ�
$objDB->open("", "", "", "");

// ���å�����ǧ
$objAuth = fncIsSession( $_REQUEST["strSessionID"], $objAuth, $objDB );

// 1900 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1901 �ⷿ����(��Ͽ)
if ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ¸�ߥ����å����顼�ꥹ��
$errMstList = array();
// ��̣�����å����顼�ꥹ��
$errSemanticList = array();

// �桼�ƥ���ƥ����饹�Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilValidation = UtilValidation::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// �ȥ�󥶥�����󳫻�
$objDB->transactionBegin();

// �ⷿ��Ϣ�ơ��֥�Υ�å�
// ������λ���Υ���Хå�����Ԥ���
pg_query("LOCK m_moldreport");
pg_query("LOCK t_moldreportdetail");
pg_query("LOCK t_moldreportrelation");
pg_query("LOCK t_moldhistory");

// ------------------------------------------------------------------------------
// ¸�ߥ����å�(���եե����ޥåȥ����å���Ʊ���˹Ԥ�)
// ------------------------------------------------------------------------------
// ���ʥ�����
if (!$utilProduct->existsProductCode($_REQUEST[FormMoldReport::ProductCode]))
{
	$errMstList[FormMoldReport::ProductCode] =
		"[���ʥ�����]->���ʥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::ProductCode];
}

// Ģɼ��ʬ -> ��̳�����ɥޥ�������
if (!$utilBussinesscode->getDescription("Ģɼ��ʬ", $_REQUEST[FormMoldReport::ReportCategory], true))
{
	$errMstList[FormMoldReport::ReportCategory] =
		"[Ģɼ��ʬ]->��̳�����ɥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::ReportCategory];
}
// ������ -> ���եե����ޥåȥ����å�
if (!$utilValidation::checkDateFormatYMD($_REQUEST[FormMoldReport::RequestDate]))
{
	$errMstList[FormMoldReport::RequestDate] =
		"[������]->yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������:".
		$_REQUEST[FormMoldReport::RequestDate];
}
// �����ʬ -> ��̳�����ɥޥ�������
if (!$utilBussinesscode->getDescription("�����ʬ", $_REQUEST[FormMoldReport::RequestCategory], true))
{
	$errMstList[FormMoldReport::RequestCategory] =
		"[�����ʬ]->��̳�����ɥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::RequestCategory];
}

// ��˾�� -> ���եե����ޥåȥ����å�
if (!$utilValidation::checkDateFormatYMD($_REQUEST[FormMoldReport::ActionRequestDate]))
{
	$errMstList[FormMoldReport::ActionRequestDate] =
		"[��˾��]->yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������:".
		$_REQUEST[FormMoldReport::ActionRequestDate];
}

// �ؼ���ʬ -> ��̳�����ɥޥ�������
if (!$utilBussinesscode->getDescription("�ؼ���ʬ", $_REQUEST[FormMoldReport::InstructionCategory], true))
{
	$errMstList[FormMoldReport::InstructionCategory] =
		"[�ؼ���ʬ]->��̳�����ɥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::InstructionCategory];
}

// ������(�ܵ�)������ -> ��ҥޥ������� (ɽ����ҥ�����)
if (!$utilCompany->existsCustomerCode($_REQUEST[FormMoldReport::CustomerCode]))
{
	$errMstList[FormMoldReport::CustomerCode] =
		"[������(�ܵ�)]->��ҥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::CustomerCode];
}

// ô������ -> ���롼�ץޥ�������(ɽ�����롼�ץ�����)
if (!$utilGroup->existsGroupCode($_REQUEST[FormMoldReport::KuwagataGroupCode]))
{
	$errMstList[FormMoldReport::KuwagataGroupCode] =
		"[ô������]->���롼�ץޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::KuwagataGroupCode];
}
// ô���� -> �桼���ޥ�������(ɽ���桼��������)
if (!$utilUser->existsUserCode($_REQUEST[FormMoldReport::KuwagataUserCode]))
{
	$errMstList[FormMoldReport::KuwagataUserCode] =
		"[ô����]->�桼���ޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldReport::KuwagataUserCode];
}

// Ģɼ��ʬ�����顼�Ǥʤ���10:��ư������20:�ֵ��Ǥξ��
if (!$errMstList[FormMoldReport::ReportCategory] &&
		($_REQUEST[FormMoldReport::ReportCategory] == "10" || $_REQUEST[FormMoldReport::ReportCategory] == "20"))
{
	// ��ư��ˡ -> ��̳�����ɥޥ�������
	if (!$utilBussinesscode->getDescription("��ư��ˡ", $_REQUEST[FormMoldReport::TransferMethod], true))
	{
		$errMstList[FormMoldReport::TransferMethod] =
			"[��ư��ˡ]->��̳�����ɥޥ������¸�ߤ��ʤ��ͤǤ���:".
			$_REQUEST[FormMoldReport::TransferMethod];
	}

	// ������ν��� -> ��̳�����ɥޥ�������
	if (!$utilBussinesscode->getDescription("������ν���", $_REQUEST[FormMoldReport::FinalKeep], true))
	{
		$errMstList[FormMoldReport::FinalKeep] =
			"[������ν���]->��̳�����ɥޥ������¸�ߤ��ʤ��ͤǤ���:".
			$_REQUEST[FormMoldReport::FinalKeep];
	}
	// 20:�ݴɹ�����ֵѤ��� �ξ��
	else if ($_REQUEST[FormMoldReport::FinalKeep] == "20")
	{
		// �ֵ�ͽ���� -> ���եե����ޥåȥ����å�
		if (!$utilValidation->checkDateFormatYMD($_REQUEST[FormMoldReport::ReturnSchedule]))
		{
			$errMstList[FormMoldReport::ReturnSchedule] =
				"[�ֵ�ͽ����]->yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������:".
				$_REQUEST[FormMoldReport::ReturnSchedule];
		}
	}

	// �ݴɸ����� -> ��ҥޥ������� (ɽ����ҥ�����)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldReport::SourceFactory]))
	{
		$errMstList[FormMoldReport::SourceFactory] =
			"[�ݴɸ�����]->��ҥޥ������¸�ߤ��ʤ��ͤǤ���:".
			$_REQUEST[FormMoldReport::SourceFactory];
	}

	// ��ư�蹩�� -> ��ҥޥ������� (ɽ����ҥ�����)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldReport::DestinationFactory]))
	{
		$errMstList[FormMoldReport::DestinationFactory] =
			"[��ư�蹩��]->��ҥޥ������¸�ߤ��ʤ��ͤǤ���:".
			$_REQUEST[FormMoldReport::DestinationFactory];
	}
}

// �ⷿNO���Ǥ����
$molds = $utilMold::extractArray($_REQUEST, FormMoldReport::MoldNo);
$descs = $utilMold::extractArray($_REQUEST, FormMoldReport::MoldDescription);

// �ⷿNO/�ⷿ�����η����0��ξ��
if (!count($molds) || !count($descs))
{
	$errMstList["Mold"] =
			"[�ⷿ]->�ⷿ�����򤷤Ƥ���������";
}
// �ⷿNO�ȶⷿ�����η�����԰���
else if(count($molds) != count($descs))
{
	$errMstList["Mold"] =
			"[�ⷿ]->�ⷿ�ȶⷿ�����η�����԰��פǤ���";
}

// �ⷿNO/�ⷿ�����θĿ�������ξ��
else
{
	// �ⷿNO���ǿ�ʬ����
	foreach($molds as $index => $moldNo)
	{
		// �ⷿNO��¸�ߥ����å�
		if (!$utilMold->existsMoldNo($moldNo))
		{
			$errMstList[$index] =
				"[".$index."]�ⷿ�ޥ������¸�ߤ��ʤ��ͤǤ���:".$moldNo;
		}
	}
}

// ------------------------------------------------------------------------------
// ��̣�����å� (¸�ߥ����å����̤ä����ˤΤߥ����å���Ԥ�)
// ------------------------------------------------------------------------------
if (!count($errMstList))
{
	$usedMoldNoList = array();
	$moldNoList = array();
	// �ָܵ�����(���ʥ�����)�פ������ʥ����ɡפ�ɳ�դ���Τ������å�
	if (!$utilProduct->existsGoodsCodeWithProductCode($_REQUEST[FormMoldReport::GoodsCode],
			$_REQUEST[FormMoldReport::ProductCode]))
	{
		$errSemanticList[FormMoldReport::GoodsCode] =
			"[�ܵ�����]���ʥ����ɤȤ��ȹ礻���԰��פǤ���";
	}

	// ��Ͽ��ǽ�ʶⷿ�ֹ�ꥹ��
	$moldSelectionList = $utilMold->selectMoldSelectionList($_REQUEST[FormMoldHistory::ProductCode]);
	// �ۥ磻�ȥꥹ�Ⱥ���
	foreach ($moldSelectionList as $row => $columns)
	{
		$moldNoList[] = $columns[TableMoldHistory::MoldNo];
	}
	// �ֶⷿNO�� <=> �����ʥ����ɡ״�Ϣ�����å�
	foreach ($molds as $index => $moldNo)
	{
		if (!$utilMold->existsMoldNoWithProductCode($moldNo,
				$_REQUEST[FormMoldReport::ProductCode],
				$_REQUEST[FormMoldReport::ReviseCode]))
		{
			$message = "[�ⷿNO]���ʥ�����(".$_REQUEST[FormMoldReport::ProductCode]."_".$_REQUEST[FormMoldReport::ReviseCode].")��".
					"�ⷿNO(".$moldNo.")�ȹ礻���԰��פǤ���";

			array_key_exists("MoldNo<->ProductCode", $errSemanticList) ?
			$errSemanticList["MoldNo<->ProductCode"] = $message :
			$errSemanticList["MoldNo<->ProductCode"] += $message;
		}
		// ��Ͽ��ǽ�������å�
		if(!in_array($moldNo, $moldNoList))
		{
			$usedMoldNoList[] = $moldNo;
		}
	}
	// ��Ͽ�ԲĤʶⷿ�ֹ椬���Ф��줿���
	if(count($usedMoldNoList))
	{
		$message = implode("\n ", $usedMoldNoList);

		$errSemanticList[FormMoldHistory::MoldNo] =
		"����:\n ".
		"�ⷿNO\n".
		"�о�:\n ".
		$message."\n".
		"\n".
		"�ʲ��˳�������ⷿ������Ǥ��ޤ���\n".
		"���Ѵ����줿�ⷿ\n".
		"��̤�����μ»�������Ķⷿ\n".
		"��̤��λ�ζⷿĢɼ��ɳ�Ť��ⷿ\n";
	}

	// ��ô���ԡפ���ô������פλҤǤ��뤳��
	if (!$utilUser->existsUserCodeWithGroupCode($_REQUEST[FormMoldReport::KuwagataUserCode],
			$_REQUEST[FormMoldReport::KuwagataGroupCode]))
	{
		$errSemanticList[FormMoldReport::KuwagataUserCode] =
			"[ô����]����Ȥ��ȹ礻���԰��פǤ���";
	}

	// �ִ�˾���פ�����������̤�����Ǥ��뤳��
	if (!$utilValidation->isFutureDate($_REQUEST[FormMoldReport::ActionRequestDate]))
	{
		$errSemanticList[FormMoldReport::ActionRequestDate] =
			"[��˾��]�����ʹߤ����դ����Ϥ��Ƥ���������";
	}

	// ��������ν����פ� 20:RETURN TO ORIGINAL(�ݴɹ�����ֵѤ���)�ξ��
	if ($_REQUEST[FormMoldReport::FinalKeep] == "20")
	{
		// ���ֵ�ͽ�����פ��������ȡִ�˾���פ���̤�����Ǥ��뤳��
		if (!$utilValidation->isFutureDate($_REQUEST[FormMoldReport::ReturnSchedule]))
		{
			$errSemanticList[FormMoldReport::ReturnSchedule] =
			"[�ֵ�ͽ����]�����ʹߤ����դ����Ϥ��Ƥ���������";
		}
		// ���ֵ�ͽ�����פ��ִ�˾���פ���̤�����Ǥ��뤳��
		else if ($utilValidation->compareDate($_REQUEST[FormMoldReport::ReturnSchedule],
				$_REQUEST[FormMoldReport::ActionRequestDate]) != 1)
		{
			$errSemanticList[FormMoldReport::ReturnSchedule] =
			"[�ֵ�ͽ����]�ִ�˾���פ������ʹߤ����դ����Ϥ��Ƥ���������";
		}
	}

	// Ģɼ��ʬ��10:��ư������20:�ֵ��Ǥξ��
	if (($_REQUEST[FormMoldReport::ReportCategory] == "10" ||
		 $_REQUEST[FormMoldReport::ReportCategory] == "20"))
	{
		// �оݤΡֶⷿNO�פ����Ƥ��ݴɹ��줬���Ϥ��줿�ݴɸ������Ʊ�칩��Ǥ��뤳��
		// ���Ԥ����ҥ�����
		$expectedCompanyCode =
		$utilCompany->selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldReport::SourceFactory]);

		foreach ($molds as $index => $moldNo)
		{
			// �ⷿ���򤫤�ǿ��ΰ�ư�蹩������
			$currentFactory = $utilMold->selectCurrentStorageOfMold($moldNo);

			// �ⷿ����¸�ߤ��ʤ����(���)
			if (!$currentFactory)
			{
				// ͭ���ʶⷿ����¸�ߤ��ʤ��ⷿ�ϻ����층�򸽺ߤ��ݴɹ���Ȥ��ư���
				$currentFactory = $utilMold->selectMoldVender($moldNo);
			}

			// ���ߤ��ݴɹ�������Ϥ��줿�ݴɹ��줬���פ��ʤ����
			if ($currentFactory != $utilCompany->
					selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldReport::SourceFactory]))
			{
				$message = "[�ݴɸ�����]���ꤵ�줿�ݴɹ���ȶⷿNO:".$moldNo."�θ��ߤ��ݴɹ��줬���פ��ޤ���Ǥ�����\n";
				array_key_exists(FormMoldReport::SourceFactory, $errSemanticList) ?
				$errSemanticList[FormMoldReport::SourceFactory] += $message:
				$errSemanticList[FormMoldReport::SourceFactory] = $message;
			}
		}

		// �ݴɸ�����Ȱ�ư�蹩�줬Ʊ�칩��Ǥʤ�����
		if ($_REQUEST[FormMoldReport::SourceFactory] == $_REQUEST[FormMoldReport::DestinationFactory])
		{
			$errSemanticList[FormMoldReport::DestinationFactory] =
			"[��ư�蹩��]�ݴɹ����Ʊ��ι���ϻ���Ǥ��ޤ���";
		}
	}
}

// ���ڤ�OK�ξ��
if (!count($errMstList) && !count($errSemanticList))
{
	// Ģɼ��ʬ�̤�;�פʥե�����ǡ�����������
	switch ($_REQUEST[FormMoldReport::ReportCategory])
	{
		case "10": // ��ư��
		case "20": // �ֵ���
			// ��������ν����פ� 20:RETURN TO ORIGINAL(�ݴɹ�����ֵѤ���)�ʳ�
			if ($_REQUEST[FormMoldReport::FinalKeep] != "20")
			{
				// �ֵ�ͽ��������
				unset($_REQUEST[FormMoldReport::ReturnSchedule]);
			}
			break;
		default: // ����ʳ�(�Ѵ���)
			// �ݴɸ��������
			unset($_REQUEST[FormMoldReport::SourceFactory]);
			// ��ư�蹩�����
			unset($_REQUEST[FormMoldReport::DestinationFactory]);
			// ��ư��ˡ����
			unset($_REQUEST[FormMoldReport::TransferMethod]);
			// ������ν�������
			unset($_REQUEST[FormMoldReport::FinalKeep]);
			// �ֵ�ͽ��������
			unset($_REQUEST[FormMoldReport::ReturnSchedule]);
			break;
	}

	// ʸ����򥵥˥�����
	// foreach ($_REQUEST as $key => $value)
	// {
	// 	if (is_string($value))
	// 	{
	// 		$_REQUEST[$key] = htmlspecialchars($value);
	// 	}
	// }

	// �ⷿ����Υ����������Ⱥ���
	$summaryHistory = $utilMold->selectSummaryOfMoldHistory($molds);
	$digestHistory = FormCache::hash_arrays($summaryHistory);
	$_REQUEST["digest_history"] = $digestHistory;

	// �ⷿĢɼ�Υ����������Ⱥ���
	$summaryReport = $utilMold->selectSummaryOfMoldReport($molds);
	$digestReport = FormCache::hash_arrays($summaryReport);
	$_REQUEST["digest_report"] = $digestReport;

	// �ⷿ�ꥹ�Ȥ�ꥯ�����Ȥ˳�Ǽ
	$_REQUEST["list_moldno"] = $molds;

	// ����å��奤�󥹥��󥹤μ���
	$formCache = FormCache::getInstance();
	// �桼������������
	$formCache->setUserCode($objAuth->UserCode);

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	try
	{
		// �ե����७��å���˳�Ǽ
		$resultHash["resultHash"] = $formCache::hash_arrays($_REQUEST);
		$formCache->add($resultHash["resultHash"], $_REQUEST);
	}
	catch (Exception $e)
	{
		// �ȥ�󥶥������ ����Хå�
		$objDB->transactionRollback();
		throw $e;
	}

	// �ȥ�󥶥������ ���ߥå�
	$objDB->transactionCommit();

	// �쥹�ݥ󥹥إå�����)(json)
	header('Content-Type: application/json');
	$json = json_encode($resultHash, JSON_PRETTY_PRINT);
	echo $json;
}
// ���ڤ�NG�ξ��
else
{
	// ���顼�������Υޡ���
	$errors = array_merge($errMstList, $errSemanticList);

	// json�Ѵ��ΰ١����Ū��UTF-8���Ѵ�
	mb_convert_variables('UTF-8', 'EUC-JP', $errors);
	// �쥹�ݥ󥹥إå�����)(json)
	header('Content-Type: application/json');
	$json = json_encode($errors, JSON_PRETTY_PRINT);
	echo $json;
}