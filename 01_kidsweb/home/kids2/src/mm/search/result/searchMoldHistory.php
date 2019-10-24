<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿ�������  ��������
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

// ���쥳���ɤ����(0->false: �Ѹ�, 1->true: ���ܸ�)
$lngLanguageCode = 1;

// ���å�����ǧ
$objAuth = fncIsSession( $_REQUEST["strSessionID"], $objAuth, $objDB );

// 1800 �ⷿ�������
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1802 �ⷿ�������(����)
if ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
$options = UtilSearchForm::extractArrayByOption($_REQUEST);
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// ���������Ω�˻��Ѥ���ե�����ǡ��������
$optionColumns = array();
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// ���ץ������ܤ����
foreach ($options as $key => $flag)
{
	if ($flag == "on")
	{
		$optionColumns[$key] = $key;
	}
}

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
$query[] = "      tmrr.moldreportid";
$query[] = "    , tmrr.revision";
$query[] = "    , ms.dtmappropriationdate";   // �����ޥ���.�����׾���
$query[] = "    , mo.strOrderCode AS strOrderCode";   // ȯ��ޥ���.ȯ������
$query[] = "    , '[' || mc.strCompanyDisplayCode || ']' || mc.strCompanyDisplayname AS strCompanyDisplayCode";   // [��ҥޥ���.ɽ����ҥ�����] ��ҥޥ���.ɽ�����̾��
$query[] = "    , mp.strproductcode";            // ���ʥޥ���.���ʥ�����
$query[] = "    , mp.strproductname";            // ���ʥޥ���.���ʥ�����(���ܸ�)
$query[] = "    , mp.strproductenglishname";     // ���ʥޥ���.����̾��(�Ѹ�)
$query[] = "    , mp.strgoodscode";              // ���ʥޥ���.�ܵ�����
$query[] = "    , '[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname as strgroupdisplaycode";   // [���롼�ץޥ���.ɽ�����롼�ץ�����] ���롼�ץޥ���.ɽ�����롼��̾
$query[] = "    , '[' || mu.struserdisplaycode || ']' || mu.struserdisplayname as struserdisplaycode";   // [�桼���ޥ���.ɽ���桼��������] �桼���ޥ���.ɽ���桼��̾
$query[] = "    , tmh.moldno";                // �ⷿ����.�ⷿNO
$query[] = "    , tmh.historyno";             // �ⷿ����.�����ֹ�
$query[] = "    , ms.lngProductQuantity";    // �����ܺ�.����
$query[] = "    , mou.strMonetaryUnitSign || ms.curSubTotalPrice as strMonetaryUnitSign"; // �̲�ñ�̥ޥ���.�̲�ñ�� || �����ܺ�.���֥ȡ��������
$query[] = "    , tmh.status";                // �ⷿ����.�ⷿ���ơ�����
$query[] = "    , tmh.actiondate";            // �ⷿ����.�»���
$query[] = "  , mc_sf.strcompanydisplaycode as strsourfacdisplaycode";
$query[] = "  , mc_sf.strcompanydisplayname as strsourfacdisplayname";
$query[] = "  , tmh.sourcefactory";
$query[] = "  , tmh.destinationfactory";
$query[] = "  , mc_df.strcompanydisplaycode as strdescfacdisplaycode";
$query[] = "  , mc_df.strcompanydisplayname as strdescfacdisplayname";
$query[] = "  , tmh.created ::date";
$query[] = "  , tmh.createby";
$query[] = "  , mu_c.struserdisplaycode as strcreateuserdisplaycode";
$query[] = "  , mu_c.struserdisplayname as strcreateuserdisplayname";
$query[] = "  , tmh.updated ::date";
$query[] = "  , tmh.updateby";
$query[] = "  , mu_u.struserdisplaycode as strupdateuserdisplaycode";
$query[] = "  , mu_u.struserdisplayname as strupdateuserdisplayname";
$query[] = "    , tmh.version";               // �ⷿ����.�С������
$query[] = "    , tmh.deleteflag";            // �ⷿ����.����ե饰
$query[] = "FROM";
$query[] = "    t_moldhistory tmh";
$query[] = "INNER JOIN";
$query[] = "    m_mold mm";
$query[] = "  ON";
$query[] = "    tmh.moldno = mm.moldno";
$query[] = "LEFT OUTER JOIN";
$query[] = "    t_moldreportrelation tmrr";
$query[] = "  ON";
$query[] = "        tmh.moldno = tmrr.moldno";
$query[] = "    AND tmh.historyno = tmrr.historyno";
// $query[] = "----------------------------------------------";
// $query[] = "--  �����ܺ� - �� �� ���� - ���롼�� - �桼��";
// $query[] = "----------------------------------------------";
$query[] = "  LEFT OUTER JOIN ( ";
$query[] = "    select";
$query[] = "      tsd.*";
$query[] = "      , s.dtmappropriationdate";
$query[] = "      , s.lngcustomercompanycode";
$query[] = "      , s.lngmonetaryunitcode ";
$query[] = "    from";
$query[] = "      t_stockdetail tsd ";
$query[] = "      inner join ( ";
$query[] = "        SELECT";
$query[] = "          ims.* ";
$query[] = "        FROM";
$query[] = "          m_stock ims ";
$query[] = "          inner join ( ";
$query[] = "            SELECT";
$query[] = "              MAX(lngRevisionNo) lngrevisionno";
$query[] = "              , strStockCode ";
$query[] = "            FROM";
$query[] = "              m_Stock ";
$query[] = "            WHERE";
$query[] = "              bytInvalidFlag = false ";
$query[] = "            group by";
$query[] = "              strStockCode";
$query[] = "          ) s ";
$query[] = "            on ims.lngrevisionno = s.lngrevisionno ";
$query[] = "            and ims.strstockcode = s.strStockCode ";
$query[] = "        where";
$query[] = "          not exists ( ";
$query[] = "            select";
$query[] = "              s1.strStockCode ";
$query[] = "            from";
$query[] = "              ( ";
$query[] = "                SELECT";
$query[] = "                  min(lngRevisionNo) lngRevisionNo";
$query[] = "                  , strStockCode ";
$query[] = "                FROM";
$query[] = "                  m_Stock ";
$query[] = "                where";
$query[] = "                  bytInvalidFlag = false ";
$query[] = "                group by";
$query[] = "                  strStockCode";
$query[] = "              ) as s1 ";
$query[] = "            where";
$query[] = "              s1.strStockCode = ims.strstockcode AND s1.lngRevisionNo < 0";
$query[] = "          )";
$query[] = "      ) s ";
$query[] = "        on tsd.lngstockno = s.lngstockno ";
$query[] = "        and ( ";
$query[] = "          ( ";
$query[] = "            tsd.lngStockSubjectCode = 433 ";
$query[] = "            AND tsd.lngStockItemCode = 1";
$query[] = "          ) ";
$query[] = "          OR ( ";
$query[] = "            tsd.lngStockSubjectCode = 431 ";
$query[] = "            AND tsd.lngStockItemCode = 8";
$query[] = "          )";
$query[] = "        )";
$query[] = "  ) ms ";
$query[] = "    ON tmh.moldno = ms.strmoldno ";
$query[] = "LEFT JOIN";
$query[] = "    m_tax   mt";
$query[] = "  ON";
$query[] = "    ms.lngtaxcode = mt.lngtaxcode";
$query[] = "  LEFT OUTER JOIN ( ";
$query[] = "    SELECT";
$query[] = "      p.* ";
$query[] = "    FROM";
$query[] = "      m_product p ";
$query[] = "      inner join ( ";
$query[] = "        SELECT";
$query[] = "          MAX(lngproductno) lngproductno";
$query[] = "          , strproductcode ";
$query[] = "        FROM";
$query[] = "          m_product ";
$query[] = "        WHERE";
$query[] = "          bytInvalidFlag = false ";
$query[] = "        group by";
$query[] = "          strproductcode";
$query[] = "      ) p1 ";
$query[] = "        on p.lngproductno = p1.lngproductno ";
$query[] = "    where";
$query[] = "      lngrevisionno >= 0";
$query[] = "  ) mp ";
$query[] = "  ON";
$query[] = "     ms.strproductcode = mp.strproductcode";
$query[] = "  LEFT JOIN m_group mg ";
$query[] = "    ON mp.lnginchargegroupcode = mg.lnggroupcode ";
$query[] = "  LEFT JOIN m_user mu ";
$query[] = "    ON mp.lnginchargeusercode = mu.lngusercode ";
$query[] = "  LEFT JOIN m_user mu_u";
$query[] = "    ON tmh.updateby = mu_u.lngusercode ";
$query[] = "  LEFT JOIN m_user mu_c";
$query[] = "    ON tmh.createby = mu_c.lngusercode ";
$query[] = "  LEFT OUTER JOIN m_order mo ";
$query[] = "    ON ms.lngorderno = mo.lngorderno ";
$query[] = "  LEFT JOIN m_Company mc ";
$query[] = "    ON ms.lngCustomerCompanyCode = mc.lngCompanyCode ";
$query[] = "  LEFT JOIN m_Company mc_sf";
$query[] = "    ON tmh.sourcefactory = mc_sf.lngCompanyCode ";
$query[] = "  LEFT JOIN m_Company mc_df";
$query[] = "    ON tmh.destinationfactory = mc_df.lngCompanyCode ";
$query[] = "  LEFT JOIN m_MonetaryUnit mou ";
$query[] = "    ON ms.lngMonetaryUnitCode = mou.lngMonetaryUnitCode ";
$query[] = "WHERE";// ������������
if (!array_key_exists("IsDetail", $optionColumns))
{
$query[] = "    tmh.deleteflag = false";
}
$query[] = "AND mm.deleteflag = false";

// �桼�ƥ���ƥ��Υ��󥹥��󥹼���
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilMold = UtilMold::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();

// �������ܤΥ�����ʸ�����Ѵ�
$searchColumns = array_change_key_case($searchColumns, CASE_LOWER);
// �����ͤΥ�����ʸ�����Ѵ�
$searchValue = array_change_key_case($searchValue, CASE_LOWER);
$from = array_change_key_case($from, CASE_LOWER);
$to = array_change_key_case($to, CASE_LOWER);

// �ⷿĢɼID
if (array_key_exists(TableMoldReport::MoldReportId, $searchColumns) &&
	array_key_exists(TableMoldReport::MoldReportId, $from) &&
	array_key_exists(TableMoldReport::MoldReportId, $to))
{
	$query[] = "AND tmrr.moldreportid".
			" between '".pg_escape_string($from[TableMoldReport::MoldReportId])."'".
			" AND "."'".pg_escape_string($to[TableMoldReport::MoldReportId])."'";
}

// �׾���
if (array_key_exists("dtmappropriationdate", $searchColumns) &&
	array_key_exists("dtmappropriationdate", $from) &&
	array_key_exists("dtmappropriationdate", $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::Updated]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::Updated]))
{
	$query[] = "AND ms.dtmappropriationdate".
				" between '".pg_escape_string($from["dtmappropriationdate"])."'".
				" AND "."'".pg_escape_string($to["dtmappropriationdate"])."'";
}

// ȯ������
if (array_key_exists("strordercode", $searchColumns) &&
	array_key_exists("strordercode", $from) &&
	array_key_exists("strordercode", $to))
{
	$query[] = "AND mo.strordercode".
				" between '".pg_escape_string($from["strordercode"])."'".
				" AND "."'".pg_escape_string($to["strordercode"])."'";
}

// ������
if (array_key_exists("strcompanydisplaycode", $searchColumns) &&
	array_key_exists("strcompanydisplaycode", $searchValue))
{
	$query[] = "AND mc.strcompanydisplaycode = '".pg_escape_string($searchValue["strcompanydisplaycode"])."'";
}

// ���ʥ�����
if (array_key_exists("strproductcode", $searchColumns) &&
	array_key_exists("strproductcode", $from) &&
	array_key_exists("strproductcode", $to))
{
	$query[] = "AND mp.strproductcode".
				" between '".pg_escape_string($from["strproductcode"])."'".
				" AND "."'".pg_escape_string($to["strproductcode"])."'";
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
if (array_key_exists("strgoodscode", $searchColumns) &&
	array_key_exists("strgoodscode", $searchValue))
{
	$query[] = "AND mp.strgoodscode = '".pg_escape_string($searchValue["strgoodscode"])."'";
}

// ô������
if (array_key_exists("strgroupdisplaycode", $searchColumns) &&
	array_key_exists("strgroupdisplaycode", $searchValue))
{
	$query[] = "AND mg.strgroupdisplaycode = '".pg_escape_string($searchValue["strgroupdisplaycode"])."'";
}

// ô����
if (array_key_exists("struserdisplaycode", $searchColumns) &&
	array_key_exists("struserdisplaycode", $searchValue))
{
	$query[] = "AND mu.struserdisplaycode = '".pg_escape_string($searchValue["struserdisplaycode"])."'";
}

// �ⷿNO
if (array_key_exists("moldno", $searchColumns) &&
	array_key_exists("choosenmoldlist", $searchValue) &&
	count($searchValue["choosenmoldlist"]))
{
	$query[] = "AND tmh.moldno in";
	$query[] = "    (";

	// �ⷿ���ʬ����
	foreach ($searchValue["choosenmoldlist"] as $index => $moldno)
	{
		$query[] = "        '".pg_escape_string($moldno)."',";
	}

	// �����Υ���ޤ���
	$query[] = rtrim(array_pop($query), ',');
	$query[] = "    )";
}

// �ⷿ���ơ�����
if (array_key_exists(TableMoldHistory::Status, $searchColumns) &&
	array_key_exists(TableMoldHistory::Status, $searchValue))
{
	// ��̳�����ɥޥ������¸�ߤ����ͤξ��
	if($utilBussinesscode->getDescription('�ⷿ���ơ�����', $searchValue[TableMoldHistory::Status], true))
	{
		$query[] = "AND tmh.status = '".$searchValue[TableMoldHistory::Status]."'";
	}
}

// �»���
if (array_key_exists(TableMoldHistory::ActionDate, $searchColumns) &&
	array_key_exists(TableMoldHistory::ActionDate, $from) &&
	array_key_exists(TableMoldHistory::ActionDate, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::ActionDate]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::ActionDate]))
{
	$query[] = "AND tmh.actiondate".
				" between '".$from[TableMoldHistory::ActionDate]."'".
				" AND "."'".$to[TableMoldHistory::ActionDate]."'";
}

// �ݴɹ���
if (array_key_exists(TableMoldHistory::SourceFactory, $searchColumns) &&
	array_key_exists(TableMoldHistory::SourceFactory, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldHistory::SourceFactory], false))
	{
		$query[] = "AND tmh.sourcefactory = '".$companyCode."'";
	}
}

// ��ư�蹩��
if (array_key_exists(TableMoldHistory::DestinationFactory, $searchColumns) &&
	array_key_exists(TableMoldHistory::DestinationFactory, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldHistory::DestinationFactory], false))
	{
		$query[] = "AND tmh.destinationfactory = '".$companyCode."'";
	}
}

// ��Ͽ��
if (array_key_exists(TableMoldHistory::Created, $searchColumns) &&
	array_key_exists(TableMoldHistory::Created, $from) &&
	array_key_exists(TableMoldHistory::Created, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::Created]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::Created]))
{
	$query[] = "AND tmh.created".
				" between '".$from[TableMoldHistory::Created]." 00:00:00'".
				" AND "."'".$to[TableMoldHistory::Created]." 23:59:59.99999'";
}

// ��Ͽ��
if (array_key_exists(TableMoldHistory::CreateBy, $searchColumns) &&
	array_key_exists(TableMoldHistory::CreateBy, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldHistory::CreateBy], false))
	{
		$query[] = "AND tmh.createby = '".$userCode."'";
	}
}

// ������
if (array_key_exists(TableMoldHistory::Updated, $searchColumns) &&
	array_key_exists(TableMoldHistory::Updated, $from) &&
	array_key_exists(TableMoldHistory::Updated, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::Updated]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::Updated]))
{
	$query[] = "AND tmh.updated".
				" between '".$from[TableMoldHistory::Updated]." 00:00:00'".
				" AND "."'".$to[TableMoldHistory::Updated]." 23:59:59.99999'";
}

// ������
if (array_key_exists(TableMoldHistory::UpdateBy, $searchColumns) &&
	array_key_exists(TableMoldHistory::UpdateBy, $searchValue))
{
	// ɽ����ҥ����ɤ��˲�ҥ����ɤ����
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldHistory::UpdateBy], false))
	{
		$query[] = "AND tmh.updateby = '".$userCode."'";
	}
}


$query[] = "ORDER BY";
$query[] = "      mm.productcode";
$query[] = "    , tmh.historyno desc";
$query[] = "    , tmh.moldno";
$query[] = ";";

// �������ʿ�פ�ʸ������Ѵ�
$query = implode("\n",$query);

// ������¹�
$lngResultID = pg_query($query);

$lngResultNum = pg_num_rows($lngResultID);

// �������������ξ��
if ($lngResultNum > 0) {
    // ������ʾ�ξ�票�顼��å�������ɽ������
    // if ($lngResultNum > DEF_SEARCH_MAX) {
    //     $errorFlag = true;
    //     $lngErrorCode = 9068;
    //     $aryErrorMessage = DEF_SEARCH_MAX;
    // }
} else {
    $errorFlag = true;
    $lngErrorCode = 603;
    $aryErrorMessage = "";
}
if ($errorFlag) {
    // ���顼���̤������
    $strReturnPath = "../mm/search/index.php?strSessionID=" . $aryData["strSessionID"];

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, $strReturnPath, $objDB);

    // [strErrorMessage]�񤭽Ф�
    $aryHtml["strErrorMessage"] = $strMessage;

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // �ƥ�ץ졼������
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML����
    echo $objTemplate->strTemplate;

    exit;
}

// �������Ϣ����������
$records = pg_fetch_all($lngResultID);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ( "/mm/search/mm_search_result.html" );

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
$columnOrder = UtilSearchForm::getColumnOrderForMoldHistory();

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
$allowedDetail = fncCheckAuthority( DEF_FUNCTION_MM3, $objAuth );
// �����ܥ����ɽ��
$allowedModify = fncCheckAuthority( DEF_FUNCTION_MM4, $objAuth );
// ���������ɽ��
$allowedDelete = fncCheckAuthority( DEF_FUNCTION_MM5, $objAuth );

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
			// �ⷿĢɼ��Ϣ.�ⷿĢɼID
			case TableMoldReport::MoldReportId:
				$th = $doc->createElement("th", toUTF8("�ⷿĢɼID"));
				$trHead->appendChild($th);
				break;
			// �����ޥ���.�����׾���
			case "dtmappropriationdate":
				$th = $doc->createElement("th", toUTF8("�׾���"));
				$trHead->appendChild($th);
				break;
			// ȯ��ޥ���.ȯ������-ȯ��ޥ���.����������
			case "strordercode":
				$th = $doc->createElement("th", toUTF8("ȯ������"));
				$trHead->appendChild($th);
				break;
			// [��ҥޥ���.ɽ����ҥ�����] ��ҥޥ���.ɽ�����̾��
			case "strcompanydisplaycode":
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.���ʥ�����
			case "strproductcode":
				$th = $doc->createElement("th", toUTF8("���ʥ�����"));
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.���ʥ�����(���ܸ�)
			case "strproductname":
				$th = $doc->createElement("th", toUTF8("����̾��"));
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.����̾��(�Ѹ�)
			case "strproductenglishname":
				$th = $doc->createElement("th", toUTF8("����̾��(�Ѹ�)"));
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.�ܵ�����
			case "strgoodscode":
				$th = $doc->createElement("th", toUTF8("�ܵ�����"));
				$trHead->appendChild($th);
				break;
			// [���롼�ץޥ���.ɽ�����롼�ץ�����] ���롼�ץޥ���.ɽ�����롼��̾
			case "strgroupdisplaycode":
				$th = $doc->createElement("th", toUTF8("ô������"));
				$trHead->appendChild($th);
				break;
			// [�桼���ޥ���.ɽ���桼��������] �桼���ޥ���.ɽ���桼��̾
			case "struserdisplaycode":
				$th = $doc->createElement("th", toUTF8("ô����"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�ⷿNO
			case TableMoldHistory::MoldNo:
				$th = $doc->createElement("th", toUTF8("�ⷿNO"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�����ֹ�
			case TableMoldHistory::HistoryNo:
				$th = $doc->createElement("th", toUTF8("�����ֹ�"));
				$trHead->appendChild($th);
				break;
			// �����ܺ�.���ʿ���
			case "lngproductquantity":
				$th = $doc->createElement("th", toUTF8("����"));
				$trHead->appendChild($th);
				break;
			// �̲�ñ�̥ޥ���.�̲�ñ�� || �����ܺ�.��ȴ���
			case "strmonetaryunitsign":
				$th = $doc->createElement("th", toUTF8("��ȴ���"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�ⷿ���ơ�����
			case TableMoldHistory::Status:
				$th = $doc->createElement("th", toUTF8("�ⷿ���ơ�����"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�»���
			case TableMoldHistory::ActionDate:
				$th = $doc->createElement("th", toUTF8("�»���"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�ݴɹ���
			case TableMoldHistory::SourceFactory:
				$th = $doc->createElement("th", toUTF8("�ݴɸ�����"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��ư�蹩��
			case TableMoldHistory::DestinationFactory:
				$th = $doc->createElement("th", toUTF8("��ư�蹩��"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��Ͽ����
			case TableMoldHistory::Created :
				$th = $doc->createElement("th", toUTF8("��Ͽ��"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��Ͽ��
			case TableMoldHistory::CreateBy :
				$th = $doc->createElement("th", toUTF8("��Ͽ��"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��������
			case TableMoldHistory::Updated :
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.������
			case TableMoldHistory::UpdateBy :
				$th = $doc->createElement("th", toUTF8("������"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�С������
			case TableMoldHistory::Version :
				$th = $doc->createElement("th", toUTF8("�С������"));
				$trHead->appendChild($th);
				break;
			// �ⷿ����.����ե饰
			case TableMoldHistory::DeleteFlag :
				$th = $doc->createElement("th", toUTF8("����ե饰"));
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

		// �ܺ٥ܥ����ɽ��
		if ($allowedDetail)
		{
			// �ܺ٥ܥ���
			$imgDetail = $doc->createElement("img");
			$imgDetail->setAttribute("src", "/mold/img/detail_off_bt.gif");
			$imgDetail->setAttribute("id", $record[TableMoldHistory::MoldNo]);
			$imgDetail->setAttribute("historyno", $record[TableMoldHistory::HistoryNo]);
			$imgDetail->setAttribute("version", $record[TableMoldHistory::Version]);
			$imgDetail->setAttribute("class", "detail button");
			// td > img
			$tdDetail->appendChild($imgDetail);
		}
		// tr > td
		$trBody->appendChild($tdDetail);
	}

	// �������ܤ�ɽ��
	if($existsModify)
	{
		// ��������
		$tdModify = $doc->createElement("td");
		$tdModify->setAttribute("class", $exclude);

		// �����ܥ����ɽ��
		if ($allowedModify)
		{
			// �����ܥ���
			$imgModify = $doc->createElement("img");
			$imgModify->setAttribute("src", "/mold/img/renew_off_bt.gif");
			$imgModify->setAttribute("id", $record[TableMoldHistory::MoldNo]);
			$imgModify->setAttribute("historyno", $record[TableMoldHistory::HistoryNo]);
			$imgModify->setAttribute("version", $record[TableMoldHistory::Version]);
			$imgModify->setAttribute("class", "modify button");
			// td > img
			$tdModify->appendChild($imgModify);
		}
		// tr > td
		$trBody->appendChild($tdModify);
	}
	// �ץ�ӥ塼���ܤ�ɽ��(����ϥ��ԡ�����)
	if ($existsPreview)
	{
		// �ץ�ӥ塼����
		$tdPreview = $doc->createElement("td");
		$tdPreview->setAttribute("class", $exclude);

		// �ⷿĢɼ��Ϣ�������Ǥ����������å�
		if($relation = $utilMold->selectMoldReportRelationByHistory($record[TableMoldHistory::MoldNo], $record[TableMoldHistory::HistoryNo]))
		{
			// �ץ�ӥ塼�ܥ����ɽ��
			$imgPreview = $doc->createElement("img");
			$imgPreview->setAttribute("src", "/mold/img/preview_off_bt.gif");
			$imgPreview->setAttribute("id", $relation[0][TableMoldReportRelation::MoldReportId]);
			$imgPreview->setAttribute("revision", $relation[0][TableMoldReportRelation::Revision]);
			$imgPreview->setAttribute("version", $relation[0]["report_version"]);

			// ���ܰ����Ѥξ��
			if ($relation[0][TableMoldReport::Printed] == "t")
			{
				// COPYĢɼ�����
				$imgPreview->setAttribute("class", "copy-preview button");
			}
			else
			{
				// ����Ģɼ�����
				$imgPreview->setAttribute("class", "preview button");
			}

			// td > img
			$tdPreview->appendChild($imgPreview);
		}

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
				// �ⷿĢɼ��Ϣ.�ⷿĢɼID
				case TableMoldReport::MoldReportId : // �ⷿĢɼID
					$td = $doc->createElement("td", $record[TableMoldReport::MoldReportId]);
					$trBody->appendChild($td);
					break;
				// �����ޥ���.�����׾���
				case "dtmappropriationdate":
					$td = $doc->createElement("td", $record["dtmappropriationdate"]);
					$trBody->appendChild($td);
					break;
				// ȯ��ޥ���.ȯ������-ȯ��ޥ���.����������
				case "strordercode":
					$td = $doc->createElement("td", $record["strordercode"]);
					$trBody->appendChild($td);
					break;
				// [��ҥޥ���.ɽ����ҥ�����] ��ҥޥ���.ɽ�����̾��
				case "strcompanydisplaycode":
					$td = $doc->createElement("td", toUTF8($record["strcompanydisplaycode"]));
					$trBody->appendChild($td);
					break;
				// ���ʥޥ���.���ʥ�����
				case "strproductcode":
					$td = $doc->createElement("td", $record["strproductcode"]);
					$trBody->appendChild($td);
					break;
				// ���ʥޥ���.���ʥ�����(���ܸ�)
				case "strproductname":
					$td = $doc->createElement("td", toUTF8($record["strproductname"]));
					$trBody->appendChild($td);
					break;
				// ���ʥޥ���.����̾��(�Ѹ�)
				case "strproductenglishname":
					$td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
					$trBody->appendChild($td);
					break;
				// ���ʥޥ���.�ܵ�����
				case "strgoodscode":
					$td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
					$trBody->appendChild($td);
					break;
				// [���롼�ץޥ���.ɽ�����롼�ץ�����] ���롼�ץޥ���.ɽ�����롼��̾
				case "strgroupdisplaycode":
					$td = $doc->createElement("td", toUTF8($record["strgroupdisplaycode"]));
					$trBody->appendChild($td);
					break;
				// [�桼���ޥ���.ɽ���桼��������] �桼���ޥ���.ɽ���桼��̾
				case "struserdisplaycode":
					$td = $doc->createElement("td", toUTF8($record["struserdisplaycode"]));
					$trBody->appendChild($td);
					break;
				// �ⷿ����.�ⷿNO
				case TableMoldHistory::MoldNo:
					$td = $doc->createElement("td", $record[TableMoldHistory::MoldNo]);
					$trBody->appendChild($td);
					break;
				// �ⷿ����.�����ֹ�
				case TableMoldHistory::HistoryNo:
					$td = $doc->createElement("td", $record[TableMoldHistory::HistoryNo]);
					$trBody->appendChild($td);
					break;
				// �����ܺ�.���ʿ���
				case "lngproductquantity":
					$td = $doc->createElement("td", $record["lngproductquantity"]);
					$trBody->appendChild($td);
					break;
				// �̲�ñ�̥ޥ���.�̲�ñ�� || �����ܺ�.��ȴ���
				case "strmonetaryunitsign":
					$td = $doc->createElement("td", toUTF8($record["strmonetaryunitsign"]));
					$trBody->appendChild($td);
					break;
				// �ⷿ����.�ⷿ���ơ�����
				case TableMoldHistory::Status:
					$record[TableMoldHistory::Status] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("�ⷿ���ơ�����", $record[TableMoldHistory::Status])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				// �ⷿ����.�»���
				case TableMoldHistory::ActionDate:
					$td = $doc->createElement("td", $record[TableMoldHistory::ActionDate]);
					$trBody->appendChild($td);
					break;
				// �ⷿ����.�ݴɹ���
				case TableMoldHistory::SourceFactory:
					if ($record[TableMoldHistory::SourceFactory] || $record[TableMoldHistory::DestinationFactory] === "0")
					{
						$textContent = "[".$record["strsourfacdisplaycode"]."]"." ".$record["strsourfacdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// �ⷿ����.��ư�蹩��
				case TableMoldHistory::DestinationFactory:
					if ($record[TableMoldHistory::DestinationFactory] || $record[TableMoldHistory::DestinationFactory] === "0")
					{
						$textContent = "[".$record["strdescfacdisplaycode"]."]"." ".$record["strdescfacdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// �ⷿ����.��Ͽ����
				case TableMoldHistory::Created :
					$td = $doc->createElement("td", $record[TableMoldHistory::Created]);
					$trBody->appendChild($td);
					break;
				// �ⷿ����.��Ͽ��
				case TableMoldHistory::CreateBy :
					if ($record[TableMoldHistory::CreateBy])
					{
						$textContent = "[".$record["strcreateuserdisplaycode"]."]"." ".$record["strcreateuserdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// �ⷿ����.��������
				case TableMoldHistory::Updated :
					$td = $doc->createElement("td", $record[TableMoldHistory::Updated]);
					$trBody->appendChild($td);
					break;
				// �ⷿ����.������
				case TableMoldHistory::UpdateBy :
					if ($record[TableMoldHistory::UpdateBy])
					{
						$textContent = "[".$record["strupdateuserdisplaycode"]."]"." ".$record["strupdateuserdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// �ⷿ����.�С������
				case TableMoldHistory::Version :
					$td = $doc->createElement("td", $record[TableMoldHistory::Version]);
					$trBody->appendChild($td);
					break;
				// �ⷿ����.����ե饰
				case TableMoldHistory::DeleteFlag :
					$td = $doc->createElement("td", $record[TableMoldHistory::DeleteFlag]);
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

		// ����ܥ����ɽ��
		if ($allowedDelete)
		{
			// ����ܥ���
			$imgDelete = $doc->createElement("img");
			$imgDelete->setAttribute("src", "/mold/img/remove_off_bt.gif");
			$imgDelete->setAttribute("id", $record[TableMoldHistory::MoldNo]);
			$imgDelete->setAttribute("historyno", $record[TableMoldHistory::HistoryNo]);
			$imgDelete->setAttribute("version", $record[TableMoldHistory::Version]);
			$imgDelete->setAttribute("class", "delete button");
			// td > img
			$tdDelete->appendChild($imgDelete);
		}
		// tr > td
		$trBody->appendChild($tdDelete);
	}

	// tbody > tr
	$tbody->appendChild($trBody);
}

// HTML����
echo $doc->saveHTML();

