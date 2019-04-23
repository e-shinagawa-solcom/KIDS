<?
/** 
*	���Ѹ������� �������ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// -------------------------------------------------------------------------
// search.php -> strSessionID           -> index.php
// search.php -> lngFunctionCode        -> index.php

// search.php -> strProductCodeFrom          -> index.php
// search.php -> strProductCodeTo            -> index.php
// search.php -> strProductName              -> index.php
// search.php -> strInchargeGroupDisplayCode -> index.php
// search.php -> strInchargeUserDisplayCode  -> index.php
// search.php -> strInputUserDisplayCode     -> index.php
// search.php -> dtmCreationDateFrom         -> index.php
// search.php -> dtmCreationDateTo           -> index.php
// search.php -> dtmDeliveryLimitDateFrom    -> index.php
// search.php -> dtmDeliveryLimitDateTo      -> index.php

// search.php -> strProductCodeConditions              -> index.php
// search.php -> strProductNameConditions              -> index.php
// search.php -> strInchargeGroupDisplayCodeConditions -> index.php
// search.php -> strInchargeUserDisplayCodeConditions  -> index.php
// search.php -> strInputUserDisplayCodeConditions     -> index.php
// search.php -> dtmCreationDateConditions             -> index.php
// search.php -> dtmDeliveryLimitDateConditions        -> index.php

// search.php -> btnDetailVisible	                -> index.php
// search.php -> strProductCodeVisible              -> index.php
// search.php -> strProductNameVisible              -> index.php
// search.php -> strInchargeGroupDisplayCodeVisible -> index.php
// search.php -> strInchargeUserDisplayCodeVisible  -> index.php
// search.php -> strInputUserDisplayCodeVisible     -> index.php
// search.php -> dtmCreationDateVisible             -> index.php
// search.php -> dtmDeliveryLimitDateVisible        -> index.php
// search.php -> curProductPriceVisible             -> index.php
// search.php -> curRetailPriceVisible              -> index.php
// search.php -> lngCartonQuantityVisible           -> index.php
// search.php -> lngPlanCartonProductionVisible     -> index.php
// search.php -> lngProductionQuantityVisible       -> index.php
// search.php -> strEsitimateVisible                -> index.php
// search.php -> curFixedCostVisible                -> index.php
// search.php -> curMemberCostVisible               -> index.php
// search.php -> curManufacturingCostVisible        -> index.php
// search.php -> curAmountOfSalesVisible            -> index.php
// search.php -> curTargetProfitVisible             -> index.php
// search.php -> curAchievementRatioVisible         -> index.php
// search.php -> curStandardRateVisible             -> index.php
// search.php -> curProfitOnSalesVisible            -> index.php

// �����ɤ߹���
include_once('conf.inc');
require_once( LIB_DEBUGFILE );
require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "estimate/cmn/lib_e.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
	$options = UtilSearchForm::extractArrayByOption($_REQUEST);
	$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
	$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to = UtilSearchForm::extractArrayByTo($_REQUEST);
	$searchValue = $_REQUEST;
	$isDisplay=array_keys($isDisplay);
	$isSearch=array_keys($isSearch);

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////

	$aryData['ViewColumn']=$isDisplay;
	$aryData['SearchColumn']=$isSearch;
	foreach($from as $key=> $item){
		$aryData[$key.'From']=$item;
	}
	foreach($to as $key=> $item){
		$aryData[$key.'To']=$item;
	}
	foreach($searchValue as $key=> $item){
		$aryData[$key]=$item;
	}


// ���å���������쥳���ɤ����
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// ����ɽ�����ܼ���
if ( $lngArrayLength = count ( $aryData["ViewColumn"] ) )
{
	$aryViewColumn = $aryData["ViewColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryViewColumn[$i]] = 1;
	}
	unset ( $aryData["ViewColumn"] );
}

// ���������ܼ���
if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
{
	$arySearchColumn = $aryData["SearchColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$arySearchColumn[$i]] = 1;
	}
	unset ( $aryData["SearchColumn"] );
}
unset ( $lngArrayLength );
$aryData = fncToHTMLString( $aryData );

//echo getArrayTable( $aryData, "TABLE" );
//exit;

//////////////////////////////////////////////////////////////////////////
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ������̤Υ����ɽ���θ�������
if ( !$aryData["lngLanguageCode"] )
{
	$aryColumnLang = Array (
		"detail"						=> "Detail",
		"update"						=> "Fix",

		"strProductCode"				=> "Product Code",
		"strProductName"				=> "Product Name",
		"strInchargeGroupDisplayCode"	=> "Group",
		"strInchargeUserDisplayCode"	=> "User",
		"strInputUserDisplayCode"		=> "Input User",
		"dtmCreationDate"				=> "Creation Date",
		"dtmDeliveryLimitDate"			=> "Limit Date",
		"curProductPrice"				=> "Product Price",
		"curRetailPrice"				=> "Retail Price",
		"lngCartonQuantity"				=> "Carton Quantity Cost",
		"lngPlanCartonProduction"		=> "Plan Carton Production",
		"lngProductionQuantity"			=> "Production Quantity",
		"431"							=> "431",
		"433"							=> "433",
		"403"							=> "403",
		"curFixedCost"					=> "Fixed Cost",
		"402"							=> "402",
		"401"							=> "401",
		"420"							=> "420",
		"1224"							=> "1224",
		"1230"							=> "1230",
		"curMemberCost"					=> "Member Cost",
		"curManufacturingCost"			=> "Manufacturing Cost",
		"curAmountOfSales"				=> "Sales Amount",
		"curTargetProfit"				=> "Profit",
		"curAchievementRatio"			=> "AchieveRate",
		"curStandardRate"				=> "IndirectCost",
		"curProfitOnSales"				=> "Sales",
		"bytDecisionFlag"				=> "Saved",

		"lngWorkFlowStatusCode"			=> "Work flow status",

		"delete"						=> "Del"
	);
}
else
{
	$aryColumnLang = Array (
		"detail"						=> "�ܺ�",
		"update"						=> "����",

		"strProductCode"				=> "���ʥ�����",
		"strProductName"				=> "����̾��(���ܸ�)",
		"strInchargeGroupDisplayCode"	=> "����",
		"strInchargeUserDisplayCode"	=> "ô����",
		"strInputUserDisplayCode"		=> "���ϼ�",
		"dtmCreationDate"				=> "��������",
		"dtmDeliveryLimitDate"			=> "Ǽ��",
		"curProductPrice"				=> "Ǽ��",
		"curRetailPrice"				=> "����",
		"lngCartonQuantity"				=> "�����ȥ�����",
		"lngPlanCartonProduction"		=> "�ײ�C/t",
		"lngProductionQuantity"			=> "����ͽ���",
		"431"							=> "[431] �ⷿ���ѹ� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"433"							=> "[433] �ⷿ�������ѹ� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"403"							=> "[403] �����ġ�������� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"curFixedCost"					=> "���ѹ�׶��", // "�������׶��",
		"402"							=> "[402] ͢���ѡ��Ļ����� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"401"							=> "[401] �����ѡ��Ļ����� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"420"							=> "[420] ����ù��� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"1224"							=> "[1224] ���㡼�� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"1230"							=> "[1230] ���� [��������][�����о�][������][�ײ�Ŀ�][ñ��][�ײ踶��]",
		"curMemberCost"					=> "�������׶��",
		"curManufacturingCost"			=> "��¤���ѹ��", // "����¤����",
		"curAmountOfSales"				=> "ͽ��������", // "ͽ������",
		"curTargetProfit"				=> "�����ɸ������", // "�����ɸ����",
		"curAchievementRatio"			=> "��ɸ����Ψ",
		"curStandardRate"				=> "������¤����",
		"curProfitOnSales"				=> "���������",
		"bytDecisionFlag"				=> "��¸����",

		"lngWorkFlowStatusCode"			=> "����ե�����",

		"delete"						=> "���",
			
		"1"			=> "[1] ��������� [����ʬ] [�ܵ���] [����] [ñ��] [����]",
		"curFixedPlanProfit"			=> "����������",
		"curSubjectTotalCost"			=> "�����������",
		"curNonFixedCost"				=> "�����оݳ���׶��"
	);
}


//////////////////////////////////////////////////////////////////////////
// ʸ��������å�
//////////////////////////////////////////////////////////////////////////
$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["strProductCodeFrom"]          = "number(0,2147483647)";
$aryCheck["strProductCodeTo"]            = "number(0,2147483647)";
$aryCheck["strProductName"]              = "length(0,80)";
$aryCheck["strInchargeGroupDisplayCode"] = "numenglish(0,32767)";
$aryCheck["strInchargeUserDisplayCode"]  = "numenglish(0,32767)";
$aryCheck["strInputUserDisplayCode"]     = "numenglish(0,32767)";
$aryCheck["dtmCreationDateFrom"]         = "date(/)";
$aryCheck["dtmCreationDateTo"]           = "date(/)";
$aryCheck["dtmDeliveryLimitDateFrom"]    = "date(/)";
$aryCheck["dtmDeliveryLimitDateTo"]      = "date(/)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// ���Ѹ��������ǡ����ɤ߹��ߡ��������ܺپ������������ؿ�
list ( $lngResultID, $lngResultNum, $baseData["strErrorMessage"] ) = getEstimateQuery( $objAuth->UserCode, $aryData, $objDB );

// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
$strURL = fncGetURL( $aryData );
//echo $strURL;exit;


//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
// �����̾����
if ( $aryData["btnDetailVisible"] )
{
	// �ܺ�
	$baseData["detail"] = "<td nowarp>" . $aryColumnLang["detail"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["btnFixVisible"] )
{
	// ����
	$baseData["update"] = "<td nowarp>" . $aryColumnLang["update"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["strProductCodeVisible"] )
{
	// ���ʥ�����
	$baseData["column1"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_1_ASC';\"><a href=\"#\">" . $aryColumnLang["strProductCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strProductNameVisible"] )
{
	// ����̾
	$baseData["column2"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_2_ASC';\"><a href=\"#\">" . $aryColumnLang["strProductName"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strInchargeGroupDisplayCodeVisible"] )
{
	// ô�����롼��ɽ��������
	$baseData["column3"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_3_ASC';\"><a href=\"#\">" . $aryColumnLang["strInchargeGroupDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strInchargeUserDisplayCodeVisible"] )
{
	// ô����ɽ��������
	$baseData["column4"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_4_ASC';\"><a href=\"#\">" . $aryColumnLang["strInchargeUserDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strInputUserDisplayCodeVisible"] )
{
	// ���ϼԥ�����
	$baseData["column5"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_5_ASC';\"><a href=\"#\">" . $aryColumnLang["strInputUserDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["dtmCreationDateVisible"] )
{
	// ��������
	$baseData["column6"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_6_ASC';\"><a href=\"#\">" . $aryColumnLang["dtmCreationDate"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["dtmDeliveryLimitDateVisible"] )
{
	// Ǽ��
	$baseData["column7"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_7_ASC';\"><a href=\"#\">" . $aryColumnLang["dtmDeliveryLimitDate"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curProductPriceVisible"] )
{
	// Ǽ��
	$baseData["column8"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_8_ASC';\"><a href=\"#\">" . $aryColumnLang["curProductPrice"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curRetailPriceVisible"] )
{
	// ����
	$baseData["column9"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_9_ASC';\"><a href=\"#\">" . $aryColumnLang["curRetailPrice"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngCartonQuantityCostVisible"] )
{
	// �����ȥ�����
	$baseData["column10"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_10_ASC';\"><a href=\"#\">" . $aryColumnLang["lngCartonQuantity"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngPlanCartonProductionVisible"] )
{
	// ����ͽ���/�����ȥ��������ײ�C/t
	$baseData["column11"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_11_ASC';\"><a href=\"#\">" . $aryColumnLang["lngPlanCartonProduction"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngProductionQuantityVisible"] )
{
	// ����ͽ���
	$baseData["column12"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_12_ASC';\"><a href=\"#\">" . $aryColumnLang["lngProductionQuantity"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strEstimateVisible"] )
{
	// ���Ѿ���
	$baseData["column13"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["431"] . "</td>";
	$baseData["column14"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["433"] . "</td>";
	$baseData["column15"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["403"] . "</td>";
	$baseData["column17"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["402"] . "</td>";
	$baseData["column18"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["401"] . "</td>";
	$baseData["column19"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["420"] . "</td>";
	$baseData["column20"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["1224"] . "</td>";
	$baseData["column21"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["1230"] . "</td>";
	$lngColumnNum += 8;
}

if ( $aryData["curFixedCostVisible"] )
{
	// �������׶��
	$baseData["column16"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_16_ASC';\"><a href=\"#\">" . $aryColumnLang["curFixedCost"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curMemberCostVisible"] )
{
	// �������׶��
	$baseData["column22"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_22_ASC';\"><a href=\"#\">" . $aryColumnLang["curMemberCost"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curManufacturingCostVisible"] )
{
	// ����¤����
	$baseData["column23"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_23_ASC';\"><a href=\"#\">" . $aryColumnLang["curManufacturingCost"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curAmountOfSalesVisible"] )
{
	// ͽ��������
	$baseData["column24"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_24_ASC';\"><a href=\"#\">" . $aryColumnLang["curAmountOfSales"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curTargetProfitVisible"] )
{
	// �����ɸ����
	$baseData["column25"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_25_ASC';\"><a href=\"#\">" . $aryColumnLang["curTargetProfit"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curAchievementRatioVisible"] )
{
	// ��ɸã��Ψ
	$baseData["column26"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_26_ASC';\"><a href=\"#\">" . $aryColumnLang["curAchievementRatio"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curStandardRateVisible"] )
{
	// ������¤����
	$baseData["column27"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_27_ASC';\"><a href=\"#\">" . $aryColumnLang["curStandardRate"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curProfitOnSalesVisible"] )
{
	// ���������
	$baseData["column28"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_28_ASC';\"><a href=\"#\">" . $aryColumnLang["curProfitOnSales"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["bytDecisionFlagVisible"] )
{
	// ��¸����
	$baseData["column29"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["bytDecisionFlag"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["lngWorkFlowStatusCodeVisible"] )
{
	// ����ե�����
	$baseData["column30"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["lngWorkFlowStatusCode"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["btnDeleteVisible"] )
{
	// ���
	$baseData["delete"] = "<td nowarp>" . $aryColumnLang["delete"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["strEstimateVisible"] )
{
	// ���ʬ��
	$baseData["column31"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["1"] . "</td>";
	$lngColumnNum += 1;
}

	// ����׶��
	$baseData["column32"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["curSubjectTotalCost"] . "</td>";
	// �����оݳ���׶��
	$baseData["column33"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["curNonFixedCost"] . "</td>";
	// ����������
	$baseData["column34"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["curFixedPlanProfit"] . "</td>";
	$lngColumnNum += 3;


// Ʊ�����ܤΥ����Ȥϵս�ˤ������
list ( $column, $lngSort, $DESC ) = explode ( "_", $aryData["strSort"] );

if ( $DESC == 'ASC' )
{
	$baseData["column" . $lngSort] = preg_replace ( "/ASC/", "DESC", $baseData["column" . $lngSort] );
}


//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
// ��̹Խ���
//$aryPayOffTargetFlag = Array ("t" => "�����о�", "f" => "���о�" );
$aryPayOffTargetFlag = Array ("t" => "��", "f" => "" );
$aryDecisionFlag = Array ("t" => "��", "f" => "����¸" );

// �ѡ��ĥƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/result/parts.tmpl" );


// �ѡ��ĥƥ�ץ졼�ȥ��ԡ�
$strTemplate = $objTemplate->strTemplate;

$baseData["lngColumnNum"] =& $lngColumnNum;


//////////////////////////////////////////////////////////////////////////
// ���Ѿ���HTML����
//////////////////////////////////////////////////////////////////////////
$aryDetailHtml = array();
$aryDetailHtmlSales = array();
$aryNonFixedCost = array();
$arySubjectTotalCost = array();

// �̲ߥ�����->�̲ߵ���
$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "��", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );

//if ( $aryData["strEstimateVisible"] )
//{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		if ( $objResult->bytpercentinputflag == "t" )
		{
			$lngProductRate = "(" . number_format( $objResult->curproductrate * 100, 2, ".", "," ) . "%)";
			$cutDetailProductPrice = $lngProductRate;
		}
		else
		{
			$cutDetailProductPrice = $aryMonetaryUnit[$objResult->lngmonetaryunitcode] . $objResult->curdetailproductprice;
		}

		$aryDetailHtmlTD = array();

		// ----------------------------------------------------------------
		// �������ܤ��󤫡����ʬ����󤫡���Ƚ�Ǥ���
		// null�Ǥ�̵����0�ʳ���������������
		// 0 �������ʬ��
		// ----------------------------------------------------------------
		if( !is_null($objResult->lngstocksubjectcode) && $objResult->lngstocksubjectcode != 0 )
		{
			// �������ܡ���ʬ���ν���

			// ���Ѹ����ֹ桢�������ܥ����ɤ򥭡��Ȥ���Ϣ�������
			// HTML�ơ��֥�Ԥ򥻥å�
			$aryDetailHtmlTD[] = "[" . $objResult->lngstockitemcode . " " . $objResult->strstockitemname . "]";
			$aryDetailHtmlTD[] = "[" . $aryPayOffTargetFlag[$objResult->bytpayofftargetflag] . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->lngproductquantity . "]";
			$aryDetailHtmlTD[] = "[" . $cutDetailProductPrice . "]";
			$aryDetailHtmlTD[] = "[" . $aryMonetaryUnit[$objResult->lngmonetaryunitcode] . $objResult->cursubtotalprice . "]";

			// <tr><td>***</td>������</tr>������
			$aryDetailHtml[$objResult->lngestimateno][$objResult->lngstocksubjectcode] .= "<tr><td nowrap align=left>" . join ( "</td><td nowrap align=left>", $aryDetailHtmlTD ) . "</td></tr>\n";

			// �����оݳ����
			if( $objResult->lngstockclasscode == 2 && $objResult->bytpayofftargetflag == 'f' )
			{
				$aryNonFixedCost[$objResult->lngestimateno] += ($objResult->cursubtotalpricedefault * $objResult->curconversionrate);
//fncDebug( 'estimate_result_index_01.txt', ($objResult->cursubtotalpricedefault * $objResult->curconversionrate), __FILE__, __LINE__,'a');
			}
		}
		else
		{
			// ���ʬ�ࡦ���ܡ��ν���

			// ���Ѹ����ֹ桢�������ܥ����ɤ򥭡��Ȥ���Ϣ�������
			// HTML�ơ��֥�Ԥ򥻥å�
			$aryDetailHtmlTD[] = "[" . $objResult->lngsalesclasscode. " " . $objResult->strsalesclassname . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->lngproductquantity . "]";
			$aryDetailHtmlTD[] = "[" . $cutDetailProductPrice . "]";
			$aryDetailHtmlTD[] = "[" . $aryMonetaryUnit[$objResult->lngmonetaryunitcode] . $objResult->cursubtotalprice . "]";

			// <tr><td>***</td>������</tr>������
			$aryDetailHtmlSales[$objResult->lngestimateno][$objResult->lngsalesdivisioncode] .= "<tr><td nowrap align=left>" . join ( "</td><td nowrap align=left>", $aryDetailHtmlTD ) . "</td></tr>\n";

			// �����������
			$arySubjectTotalCost[$objResult->lngestimateno] += ($objResult->cursubtotalpricedefault * $objResult->curconversionrate);


//fncDebug( 'estimate_result_index_02.txt', $objResult->curSubTotalPriceDefault * $objResult->curConversionRate, __FILE__, __LINE__,'a');
		}




		unset ($lngProductRate );
		unset ($objResult );
	}
//}



//////////////////////////////////////////////////////////////////////////
// ���������ѡ��ĥƥ�ץ졼�Ȥ�������
//////////////////////////////////////////////////////////////////////////
$curFixedPlanProfit = 0;
$curAmountOfSales = 0;
$curTargetProfit = 0;
$curAchievementRatio = 0;
$IndirectManufactExpenditure = 0;
$curProfitOnSales = 0;


for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	if ( $objResult->lngestimateno != $lngEstimateNo )
	{
		// Ϣ��
		$partsData["number"] = ++$j;

		// �ܺ�URL
		if ( $aryData["btnDetailVisible"] )
		{
			//$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('detail.php?strSessionID=$aryData[strSessionID]&lngEstimateNo=" . $objResult->lngestimateno . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'detail' );\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";

//			$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"detail.php?strSessionID=$aryData[strSessionID]&lngEstimateNo=" . $objResult->lngestimateno . "\" target=_blank><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
			$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"/estimate/result/detail.php?strSessionID=$aryData["strSessionID"]&lngEstimateNo=" . $objResult->lngestimateno . "\" target=\"_blank\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
		}

//fncDebug( 'estimate_result_index_01.txt', "/estimate/result/detail.php?strSessionID=$aryData[strSessionID]&lngEstimateNo=$objResult->lngestimateno", __FILE__, __LINE__);


		if( $aryData["btnFixVisible"] )
		{
			// ������桼���������Ϥ�����Τ��Ĳ���¸���֤Τ�Ρ�
			// �ޤ��ϡ�����ե���ǧ�ʳ��Τ�Τϡ������ܥ����ɽ������
			if( ( $objResult->bytdecisionflag == "f" && $objResult->lnginputusercode == $objAuth->UserCode ) ||
				( $objResult->bytdecisionflag == "t" && $objResult->lngestimatestatuscode != DEF_ESTIMATE_APPLICATE ) )
			{
				// ����
				//$partsData["update"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/estimate/regist/renew.php?strSessionID=$aryData[strSessionID]&lngFunctionCode=" . DEF_FUNCTION_E3 . "&lngEstimateNo=" . $objResult->lngestimateno . "&lngEstimateNoCondition=1' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				$partsData["update"] = '<td bgcolor="#ffffff">&nbsp;</td>';
			}
			else
			{
				$partsData["update"] = '<td bgcolor="#ffffff">&nbsp;</td>';
			}
		}



		// ���ʥ�����
		if ( $aryData["strProductCodeVisible"] )
		{
			$partsData["strProductCode"] = "<td nowrap align=\"left\">" . $objResult->strproductcode . "</td>";
		}
		// ����̾
		if ( $aryData["strProductNameVisible"] )
		{
			$partsData["strProductName"] = "<td nowrap align=\"left\">" . $objResult->strproductname . "</td>";
		}
		// ô�����롼��ɽ��������
		if ( $aryData["strInchargeGroupDisplayCodeVisible"] )
		{
			$partsData["strInchargeGroup"] = "<td nowrap align=\"left\">" . "[" . $objResult->strinchargegroupdisplaycode . "] " . $objResult->strinchargegroupdisplayname . "</td>";
		}
		// ô����ɽ��������
		if ( $aryData["strInchargeUserDisplayCodeVisible"] )
		{
			$partsData["strInchargeUser"] = "<td nowrap align=\"left\">" . "[" . $objResult->strinchargeuserdisplaycode . "] " . $objResult->strinchargeuserdisplayname . "</td>";
		}
		// ���ϼԥ�����
		if ( $aryData["strInputUserDisplayCodeVisible"] )
		{
			$partsData["strInputUser"] = "<td nowrap align=\"left\">" . "[" . $objResult->strinputuserdisplaycode . "] " . $objResult->strinputuserdisplayname . "</td>";
		}
		// ��������
		if ( $aryData["dtmCreationDateVisible"] )
		{
			$partsData["dtmCreationDate"] = "<td nowrap align=\"left\">" . $objResult->dtmcreationdate . "</td>";
		}
		// Ǽ�ʴ�����
		if ( $aryData["dtmDeliveryLimitDateVisible"] )
		{
			if ( $objResult->dtmdeliverylimitdate != "" )
			{
				$dtmDeliveryLimitDate = substr( $objResult->dtmdeliverylimitdate, 0, 7 );
			}
			$partsData["dtmDeliveryLimitDate"] = "<td nowrap align=\"left\">" . $dtmDeliveryLimitDate . "</td>";
		}
		// ����
		if ( $aryData["curProductPriceVisible"] )
		{
			$partsData["curProductPrice"] = "<td nowrap align=\"right\">��" . $objResult->curproductprice . "</td>";
		}
		// �������
		if ( $aryData["curRetailPriceVisible"] )
		{
			$partsData["curRetailPrice"] = "<td nowrap align=\"right\">��" . $objResult->curretailprice . "</td>";
		}
		// �����ȥ�����
		if ( $aryData["lngCartonQuantityCostVisible"] )
		{
			$partsData["lngCartonQuantity"] = "<td nowrap align=\"right\">" . $objResult->lngcartonquantity . "</td>";
		}
		// ����ͽ���/�����ȥ�����
		if ( $aryData["lngPlanCartonProductionVisible"] )
		{
			$partsData["lngPlanCartonProduction"] = "<td nowrap align=\"right\">" . $objResult->lngplancartonproduction . "</td>";
		}
		// ����ͽ���
		if ( $aryData["lngProductionQuantityVisible"] )
		{
			$partsData["lngProductionQuantity"] = "<td nowrap align=\"right\">" . $objResult->lngproductionquantity . "</td>";
		}
		
		// ���Ѿ���
		if ( $aryData["strEstimateVisible"] )
		{
			// ���ʬ��
			$partsData["lngSalesDivisionCode"] 	= "<td nowrap><table width=100% align=\"left\">".  $aryDetailHtmlSales[$objResult->lngestimateno][1] . "</table></td>";
		}

		// �����������
		$partsData["curSubjectTotalCost"] 	= '<td nowrap align="right">��'. number_format( $arySubjectTotalCost[$objResult->lngestimateno], 2, '.', ',' ) . '</td>';
		// �����оݳ���׶��
		$partsData["curNonFixedCost"] 		= '<td nowrap align="right">��'. number_format( $aryNonFixedCost[$objResult->lngestimateno], 2, '.', ',' ) . '</td>';
		// ����������
		$curFixedPlanProfit = ($arySubjectTotalCost[$objResult->lngestimateno] - $aryNonFixedCost[$objResult->lngestimateno]);
		$partsData["curFixedPlanProfit"] 	= '<td nowrap align="right">��'. number_format( $curFixedPlanProfit, 2, '.', ',' ) . '</td>';
		
		
		// ���Ѿ���
		if ( $aryData["strEstimateVisible"] )
		{
			// ����������
			$partsData["curFixedCostEstimate"] = "<td nowrap><table width=100% align=\"left\">" . $aryDetailHtml[$objResult->lngestimateno][431] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][433] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][403] . "</table></td>";

			// ����������
			$partsData["curMemberCostEstimate"] = "<td nowrap><table width=100% align=\"left\">" . $aryDetailHtml[$objResult->lngestimateno][402] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][401] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][420] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][1224] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][1230] . "</table></td>";
		}
		// �������׶��
		if ( $aryData["curFixedCostVisible"] )
		{
			$partsData["curFixedCost"] = "<td nowrap align=\"right\">��" . $objResult->curfixedcost . "</td>";
		}
		// �������׶��
		if ( $aryData["curMemberCostVisible"] )
		{
			$partsData["curMemberCost"] = "<td nowrap align=\"right\">��" . $objResult->curmembercost . "</td>";
		}
		// ����¤����
		if ( $aryData["curManufacturingCostVisible"] )
		{
			$partsData["curManufacturingCost"] = "<td nowrap align=\"right\">��" . $objResult->curmanufacturingofcost . "</td>";
		}
		// ͽ��������
		$curAmountOfSales = ($objResult->cursalesamount + $arySubjectTotalCost[$objResult->lngestimateno]);
		if ( $aryData["curAmountOfSalesVisible"] )
		{
			$partsData["curAmountOfSales"] = "<td nowrap align=\"right\">��" . number_format($curAmountOfSales, 2, '.', ',' ) . "</td>";
		}
		// �����ɸ���� = ͽ������ �� ����¤���� + ����������
		$curTargetProfit = ($objResult->cursalesamount - $objResult->curmanufacturingcost + $curFixedPlanProfit);
		if ( $aryData["curTargetProfitVisible"] )
		{
			$partsData["curTargetProfit"] = "<td nowrap align=\"right\">��" . number_format($curTargetProfit, 2, '.', ',' ) . "</td>";
		}
		// ��ɸ����Ψ = �����ɸ������ / ͽ��������
		$curAchievementRatio = 0;
		if( $objResult->cursalesamount != 0)
		{
			$curAchievementRatio = ($curTargetProfit / $curAmountOfSales) * 100;
		}
		if ( $aryData["curAchievementRatioVisible"] )
		{
			($curAchievementRatio >= 0) ? $strStyle = '' : $strStyle = 'style="color:red;"';	// �ޥ��ʥ��ͤ��ֻ���
			$partsData["curAchievementRatio"] = "<td nowrap align=\"right\" $strStyle>" . number_format($curAchievementRatio, 2, '.', ',' ) . " %</td>";
		}
		// ������¤���� = ͽ�������� * ɸ����
		$IndirectManufactExpenditure = $curAmountOfSales * $objResult->curstandardrate;
		if ( $aryData["curStandardRateVisible"] )
		{
			$partsData["curStandardRate"] = "<td nowrap align=\"right\">��" . number_format($IndirectManufactExpenditure, 2, '.', ',' ) . "</td>";
		}
		// ��������� = �����ɸ���� - ������¤����
		$curProfitOnSales = $curTargetProfit - $IndirectManufactExpenditure;
		if ( $aryData["curProfitOnSalesVisible"] )
		{
			$partsData["curProfitOnSales"] = "<td nowrap align=\"right\">��" . number_format($curProfitOnSales, 2, '.', ',' ) . "</td>";
		}
		// ��¸����
		if ( $aryData["bytDecisionFlagVisible"] )
		{
			$partsData["bytDecisionFlag"] = "<td nowrap align=\"center\">" . $aryDecisionFlag[$objResult->bytdecisionflag] . "</td>";
		}

/*
		// ����ե�����
		if ( $aryData["lngWorkFlowStatusCodeVisible"] )
		{
			$partsData["lngWorkFlowStatusCode"] = "<td nowrap align=\"center\">" . $lngWorkFlowStatusCode[$objResult->lngworkflowstatuscode] . "</td>";
		}
*/
		// ����ե�����
		if ( $aryData["lngWorkFlowStatusCodeVisible"] )
		{
			if( empty($objResult->lngestimatestatuscode) )
			{
				$partsData["lngWorkFlowStatusCode"] = "<td nowrap align=\"center\">" ." ". "</td>";
			}
			else
			{
				$partsData["lngWorkFlowStatusCode"] = "<td nowrap align=\"center\">" . fncGetMasterValue( "m_estimatestatus", "lngestimatestatuscode", "strestimatestatusname", $objResult->lngestimatestatuscode . "", '', $objDB ) . "</td>";
			}
		}


		//$partsData["update"] = "<td bgcolor=\"#FFFFFF\"><br></td>";

		//$partsData["delete"] = "<td bgcolor=\"#FFFFFF\"><br></td>";



		if( $aryData["btnDeleteVisible"] )
		{
			// ������桼���������Ϥ�����Τ��Ĳ���¸���֤Τ�Ρ�
			// �ޤ��ϡ�����ե���ǧ�ʳ��Τ�Τϡ�����ܥ����ɽ������
			if( ( $objResult->bytdecisionflag == "f" && $objResult->lnginputusercode == $objAuth->UserCode ) ||
				( $objResult->bytdecisionflag == "t" && $objResult->lngestimatestatuscode != DEF_ESTIMATE_APPLICATE ) )
			{
				// ���
				$partsData["delete"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/estimate/regist/confirm.php?strSessionID=$aryData["strSessionID"]&lngFunctionCode=" . DEF_FUNCTION_E4 . "&lngEstimateNo=" . $objResult->lngestimateno . "&lngEstimateNoCondition=1' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></td>";
			}
			else
			{
				$partsData["delete"] = '<td bgcolor="#ffffff">&nbsp;</td>';
			}
		}



		// ���Ѹ����ֹ���Ǽ
		$lngEstimateNo = $objResult->lngestimateno;

		// �ǡ���Ϣ������Υ���������˼���
		$objTemplate->replace( $partsData );

		// �ѡ��ĥƥ�ץ졼������
		$baseData["tabledata"] .= $objTemplate->strTemplate;
		// �ƥ�ץ졼�Ȥ����Υƥ�ץ졼�Ⱦ��֤��᤹
		$objTemplate->strTemplate = $strTemplate;
	}
}





$objDB->freeResult( $lngResultID );
unset ( $aryDetailHtml );
unset ( $objTemplate );
unset ( $strTemplate );
unset ( $partsData );
unset ( $objResult );


//���å����ξ����hidden�ǻ���
$baseData["strSessionID"] = $aryData["strSessionID"];

/////////�ƥ��Ȥ�������
// POST���줿�ǡ�����Hidden�ˤ����ꤹ��
unset($ary_keys);
$ary_Keys = array_keys( $aryData );
while ( list ($strKeys, $strValues ) = each ( $ary_Keys ) )
{
/*
	if( $strValues == "ViewColumn")
	{
//		reset( $aryData["ViewColumn"] );
		for ( $i = 0; $i < count( $aryData["ViewColumn"] ); $i++ )
		{
			$aryHidden[] = "<input type='hidden' name='ViewColumn[]' value='" .$aryData["ViewColumn"][$i]. "'>";
		}
	}
	elseif( $strValues == "SearchColumn")
	{
//		reset( $aryData["SearchColumn"] );
		for ( $j = 0; $j < count( $aryData["SearchColumn"] ); $j++ )
		{
			$aryHidden[] = "<input type='hidden' name='SearchColumn[]' value='". $aryData["SearchColumn"][$j] ."'>";
		}
	}
*/
	if( $strValues == "strSort" || $strValues == "strSortOrder" )
	{
		//���⤷�ʤ�
	} 
	else
	{
		$aryHidden[] = "<input type='hidden' name='". $strValues."' value='".$aryData[$strValues]."'>";
	}
}

for ( $i = 0; $i < count( $aryViewColumn ); $i++ )
{
	$aryHidden[] = "<input type='hidden' name='ViewColumn[]' value='" . $aryViewColumn[$i]. "'>";
}

for ( $i = 0; $i < count( $arySearchColumn ); $i++ )
{
	$aryHidden[] = "<input type='hidden' name='SearchColumn[]' value='". $arySearchColumn[$i] ."'>";
}


$aryHidden[] = "<input type='hidden' name='strSort'>";
$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
$strHidden = implode ("\n", $aryHidden );

$baseData["strHidden"] = $strHidden;
/////////�ƥ��Ȥ����ޤ�




// �١����ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/result/base.tmpl" );

// �١����ƥ�ץ졼������
$objTemplate->replace( $baseData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();


return TRUE;
?>
