<?
/** 
*	�ޥ������� ����ե�����ޥ��� �ǡ������ϲ���
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
// index.php -> strSessionID  -> edit.php
// index.php -> lngActionCode -> edit.php
//
// ��ǧ���̤�
// edit.php -> strSessionID              -> confirm.php
// edit.php -> lngActionCode             -> confirm.php
// edit.php -> strWorkflowOrderName      -> confirm.php
// edit.php -> lngWorkflowOrderGroupCode -> confirm.php
// edit.php -> strOrderData              -> confirm.php


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


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
$aryCheck["lngActionCode"] = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_INSERT . ")";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// ���顼���ʤ���硢�ޥ��������֥�������������ʸ��������å��¹�
if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
}

// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( "m_WorkflowOrder", "lngWorkflowOrderCode", "", "", $objDB );



// ����������
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// ������ɽ������
//////////////////////////////////////////////////////////////////////////
// ����ե����̾
$aryParts["strWorkflowOrderName"] = fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] );

// ���롼��̾
$aryParts["strWorkflowOrderGroupCode"] = fncGetPulldown( "m_Group", "lngGroupCode", "strGroupDisplayCode || ':' || strGroupDisplayName", $aryData["lngWorkflowOrderGroupCode"], "", $objDB );

// ��������

// ���֥ǡ������򸵥ꥹ��
$aryParts["strOrderDataFrom"] = "";
if ( $aryData["lngWorkflowOrderGroupCode"] > -1 )
{
	$aryParts["strOrderDataFrom"] = fncGetPulldown( "m_User u, m_GroupRelation gr, m_AuthorityGroup ag", "u.lngUserCode", "u.strUserDisplayName || ':' || ag.strAuthorityGroupName", "", " WHERE gr.lngGroupCode = " . $aryData["lngWorkflowOrderGroupCode"] . " AND ag.lngAuthorityLevel >= 100 AND u.bytInvalidFlag = FALSE AND u.lngUserCode = gr.lngUserCode AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode", $objDB );
}



//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );



$aryParts["strSessionID"]    =& $aryData["strSessionID"];
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]    =& $objMaster->strTableName;
$aryParts["lngActionCode"]   = DEF_ACTION_INSERT;

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/wf/edit.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;


/*
?>
<!-- �ǡ����Х�����ѥ��֥������� -->
<script type="text/javascript" language="javascript" src="/cmn/masterlib.js"></script>
<script type="text/javascript" language="javascript" src="functions.js"></script>
<OBJECT CLASSID="clsid:333C7BC4-460F-11D0-BC04-0080C7055A83" ID="objDataSourceSetting" WIDTH="0" HEIGHT="0"></OBJECT>
<Script for="objDataSourceSetting" event="ondatasetcomplete">
	subLoadMasterSetting(this.recordset,0);
</script>
<form action="confirm.php" method="GET">
<table border>
<tr>
  <th colspan="3">strWorkflowName</th>
</tr>
<tr>
  <td colspan="3"><input type="text" name="strWorkflowOrderName" value="<? echo fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] ); ?>"></th>
</tr>
<tr>
  <th colspan="3">lngWorkflowOrderGroupCode</th>
</tr>
<tr>
  <td colspan="3">
    <select name="lngWorkflowOrderGroupCode" onChange="
    subLoadMasterOption( 'cnWorkflowOrder', this, strOrderDataFrom, Array(this.value), objDataSourceSetting, 0 );
    strOrderDataTo.length = 0;
    strOrderData.value = '';
    ">
      <option value="0"></option>
      <? echo fncGetPulldown( "m_Group", "lngGroupCode", "strGroupDisplayCode || ':' || strGroupDisplayName", $aryData["lngWorkflowOrderGroupCode"], "", $objDB ); ?>
    </select>
  </td>
</tr>
<tr>
  <th>strOrderData</th>
  <th>lngLimitDays</th>
  <th>strOrderDataFrom</th>
</tr>
<tr>
  <td>
    <select name="strOrderDataTo" size="5">
    </select>
  </td>
  <th>
    <p>
      <input type="button" value="<-" onClick="
      strQuery = fncAddGroupUser(strOrderDataFrom, strOrderDataTo, strOrderData, lngLimitDays.value );
      if ( strQuery ){
        subLoadMasterOption( 'cnWorkflowOrder2', strOrderDataTo, strOrderDataFrom, Array(lngWorkflowOrderGroupCode.value, strOrderDataFrom.value, strQuery), objDataSourceSetting, 0 );
      }
      ">
    </p>
    <p><input type="text" name="lngLimitDays"></p>
  </th>
  <td>
    <select name="strOrderDataFrom" size="5">
      <? echo fncGetPulldown( "m_User u, m_GroupRelation gr, m_AuthorityGroup ag", "u.lngUserCode", "u.strUserDisplayName || ':' || ag.strAuthorityGroupName", "", " WHERE gr.lngGroupCode = " . $aryData["lngWorkflowOrderGroupCode"] . " AND ag.lngAuthorityLevel >= 100 AND u.bytInvalidFlag = FALSE AND u.lngUserCode = gr.lngUserCode AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode", $objDB ); ?>
    </select>
  </td>
</tr>
</table>
<input type="hidden" name="strOrderData" value="">
<input type="hidden" name="strSessionID" value="<? echo $aryData["strSessionID"]; ?>">
<input type="hidden" name="lngActionCode" value="<? echo DEF_ACTION_INSERT; ?>">
<input type="submit">
<input type="reset" onClick="strOrderData.value='';strOrderDataTo.length=0;strOrderDataFrom.length=0;">
</form>
<?
*/
$objDB->close();


return TRUE;
?>
