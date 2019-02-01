<?
/** 
*	�ޥ������� ���̥ޥ��� �ǡ������ϲ���
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ��Ͽ����
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
//
// ��������
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
// index.php -> lngKeyCode            -> edit.php
// index.php -> (lngStockSubjectCode) -> edit.php
//
// ��ǧ���̤�
// edit.php -> strSessionID          -> confirm.php
// edit.php -> lngActionCode         -> confirm.php
// edit.php -> strMasterTableName    -> confirm.php
// edit.php -> strKeyName            -> confirm.php
// edit.php -> lngKeyCode            -> confirm.php
// edit.php -> (lngStockSubjectCode) -> confirm.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;

// �������ʤξ��˻��Ѥ���lngStockSubjectCode������
list ( $aryData["lngstocksubjectcode"], $i ) = mb_split ( ":", $aryData["lngstocksubjectcode"] );


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
$aryCheck["strKeyName"]         = "ascii(1,32)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], $aryData["strKeyName"], $aryData[$aryData["strKeyName"]], Array ( "lngstocksubjectcode" => $aryData["lngstocksubjectcode"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData[$aryData["strKeyName"]], $aryData["lngstocksubjectcode"] );



// ����������
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// ���������ɤ�ɽ������
//////////////////////////////////////////////////////////////////////////
// �����ξ�硢���������ɤΥ��ꥢ�����
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// �������ܥޥ������������ʥޥ�������ޥ������ȿ��ޥ����ʳ���
	// ���ꥢ��ˤƿ���������ȯ��
	if ( $objMaster->strTableName != "m_StockSubject" && $objMaster->strTableName != "m_StockItem" && $objMaster->strTableName != "m_Country" && $objMaster->strTableName != "m_Organization" )
	{
	//	$seq = fncIsSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );

		//$seq = $objMaster->lngRecordRow + 1;
		// ���󥯥���ȸ�Υ������󥹤�99���ä���礵���1­�������������
		//if ( $seq == 99 )
		//{
			//$seq = fncGetSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
		//	$seq++;
		//}

		// ��äƤ����ݤν���
		if ( $aryData[$aryData["strKeyName"]] )
		{
			$seq = $aryData[$aryData["strKeyName"]];
		}
		else
		{
			$seq = $objMaster->lngRecordRow;
		}

		$aryParts["MASTER"][0] = "<span class=\"InputSegs\"><input id=\"Input0\" type=\"text\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100 disabled></span>\n";

		$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $seq . "\">\n";

	}

	// �������ܥޥ������������ʥޥ�������ޥ�����ľ�����Ϥˤƿ���������ȯ��
	else
	{
		$aryParts["MASTER"][0] = "<span class=\"InputSegs\"><input id=\"Input0\" type=\"text\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $aryData[$objMaster->aryColumnName[0]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100></span>\n";
	}
}

// ��Ͽ�ʳ��ξ�硢�������Ϲ��ܤ˥��⡼����ݤ���ɽ������
else
{
	// ����������ɽ��
	$aryParts["MASTER"][0] = "<span class=\"InputSegs\"><input id=\"Input0\" type=\"text\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100 disabled></span>\n";
	$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";
}

$aryData["COLUMN"] = "<span id=\"Column0\" class=\"ColumnSegs\"></span>\n";


// �Ĥ�Υ����ɽ��
for ( $i = 1; $i < $lngColumnNum; $i++ )
{
	// ������Ͽ�ξ��
	if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{
		$aryParts["MASTER"][$i] = "<span class=\"InputSegs\"><input id=\"Input$i\" type=\"text\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . fncHTMLSpecialChars( $aryData[$objMaster->aryColumnName[$i]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100></span>\n";
	}

	// �����ξ��
	else
	{
		$aryParts["MASTER"][$i] = "<span class=\"InputSegs\"><input id=\"Input$i\" type=\"text\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . fncHTMLSpecialChars( $objMaster->aryData[0][$objMaster->aryColumnName[$i]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=100></span>\n";
	}

	$aryData["COLUMN"] .= "<span id=\"Column$i\" class=\"ColumnSegs\"></span>\n";
}

/////////////////////////////////////////////////////////////////////////
// �������ܥޥ������ޤ��ϻ������ʥޥ����ξ�硢
// ������2�ĤΤ�����ü������Ԥ�
/////////////////////////////////////////////////////////////////////////
if ( $objMaster->strTableName == "m_StockSubject" || $objMaster->strTableName == "m_StockItem" )
{
	// �ץ�������˥塼����
	list ( $aryParts["MASTER"][1], $hidden ) = fncSpecialTableManage( $aryData["lngActionCode"], $objMaster, $aryData, $objDB );
	$aryData["HIDDEN"] .= $hidden;

	// �������ʥޥ������ä���硢ID�򤺤餹������Ԥ�
	if ( $objMaster->strTableName == "m_StockItem" )
	{
		$aryParts["MASTER"][2] = preg_replace ( "/Input2/", "Input3", $aryParts["MASTER"][2] );
		$aryData["COLUMN"] .= "<span id=\"Column3\" class=\"ColumnSegs\"></span>\n";
	}
}

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];
}

$objDB->close();


$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryData["strTableName"]    = $objMaster->strTableName;

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>

<?

/////////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	������2�ĤΥޥ������Τ�����ü�����ؿ�
*
*	@param  Long   $lngActionCode ����������
*	@param  Object $objMaster     �ޥ������ơ��֥륪�֥�������
*	@param  Array  $aryData       FORM�ǡ�������
*	@param  Object $objDB         DB���֥�������
*	@return Array  $partsMaster   �ƥ�ץ졼�ȥǡ���
*	@access public
*/
// -----------------------------------------------------------------
function fncSpecialTableManage( $lngActionCode, $objMaster, $aryData, $objDB )
{
	// ��˥塼�����
	$strParts = "";
	$lngKeyNumber = 1;

	////////////////////////////////////////////////////////////////
	// ��1�ץ������(�������ʥޥ����Τ�)
	////////////////////////////////////////////////////////////////
	// �������ʤξ�硢������ʬSELECT ��˥塼������
	if ( $objMaster->strTableName == "m_StockItem" )
	{
		// ��Ͽ�ξ�硢SELECT��˥塼��������
		if ( $lngActionCode == DEF_ACTION_INSERT )
		{
			$strParts = "<span class=\"InputSegs\"><select id=Input1 onChange=\"subLoadMasterOption( 'cnStockSubjectCode', this, document.forms[0]." . $objMaster->aryColumnName[1] . ", Array(this.value), objDataSourceSetting, 0 );\">\n";
		}

		// �����ξ�硢disabled ��SELECT��˥塼��������
		elseif ( $lngActionCode == DEF_ACTION_UPDATE )
		{
			$strParts = "<span class=\"InputSegs\"><select id=Input1 disabled>\n";
		}

		// �������ܥޥ��������ʬ�����ɤ����(ľ�ܥե�����ǻ����ʤ�����հ���)
		if ( $aryData[$objMaster->aryColumnName[2]] > -1 )
		{
			$lngStockClassCode = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "lngStockClassCode", $aryData[$objMaster->aryColumnName[1]], "", $objDB );
		}

		// ������ʬ�ޥ�����SELECT ��˥塼������
		$strParts .= "<option value=\"\"></option>\n";
		$strParts .= fncGetPulldown( "m_StockClass", "lngStockClassCode", "lngStockClassCode || ':' || strStockClassName", $lngStockClassCode, "", $objDB );
		$strParts .= "</select></span>";

		$lngKeyNumber++;
	}

	////////////////////////////////////////////////////////////////
	// ��2�ץ������
	////////////////////////////////////////////////////////////////
	// ��Ͽ�ξ�硢SELECT��˥塼��������
	if ( $lngActionCode == DEF_ACTION_INSERT )
	{
		$strParts .= "<span class=\"InputSegs\"><select name=\"" . $objMaster->aryColumnName[1] . "\" id=Input$lngKeyNumber>\n";
	}

	// �����ξ�硢HIDDEN �� disabled ��SELECT��˥塼��������
	elseif ( $lngActionCode == DEF_ACTION_UPDATE )
	{
		$aryParts["HIDDEN"] = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[1]] . "\">\n";

		$strParts .= "<span class=\"InputSegs\"><select id=Input$lngKeyNumber disabled>\n";
	}

	// �������ܤξ��Ρ�SELECT ��˥塼
	if ( $objMaster->strTableName == "m_StockSubject" )
	{
		$strParts .= fncGetPulldown( "m_StockClass", "lngStockClassCode", "lngStockClassCode || ':' || strStockClassName", $aryData[$objMaster->aryColumnName[1]], "", $objDB );
	}

	// �������ʤξ��Ρ�SELECT ��˥塼
	else
	{
			if ( $lngStockClassCode > -1 )
			{
				$lngStockClassCode = " WHERE lngStockClassCode = " . $lngStockClassCode;
			}
			$strParts .= fncGetPulldown( "m_StockSubject", "lngStockSubjectCode", "lngStockSubjectCode || ':' || strStockSubjectName", $aryData[$objMaster->aryColumnName[1]], $lngStockClassCode, $objDB );
	}

	// SELECT ��˥塼���Ĥ�
	$strParts .= "</select></span>\n";

	return array ( $strParts, $aryParts["HIDDEN"] );
}

?>
