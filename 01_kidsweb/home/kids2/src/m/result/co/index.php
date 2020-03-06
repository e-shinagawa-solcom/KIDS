<?
/** 
*	マスタ管理 会社マスタ マスターテーブル結果一覧画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID          -> index.php
// index.php -> lngAttributeCode      -> index.php
// index.php -> strCompanyDisplayName -> index.php
//
// 登録画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngAttributeCode      -> edit.php
// index.php -> strCompanyDisplayName -> edit.php
//
// 修正画面
// index.php -> strSessionID          -> confirm.php
// index.php -> lngActionCode         -> confirm.php
// index.php -> lngAttributeCode      -> confirm.php
// index.php -> strCompanyDisplayName -> confirm.php
// index.php -> lngcompanycode        -> confirm.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;
$aryData["lngLanguageCode"] = 1;


// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryCheck["lngAttributeCode"]      = "number(0,2147483647)";
$aryCheck["strCompanyDisplayName"] = "length(1,100)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->strTableName = "m_Company";

// 検索クエリ生成
$strQuery = "SELECT DISTINCT ON ( com.lngCompanyCode ) * FROM m_Company com, m_AttributeRelation ar, m_Country con, m_Organization o, m_ClosedDay clo";

// 属性コード条件生成
if ( $aryData["lngAttributeCode"] != "" )
{
	$aryWhereString[] = " ar.lngAttributeCode = " . $aryData["lngAttributeCode"];
}

// 表示会社名条件生成
if ( $aryData["strCompanyDisplayName"] )
{
	$aryWhereString[] = " com.strCompanyDisplayName LIKE '%" . $aryData["strCompanyDisplayName"] . "%'";
}

// 属性テーブルとの結合
$aryWhereString[] = " com.lngCompanyCode = ar.lngCompanyCode";
$aryWhereString[] = " com.lngCountryCode = con.lngCountryCode";
$aryWhereString[] = " com.lngOrganizationCode = o.lngOrganizationCode";
$aryWhereString[] = " com.lngClosedDayCode = clo.lngClosedDayCode";

// 条件分を生成、クエリに追加
$strWhereString = join ( " AND", $aryWhereString );
$strQuery .= " WHERE " . $strWhereString;

// データの取得とオブジェクトへのセット
$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );


if ( $lngResultNum )
{
	///////////////////////////////////////////////////////////////////
	// テーブル生成
	///////////////////////////////////////////////////////////////////
	// 結果行表示
	$count = 0;

	// aryOrganizationFront 取得
	$aryOrganizationFront = Array ( "t" => "前", "f" => "後" );

	// bytCompanyDisplayFlag 取得
	$aryCompanyDisplayFlag = Array ( "t" => "表示", "f" => "非表示" );

	// lngAttributeCode (CODE+NAME)取得
	$strQuery = "SELECT * FROM m_Attribute a, m_AttributeRelation ar WHERE a.lngAttributeCode = ar.lngAttributeCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngResultID, $i );
			$aryAttributeCode[$objResult->lngcompanycode] .= $objResult->strattributename . " ";
		}
	}


	// レコード表示処理
	foreach ( $objMaster->aryData as $record )
	{
		$count++;
		$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";

		// カラム生成
		$aryParts["strResultHtml"] .= "		<th>$count</th>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[0]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["strcountryname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["strorganizationname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryOrganizationFront[$record[$objMaster->aryColumnName[3]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[4]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryCompanyDisplayFlag[$record[$objMaster->aryColumnName[5]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[6]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[7]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[8]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[9]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[10]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[11]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[12]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[13]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[14]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[15]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[16]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[17]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[18]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["strcloseddaycode"] . ":" . $record["lngclosedday"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryAttributeCode[$record[$objMaster->aryColumnName[0]]] . "</td>\n";


		// 修正ボタン生成
		$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"/m/regist/co/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . fncGetUrl( $aryData ) . "&lngcompanycode=" . $record[$objMaster->aryColumnName[0]] . "\" name=\"fix\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

		// 削除ボタン生成
		$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"/m/regist/co/confirm.php?lngActionCode=" . DEF_ACTION_DELETE . fncGetUrl( $aryData ) . "&lngcompanycode=" . $record[$objMaster->aryColumnName[0]] . "\" name=\"delete\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></td>\n";


		$aryParts["strResultHtml"] .= "	</tr>\n";

	}
}
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . ( count ( $objMaster->aryColumnName ) + 1 ) . ">結果無し。</th></tr>";
}

// カラム行HTML取得
// $aryParts["strColumnHtml"] = $objMaster->getColumnHtmlTable( 21 );
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>会社コード</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>国コード</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>組織コード</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>組織表記</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>会社名称</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>表示会社許可</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>表示会社コード</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>表示会社名称</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>省略名称</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>郵便番号 </td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>住所1 / 都道府県 </td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>住所2 / 市、区、郡 </td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>住所3 / 町、番地 </td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>住所4 / ビル等、建物名</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>電話番号1</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>電話番号2</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>ファックス番号1</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>ファックス番号2</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>識別コード</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>締め日コード</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"Column0\" nowrap>会社属性</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"FixColumn\" nowrap>修正</td>\n";
$aryParts["strColumnHtml"] .= "<td id=\"DeleteColumn\" nowrap>削除</td>\n";
$objDB->close();



// index.php -> lngAttributeCode      -> index.php
// index.php -> strCompanyDisplayName -> index.php
$aryParts["HIDDEN"]           = "<input type=hidden name=strSessionID value=" .$aryData["strSessionID"] .">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngAttributeCode value=" .$aryData["lngAttributeCode"].">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=strCompanyDisplayName value=" .$aryData["strCompanyDisplayName"]. ">\n";
$aryParts["lngLanguageCode"]  =1;
$aryParts["strTableName"]     =& $objMaster->strTableName;
$aryParts["lngColumnNum"]     = 20;
$aryParts["strEditURL"]       = "/m/regist/co/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];
//$aryParts["strEditURL"]       = "/m/regist/co/edit.php?lngActionCode=" . DEF_ACTION_INSERT . fncGetUrl( $aryData );


// HTML出力
//echo getArrayTable( $aryData, "TABLE" );exit;
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
