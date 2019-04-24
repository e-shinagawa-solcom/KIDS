<?
/** 
*	マスタ管理 会社マスタ データ入力画面
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
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngAttributeCode      -> edit.php
// index.php -> strCompanyDisplayName -> edit.php
//
// 修正画面
// index.php -> strSessionID   -> edit.php
// index.php -> lngActionCode  -> edit.php
// index.php -> lngcompanycode -> edit.php
//
// 確認画面へ
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



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// データ取得
if ( $_GET )
{
	$aryData = $_GET;
}
else
{
	$aryData = $_POST;
}


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
if ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	$aryCheck["lngcompanycode"]  = "null:number(0,2147483647)";
}

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
$objMaster->setMasterTable( "m_Company", "lngcompanycode", $aryData["lngcompanycode"], "", $objDB );
$objMaster->setAryMasterInfo( $aryData["lngcompanycode"], "" );

// カラム数取得
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// 入力欄表示処理
//////////////////////////////////////////////////////////////////////////
// 新規の場合、キー入力項目生成
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// データ初期化
	$objMaster->aryData[0] = Array ();

	//$seq = fncIsSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
	if ( !$aryData[$objMaster->aryColumnName[0]] )
	{
		$aryData[$objMaster->aryColumnName[0]] = $objMaster->lngRecordRow;
	}

	// インクリメント後のシーケンスが9999だった場合さらに1足した数字を取得
	//if ( $seq == 9999 )
	//{
		//$seq = fncGetSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
	//	$seq++;
	//}

	// 会社コードのセット
	$objMaster->aryData[0][$objMaster->aryColumnName[0]] = $aryData[$objMaster->aryColumnName[0]];

	// 国コードのセット
	$objMaster->aryData[0][$objMaster->aryColumnName[1]] =& $aryData[$objMaster->aryColumnName[1]];

	// 組織コード
	$objMaster->aryData[0][$objMaster->aryColumnName[2]] =& $aryData[$objMaster->aryColumnName[2]];

	// 組織表記コード
	$objMaster->aryData[0][$objMaster->aryColumnName[3]] =& $aryData[$objMaster->aryColumnName[3]];

	// 会社名称
	$objMaster->aryData[0][$objMaster->aryColumnName[4]] =& $aryData[$objMaster->aryColumnName[4]];

	// 表示会社フラグ
	$objMaster->aryData[0][$objMaster->aryColumnName[5]] =& $aryData[$objMaster->aryColumnName[5]];

	// 表示会社コード
	$objMaster->aryData[0][$objMaster->aryColumnName[6]] =& $aryData[$objMaster->aryColumnName[6]];

	// 表示会社名称
	$objMaster->aryData[0][$objMaster->aryColumnName[7]] =& $aryData[$objMaster->aryColumnName[7]];

	// 省略名称
	$objMaster->aryData[0][$objMaster->aryColumnName[8]] =& $aryData[$objMaster->aryColumnName[8]];

	// 郵便番号
	$objMaster->aryData[0][$objMaster->aryColumnName[9]] =& $aryData[$objMaster->aryColumnName[9]];

	// 都道府県
	$objMaster->aryData[0][$objMaster->aryColumnName[10]] =& $aryData[$objMaster->aryColumnName[10]];

	// 市区郡
	$objMaster->aryData[0][$objMaster->aryColumnName[11]] =& $aryData[$objMaster->aryColumnName[11]];

	// 町・番地
	$objMaster->aryData[0][$objMaster->aryColumnName[12]] =& $aryData[$objMaster->aryColumnName[12]];

	// ビル等、建物名
	$objMaster->aryData[0][$objMaster->aryColumnName[13]] =& $aryData[$objMaster->aryColumnName[13]];

	// 電話番号1
	$objMaster->aryData[0][$objMaster->aryColumnName[14]] =& $aryData[$objMaster->aryColumnName[14]];

	// 電話番号2
	$objMaster->aryData[0][$objMaster->aryColumnName[15]] =& $aryData[$objMaster->aryColumnName[15]];

	// FAX番号1
	$objMaster->aryData[0][$objMaster->aryColumnName[16]] =& $aryData[$objMaster->aryColumnName[16]];

	// FAX番号2
	$objMaster->aryData[0][$objMaster->aryColumnName[17]] =& $aryData[$objMaster->aryColumnName[17]];

	// 識別番号
	$objMaster->aryData[0][$objMaster->aryColumnName[18]] =& $aryData[$objMaster->aryColumnName[18]];

	// 締め日コード
	$objMaster->aryData[0][$objMaster->aryColumnName[19]] =& $aryData[$objMaster->aryColumnName[19]];

	// 属性コード
	$aryAttributeCode = explode ( ":", $aryData["strattributecode"] );
}

// HTML特殊文字列の変換
$objMaster->aryData[0] = fncToHTMLString( $objMaster->aryData[0] );


// 会社コード
if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$strOutputCompanyCode = $objMaster->aryData[0][$objMaster->aryColumnName[0]];
}
$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><input type=\"text\" id=\"Input0\" value=\"" . $strOutputCompanyCode . "\" maxlength=\"10\" disabled></span>\n";

$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";

// 国
$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" name=\"" . $objMaster->aryColumnName[1] . "\">\n";
$aryParts["MASTER"][1] .= fncGetPulldown( "m_Country", "lngCountryCode", "strCountryName",  $objMaster->aryData[0][$objMaster->aryColumnName[1]], "", $objDB );
$aryParts["MASTER"][1] .= "</select></span>\n";

// 組織
$aryParts["MASTER"][2]  = "<span class=\"InputSegs\"><select id=\"Input2\" name=\"" . $objMaster->aryColumnName[2] . "\">\n";
$aryParts["MASTER"][2] .= fncGetPulldown( "m_Organization", "lngOrganizationCode", "strOrganizationName",  $objMaster->aryData[0][$objMaster->aryColumnName[2]], "", $objDB );
$aryParts["MASTER"][2] .= "</select></span>\n";

// 組織表記フラグ
$strChecked = "";
if ( $objMaster->aryData[0][$objMaster->aryColumnName[3]] == "t" )
{
	$strChecked = " checked";
}
$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"checkbox\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"t\"$strChecked></span>\n";

// 会社名称
$aryParts["MASTER"][4] = "<span class=\"InputSegs\"><input id=\"Input4\" type=\"text\" name=\"" . $objMaster->aryColumnName[4] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[4]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";


// 表示会社フラグ
$strChecked = "";
if ( $objMaster->aryData[0][$objMaster->aryColumnName[5]] == "t" || $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	$strChecked = " checked";
}
$aryParts["MASTER"][5] = "<span class=\"InputSegs\"><input id=\"Input5\" type=\"checkbox\" name=\"" . $objMaster->aryColumnName[5] . "\" value=\"t\"$strChecked></span>\n";

// 表示会社コード
$aryParts["MASTER"][6] = "<span class=\"InputSegs\"><input id=\"Input6\" type=\"text\" name=\"" . $objMaster->aryColumnName[6] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[6]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

// 表示会社名称
$aryParts["MASTER"][7] = "<span class=\"InputSegs\"><input id=\"Input7\" type=\"text\" name=\"" . $objMaster->aryColumnName[7] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[7]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 省略名称
$aryParts["MASTER"][8] = "<span class=\"InputSegs\"><input id=\"Input8\" type=\"text\" name=\"" . $objMaster->aryColumnName[8] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[8]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 郵便番号
$aryParts["MASTER"][9] = "<span class=\"InputSegs\"><input id=\"Input9\" type=\"text\" name=\"" . $objMaster->aryColumnName[9] . "\" value=\"" . trim ( $objMaster->aryData[0][$objMaster->aryColumnName[9]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"20\"></span>\n";

// 都道府県
$aryParts["MASTER"][10] = "<span class=\"InputSegs\"><input id=\"Input10\" type=\"text\" name=\"" . $objMaster->aryColumnName[10] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[10]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 市、区、郡
$aryParts["MASTER"][11] = "<span class=\"InputSegs\"><input id=\"Input11\" type=\"text\" name=\"" . $objMaster->aryColumnName[11] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[11]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 町、番地
$aryParts["MASTER"][12] = "<span class=\"InputSegs\"><input id=\"Input12\" type=\"text\" name=\"" . $objMaster->aryColumnName[12] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[12]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ビル等、建物名
$aryParts["MASTER"][13] = "<span class=\"InputSegs\"><input id=\"Input13\" type=\"text\" name=\"" . $objMaster->aryColumnName[13] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[13]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 電話番号1
$aryParts["MASTER"][14] = "<span class=\"InputSegs\"><input id=\"Input14\" type=\"text\" name=\"" . $objMaster->aryColumnName[14] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[14]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 電話番号2
$aryParts["MASTER"][15] = "<span class=\"InputSegs\"><input id=\"Input15\" type=\"text\" name=\"" . $objMaster->aryColumnName[15] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[15]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ファックス番号1
$aryParts["MASTER"][16] = "<span class=\"InputSegs\"><input id=\"Input16\" type=\"text\" name=\"" . $objMaster->aryColumnName[16] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[16]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ファックス番号2
$aryParts["MASTER"][17] = "<span class=\"InputSegs\"><input id=\"Input17\" type=\"text\" name=\"" . $objMaster->aryColumnName[17] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[17]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 識別コード
$aryParts["MASTER"][18] = "<span class=\"InputSegs\"><input id=\"Input18\" type=\"text\" name=\"" . $objMaster->aryColumnName[18] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[18]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 締め日
$aryParts["MASTER"][19]  = "<span class=\"InputSegs\"><select id=\"Input19\" name=\"" . $objMaster->aryColumnName[19] . "\">\n";
$aryParts["MASTER"][19] .= fncGetPulldown( "m_ClosedDay", "lngClosedDayCode", "strClosedDayCode || ':' || lngClosedDay",  $objMaster->aryData[0][$objMaster->aryColumnName[19]], "", $objDB );
$aryParts["MASTER"][19] .= "</select></span>\n";

// 属性
$aryParts["MASTER"][20] = fncGetAttributeHtml( $aryData["lngActionCode"], $aryData["lngcompanycode"], $aryAttributeCode, $objDB );




//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];

	// カラム表示
	$aryData["COLUMN"] .= "<span id=\"Column$i\" class=\"ColumnSegs\"></span>\n";
}

$objDB->close();


$aryData["strTableName"]    = $objMaster->strTableName;


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/co/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

// 所属属性表示関数
function fncGetAttributeHtml( $lngActionCode, $lngcompanycode, $aryAttributeCode, $objDB )
{
	$objAttribute = new clsMaster();

	// 新規かつ戻ってきた場合、所属属性オブジェクトを取得
	if ( $lngActionCode == DEF_ACTION_INSERT && count ( $aryAttributeCode ) )
	{
		$objAttribute->aryData = $aryAttributeCode;
	}

	// 修正の場合、所属属性オブジェクトを取得
	elseif ( $lngActionCode == DEF_ACTION_UPDATE )
	{
		$objAttribute->setMasterTable( "m_AttributeRelation", "lngCompanyCode", $lngcompanycode, "", $objDB );
		$objAttribute->setAryMasterInfo( $lngcompanycode, "" );
	}

	// 属性一覧取得
	list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT * FROM m_Attribute", $objDB );

	$strParts = "<span class=\"InputSegs_for_attribute\"><select id=\"Input20\" name=\"aryattributecode\" multiple size=\"$lngResultNum\">\n";

	// 属性一覧表示
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		// "selected" 文字列初期化
		$strSelected = "";

		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// 所属属性の数だけチェック
		for ( $j = 0; $j < count ( $objAttribute->aryData ); $j++ )
		{
			
			// 表示する属性に所属していた場合、選択状態にする処理
			if ( !empty($objAttribute->aryData[$j]) && $objResult->lngattributecode == $objAttribute->aryData[$j]["lngattributecode"] )
			{
				$strSelected = " selected";
				break;
			}
		}
		$strParts .= "<option value=" . $objResult->lngattributecode . $strSelected . ">" . $objResult->strattributename . "</option>\n";
	}

	$strParts .= "</select></span>\n";
	return $strParts;
}



return TRUE;
?>
