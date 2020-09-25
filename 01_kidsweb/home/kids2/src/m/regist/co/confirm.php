<?
/** 
*	マスタ管理 会社マスタ 確認画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 登録、修正
// edit.php -> strSessionID           -> confirm.php
// edit.php -> lngActionCode          -> confirm.php
// edit.php -> lngcompanycode         -> confirm.php
// edit.php -> lngcountrycode         -> confirm.php
// edit.php -> lngorganizationcode    -> confirm.php
// edit.php -> bytorganizationfront   -> confirm.php
// edit.php -> strcompanyname         -> confirm.php
// edit.php -> bytcompanydisplayflag  -> confirm.php
// edit.php -> strcompanydisplaycode  -> confirm.php
// edit.php -> strcompanydisplayname  -> confirm.php
// edit.php -> strpostalcode          -> confirm.php
// edit.php -> straddress1            -> confirm.php
// edit.php -> straddress2            -> confirm.php
// edit.php -> straddress3            -> confirm.php
// edit.php -> straddress4            -> confirm.php
// edit.php -> strtel1                -> confirm.php
// edit.php -> strtel2                -> confirm.php
// edit.php -> strfax1                -> confirm.php
// edit.php -> strfax2                -> confirm.php
// edit.php -> strdistinctcode        -> confirm.php
// edit.php -> lngcloseddaycode       -> confirm.php
// edit.php -> aryattributecode       -> confirm.php
//
// 削除
// index.php -> strSessionID          -> confirm.php
// index.php -> lngActionCode         -> confirm.php
// index.php -> lngcompanycode        -> confirm.php
//
// 登録、修正実行へ
// confirm.php -> strSessionID           -> action.php
// confirm.php -> lngActionCode          -> action.php
// confirm.php -> lngcompanycode         -> action.php
// confirm.php -> lngcountrycode         -> action.php
// confirm.php -> lngorganizationcode    -> action.php
// confirm.php -> bytorganizationfront   -> action.php
// confirm.php -> strcompanyname         -> action.php
// confirm.php -> bytcompanydisplayflag  -> action.php
// confirm.php -> strcompanydisplaycode  -> action.php
// confirm.php -> strcompanydisplayname  -> action.php
// confirm.php -> strpostalcode          -> action.php
// confirm.php -> straddress1            -> action.php
// confirm.php -> straddress2            -> action.php
// confirm.php -> straddress3            -> action.php
// confirm.php -> straddress4            -> action.php
// confirm.php -> strtel1                -> action.php
// confirm.php -> strtel2                -> action.php
// confirm.php -> strfax1                -> action.php
// confirm.php -> strfax2                -> action.php
// confirm.php -> strdistinctcode        -> action.php
// confirm.php -> lngcloseddaycode       -> action.php
// confirm.php -> strattributecode       -> action.php
//
// 削除実行へ
// confirm.php -> strSessionID          -> action.php
// confirm.php -> lngActionCode         -> action.php
// confirm.php -> lngcompanycode        -> action.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GETデータ取得
$aryData = $_GET;
if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	// 複数選択セレクトボックスから属性コードを取得する
preg_match_all ( "/(aryattributecode)=(\d+)/", $_SERVER["QUERY_STRING"], $aryAttribute );
	$lngLength = count ( $aryAttribute[2] );

	for ( $i = 0; $i < $lngLength; $i++ )
	{
		// 属性数値チェック
		if ( fncCheckString( $aryAttribute[2][$i], "number(0,2147483647)" ) == "" )
		{
			$aryAttributeCode[] = $aryAttribute[2][$i];

			// 本社または顧客属性だった場合、それぞれのフラグ真
			if ( $aryAttribute[2][$i] == DEF_ATTRIBUTE_HEADOFFICE )
			{
				$bytHeadOfficeFlag = TRUE;
			}
			elseif ( $aryAttribute[2][$i] == DEF_ATTRIBUTE_CLIENT )
			{
				$bytClientFlag = TRUE;
			}
		}
	}
}


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]  = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
$aryCheck["lngcompanycode"] = "null:number(0,2147483647)";

if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	$aryCheck["lngcompanycode"]        = "null:number(0,2147483647)";
	$aryCheck["lngcountrycode"]        = "null:number(0,2147483647)";
	$aryCheck["lngorganizationcode"]   = "null:number(0,2147483647)";
	$aryCheck["bytorganizationfront"]  = "english(1,1)";
	$aryCheck["strcompanyname"]        = "null:length(1,100)";
	$aryCheck["bytcompanydisplayflag"] = "english(1,1)";
	$aryCheck["strcompanydisplaycode"] = "null:numenglish(0,10)";
	$aryCheck["strcompanydisplayname"] = "null:length(1,100)";
	$aryCheck["strshortname"] = "length(1,100)";
	$aryCheck["strpostalcode"]         = "ascii(0,20)";
	$aryCheck["straddress1"]           = "length(1,100)";
	$aryCheck["straddress2"]           = "length(1,100)";
	$aryCheck["straddress3"]           = "length(1,100)";
	$aryCheck["straddress4"]           = "length(1,100)";
	$aryCheck["strtel1"]               = "length(1,100)";
	$aryCheck["strtel2"]               = "length(1,100)";
	$aryCheck["strfax1"]               = "length(1,100)";
	$aryCheck["strfax2"]               = "length(1,100)";
	$aryCheck["lngcloseddaycode"]      = "null:number(,2147483647)";
	$aryCheck["aryattributecode"]      = "null";
	$aryCheck["strdistinctcode"]       = "numenglish(0,100)";

	// 顧客属性がついている場合、識別コード必須に変更
	//if ( $bytClientFlag )
	//{
	//	$aryCheck["strdistinctcode"] = "null:numenglish(0,100)";
	//}
}

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );

//echo getArrayTable( $aryCheckResult, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );

// 本社と顧客双方の属性を指定されていた場合、文字列チェックエラー真
if ( $bytHeadOfficeFlag && $bytClientFlag )
{
	$aryCheckResult["aryAttributeCode_Error"] = TRUE;
}

// 属性がない場合、エラー
if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE && $lngLength < 1 )
{
	$aryCheckResult["aryattributecode"] = TRUE;
}


//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) エラーがない 場合、
// 新規登録、修正チェック実行
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( "", $aryCheckResult ) )
{
	// 会社コード重複チェック
	$strQuery = "SELECT * FROM m_Company " .
                "WHERE lngCompanyCode = " . $aryData["lngcompanycode"];

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 新規登録 かつ 結果件数が0以上
	// または
	// 修正 かつ 結果件数が1以外 の場合、エラー
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult["lngcompanycode_Error"] = 1;
		$objDB->freeResult( $lngResultID );
	}

	// 属性重複チェック
	$count = count ( $aryAttributeCode );
	for ( $i = 0; $i < $count; $i++ )
	{
		for ( $j = $i + 1; $j < $count; $j++ )
		{
			if ( $aryAttributeCode[$i] == $aryAttributeCode[$j] )
			{
				fncOutputError ( 9056, DEF_WARNING, "属性コードが重複しています。", TRUE, "", $objDB );
			}
		}
	}
}

// 削除 かつ エラーがない 場合、
// 削除チェック実行
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( "", $aryCheckResult ) )
{
// チェック対象テーブル名配列を定義
	// グループマスタ、ユーザーマスタ チェッククエリ
	$aryTableName = Array ( "m_Group", "m_User" );

	// チェッククエリ生成
	for ( $i = 0; $i < count ( $aryTableName ); $i++ )
	{
		$aryQuery[] = "SELECT lngCompanyCode FROM " . $aryTableName[$i] . " WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
	}
	// 発注マスタ チェッククエリ生成
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Order WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"] . " OR lngDeliveryPlaceCode = " . $aryData["lngcompanycode"];

	// 製品マスタ チェッククエリ生成
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Product WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"] . " OR lngFactoryCode = " . $aryData["lngcompanycode"] . " OR lngAssemblyFactoryCode = " . $aryData["lngcompanycode"] . " OR lngAssemblyFactoryCode = " . $aryData["lngcompanycode"] . " OR lngDeliveryPlaceCode = " . $aryData["lngcompanycode"];

	// 受注マスタ チェッククエリ生成
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Receive WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"];

	// 売上マスタ チェッククエリ生成
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Sales WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"];

	// 仕入マスタ チェッククエリ生成
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Stock WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"] . " OR lngDeliveryPlaceCode = " . $aryData["lngcompanycode"];

	$strQuery = join ( " UNION ", $aryQuery );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 結果が1件でもあった場合、削除不可能とし、エラー出力
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
	}

	// 削除対象表示のためのデータを取得
	$strQuery = "SELECT * FROM m_Company WHERE lngCompanyCode = " . $aryData["lngcompanycode"];

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryKeys = array_keys ( $objMaster->aryData[0] );

	foreach ( $aryKeys as $strKey )
	{
		$aryData[$strKey] = $objMaster->aryData[0][$strKey];
	}

	$strQuery = "SELECT lngAttributeCode FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$lngLength = count ( $objMaster->aryData );
	for ( $i = 0; $i < $lngLength; $i++ )
	{
		$aryAttributeCode[] = $objMaster->aryData[$i]["lngattributecode"];
	}

	$objMaster = new clsMaster();
	$aryKeys = Array ();
}



// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


// 会社表示フラグ設定
if ( $aryData["bytcompanydisplayflag"] == "t" )
{
	$aryData["bytcompanydisplayflag"] = "TRUE";
}
else
{
	$aryData["bytcompanydisplayflag"] = "FALSE";
}

// 組織表記フラグ設定
if ( $aryData["bytorganizationfront"] == "t" )
{
	$aryData["bytorganizationfront"] = "TRUE";
}
else
{
	$aryData["bytorganizationfront"] = "FALSE";
}

$aryCompanyDisplayFlag = Array ( "TRUE" => "表示", "FALSE" => "非表示" );
$aryOrganizationFront  = Array ( "TRUE" => "前", "FALSE" => "後" );


//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strSessionID"]    = $aryData["strSessionID"];


// lngCompanyCode の(CODE+NAME)取得
$aryCompanyCode = fncGetMasterValue( "m_Company", "lngCompanyCode", "strCompanyName", "Array", "", $objDB );
// bytGroupDisplayFlag の(CODE+NAME)取得
$aryGroupDisplayFlag = Array ( "TRUE" => "表示", "FALSE" => "非表示" );

// 会社コード
if ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	$strOutputCompanyCode =& $aryData["lngcompanycode"];
}
$aryParts["MASTER"] .= "				<tr><td id=\"Column0\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $strOutputCompanyCode . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcompanycode\" value=\"" . $aryData["lngcompanycode"] . "\">\n";

// 国コード
$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncGetMasterValue( "m_Country", "lngCountryCode", "strCountryName", $aryData["lngcountrycode"], "", $objDB ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcountrycode\" value=\"" . $aryData["lngcountrycode"] . "\">\n";

// 組織コード
$aryParts["MASTER"] .= "				<tr><td id=\"Column2\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncGetMasterValue( "m_Organization", "lngOrganizationCode", "strOrganizationName", $aryData["lngorganizationcode"], "", $objDB ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngorganizationcode\" value=\"" . $aryData["lngorganizationcode"] . "\">\n";

// 組織表記
$aryParts["MASTER"] .= "				<tr><td id=\"Column3\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryOrganizationFront[$aryData["bytorganizationfront"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"bytorganizationfront\" value=\"" . $aryData["bytorganizationfront"] . "\">\n";

// 会社名称
$aryParts["MASTER"] .= "				<tr><td id=\"Column4\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars( $aryData["strcompanyname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strcompanyname\" value=\"" . fncHTMLSpecialChars ( $aryData["strcompanyname"] ) . "\">\n";

// 表示会社フラグ
$aryParts["MASTER"] .= "				<tr><td id=\"Column5\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryCompanyDisplayFlag[$aryData["bytcompanydisplayflag"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"bytcompanydisplayflag\" value=\"" . $aryData["bytcompanydisplayflag"] . "\">\n";

// 表示会社コード
$aryParts["MASTER"] .= "				<tr><td id=\"Column6\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strcompanydisplaycode"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strcompanydisplaycode\" value=\"" . fncHTMLSpecialChars ( $aryData["strcompanydisplaycode"] ) . "\">\n";

// 表示会社名称
$aryParts["MASTER"] .= "				<tr><td id=\"Column7\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strcompanydisplayname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strcompanydisplayname\" value=\"" . fncHTMLSpecialChars ( $aryData["strcompanydisplayname"] ) . "\">\n";

// 省略名称
$aryParts["MASTER"] .= "				<tr><td id=\"Column8\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strshortname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strshortname\" value=\"" . fncHTMLSpecialChars ( $aryData["strshortname"] ) . "\">\n";

// 郵便番号
$aryParts["MASTER"] .= "				<tr><td id=\"Column9\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryData["strpostalcode"] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strpostalcode\" value=\"" . $aryData["strpostalcode"] . "\">\n";

// 住所1 / 都道府県
$aryParts["MASTER"] .= "				<tr><td id=\"Column10\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress1"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress1\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress1"] ) . "\">\n";

// 住所2 / 市、区、郡
$aryParts["MASTER"] .= "				<tr><td id=\"Column11\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress2"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress2\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress2"] ) . "\">\n";

// 住所3 / 町、番地
$aryParts["MASTER"] .= "				<tr><td id=\"Column12\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress3"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress3\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress3"] ) . "\">\n";

// 住所4 / ビル等、建物名
$aryParts["MASTER"] .= "				<tr><td id=\"Column13\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress4"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress4\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress4"] ) . "\">\n";

// 電話番号1
$aryParts["MASTER"] .= "				<tr><td id=\"Column14\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strtel1"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strtel1\" value=\"" . fncHTMLSpecialChars ( $aryData["strtel1"] ) . "\">\n";

// 電話番号2
$aryParts["MASTER"] .= "				<tr><td id=\"Column15\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strtel2"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strtel2\" value=\"" . fncHTMLSpecialChars ( $aryData["strtel2"] ) . "\">\n";

// ファックス番号1
$aryParts["MASTER"] .= "				<tr><td id=\"Column16\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strfax1"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strfax1\" value=\"" . fncHTMLSpecialChars ( $aryData["strfax1"] ) . "\">\n";

// ファックス番号2
$aryParts["MASTER"] .= "				<tr><td id=\"Column17\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strfax2"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strfax2\" value=\"" . fncHTMLSpecialChars ( $aryData["strfax2"] ) . "\">\n";

// 識別コード
$aryParts["MASTER"] .= "				<tr><td id=\"Column18\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strdistinctcode"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strdistinctcode\" value=\"" . fncHTMLSpecialChars ( $aryData["strdistinctcode"] ) . "\">\n";

// 締め日コード
$aryParts["MASTER"] .= "				<tr><td id=\"Column19\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncGetMasterValue( "m_ClosedDay", "lngClosedDayCode", "strClosedDayCode || ':' || lngClosedDay", $aryData["lngcloseddaycode"], "", $objDB ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcloseddaycode\" value=\"" . $aryData["lngcloseddaycode"] . "\">\n";

// 属性
// 属性設定がある場合、属性プルダウンメニューの設定と属性データ生成を行う
if ( $lngLength > 0 )
{
	// 属性プルダウンメニューの設定(やり直し処理時に必要)
	$strQueryWhere = "WHERE lngAttributeCode = " . join ( " OR lngAttributeCode = ", $aryAttributeCode );

	// 属性データ生成(登録処理時に必要)
	$aryData["strattributecode"] = join ( ":", $aryAttributeCode );
}
list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT strAttributeName FROM m_Attribute " . $strQueryWhere, $objDB );
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryAttributeName[] = $objResult->strattributename;
}
$aryParts["MASTER"] .= "				<tr><td id=\"Column20\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . join ( " / ", $aryAttributeName ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strattributecode\" value=\"" . $aryData["strattributecode"] . "\">\n";



if ( $bytErrorFlag )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">";
	echo "<form action=\"/m/regist/co/edit.php\" method=\"POST\">";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=\"javascript\">document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/co/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


