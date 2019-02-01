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
$lngLanguageCode = $_COOKIE["lngLanguageCode"];

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
$query[] = "    , ms.dtmappropriationdate   -- �����ޥ���.�����׾���";
$query[] = "    , mo.strOrderCode || '-' || mo.strReviseCode AS strOrderCode -- ȯ��ޥ���.ȯ������-ȯ��ޥ���.����������";
$query[] = "    , '[' || mc.strCompanyDisplayCode || ']' || mc.strCompanyDisplayname AS strCompanyDisplayCode -- [��ҥޥ���.ɽ����ҥ�����] ��ҥޥ���.ɽ�����̾��";
$query[] = "    , mp.strproductcode         -- ���ʥޥ���.���ʥ�����";
$query[] = "    , mp.strproductname         -- ���ʥޥ���.���ʥ�����(���ܸ�)";
$query[] = "    , mp.strproductenglishname  -- ���ʥޥ���.����̾��(�Ѹ�)";
$query[] = "    , mp.strgoodscode           -- ���ʥޥ���.�ܵ�����";
$query[] = "    , '[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname as strgroupdisplaycode -- [���롼�ץޥ���.ɽ�����롼�ץ�����] ���롼�ץޥ���.ɽ�����롼��̾";
$query[] = "    , '[' || mu.struserdisplaycode || ']' || mu.struserdisplayname as struserdisplaycode -- [�桼���ޥ���.ɽ���桼��������] �桼���ޥ���.ɽ���桼��̾";
$query[] = "    , tmh.moldno                -- �ⷿ����.�ⷿNO";
$query[] = "    , tmh.historyno             -- �ⷿ����.�����ֹ�";
$query[] = "    , tsd.lngProductQuantity    -- �����ܺ�.����";
$query[] = "    , mou.strMonetaryUnitSign || tsd.curSubTotalPrice as strMonetaryUnitSign -- �̲�ñ�̥ޥ���.�̲�ñ�� || �����ܺ�.���֥ȡ��������";
$query[] = "    , tmh.status                -- �ⷿ����.�ⷿ���ơ�����";
$query[] = "    , tmh.actiondate            -- �ⷿ����.�»���";
$query[] = "    , tmh.sourcefactory         -- �ⷿ����.�ݴɹ���";
$query[] = "    , tmh.destinationfactory    -- �ⷿ����.��ư�蹩��";
$query[] = "    , tmh.created::date         -- �ⷿ����.��Ͽ��";
$query[] = "    , tmh.createby              -- �ⷿ����.��Ͽ��";
$query[] = "    , tmh.updated::date         -- �ⷿ����.��Ͽ��";
$query[] = "    , tmh.updateby              -- �ⷿ����.������";
$query[] = "    , tmh.version               -- �ⷿ����.�С������";
$query[] = "    , tmh.deleteflag            -- �ⷿ����.����ե饰";
$query[] = "FROM";
// ������������
if (array_key_exists("IsDetail", $optionColumns))
{
	$query[] = "    t_moldhistory tmh";
}
// �ǿ�������Τߤ�ɽ������
else
{
	$query[] = "(";
	$query[] = "    SELECT";
	$query[] = "        *";
	$query[] = "    FROM";
	$query[] = "        t_moldhistory";
	$query[] = "    WHERE";
	$query[] = "    (moldno, historyno) in";
	$query[] = "    (";
	$query[] = "        SELECT";
	$query[] = "              itmh.moldno";
	$query[] = "            , itmh.historyno";
	$query[] = "        FROM";
	$query[] = "            t_moldhistory itmh";
	$query[] = "        WHERE";
	$query[] = "            deleteflag = false";
	$query[] = "        group by";
	$query[] = "              itmh.moldno";
	$query[] = "            , itmh.historyno";
	$query[] = "    )";
	$query[] = ") as tmh";
}
$query[] = "INNER JOIN";
$query[] = "    m_mold mm";
$query[] = "  ON";
$query[] = "    tmh.moldno = mm.moldno";
$query[] = "LEFT OUTER JOIN";
$query[] = "    t_moldreportrelation tmrr";
$query[] = "  ON";
$query[] = "        tmh.moldno = tmrr.moldno";
$query[] = "    AND tmh.historyno = tmrr.historyno";
$query[] = "----------------------------------------------";
$query[] = "--  �����ܺ� - �� �� ���� - ���롼�� - �桼��";
$query[] = "----------------------------------------------";
$query[] = "LEFT OUTER JOIN";
$query[] = "(";
$query[] = "    SELECT";
$query[] = "        itsd.*";
$query[] = "    FROM";
$query[] = "        t_stockdetail itsd";
$query[] = "    WHERE";
$query[] = "        (itsd.lngstockno, itsd.lngstockdetailno, itsd.lngrevisionno) IN";
$query[] = "        (";
$query[] = "            SELECT";
$query[] = "                  lngstockno";
$query[] = "                , lngstockdetailno";
$query[] = "                , MAX(lngrevisionno)";
$query[] = "            FROM";
$query[] = "                t_stockdetail iitsd";
$query[] = "            WHERE";
$query[] = "                (iitsd.strmoldno, iitsd.lngstockno, lngstockdetailno) IN";
$query[] = "                (";
$query[] = "                    SELECT";
$query[] = "                          iiitsd.strmoldno";
$query[] = "                        , max(iiitsd.lngstockno)";
$query[] = "                        , max(iiitsd.lngstockdetailno)";
$query[] = "                    FROM";
$query[] = "                        t_stockdetail iiitsd";
$query[] = "                    WHERE";
$query[] = "                        (";
$query[] = "                             (iiitsd.lngStockSubjectCode = 433 AND iiitsd.lngStockItemCode = 1)";
$query[] = "                          OR (iiitsd.lngStockSubjectCode = 431 AND iiitsd.lngStockItemCode = 8)";
$query[] = "                        )";
$query[] = "                    GROUP BY";
$query[] = "                          iiitsd.strmoldno";
$query[] = "                )";
$query[] = "            GROUP BY";
$query[] = "                  lngstockno";
$query[] = "                , lngstockdetailno";
$query[] = "        )";
$query[] = ") tsd";
$query[] = "  ON";
$query[] = "    mm.moldno = tsd.strmoldno";
$query[] = "LEFT JOIN";
$query[] = "    m_tax   mt";
$query[] = "  ON";
$query[] = "    tsd.lngtaxcode = mt.lngtaxcode";
$query[] = "LEFT OUTER JOIN";
$query[] = "    m_product mp";
$query[] = "  ON";
$query[] = "     tsd.strproductcode = mp.strproductcode";
$query[] = "LEFT JOIN ";
$query[] = "    m_group mg";
$query[] = "  ON";
$query[] = "    mp.lnginchargegroupcode = mg.lnggroupcode";
$query[] = "LEFT JOIN";
$query[] = "    m_user  mu";
$query[] = "  ON";
$query[] = "    mp.lnginchargeusercode = mu.lngusercode";
$query[] = "----------------------------------------------";
$query[] = "--  �����ޥ��� �� ȯ�� - ��� - �̲�ñ��";
$query[] = "----------------------------------------------";
$query[] = "LEFT OUTER JOIN";
$query[] = "(";
$query[] = "    SELECT";
$query[] = "        ims.*";
$query[] = "    FROM";
$query[] = "        m_stock ims";
$query[] = "    WHERE";
$query[] = "        ims.lngRevisionNo = ";
$query[] = "        (";
$query[] = "            SELECT";
$query[] = "                MAX( s1.lngRevisionNo )";
$query[] = "            FROM";
$query[] = "                m_Stock s1";
$query[] = "            WHERE";
$query[] = "                    s1.strStockCode = ims.strStockCode";
$query[] = "                AND s1.bytInvalidFlag = false";
$query[] = "        )";
$query[] = "    AND 0 <= ";
$query[] = "        ( ";
$query[] = "            SELECT";
$query[] = "                MIN( s2.lngRevisionNo )";
$query[] = "            FROM";
$query[] = "                m_Stock s2";
$query[] = "            WHERE";
$query[] = "                    s2.bytInvalidFlag = false";
$query[] = "                AND s2.strStockCode = ims.strStockCode";
$query[] = "        )";
$query[] = ") ms";
$query[] = "  ON";
$query[] = "    tsd.lngstockno = ms.lngstockno";
$query[] = "LEFT OUTER JOIN";
$query[] = "    m_order mo";
$query[] = "  ON";
$query[] = "    ms.lngorderno = mo.lngorderno";
$query[] = "LEFT JOIN";
$query[] = "    m_Company mc";
$query[] = "  ON";
$query[] = "    ms.lngCustomerCompanyCode = mc.lngCompanyCode";
$query[] = "LEFT JOIN";
$query[] = "    m_MonetaryUnit mou";
$query[] = "  ON";
$query[] = "    ms.lngMonetaryUnitCode = mou.lngMonetaryUnitCode";
$query[] = "WHERE";
$query[] = "    tmh.deleteflag = false";
$query[] = "AND mm.deleteflag = false";
$query[] = "AND (";
$query[] = "         (tsd.lngStockSubjectCode = 433 AND tsd.lngStockItemCode = 1)";
$query[] = "      OR (tsd.lngStockSubjectCode = 431 AND tsd.lngStockItemCode = 8)";
$query[] = "    )";

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



// ������̤������ʤ��ä����
if (!pg_num_rows($lngResultID))
{
	// ����Ģɼ�ǡ����ʤ�
	$strMessage = fncOutputError(9068, DEF_WARNING, "" ,FALSE, "", $objDB );

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
	$thDetail = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ܺ�") : "Detail");
	$thDetail->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thDetail);
}

// �������ܤ�ɽ��
if($existsModify)
{
	// ���������
	$thModify = $doc->createElement("th", $lngLanguageCode ? toUTF8("����") : "Modify");
	$thModify->setAttribute("class", $exclude);
	// �إå����ɲ�
	$trHead->appendChild($thModify);
}

// COPY/�ץ�ӥ塼���ܤ�ɽ��
if ($existsPreview)
{
	// �ץ�ӥ塼�����
	$thPreview = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ץ�ӥ塼") : "Preview");
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
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ⷿĢɼID") : "Mold Report ID");
				$trHead->appendChild($th);
				break;
			// �����ޥ���.�����׾���
			case "dtmappropriationdate":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�׾���") : "Date");
				$trHead->appendChild($th);
				break;
			// ȯ��ޥ���.ȯ������-ȯ��ޥ���.����������
			case "strordercode":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("ȯ������") : "Order Code");
				$trHead->appendChild($th);
				break;
			// [��ҥޥ���.ɽ����ҥ�����] ��ҥޥ���.ɽ�����̾��
			case "strcompanydisplaycode":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("������") : "Vender");
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.���ʥ�����
			case "strproductcode":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("���ʥ�����") : "Products Code");
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.���ʥ�����(���ܸ�)
			case "strproductname":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("����̾��") : "Products Name");
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.����̾��(�Ѹ�)
			case "strproductenglishname":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("����̾��(�Ѹ�)") : "Products English Name");
				$trHead->appendChild($th);
				break;
			// ���ʥޥ���.�ܵ�����
			case "strgoodscode":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ܵ�����") : "Goods Code");
				$trHead->appendChild($th);
				break;
			// [���롼�ץޥ���.ɽ�����롼�ץ�����] ���롼�ץޥ���.ɽ�����롼��̾
			case "strgroupdisplaycode":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("ô������") : "Group");
				$trHead->appendChild($th);
				break;
			// [�桼���ޥ���.ɽ���桼��������] �桼���ޥ���.ɽ���桼��̾
			case "struserdisplaycode":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("ô����") : "User");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�ⷿNO
			case TableMoldHistory::MoldNo:
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ⷿNO") : "Mold No");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�����ֹ�
			case TableMoldHistory::HistoryNo:
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�����ֹ�") : "History No");
				$trHead->appendChild($th);
				break;
			// �����ܺ�.���ʿ���
			case "lngproductquantity":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("����") : "Products Qty");
				$trHead->appendChild($th);
				break;
			// �̲�ñ�̥ޥ���.�̲�ñ�� || �����ܺ�.��ȴ���
			case "strmonetaryunitsign":
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("��ȴ���") : "Amt Bfr tax");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�ⷿ���ơ�����
			case TableMoldHistory::Status:
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ⷿ���ơ�����") : "Mold Status");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�»���
			case TableMoldHistory::ActionDate:
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�»���") : "Action Date");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�ݴɹ���
			case TableMoldHistory::SourceFactory:
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�ݴɸ�����") : "Source Factory");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��ư�蹩��
			case TableMoldHistory::DestinationFactory:
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("��ư�蹩��") : "Destination Factory");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��Ͽ����
			case TableMoldHistory::Created :
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("��Ͽ��") : "Created");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��Ͽ��
			case TableMoldHistory::CreateBy :
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("��Ͽ��") : "Create By");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.��������
			case TableMoldHistory::Updated :
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("������") : "Updated");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.������
			case TableMoldHistory::UpdateBy :
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("������") : "Update By");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.�С������
			case TableMoldHistory::Version :
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("�С������") : "Version");
				$trHead->appendChild($th);
				break;
			// �ⷿ����.����ե饰
			case TableMoldHistory::DeleteFlag :
				$th = $doc->createElement("th", $lngLanguageCode ? toUTF8("����ե饰") : "Delete Flag");
				$trHead->appendChild($th);
				break;
		}
	}
}

// ������ܤ�ɽ��
if($existsDelete)
{
	// ��������
	$thDelete = $doc->createElement("th", $lngLanguageCode ? toUTF8("���") : "Delete");
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
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldHistory::SourceFactory]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldHistory::SourceFactory]);
						$textContent = "[".$displayCode."]"." ".$displayName;
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
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldHistory::DestinationFactory]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldHistory::DestinationFactory]);
						$textContent = "[".$displayCode."]"." ".$displayName;
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
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldHistory::CreateBy]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldHistory::CreateBy]);
						$textContent = "[".$displayCode."]"." ".$displayName;
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
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldHistory::UpdateBy]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldHistory::UpdateBy]);
						$textContent = "[".$displayCode."]"." ".$displayName;
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

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"));
}
