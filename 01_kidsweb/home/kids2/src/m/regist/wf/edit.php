<?
/** 
*	マスタ管理 ワークフロー順序マスタ データ入力画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 登録画面
// index.php -> strSessionID  -> edit.php
// index.php -> lngActionCode -> edit.php
//
// 確認画面へ
// edit.php -> strSessionID              -> confirm.php
// edit.php -> lngActionCode             -> confirm.php
// edit.php -> strWorkflowOrderName      -> confirm.php
// edit.php -> lngWorkflowOrderGroupCode -> confirm.php
// edit.php -> strOrderData              -> confirm.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]  = "null:numenglish(32,32)";
$aryCheck["lngActionCode"] = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_INSERT . ")";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// エラーがない場合、マスターオブジェクト生成、文字列チェック実行
if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable( "m_WorkflowOrder", "lngWorkflowOrderCode", "", "", $objDB );



// カラム数取得
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// 入力欄表示処理
//////////////////////////////////////////////////////////////////////////
// ワークフロー順序名
$aryParts["strWorkflowOrderName"] = fncHTMLSpecialChars( $aryData["strWorkflowOrderName"] );

// グループ名
$aryParts["strWorkflowOrderGroupCode"] = fncGetPulldown( "m_Group", "lngGroupCode", "strGroupDisplayCode || ':' || strGroupDisplayName", $aryData["lngWorkflowOrderGroupCode"], "", $objDB );

// 期限入力

// 順番データ選択元リスト
$aryParts["strOrderDataFrom"] = "";
if ( $aryData["lngWorkflowOrderGroupCode"] > -1 )
{
	$aryParts["strOrderDataFrom"] = fncGetPulldown( "m_User u, m_GroupRelation gr, m_AuthorityGroup ag", "u.lngUserCode", "u.strUserDisplayName || ':' || ag.strAuthorityGroupName", "", " WHERE gr.lngGroupCode = " . $aryData["lngWorkflowOrderGroupCode"] . " AND ag.lngAuthorityLevel >= 100 AND u.bytInvalidFlag = FALSE AND u.lngUserCode = gr.lngUserCode AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode", $objDB );
}



//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );



$aryParts["strSessionID"]    =& $aryData["strSessionID"];
$aryParts["lngLanguageCode"] =1;
$aryParts["strTableName"]    =& $objMaster->strTableName;
$aryParts["lngActionCode"]   = DEF_ACTION_INSERT;

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/wf/edit.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;


/*
?>
<!-- データバインド用オブジェクト -->
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
