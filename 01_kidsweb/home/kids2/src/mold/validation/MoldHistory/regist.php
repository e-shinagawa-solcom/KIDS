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
require_once (SRC_ROOT.'/mold/lib/index/FormMoldHistory.class.php');
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

// 1800 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1801 �ⷿ����(��Ͽ)
if ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ¸�ߥ����å����顼�ꥹ��
$errMstList = array();
// ��̣�����å����顼
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
// �ޥ��������å�(���եե����ޥåȥ����å���Ʊ���˹Ԥ�)
// ------------------------------------------------------------------------------
// ���ʥ�����
if (!$utilProduct->existsProductCode($_REQUEST[FormMoldHistory::ProductCode]))
{
	$errMstList[FormMoldHistory::ProductCode] =
		"[���ʥ�����]->���ʥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldHistory::ProductCode];
}

// �ⷿ���ơ����� -> ��̳�����ɥޥ�������
if (!$utilBussinesscode->getDescription("�ⷿ���ơ�����", $_REQUEST[FormMoldHistory::Status], true))
{
	$errMstList[FormMoldHistory::ReportCategory] =
		"[���ơ�����]->��̳�����ɥޥ������¸�ߤ��ʤ��ͤǤ���:".
		$_REQUEST[FormMoldHistory::ReportCategory];
}
// �»��� -> ���եե����ޥåȥ����å�
if (!$utilValidation::checkDateFormatYMD($_REQUEST[FormMoldHistory::ActionDate]))
{
	$errMstList[FormMoldHistory::ActionDate] =
		"[�»���]->yyyy/mm/dd��������ͭ�������դ����Ϥ��Ƥ���������:".
		$_REQUEST[FormMoldHistory::ActionDate];
}
// �ⷿ���ơ����������顼�Ǥʤ���10:��ư������20:�ֵ��Ǥξ��
if (!$errMstList[FormMoldHistory::Status] &&
		($_REQUEST[FormMoldHistory::Status] == "10" || $_REQUEST[FormMoldHistory::Status] == "20"))
{
	// �ݴɸ����� -> ��ҥޥ������� (ɽ����ҥ�����)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldHistory::SourceFactory]))
	{
		$errMstList[FormMoldHistory::SourceFactory] =
			"[�ݴɸ�����]->��ҥޥ������¸�ߤ��ʤ��ͤǤ���:".
			$_REQUEST[FormMoldHistory::SourceFactory];
	}

	// ��ư�蹩�� -> ��ҥޥ������� (ɽ����ҥ�����)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldHistory::DestinationFactory]))
	{
		$errMstList[FormMoldHistory::DestinationFactory] =
			"[��ư�蹩��]->��ҥޥ������¸�ߤ��ʤ��ͤǤ���:".
			$_REQUEST[FormMoldHistory::DestinationFactory];
	}
}

// �ⷿNO���Ǥ����
$molds = $utilMold::extractArray($_REQUEST, FormMoldHistory::MoldNo);

// �ⷿNO/�ⷿ�����η����0��ξ��
if (!count($molds))
{
	$errMstList["Mold"] =
			"[�ⷿ]->�ⷿ�����򤷤Ƥ���������";
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
// ��̣�����å� (�ޥ��������å����̤ä����ˤΤߥ����å���Ԥ�)
// ------------------------------------------------------------------------------
if (!count($errMstList))
{
	// ��Ͽ��ǽ�ʶⷿ�ֹ�ꥹ��
	$moldSelectionList = $utilMold->selectMoldSelectionList($_REQUEST[FormMoldHistory::ProductCode]);
	// �ۥ磻�ȥꥹ�Ⱥ���
	foreach ($moldSelectionList as $row => $columns)
	{
		$moldNoList[] = $columns[TableMoldHistory::MoldNo];
	}
	// ���򤷤��ⷿ���ʬ����
	foreach ($molds as $index => $moldNo)
	{
		// �ֶⷿNO�� <=> �����ʥ����ɡ״�Ϣ�����å�
		if (!$utilMold->existsMoldNoWithProductCode($moldNo,
				$_REQUEST[FormMoldHistory::ProductCode]))
		{
			$message = "[�ⷿNO]���ʥ�����(".$_REQUEST[FormMoldHistory::ProductCode].")��".
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

	// �ⷿ���ơ�������10:��ư������20:�ֵ��Ǥξ��
	if (($_REQUEST[FormMoldHistory::Status] == "10" ||
		$_REQUEST[FormMoldHistory::Status] == "20"))
	{
		// �оݤΡֶⷿNO�פ����Ƥ��ݴɹ��줬���Ϥ��줿�ݴɸ������Ʊ�칩��Ǥ��뤳��
		// ���Ԥ����ҥ�����
		$expectedCompanyCode =
		$utilCompany->selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldHistory::SourceFactory]);

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
					selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldHistory::SourceFactory]))
			{
				$message = "[�ݴɸ�����]���ꤵ�줿�ݴɹ���ȶⷿNO:".$moldNo."�θ��ߤ��ݴɹ��줬���פ��ޤ���Ǥ�����\n";
				array_key_exists(FormMoldHistory::SourceFactory, $errSemanticList) ?
				$errSemanticList[FormMoldHistory::SourceFactory] += $message:
				$errSemanticList[FormMoldHistory::SourceFactory] = $message;
			}
		}

		// �ݴɸ�����Ȱ�ư�蹩�줬Ʊ�칩��Ǥʤ�����
		if ($_REQUEST[FormMoldHistory::SourceFactory] == $_REQUEST[FormMoldHistory::DestinationFactory])
		{
			$errSemanticList[FormMoldHistory::DestinationFactory] =
			"[��ư�蹩��]�ݴɹ����Ʊ��ι���ϻ���Ǥ��ޤ���";
		}
	}
}

// ���ڤ�OK�ξ��
if (!count($errMstList) && !count($errSemanticList))
{
	// Ģɼ��ʬ�̤�;�פʥե�����ǡ�����������
	switch ($_REQUEST[FormMoldHistory::Status])
	{
		case "10": // ��ư��
		case "20": // �ֵ���
			// ������Ǥʤ�
			break;
		default: // ����ʳ�(�Ѵ���)
			// �ݴɸ��������
			unset($_REQUEST[FormMoldHistory::SourceFactory]);
			// ��ư�蹩�����
			unset($_REQUEST[FormMoldHistory::DestinationFactory]);
			break;
	}

	// ʸ����򥵥˥�����
	foreach ($_REQUEST as $key => $value)
	{
		if (is_string($value))
		{
			$_REQUEST[$key] = htmlspecialchars($value);
		}
	}

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