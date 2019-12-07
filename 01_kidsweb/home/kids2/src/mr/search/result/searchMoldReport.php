<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿĢɼ����  ��������
*/
// ----------------------------------------------------------------------------
include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

$objDB   = new clsDB();
$objAuth = new clsAuth();

// DB�����ץ�
$objDB->open("", "", "", "");

// �ꥯ�����ȼ���
$aryData = $_REQUEST;

// ���쥳���ɤ����(0->false: �Ѹ�, 1->true: ���ܸ�)
$lngLanguageCode = 1;

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 1900 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1902 �ⷿĢɼ����(����)
if ( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// ���������Ω�˻��Ѥ���ե�����ǡ��������
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// ɽ�����ܤ����
foreach($isDisplay as $key => $flag)
{
	if ($flag == "on")
	{
		$displayColumns[$key] = $key;
	}
}

// �������ܤ����
foreach($isSearch as $key => $flag)
{
	if ($flag == "on")
	{
		$searchColumns[$key] = $key;
	}
}

// ���������Ω��
$query = array();
$query[] = "SELECT";
$query[] = "      mr.moldreportid";
$query[] = "    , mr.revision";
$query[] = "    , mr.reportcategory";
$query[] = "    , mr.status";
$query[] = "    , mr.requestdate";
$query[] = "    , mr.sendto";
$query[] = "    , mr.attention";
$query[] = "    , mr.carboncopy";
$query[] = "    , mr.productcode || '_' || mr.strrevisecode as productcode";
$query[] = "    , mp.strproductname";
$query[] = "    , mp.strproductenglishname";
$query[] = "    , mr.goodscode";
$query[] = "    , mr.requestcategory";
$query[] = "    , mr.actionrequestdate";
$query[] = "    , mr.actiondate";
$query[] = "    , mr.transfermethod";
$query[] = "    , mr.sourcefactory";
$query[] = "    , mr.destinationfactory";
$query[] = "    , mr.instructioncategory";
$query[] = "    , mr.customercode";
$query[] = "    , mr.kuwagatagroupcode";
$query[] = "    , mr.kuwagatausercode";
$query[] = "    , mr.note";
$query[] = "    , mr.finalkeep";
$query[] = "    , mr.returnschedule";
$query[] = "    , mr.marginalnote";
$query[] = "    , mr.created::date";
$query[] = "    , mr.createby";
$query[] = "    , mr.updated::date";
$query[] = "    , mr.updateby";
$query[] = "    , mr.version";
$query[] = "    , mr.deleteflag";
$query[] = "    , mrd.moldno";
$query[] = "FROM";
$query[] = "    m_moldreport mr";
$query[] = "INNER JOIN";
$query[] = "(";
$query[] = "    SELECT";
$query[] = "         moldreportid";
$query[] = "       , max(revision) revision";
$query[] = "       , array_to_string(array_agg(moldno), ',') moldno";
$query[] = "    FROM";
$query[] = "        t_moldreportdetail";
$query[] = "    WHERE";
$query[] = "        deleteflag = false";
$query[] = "    GROUP BY";
$query[] = "        moldreportid";
$query[] = ") mrd";
$query[] = "  ON";
$query[] = "        mr.moldreportid = mrd.moldreportid";
$query[] = "    AND mrd.revision = mrd.revision";
$query[] = "  LEFT OUTER JOIN ( ";
$query[] = "    SELECT";
$query[] = "      p.* ";
$query[] = "    FROM";
$query[] = "      m_product p ";
$query[] = "      inner join ( ";
$query[] = "        SELECT";
$query[] = "          MAX(lngrevisionno) lngrevisionno";
$query[] = "          ,lngproductno, strrevisecode  ";
$query[] = "        FROM";
$query[] = "          m_product ";
$query[] = "        WHERE";
$query[] = "          bytInvalidFlag = false ";
$query[] = "        group by";
$query[] = "          lngproductno, strrevisecode";
$query[] = "      ) p1 ";
$query[] = "        on p.lngproductno = p1.lngproductno ";
$query[] = "        and p.strrevisecode = p1.strrevisecode ";
$query[] = "        and p.lngrevisionno = p1.lngrevisionno ";
$query[] = "    where";
$query[] = "      p.lngrevisionno >= 0";
$query[] = "  ) mp ";
$query[] = "  ON";
$query[] = "    mr.productcode = mp.strproductcode";
$query[] = "  AND  mr.strrevisecode = mp.strrevisecode";
$query[] = "WHERE";
$query[] = "    (mr.moldreportid, mr.revision) in";
$query[] = "    (";
$query[] = "        SELECT";
$query[] = "              moldreportid";
$query[] = "            , max(revision)";
$query[] = "        FROM";
$query[] = "            m_moldreport";
$query[] = "        WHERE";
$query[] = "            deleteflag = false";
$query[] = "        GROUP BY";
$query[] = "            moldreportid";
$query[] = "    )";
$query[] = "AND (mr.moldreportid, mr.revision) in";
$query[] = "    (";
$query[] = "        SELECT";
$query[] = "              moldreportid";
$query[] = "            , max(revision)";
$query[] = "        FROM";
$query[] = "            t_moldreportdetail";
$query[] = "        WHERE";
$query[] = "            deleteflag = false";
$query[] = "        GROUP BY";
$query[] = "            moldreportid";
$query[] = "    )";

// �桼�ƥ���ƥ��Υ��󥹥��󥹼���
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();

// �������ܤΥ�����ʸ�����Ѵ�
$searchColumns = array_change_key_case($searchColumns, CASE_LOWER);
// �����ͤΥ�����ʸ�����Ѵ�
$searchValue = array_change_key_case($searchValue, CASE_LOWER);
$from = array_change_key_case($from, CASE_LOWER);
$to = array_change_key_case($to, CASE_LOWER);

// Ģɼ��ʬ
if (array_key_exists(TableMoldReport::ReportCategory, $searchColumns) &&
	array_key_exists(TableMoldReport::ReportCategory, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('Ģɼ��ʬ', $searchValue[TableMoldReport::ReportCategory], true))
	{
		$query[] = "AND mr.reportcategory = '".$searchValue[TableMoldReport::ReportCategory]."'";
	}
}

// Ģɼ���ơ�����
if (array_key_exists(TableMoldReport::Status, $searchColumns) &&
	array_key_exists(TableMoldReport::Status, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('Ģɼ���ơ�����', $searchValue[TableMoldReport::Status], true))
	{
		$query[] = "AND mr.status = '".$searchValue[TableMoldReport::Status]."'";
	}
}

// ������
if (array_key_exists(TableMoldReport::RequestDate, $searchColumns) &&
	array_key_exists(TableMoldReport::RequestDate, $from) &&
	array_key_exists(TableMoldReport::RequestDate, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::RequestDate]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::RequestDate]))
{
	$query[] = "AND mr.requestdate".
					" between '".$from[TableMoldReport::RequestDate]."'".
					" AND "."'".$to[TableMoldReport::RequestDate]."'";
}

// �ⷿĢɼID
if (array_key_exists(TableMoldReport::MoldReportId, $searchColumns) &&
	array_key_exists(TableMoldReport::MoldReportId, $from) &&
	array_key_exists(TableMoldReport::MoldReportId, $to))
{
	$query[] = "AND mr.moldreportid".
					" between '".pg_escape_string($from[TableMoldReport::MoldReportId])."'".
					" AND "."'".pg_escape_string($to[TableMoldReport::MoldReportId])."'";
}

// ���ʥ�����
if (array_key_exists(TableMoldReport::ProductCode, $searchColumns) &&
	array_key_exists(TableMoldReport::ProductCode, $from) &&
	array_key_exists(TableMoldReport::ProductCode, $to))
{
	$query[] = "AND mr.productcode".
					" between '".pg_escape_string($from[TableMoldReport::ProductCode])."'".
					" AND "."'".pg_escape_string($to[TableMoldReport::ProductCode])."'";
}

// ����̾��
if (array_key_exists("strproductname", $searchColumns) &&
	array_key_exists("strproductname", $searchValue))
{
	$query[] = "AND mp.strproductname like '%".pg_escape_string($searchValue["strproductname"])."%'";
}
// ����̾��(�Ѹ�)
if (array_key_exists("strproductenglishname", $searchColumns) &&
	array_key_exists("strproductenglishname", $searchValue))
{
	$query[] = "AND mp.strproductenglishname like '%".pg_escape_string($searchValue["strproductenglishname"])."%'";
}

// �ܵ�����
if (array_key_exists(TableMoldReport::GoodsCode, $searchColumns) &&
	array_key_exists(TableMoldReport::GoodsCode, $searchValue))
{
	$query[] = "AND mr.goodscode = '".pg_escape_string($searchValue[TableMoldReport::GoodsCode])."'";
}

// �ⷿNO
if (array_key_exists("moldno", $searchColumns) &&
	array_key_exists("choosenmoldlist", $searchValue) &&
	count($searchValue["choosenmoldlist"]))
{
	$query[] = "AND (";

	// �ⷿ���ʬ����
	foreach ($searchValue["choosenmoldlist"] as $index => $moldno)
	{
		$query[] = "        mrd.moldno SIMILAR TO '%".pg_escape_string($moldno)."%' OR";
	}

	// �����Υ���ޤ���
	$query[] = rtrim(array_pop($query), 'OR');
	$query[] = "    )";
}

// �ݴɹ���
if (array_key_exists(TableMoldReport::SourceFactory, $searchColumns) &&
	array_key_exists(TableMoldReport::SourceFactory, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldReport::SourceFactory], false))
	{
		$query[] = "AND mr.sourcefactory = '".$companyCode."'";
	}
}

// ��ư�蹩��
if (array_key_exists(TableMoldReport::DestinationFactory, $searchColumns) &&
	array_key_exists(TableMoldReport::DestinationFactory, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldReport::DestinationFactory], false))
	{
		$query[] = "AND mr.destinationfactory = '".$companyCode."'";
	}
}

// �����ʬ
if (array_key_exists(TableMoldReport::RequestCategory, $searchColumns) &&
	array_key_exists(TableMoldReport::RequestCategory, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('�����ʬ', $searchValue[TableMoldReport::RequestCategory], true))
	{
		$query[] = "AND mr.requestcategory = '".$searchValue[TableMoldReport::RequestCategory]."'";
	}
}

// ��˾��
if (array_key_exists(TableMoldReport::ActionRequestDate, $searchColumns) &&
	array_key_exists(TableMoldReport::ActionRequestDate, $from) &&
	array_key_exists(TableMoldReport::ActionRequestDate, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::ActionRequestDate]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::ActionRequestDate]))
{
	$query[] = "AND mr.actionrequestdate".
					" between '".$from[TableMoldReport::ActionRequestDate]."'".
					" AND "."'".$to[TableMoldReport::ActionRequestDate]."'";
}

// ��ư��ˡ
if (array_key_exists(TableMoldReport::TransferMethod, $searchColumns) &&
	array_key_exists(TableMoldReport::TransferMethod, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('��ư��ˡ', $searchValue[TableMoldReport::TransferMethod], true))
	{
		$query[] = "AND mr.transfermethod = '".$searchValue[TableMoldReport::TransferMethod]."'";
	}
}

// �ؼ���ʬ
if (array_key_exists(TableMoldReport::InstructionCategory, $searchColumns) &&
	array_key_exists(TableMoldReport::InstructionCategory, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('�ؼ���ʬ', $searchValue[TableMoldReport::InstructionCategory], true))
	{
		$query[] = "AND mr.instructioncategory = '".$searchValue[TableMoldReport::InstructionCategory]."'";
	}
}

// ������(�ܵ�)
if (array_key_exists(TableMoldReport::CustomerCode, $searchColumns) &&
	array_key_exists(TableMoldReport::CustomerCode, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldReport::CustomerCode], false))
	{
		$query[] = "AND mr.customercode = '".$companyCode."'";
	}
}
// KWGô������
if (array_key_exists(TableMoldReport::KuwagataGroupCode, $searchColumns) &&
	array_key_exists(TableMoldReport::KuwagataGroupCode, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($groupCode = $utilGroup->selectGroupCodeByDisplayGroupCode($searchValue[TableMoldReport::KuwagataGroupCode], false))
	{
		$query[] = "AND mr.kuwagatagroupcode = '".$groupCode."'";
	}
}

// KWGô����
if (array_key_exists(TableMoldReport::KuwagataUserCode, $searchColumns) &&
	array_key_exists(TableMoldReport::KuwagataUserCode, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldReport::KuwagataUserCode], false))
	{
		$query[] = "AND mr.kuwagatausercode = '".$userCode."'";
	}
}

// ������ν���
if (array_key_exists(TableMoldReport::FinalKeep, $searchColumns) &&
	array_key_exists(TableMoldReport::FinalKeep, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('������ν���', $searchValue[TableMoldReport::FinalKeep], true))
	{
		$query[] = "AND mr.finalkeep = '".$searchValue[TableMoldReport::FinalKeep]."'";
	}
}

// �ֵ�ͽ����
if (array_key_exists(TableMoldReport::ReturnSchedule, $searchColumns) &&
	array_key_exists(TableMoldReport::ReturnSchedule, $from) &&
	array_key_exists(TableMoldReport::ReturnSchedule, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::ReturnSchedule]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::ReturnSchedule]))
{
	$query[] = "AND mr.returnschedule".
				" between '".$from[TableMoldReport::ReturnSchedule]."'".
				" AND "."'".$to[TableMoldReport::ReturnSchedule]."'";
}

// ��Ͽ��
if (array_key_exists(TableMoldReport::Created, $searchColumns) &&
	array_key_exists(TableMoldReport::Created, $from) &&
	array_key_exists(TableMoldReport::Created, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::Created]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::Created]))
{
	$query[] = "AND mr.created".
				" between '".$from[TableMoldReport::Created]." 00:00:00'".
				" AND "."'".$to[TableMoldReport::Created]." 23:59:59.99999'";
}

// ��Ͽ��
if (array_key_exists(TableMoldReport::CreateBy, $searchColumns) &&
	array_key_exists(TableMoldReport::CreateBy, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldReport::CreateBy], false))
	{
		$query[] = "AND mr.createby = '".$userCode."'";
	}
}

// ������
if (array_key_exists(TableMoldReport::Updated, $searchColumns) &&
	array_key_exists(TableMoldReport::Updated, $from) &&
	array_key_exists(TableMoldReport::Updated, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::Updated]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::Updated]))
{
	$query[] = "AND mr.updated".
				" between '".$from[TableMoldReport::Updated]." 00:00:00'".
				" AND "."'".$to[TableMoldReport::Updated]." 23:59:59.99999'";
}

// ������
if (array_key_exists(TableMoldReport::UpdateBy, $searchColumns) &&
	array_key_exists(TableMoldReport::UpdateBy, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldReport::UpdateBy], false))
	{
		$query[] = "AND mr.updateby = '".$userCode."'";
	}
}

$query[] = "ORDER BY";
$query[] = "      mr.moldreportid";
$query[] = "    , mr.revision";

// �������ʿ�פ�ʸ������Ѵ�
$query = implode("\n",$query);

// ������¹�
$lngResultID = pg_query($query);

// ������̤������ʤ��ä����
if (!pg_num_rows($lngResultID))
{
	// ����Ģɼ�ǡ����ʤ�
	$strMessage = fncOutputError(9064, DEF_WARNING, "" ,FALSE, "", $objDB );

	// [lngLanguageCode]�񤭽Ф�
	$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

	// [strErrorMessage]�񤭽Ф�
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

// �������Ϣ����������
$records = pg_fetch_all($lngResultID);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ( "/mr/search/mr_search_result.html" );

// ������̥ơ��֥������ΰ�DOMDocument�����
$doc = new DOMDocument();
// �ѡ������顼����
libxml_use_internal_errors(true);
// DOM�ѡ���
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// �ѡ������顼���ꥢ
libxml_clear_errors();
// �ѡ������顼�������
libxml_use_internal_errors(false);

// ������̥ơ��֥�μ���
$table = $doc->getElementById("result");
$thead = $table->getElementsByTagName("thead")->item(0);
$tbody = $table->getElementsByTagName("tbody")->item(0);

// ����ʸ�����ʸ�����Ѵ�
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);

// ������ɽ����򼨤���������
$columnOrder = UtilSearchForm::getColumnOrderForMoldReport();

// -------------------------------------------------------
// �Ƽ�ܥ���ɽ�������å�/���¥����å�
// -------------------------------------------------------
// �ܺ٥�����ɽ��
$existsDetail = array_key_exists("detail", $displayColumns);
// ����������ɽ��
$existsModify = array_key_exists("modify", $displayColumns);
// �ץ�ӥ塼��ɽ��
$existsPreview = array_key_exists("preview", $displayColumns);
// ���������ɽ��
$existsDelete = array_key_exists("delete", $displayColumns);

// �ܺ٥ܥ����ɽ��
$allowedDetail = fncCheckAuthority( DEF_FUNCTION_MR3, $objAuth );
// �����ܥ����ɽ��
$allowedModify = fncCheckAuthority( DEF_FUNCTION_MR4, $objAuth );
// ���������ɽ��
$allowedDelete = fncCheckAuthority( DEF_FUNCTION_MR5, $objAuth );

// -------------------------------------------------------
// �ơ��֥�إå�����
// -------------------------------------------------------
// thead > tr���Ǻ���
$trHead = $doc->createElement("tr");

// ����åץܡ��ɽ����оݥ��饹
$exclude = "exclude-in-clip-board-target";

// ���֥����
$thIndex = $doc->createElement("th");
$thIndex->setAttribute("class", $exclude);
// ���ԡ��ܥ���
$imgCopy = $doc->createElement("img");
$imgCopy->setAttribute("src", "/mold/img/copy_off_bt.gif");
$imgCopy->setAttribute("class", "copy button");
// ���֥���� > ���ԡ��ܥ���
$thIndex->appendChild($imgCopy);
// �إå����ɲ�
$trHead->appendChild($thIndex);

// �ܺ٤�ɽ��
if($existsDetail)
{
	// �ܺ٥����
	$thDetail = $doc->createElement("th", toUTF8("�ܺ�"));
	$thDetail->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thDetail);
}

// �������ܤ�ɽ��
if($existsModify)
{
	// ���������
	$thModify = $doc->createElement("th", toUTF8("����"));
	$thModify->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thModify);
}

// COPY/�ץ�ӥ塼���ܤ�ɽ��
if ($existsPreview)
{
	// COPY�����
	$thPreview = $doc->createElement("th", toUTF8("COPY"));
	$thPreview->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thPreview);

	// �ץ�ӥ塼�����
	$thPreview = $doc->createElement("th", toUTF8("�ץ�ӥ塼"));
	$thPreview->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thPreview);
}

// TODO �ץ�ե��������
// ���ꤵ�줿�ơ��֥���ܤΥ������������
foreach($columnOrder as $columnName)
{
	// ɽ���оݤΥ����ξ��
	if (array_key_exists($columnName, $displayColumns))
	{
		// �����̤�ɽ���ƥ����Ȥ�����
		switch ($columnName)
		{
			case TableMoldReport::MoldReportId :
				$th = $doc->createElement("th", toUTF8("�ⷿĢɼID"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Revision :
				$th = $doc->createElement("th", toUTF8("��ӥ����"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ReportCategory :
				$th = $doc->createElement("th", toUTF8("Ģɼ��ʬ"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Status :
				$th = $doc->createElement("th", toUTF8("Ģɼ���ơ�����"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::RequestDate :
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ProductCode :
				$th = $doc->createElement("th", toUTF8("���ʥ�����"));
				$trHead->appendChild($th);
				break;
			case "strproductname" :
				$th = $doc->createElement("th", toUTF8("����̾��"));
				$trHead->appendChild($th);
				break;
			case "strproductenglishname" :
				$th = $doc->createElement("th", toUTF8("����̾��(�Ѹ�)"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::GoodsCode :
				$th = $doc->createElement("th", toUTF8("�ܵ�����(���ʥ�����)"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::RequestCategory :
				$th = $doc->createElement("th", toUTF8("�����ʬ"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ActionRequestDate :
				$th = $doc->createElement("th", toUTF8("��˾��"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ActionDate :
				$th = $doc->createElement("th", toUTF8("�»���"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::TransferMethod :
				$th = $doc->createElement("th", toUTF8("��ư��ˡ"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::SourceFactory :
				$th = $doc->createElement("th", toUTF8("�ݴɸ�����"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::DestinationFactory :
				$th = $doc->createElement("th", toUTF8("��ư�蹩��"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::InstructionCategory :
				$th = $doc->createElement("th", toUTF8("�ؼ���ʬ"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::CustomerCode :
				$th = $doc->createElement("th", toUTF8("������(�ܵ�)"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::KuwagataGroupCode :
				$th = $doc->createElement("th", toUTF8("ô������"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::KuwagataUserCode :
				$th = $doc->createElement("th", toUTF8("ô����"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Note :
				$th = $doc->createElement("th", toUTF8("����¾"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::FinalKeep :
				$th = $doc->createElement("th", toUTF8("������ν���"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ReturnSchedule :
				$th = $doc->createElement("th", toUTF8("�ֵ�ͽ����"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::MarginalNote :
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Created :
				$th = $doc->createElement("th", toUTF8("��Ͽ��"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::CreateBy :
				$th = $doc->createElement("th", toUTF8("��Ͽ��"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Updated :
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::UpdateBy :
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Version :
				$th = $doc->createElement("th", toUTF8("�С������"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::DeleteFlag :
				$th = $doc->createElement("th", toUTF8("����ե饰"));
				$trHead->appendChild($th);
				break;
			case TableMoldReportDetail::MoldNo :
				$th = $doc->createElement("th", toUTF8("�ⷿNO"));
				$trHead->appendChild($th);
				break;
		}
	}
}

// ������ܤ�ɽ��
if($existsDelete)
{
	// ��������
	$thDelete = $doc->createElement("th", toUTF8("���"));
	$thDelete->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thDelete);
}

// thead > tr
$thead->appendChild($trHead);
// -------------------------------------------------------
// �ơ��֥륻�����
// -------------------------------------------------------
// ������̷��ʬ����
foreach ($records as $i => $record)
{
	$index = $i + 1;

	// tbody > tr���Ǻ���
	$trBody = $doc->createElement("tr");

	// ����
	$tdIndex = $doc->createElement("td", $index);
	$tdIndex->setAttribute("class", $exclude);
	$trBody->appendChild($tdIndex);

	// �ܺ٤�ɽ��
	if($existsDetail)
	{
		// �ܺ٥���
		$tdDetail = $doc->createElement("td");
		$tdDetail->setAttribute("class", $exclude);
		// �ܺ٥ܥ���
		$imgDetail = $doc->createElement("img");
		$imgDetail->setAttribute("src", "/mold/img/detail_off_bt.gif");
		$imgDetail->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgDetail->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgDetail->setAttribute("version", $record[TableMoldReport::Version]);
		$imgDetail->setAttribute("class", "detail button");
		// td > img
		$tdDetail->appendChild($imgDetail);
		// tr > td
		$trBody->appendChild($tdDetail);
	}

	// �������ܤ�ɽ��
	if($existsModify)
	{
		// ��������
		$tdModify = $doc->createElement("td");
		$tdModify->setAttribute("class", $exclude);
		// �����ܥ���
		$imgModify = $doc->createElement("img");
		$imgModify->setAttribute("src", "/mold/img/renew_off_bt.gif");
		$imgModify->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgModify->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgModify->setAttribute("version", $record[TableMoldReport::Version]);
		$imgModify->setAttribute("class", "modify button");
		// td > img
		$tdModify->appendChild($imgModify);
		// tr > td
		$trBody->appendChild($tdModify);
	}

	// COPY/�ץ�ӥ塼���ܤ�ɽ��
	if ($existsPreview)
	{
		// COPY����
		$tdCopy = $doc->createElement("td");
		$tdCopy->setAttribute("class", $exclude);
		// COPY�ܥ���
		$imgCopy = $doc->createElement("img");
		$imgCopy->setAttribute("src", "/mold/img/copybig_off_bt.gif");
		$imgCopy->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgCopy->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgCopy->setAttribute("version", $record[TableMoldReport::Version]);
		$imgCopy->setAttribute("class", "copy-preview button");
		// td > img
		$tdCopy->appendChild($imgCopy);
		// tr > td
		$trBody->appendChild($tdCopy);

		// �ץ�ӥ塼����
		$tdPreview = $doc->createElement("td");
		$tdPreview->setAttribute("class", $exclude);
		// �ץ�ӥ塼�ܥ���
		$imgPreview = $doc->createElement("img");
		$imgPreview->setAttribute("src", "/mold/img/preview_off_bt.gif");
		$imgPreview->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgPreview->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgPreview->setAttribute("version", $record[TableMoldReport::Version]);
		$imgPreview->setAttribute("class", "preview button");
		// td > img
		$tdPreview->appendChild($imgPreview);
		// tr > td
		$trBody->appendChild($tdPreview);
	}

	// TODO �ץ�ե��������
	// ���ꤵ�줿�ơ��֥���ܤΥ�����������
	foreach($columnOrder as $columnName)
	{
		// ɽ���оݤΥ����ξ��
		if (array_key_exists($columnName, $displayColumns))
		{
			// �����̤�ɽ���ƥ����Ȥ�����
			switch ($columnName)
			{
				case TableMoldReport::MoldReportId : // �ⷿĢɼID
					$td = $doc->createElement("td", $record[TableMoldReport::MoldReportId]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Revision : // ��ӥ����
					$td = $doc->createElement("td", $record[TableMoldReport::Revision]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ReportCategory : // Ģɼ��ʬ
					$record[TableMoldReport::ReportCategory] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("Ģɼ��ʬ", $record[TableMoldReport::ReportCategory])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Status : // Ģɼ���ơ�����
					$record[TableMoldReport::Status] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("Ģɼ���ơ�����", $record[TableMoldReport::Status])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::RequestDate : // ������
					$td = $doc->createElement("td", $record[TableMoldReport::RequestDate]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ProductCode : // ���ʥ�����
					$td = $doc->createElement("td", $record[TableMoldReport::ProductCode]);
					$trBody->appendChild($td);
					break;
				case "strproductname" : // ����̾��
					$td = $doc->createElement("td", toUTF8($record["strproductname"]));
					$trBody->appendChild($td);
					break;
				case "strproductenglishname" : // ����̾��(�Ѹ�)
					$td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
					$trBody->appendChild($td);
					break;
				case TableMoldReport::GoodsCode : // �ܵ�����(���ʥ�����)
					$td = $doc->createElement("td", toUTF8($record[TableMoldReport::GoodsCode]));
					$trBody->appendChild($td);
					break;
				case TableMoldReport::RequestCategory : // �����ʬ
					$record[TableMoldReport::RequestCategory] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("�����ʬ", $record[TableMoldReport::RequestCategory])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ActionRequestDate : // ��˾��
					$td = $doc->createElement("td", $record[TableMoldReport::ActionRequestDate]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ActionDate : // �»���
					$td = $doc->createElement("td", $record[TableMoldReport::ActionDate]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::TransferMethod : // ��ư��ˡ
					$record[TableMoldReport::TransferMethod] ?
						$td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("��ư��ˡ", $record[TableMoldReport::TransferMethod])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::SourceFactory : // �ݴɸ�����
					if ($record[TableMoldReport::SourceFactory] || $record[TableMoldHistory::SourceFactory] === "0")
					{
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldReport::SourceFactory]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldReport::SourceFactory]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::DestinationFactory : // ��ư�蹩��
					if ($record[TableMoldReport::DestinationFactory] || $record[TableMoldHistory::DestinationFactory] === "0")
					{
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldReport::DestinationFactory]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldReport::DestinationFactory]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::InstructionCategory : // �ؼ���ʬ
					$record[TableMoldReport::InstructionCategory] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("�ؼ���ʬ", $record[TableMoldReport::InstructionCategory])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::CustomerCode : // ������(�ܵ�)
					if ($record[TableMoldReport::CustomerCode])
					{
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldReport::CustomerCode]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldReport::CustomerCode]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::KuwagataGroupCode : // ô������
					if ($record[TableMoldReport::KuwagataGroupCode])
					{
						$displayCode = $utilGroup->selectDisplayCodeByGroupCode($record[TableMoldReport::KuwagataGroupCode]);
						$displayName = $utilGroup->selectDisplayNameByGroupCode($record[TableMoldReport::KuwagataGroupCode]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::KuwagataUserCode : // ô����
					if ($record[TableMoldReport::KuwagataUserCode])
					{
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldReport::KuwagataUserCode]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldReport::KuwagataUserCode]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Note : // ����¾
					$td = $doc->createElement("td", $record[TableMoldReport::Note]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::FinalKeep : // ������ν���
					$record[TableMoldReport::FinalKeep] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("������ν���", $record[TableMoldReport::FinalKeep])))
						: $td = $doc->createElement("td", $record[TableMoldReport::FinalKeep]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ReturnSchedule : // �ֵ�ͽ����
					$td = $doc->createElement("td", $record[TableMoldReport::ReturnSchedule]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::MarginalNote : // ������
					$td = $doc->createElement("td", $record[TableMoldReport::MarginalNote]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Created : // ������
					$td = $doc->createElement("td", $record[TableMoldReport::Created]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::CreateBy : // ������
					if ($record[TableMoldReport::CreateBy])
					{
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldReport::CreateBy]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldReport::CreateBy]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Updated : // ��������
					$td = $doc->createElement("td", $record[TableMoldReport::Updated]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::UpdateBy : // ������
					if ($record[TableMoldReport::UpdateBy])
					{
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldReport::UpdateBy]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldReport::UpdateBy]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Version : // �С������
					$td = $doc->createElement("td", $record[TableMoldReport::Version]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::DeleteFlag : // ����ե饰
					$td = $doc->createElement("td", $record[TableMoldReport::DeleteFlag]);
					$trBody->appendChild($td);
					break;
				case TableMoldReportDetail::MoldNo : // �ⷿ����
					$td = $doc->createElement("td");
					$td->setAttribute("class", "moldinfo");
					// ����ޤ�<br>���Ǥ��֤�������
					foreach (explode(",", $record[TableMoldReportDetail::MoldNo]) as $index => $moldno)
					{
						$td->appendChild($doc->createTextNode(toUTF8($moldno)));
						$td->appendChild($doc->createElement("br"));
					}
					$trBody->appendChild($td);
					break;
			}
		}
	}

	// ������ܤ�ɽ��
	if($existsDelete)
	{
		// �������
		$tdDelete = $doc->createElement("td");
		$tdDelete->setAttribute("class", $exclude);
		// ����ܥ���
		$imgDelete = $doc->createElement("img");
		$imgDelete->setAttribute("src", "/mold/img/remove_off_bt.gif");
		$imgDelete->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgDelete->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgDelete->setAttribute("version", $record[TableMoldReport::Version]);
		$imgDelete->setAttribute("class", "delete button");
		// td > img
		$tdDelete->appendChild($imgDelete);
		// tr > td
		$trBody->appendChild($tdDelete);
	}

	// tbody > tr
	$tbody->appendChild($trBody);
}

// HTML����
echo $doc->saveHTML();

