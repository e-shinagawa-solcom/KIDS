<?
/** 
*	ユーザー管理 検索結果表示画面
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// -------------------------------------------------------------------------
// search.php -> strSessionID           -> index.php
// search.php -> lngFunctionCode        -> index.php

// search.php -> bytInvalidFlag         -> index.php
// search.php -> lngUserCode            -> index.php
// search.php -> strUserID              -> index.php
// search.php -> strMailAddress         -> index.php
// search.php -> bytMailTransmitFlag    -> index.php
// search.php -> strUserDisplayCode     -> index.php
// search.php -> strUserDisplayName     -> index.php
// search.php -> strUserFullName        -> index.php
// search.php -> lngCompanyCode         -> index.php
// search.php -> lngGroupCode           -> index.php
// search.php -> lngAuthorityGroupCode  -> index.php
// search.php -> lngAccessIPAddressCode -> index.php
// search.php -> strNote                -> index.php

// search.php -> bytInvalidFlagConditions         -> index.php
// search.php -> lngUserCodeConditions            -> index.php
// search.php -> strUserIDConditions              -> index.php
// search.php -> strMailAddressConditions         -> index.php
// search.php -> bytMailTransmitFlagConditions    -> index.php
// search.php -> strUserDisplayCodeConditions     -> index.php
// search.php -> strUserDisplayNameConditions     -> index.php
// search.php -> strUserFullNameConditions        -> index.php
// search.php -> lngCompanyCodeConditions         -> index.php
// search.php -> lngGroupCodeConditions           -> index.php
// search.php -> lngAuthorityGroupCodeConditions  -> index.php
// search.php -> lngAccessIPAddressCodeConditions -> index.php
// search.php -> strNoteConditions                -> index.php

// search.php -> bytInvalidFlagVisible         -> index.php
// search.php -> lngUserCodeVisible            -> index.php
// search.php -> strUserIDVisible              -> index.php
// search.php -> strMailAddressVisible         -> index.php
// search.php -> bytMailTransmitFlagVisible    -> index.php
// search.php -> strUserDisplayCodeVisible     -> index.php
// search.php -> strUserDisplayNameVisible     -> index.php
// search.php -> strUserFullNameVisible        -> index.php
// search.php -> lngCompanyCodeVisible         -> index.php
// search.php -> lngGroupCodeVisible           -> index.php
// search.php -> lngAuthorityGroupCodeVisible  -> index.php
// search.php -> lngAccessIPAddressCodeVisible -> index.php
// search.php -> strNoteVisible                -> index.php

// 設定読み込み
include_once('conf.inc');

require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
// フォームデータから各カテゴリの振り分けを行う
$options = UtilSearchForm::extractArrayByOption($_REQUEST);
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$searchValue = $_REQUEST;

$isDisplay=array_keys($isDisplay);
$isSearch=array_keys($isSearch);
$aryData['ViewColumn']=$isDisplay;
$aryData['SearchColumn']=$isSearch;

foreach($searchValue as $key=> $item){
	$aryData[$key]=$item;
}

// クッキーから言語コードを取得
$aryData["lngLanguageCode"] = 1;

// 検索表示項目取得
if ( is_array($aryData["ViewColumn"]) &&  $lngArrayLength = count ( $aryData["ViewColumn"] ) )
{
	$aryColumn = $aryData["ViewColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryColumn[$i]] = 1;
	}
	$aryData["ViewColumn"] = "";
	$aryColumn = "";
}

// 検索条件項目取得
if ( is_array($aryData["SearchColumn"]) && $lngArrayLength = count ( $aryData["SearchColumn"] ) )
{
	$aryColumn = $aryData["SearchColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryColumn[$i]] = 1;
	}
	$aryData["SearchColumn"] = "";
	$aryColumn = "";
}

$aryData = fncToHTMLString( $aryData );

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( $aryData["lngFunctionCode"] != DEF_FUNCTION_UC3 || !fncCheckAuthority( $aryData["lngFunctionCode"], $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// ログイン許可フラグがNULLの場合の設定
if ( !$aryData["bytInvalidFlag"] )
{
	$aryData["bytInvalidFlag"] = "TRUE";
}

// メール配信許可フラグがNULLの場合の設定
if ( !$aryData["bytMailTransmitFlag"] )
{
	$aryData["bytMailTransmitFlag"] = "FALSE";
}

// ユーザー表示フラグがNULLの場合の設定
if ( !$aryData["bytUserDisplayFlag"] )
{
	$aryData["bytUserDisplayFlag"] = "FALSE";
}

// 検索結果のカラム表記の言語設定
if ( !$aryData["lngLanguageCode"] )
{
	$aryColumnLang = Array (
		"detail"                 => "Detail",
		"bytInvalidFlag"         => "Login permission",
		"lngUserCode"            => "User code",
		"strUserID"              => "User ID",
		"bytMailTransmitFlag"    => "Email permission",
		"strMailAddress"         => "Email",
		"bytUserDisplayFlag"     => "User permission",
		"strUserDisplayCode"     => "Display user code",
		"strUserDisplayName"     => "Display user name",
		"strUserFullName"        => "User full name",
		"lngCompanyCode"         => "Company",
		"lngGroupCode"           => "Group",
		"lngAuthorityGroupCode"  => "Authority group",
		"lngAccessIPAddressCode" => "Access IP Address",
		"strNote"                => "Remark",
		"update"                 => "Fix"
	);
}
else
{
	$aryColumnLang = Array (
		"detail"                 => "詳細",
		"bytInvalidFlag"         => "ログイン許可",
		"lngUserCode"            => "ユーザーコード",
		"strUserID"              => "ユーザーID",
		"bytMailTransmitFlag"    => "メール配信許可",
		"strMailAddress"         => "メールアドレス",
		"bytUserDisplayFlag"     => "ユーザー表示",
		"strUserDisplayCode"     => "表示ユーザーコード",
		"strUserDisplayName"     => "表示ユーザー名",
		"strUserFullName"        => "フルネーム",
		"lngCompanyCode"         => "会社",
		"lngGroupCode"           => "グループ",
		"lngAuthorityGroupCode"  => "権限グループ",
		"lngAccessIPAddressCode" => "アクセスIPアドレス",
		"strNote"                => "備考",
		"update"                 => "修正"
	);
}


//////////////////////////////////////////////////////////////////////////
// 文字列チェック
//////////////////////////////////////////////////////////////////////////
$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC3 . ")";
$aryCheck["bytInvalidFlag"]         = "english(4,5)";
$aryCheck["lngUserCode"]            = "number(0,32767)";
$aryCheck["strUserID"]              = "numenglish(0,32767)";
$aryCheck["strMailAddress"]         = "ascii(1,255)";
$aryCheck["strUserDisplayCode"]     = "numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "length(0,120)";
$aryCheck["strUserFullName"]        = "length(0,120)";
$aryCheck["lngCompanyCode"]         = "number(0,32767)";
$aryCheck["lngGroupCode"]           = "number(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "number(0,32767)";
$aryCheck["strNote"]                = "length(0,1000)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



$aryInvalidFlag      = Array ("t" => "不許可", "f" => "許可" );
$aryMailTransmitFlag = Array ("t" => "許可",   "f" => "不許可" );
$aryUserDisplayFlag  = Array ("t" => "表示",   "f" => "非表示" );


// ユーザー管理
// データ読み込み、検索、詳細情報取得クエリ関数
list ( $lngResultID, $lngResultNum, $baseData["strErrorMessage"] ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
$strURL = fncGetURL( $aryData );
//echo $strURL;exit;

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
// パーツテンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "uc/result/parts.tmpl" );




// テーブルの列名とソート処理
if ( $aryData["detailVisible"] )
{
	// 詳細
	$baseData["detail"] = "<td nowarp>" . $aryColumnLang["detail"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["bytInvalidFlagVisible"] )
{
	// ログイン許可フラグ
	$baseData["column1"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_1_ASC';\"><a href=\"#\">" . $aryColumnLang["bytInvalidFlag"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngUserCodeVisible"] )
{
	// ユーザーコード
	$baseData["column2"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_2_ASC';\"><a href=\"#\">" . $aryColumnLang["lngUserCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserIDVisible"] )
{
	// ユーザーID
	$baseData["column3"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_3_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserID"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strMailAddressVisible"] )
{
	// メールアドレス
	$baseData["column4"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_4_ASC';\"><a href=\"#\">" . $aryColumnLang["strMailAddress"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["bytMailTransmitFlagVisible"] )
{
	// メール配信許可
	$baseData["column5"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_5_ASC';\"><a href=\"#\">" . $aryColumnLang["bytMailTransmitFlag"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["bytUserDisplayFlagVisible"] )
{
	// 表示ユーザーフラグ
	$baseData["column6"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_6_ASC';\"><a href=\"#\">" . $aryColumnLang["bytUserDisplayFlag"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserDisplayCodeVisible"] )
{
	// 表示ユーザーコード
	$baseData["column7"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_7_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserDisplayNameVisible"] )
{
	// 表示ユーザー名
	$baseData["column8"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_8_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserDisplayName"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserFullNameVisible"] )
{
	// フルネーム
	$baseData["column9"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_9_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserFullName"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngCompanyCodeVisible"] )
{
	// 会社
	$baseData["column10"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_10_ASC';\"><a href=\"#\">" . $aryColumnLang["lngCompanyCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngGroupCodeVisible"] )
{
	// グループ
	$baseData["column11"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_11_ASC';\"><a href=\"#\">" . $aryColumnLang["lngGroupCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngAuthorityGroupCodeVisible"] )
{
	// 権限グループ
	$baseData["column12"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_12_ASC';\"><a href=\"#\">" . $aryColumnLang["lngAuthorityGroupCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngAccessIPAddressCodeVisible"] )
{
	// アクセスIP
	$baseData["column13"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_13_ASC';\"><a href=\"#\">" . $aryColumnLang["lngAccessIPAddressCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strNoteVisible"] )
{
	// 備考
	$baseData["column14"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_14_ASC';\"><a href=\"#\">" . $aryColumnLang["strNote"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["updateVisible"] )
{
	// 修正
	$baseData["update"] = "<td nowarp>" . $aryColumnLang["update"] . "</td>";
	$lngColumnNum++;
}


// 同じ項目のソートは逆順にする処理
list ( $column, $lngSort, $DESC ) = explode ( "_", $aryData["strSort"] );

if ( $DESC == 'ASC' )
{
	$baseData["column" . $lngSort] = preg_replace ( "/ASC/", "DESC", $baseData["column" . $lngSort] );
}



// パーツテンプレートコピー
$strTemplate = $objTemplate->strTemplate;

$baseData["lngColumnNum"] =& $lngColumnNum;

// パーツテンプレートに埋め込み
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	// 連番
	$partsData["number"] = $i + 1;
	// 詳細URL
	if ( $aryData["detailVisible"] )
	{
		$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/uc/result/detail.php?strSessionID=" .$aryData["strSessionID"] ."&lngUserCode=" . $objResult->lngusercode . "&lngFunctionCode=" . DEF_FUNCTION_UC4 . "&lngUserCodeCondition=1' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'detail' );\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
	}
	// ログイン許可
	if ( $aryData["bytInvalidFlagVisible"] )
	{
		$partsData["bytInvalidFlag"] = "<td nowrap>" . $aryInvalidFlag[$objResult->bytinvalidflag] . "</td>";
	}
	// ユーザーコード
	if ( $aryData["lngUserCodeVisible"] )
	{
		$partsData["lngUserCode"] = "<td nowrap>" . $objResult->lngusercode . "</td>";
	}
	// ユーザーID
	if ( $aryData["strUserIDVisible"] )
	{
		$partsData["strUserID"] = "<td nowrap>" . $objResult->struserid . "</td>";
	}
	// メールアドレス
	if ( $aryData["strMailAddressVisible"] )
	{
		$partsData["strMailAddress"] = "<td nowrap>" . $objResult->strmailaddress . "</td>";
	}
	// メール配信許可
	if ( $aryData["bytMailTransmitFlagVisible"] )
	{
		$partsData["bytMailTransmitFlag"] = "<td nowrap>" . $aryMailTransmitFlag[$objResult->bytmailtransmitflag] . "</td>";
	}
	// 表示ユーザーフラグ
	if ( $aryData["bytUserDisplayFlagVisible"] )
	{
		$partsData["bytUserDisplayFlag"] = "<td nowrap>" . $aryUserDisplayFlag[$objResult->bytuserdisplayflag] . "</td>";
	}
	// 表示ユーザーコード
	if ( $aryData["strUserDisplayCodeVisible"] )
	{
		$partsData["strUserDisplayCode"] = "<td nowrap>" . $objResult->struserdisplaycode . "</td>";
	}
	// 表示ユーザー名
	if ( $aryData["strUserDisplayNameVisible"] )
	{
		$partsData["strUserDisplayName"] = "<td nowrap>" . $objResult->struserdisplayname . "</td>";
	}
	// フルネーム
	if ( $aryData["strUserFullNameVisible"] )
	{
		$partsData["strUserFullName"] = "<td nowrap>" . $objResult->struserfullname . "</td>";
	}
	// 会社
	if ( $aryData["lngCompanyCodeVisible"] )
	{
		$partsData["strCompanyName"] = "<td nowrap>" . $objResult->strcompanyname . "</td>";
	}
	// グループ
	if ( $aryData["lngGroupCodeVisible"] )
	{
		$partsData["strGroupName"] = "<td nowrap>" . $objResult->strgroupname . "</td>";
	}
	// 権限グループ
	if ( $aryData["lngAuthorityGroupCodeVisible"] )
	{
		$partsData["strAuthorityGroupName"] = "<td nowrap>" . $objResult->strauthoritygroupname . "</td>";
	}
	// アクセスIP
	if ( $aryData["lngAccessIPAddressCodeVisible"] )
	{
		$partsData["strAccessIPAddress"] = "<td nowrap>" . $objResult->straccessipaddress . "</td>";
	}
	// 備考
	if ( $aryData["strNoteVisible"] )
	{
		$partsData["strNote"] = "<td nowrap>" . $objResult->strnote . "</td>";
	}
	// 修正
	if ( $aryData["updateVisible"] )
	{
		$partsData["update"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/uc/regist/edit.php?strSessionID=" .$aryData["strSessionID"] ."&lngUserCode=" . $objResult->lngusercode . "&lngFunctionCode=" . DEF_FUNCTION_UC5 . "&lngUserCodeCondition=1' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
	}

	// グループカラー
	$partsData["color"] = $objResult->strgroupdisplaycolor;

	// データ連想配列のキーを配列に取得
	$objTemplate->replace( $partsData );

	// パーツテンプレート生成
	$baseData["tabledata"] .= $objTemplate->strTemplate;
	// テンプレートを初期のテンプレート状態に戻す
	$objTemplate->strTemplate = $strTemplate;
}

$objDB->freeResult( $lngResultID );

//セッションの情報をhiddenで持つ
$baseData["strSessionID"] = $aryData["strSessionID"];

/////////テストここから
// POSTされたデータをHiddenにて設定する
unset($ary_keys);
$ary_Keys = array_keys( $aryData );
while ( list ($strKeys, $strValues ) = each ( $ary_Keys ) )
{
	if( $strValues == "ViewColumn")
	{
//		reset( $aryData["ViewColumn"] );
		if (is_array($aryData["ViewColumn"])) {
			for ( $i = 0; $i < count( $aryData["ViewColumn"] ); $i++ )
			{
				$aryHidden[] = "<input type='hidden' name='ViewColumn[]' value='" .$aryData["ViewColumn"][$i]. "'>";
			}
		}
	}
	elseif( $strValues == "SearchColumn")
	{
//		reset( $aryData["SearchColumn"] );
		if (is_array($aryData["SearchColumn"])) {
			for ( $j = 0; $j < count( $aryData["SearchColumn"] ); $j++ )
			{
				$aryHidden[] = "<input type='hidden' name='SearchColumn[]' value='". $aryData["SearchColumn"][$j] ."'>";
			}
		}
	}
	elseif( $strValues == "strSort" || $strValues == "strSortOrder" )
	{
		//何もしない
	} 
	else
	{
		$aryHidden[] = "<input type='hidden' name='". $strValues."' value='".$aryData[$strValues]."'>";
	}
}

$aryHidden[] = "<input type='hidden' name='strSort'>";
$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
$strHidden = implode ("\n", $aryHidden );

$baseData["strHidden"] = $strHidden;
/////////テストここまで




// ベーステンプレート読み込み
$objTemplate->getTemplate( "uc/result/base.tmpl" );

// ベーステンプレート生成
$objTemplate->replace( $baseData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();


return TRUE;
?>
