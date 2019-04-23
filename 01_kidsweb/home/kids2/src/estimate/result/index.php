<?
/** 
*	見積原価管理 検索結果表示画面
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

// 設定読み込み
include_once('conf.inc');
require_once( LIB_DEBUGFILE );
require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "estimate/cmn/lib_e.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// フォームデータから各カテゴリの振り分けを行う
	$options = UtilSearchForm::extractArrayByOption($_REQUEST);
	$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
	$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to = UtilSearchForm::extractArrayByTo($_REQUEST);
	$searchValue = $_REQUEST;
	$isDisplay=array_keys($isDisplay);
	$isSearch=array_keys($isSearch);

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
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


// クッキーから言語コードを取得
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// 検索表示項目取得
if ( $lngArrayLength = count ( $aryData["ViewColumn"] ) )
{
	$aryViewColumn = $aryData["ViewColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryViewColumn[$i]] = 1;
	}
	unset ( $aryData["ViewColumn"] );
}

// 検索条件項目取得
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
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 検索結果のカラム表記の言語設定
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
		"detail"						=> "詳細",
		"update"						=> "修正",

		"strProductCode"				=> "製品コード",
		"strProductName"				=> "製品名称(日本語)",
		"strInchargeGroupDisplayCode"	=> "部門",
		"strInchargeUserDisplayCode"	=> "担当者",
		"strInputUserDisplayCode"		=> "入力者",
		"dtmCreationDate"				=> "作成日時",
		"dtmDeliveryLimitDate"			=> "納期",
		"curProductPrice"				=> "納価",
		"curRetailPrice"				=> "上代",
		"lngCartonQuantity"				=> "カートン入数",
		"lngPlanCartonProduction"		=> "計画C/t",
		"lngProductionQuantity"			=> "生産予定数",
		"431"							=> "[431] 金型償却高 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"433"							=> "[433] 金型海外償却高 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"403"							=> "[403] 材料ツール仕入高 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"curFixedCost"					=> "償却合計金額", // "固定費合計金額",
		"402"							=> "[402] 輸入パーツ仕入高 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"401"							=> "[401] 材料パーツ仕入高 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"420"							=> "[420] 外注加工費 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"1224"							=> "[1224] チャージ [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"1230"							=> "[1230] 経費 [仕入部品][償却対象][仕入先][計画個数][単価][計画原価]",
		"curMemberCost"					=> "部材費合計金額",
		"curManufacturingCost"			=> "製造費用合計", // "総製造費用",
		"curAmountOfSales"				=> "予定総売上高", // "予定売上高",
		"curTargetProfit"				=> "企画目標総利益", // "企画目標利益",
		"curAchievementRatio"			=> "目標利益率",
		"curStandardRate"				=> "間接製造経費",
		"curProfitOnSales"				=> "売上総利益",
		"bytDecisionFlag"				=> "保存状態",

		"lngWorkFlowStatusCode"			=> "ワークフロー状態",

		"delete"						=> "削除",
			
		"1"			=> "[1] 固定費売上 [売上区分] [顧客先] [数量] [単価] [小計]",
		"curFixedPlanProfit"			=> "固定費利益",
		"curSubjectTotalCost"			=> "固定費売上合計",
		"curNonFixedCost"				=> "償却対象外合計金額"
	);
}


//////////////////////////////////////////////////////////////////////////
// 文字列チェック
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


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// 見積原価管理データ読み込み、検索、詳細情報取得クエリ関数
list ( $lngResultID, $lngResultNum, $baseData["strErrorMessage"] ) = getEstimateQuery( $objAuth->UserCode, $aryData, $objDB );

// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
$strURL = fncGetURL( $aryData );
//echo $strURL;exit;


//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
// カラム名出力
if ( $aryData["btnDetailVisible"] )
{
	// 詳細
	$baseData["detail"] = "<td nowarp>" . $aryColumnLang["detail"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["btnFixVisible"] )
{
	// 修正
	$baseData["update"] = "<td nowarp>" . $aryColumnLang["update"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["strProductCodeVisible"] )
{
	// 製品コード
	$baseData["column1"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_1_ASC';\"><a href=\"#\">" . $aryColumnLang["strProductCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strProductNameVisible"] )
{
	// 製品名
	$baseData["column2"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_2_ASC';\"><a href=\"#\">" . $aryColumnLang["strProductName"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strInchargeGroupDisplayCodeVisible"] )
{
	// 担当グループ表示コード
	$baseData["column3"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_3_ASC';\"><a href=\"#\">" . $aryColumnLang["strInchargeGroupDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strInchargeUserDisplayCodeVisible"] )
{
	// 担当者表示コード
	$baseData["column4"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_4_ASC';\"><a href=\"#\">" . $aryColumnLang["strInchargeUserDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strInputUserDisplayCodeVisible"] )
{
	// 入力者コード
	$baseData["column5"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_5_ASC';\"><a href=\"#\">" . $aryColumnLang["strInputUserDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["dtmCreationDateVisible"] )
{
	// 作成日時
	$baseData["column6"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_6_ASC';\"><a href=\"#\">" . $aryColumnLang["dtmCreationDate"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["dtmDeliveryLimitDateVisible"] )
{
	// 納期
	$baseData["column7"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_7_ASC';\"><a href=\"#\">" . $aryColumnLang["dtmDeliveryLimitDate"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curProductPriceVisible"] )
{
	// 納価
	$baseData["column8"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_8_ASC';\"><a href=\"#\">" . $aryColumnLang["curProductPrice"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curRetailPriceVisible"] )
{
	// 上代
	$baseData["column9"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_9_ASC';\"><a href=\"#\">" . $aryColumnLang["curRetailPrice"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngCartonQuantityCostVisible"] )
{
	// カートン入数
	$baseData["column10"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_10_ASC';\"><a href=\"#\">" . $aryColumnLang["lngCartonQuantity"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngPlanCartonProductionVisible"] )
{
	// 生産予定数/カートン入数　計画C/t
	$baseData["column11"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_11_ASC';\"><a href=\"#\">" . $aryColumnLang["lngPlanCartonProduction"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngProductionQuantityVisible"] )
{
	// 生産予定数
	$baseData["column12"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_12_ASC';\"><a href=\"#\">" . $aryColumnLang["lngProductionQuantity"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strEstimateVisible"] )
{
	// 見積情報
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
	// 固定費合計金額
	$baseData["column16"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_16_ASC';\"><a href=\"#\">" . $aryColumnLang["curFixedCost"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curMemberCostVisible"] )
{
	// 部材費合計金額
	$baseData["column22"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_22_ASC';\"><a href=\"#\">" . $aryColumnLang["curMemberCost"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curManufacturingCostVisible"] )
{
	// 総製造費用
	$baseData["column23"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_23_ASC';\"><a href=\"#\">" . $aryColumnLang["curManufacturingCost"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curAmountOfSalesVisible"] )
{
	// 予定総売上高
	$baseData["column24"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_24_ASC';\"><a href=\"#\">" . $aryColumnLang["curAmountOfSales"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curTargetProfitVisible"] )
{
	// 企画目標利益
	$baseData["column25"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_25_ASC';\"><a href=\"#\">" . $aryColumnLang["curTargetProfit"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curAchievementRatioVisible"] )
{
	// 目標達成率
	$baseData["column26"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_26_ASC';\"><a href=\"#\">" . $aryColumnLang["curAchievementRatio"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curStandardRateVisible"] )
{
	// 間接製造経費
	$baseData["column27"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_27_ASC';\"><a href=\"#\">" . $aryColumnLang["curStandardRate"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["curProfitOnSalesVisible"] )
{
	// 売上総利益
	$baseData["column28"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_28_ASC';\"><a href=\"#\">" . $aryColumnLang["curProfitOnSales"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["bytDecisionFlagVisible"] )
{
	// 保存状態
	$baseData["column29"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["bytDecisionFlag"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["lngWorkFlowStatusCodeVisible"] )
{
	// ワークフロー状態
	$baseData["column30"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["lngWorkFlowStatusCode"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["btnDeleteVisible"] )
{
	// 削除
	$baseData["delete"] = "<td nowarp>" . $aryColumnLang["delete"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["strEstimateVisible"] )
{
	// 売上分類
	$baseData["column31"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["1"] . "</td>";
	$lngColumnNum += 1;
}

	// 売上合計金額
	$baseData["column32"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["curSubjectTotalCost"] . "</td>";
	// 償却対象外合計金額
	$baseData["column33"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["curNonFixedCost"] . "</td>";
	// 固定費利益
	$baseData["column34"] = "<td id=\"Columns\" nowrap>" . $aryColumnLang["curFixedPlanProfit"] . "</td>";
	$lngColumnNum += 3;


// 同じ項目のソートは逆順にする処理
list ( $column, $lngSort, $DESC ) = explode ( "_", $aryData["strSort"] );

if ( $DESC == 'ASC' )
{
	$baseData["column" . $lngSort] = preg_replace ( "/ASC/", "DESC", $baseData["column" . $lngSort] );
}


//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
// 結果行出力
//$aryPayOffTargetFlag = Array ("t" => "償却対象", "f" => "非対象" );
$aryPayOffTargetFlag = Array ("t" => "○", "f" => "" );
$aryDecisionFlag = Array ("t" => "―", "f" => "仮保存" );

// パーツテンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/result/parts.tmpl" );


// パーツテンプレートコピー
$strTemplate = $objTemplate->strTemplate;

$baseData["lngColumnNum"] =& $lngColumnNum;


//////////////////////////////////////////////////////////////////////////
// 見積情報HTML生成
//////////////////////////////////////////////////////////////////////////
$aryDetailHtml = array();
$aryDetailHtmlSales = array();
$aryNonFixedCost = array();
$arySubjectTotalCost = array();

// 通貨コード->通貨記号
$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "￥", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );

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
		// 仕入科目の列か、売上分類の列か、を判断する
		// nullでは無く、0以外　→　仕入科目
		// 0 →　売上分類
		// ----------------------------------------------------------------
		if( !is_null($objResult->lngstocksubjectcode) && $objResult->lngstocksubjectcode != 0 )
		{
			// 仕入科目・区分　の処理

			// 見積原価番号、仕入科目コードをキーとする連想配列に
			// HTMLテーブル行をセット
			$aryDetailHtmlTD[] = "[" . $objResult->lngstockitemcode . " " . $objResult->strstockitemname . "]";
			$aryDetailHtmlTD[] = "[" . $aryPayOffTargetFlag[$objResult->bytpayofftargetflag] . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->lngproductquantity . "]";
			$aryDetailHtmlTD[] = "[" . $cutDetailProductPrice . "]";
			$aryDetailHtmlTD[] = "[" . $aryMonetaryUnit[$objResult->lngmonetaryunitcode] . $objResult->cursubtotalprice . "]";

			// <tr><td>***</td>・・・</tr>を生成
			$aryDetailHtml[$objResult->lngestimateno][$objResult->lngstocksubjectcode] .= "<tr><td nowrap align=left>" . join ( "</td><td nowrap align=left>", $aryDetailHtmlTD ) . "</td></tr>\n";

			// 償却対象外合計
			if( $objResult->lngstockclasscode == 2 && $objResult->bytpayofftargetflag == 'f' )
			{
				$aryNonFixedCost[$objResult->lngestimateno] += ($objResult->cursubtotalpricedefault * $objResult->curconversionrate);
//fncDebug( 'estimate_result_index_01.txt', ($objResult->cursubtotalpricedefault * $objResult->curconversionrate), __FILE__, __LINE__,'a');
			}
		}
		else
		{
			// 売上分類・科目　の処理

			// 見積原価番号、仕入科目コードをキーとする連想配列に
			// HTMLテーブル行をセット
			$aryDetailHtmlTD[] = "[" . $objResult->lngsalesclasscode. " " . $objResult->strsalesclassname . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->strcompanydisplaycode . " " . $objResult->strcompanydisplayname . "]";
			$aryDetailHtmlTD[] = "[" . $objResult->lngproductquantity . "]";
			$aryDetailHtmlTD[] = "[" . $cutDetailProductPrice . "]";
			$aryDetailHtmlTD[] = "[" . $aryMonetaryUnit[$objResult->lngmonetaryunitcode] . $objResult->cursubtotalprice . "]";

			// <tr><td>***</td>・・・</tr>を生成
			$aryDetailHtmlSales[$objResult->lngestimateno][$objResult->lngsalesdivisioncode] .= "<tr><td nowrap align=left>" . join ( "</td><td nowrap align=left>", $aryDetailHtmlTD ) . "</td></tr>\n";

			// 固定費売上合計
			$arySubjectTotalCost[$objResult->lngestimateno] += ($objResult->cursubtotalpricedefault * $objResult->curconversionrate);


//fncDebug( 'estimate_result_index_02.txt', $objResult->curSubTotalPriceDefault * $objResult->curConversionRate, __FILE__, __LINE__,'a');
		}




		unset ($lngProductRate );
		unset ($objResult );
	}
//}



//////////////////////////////////////////////////////////////////////////
// 行生成、パーツテンプレートに埋め込み
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
		// 連番
		$partsData["number"] = ++$j;

		// 詳細URL
		if ( $aryData["btnDetailVisible"] )
		{
			//$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('detail.php?strSessionID=$aryData[strSessionID]&lngEstimateNo=" . $objResult->lngestimateno . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'detail' );\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";

//			$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"detail.php?strSessionID=$aryData[strSessionID]&lngEstimateNo=" . $objResult->lngestimateno . "\" target=_blank><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
			$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"/estimate/result/detail.php?strSessionID=$aryData["strSessionID"]&lngEstimateNo=" . $objResult->lngestimateno . "\" target=\"_blank\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
		}

//fncDebug( 'estimate_result_index_01.txt', "/estimate/result/detail.php?strSessionID=$aryData[strSessionID]&lngEstimateNo=$objResult->lngestimateno", __FILE__, __LINE__);


		if( $aryData["btnFixVisible"] )
		{
			// ログインユーザーが入力したものかつ仮保存状態のもの、
			// または、ワークフロー承認以外のものは、修正ボタンを表示する
			if( ( $objResult->bytdecisionflag == "f" && $objResult->lnginputusercode == $objAuth->UserCode ) ||
				( $objResult->bytdecisionflag == "t" && $objResult->lngestimatestatuscode != DEF_ESTIMATE_APPLICATE ) )
			{
				// 修正
				//$partsData["update"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/estimate/regist/renew.php?strSessionID=$aryData[strSessionID]&lngFunctionCode=" . DEF_FUNCTION_E3 . "&lngEstimateNo=" . $objResult->lngestimateno . "&lngEstimateNoCondition=1' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				$partsData["update"] = '<td bgcolor="#ffffff">&nbsp;</td>';
			}
			else
			{
				$partsData["update"] = '<td bgcolor="#ffffff">&nbsp;</td>';
			}
		}



		// 製品コード
		if ( $aryData["strProductCodeVisible"] )
		{
			$partsData["strProductCode"] = "<td nowrap align=\"left\">" . $objResult->strproductcode . "</td>";
		}
		// 製品名
		if ( $aryData["strProductNameVisible"] )
		{
			$partsData["strProductName"] = "<td nowrap align=\"left\">" . $objResult->strproductname . "</td>";
		}
		// 担当グループ表示コード
		if ( $aryData["strInchargeGroupDisplayCodeVisible"] )
		{
			$partsData["strInchargeGroup"] = "<td nowrap align=\"left\">" . "[" . $objResult->strinchargegroupdisplaycode . "] " . $objResult->strinchargegroupdisplayname . "</td>";
		}
		// 担当者表示コード
		if ( $aryData["strInchargeUserDisplayCodeVisible"] )
		{
			$partsData["strInchargeUser"] = "<td nowrap align=\"left\">" . "[" . $objResult->strinchargeuserdisplaycode . "] " . $objResult->strinchargeuserdisplayname . "</td>";
		}
		// 入力者コード
		if ( $aryData["strInputUserDisplayCodeVisible"] )
		{
			$partsData["strInputUser"] = "<td nowrap align=\"left\">" . "[" . $objResult->strinputuserdisplaycode . "] " . $objResult->strinputuserdisplayname . "</td>";
		}
		// 作成日時
		if ( $aryData["dtmCreationDateVisible"] )
		{
			$partsData["dtmCreationDate"] = "<td nowrap align=\"left\">" . $objResult->dtmcreationdate . "</td>";
		}
		// 納品期限日
		if ( $aryData["dtmDeliveryLimitDateVisible"] )
		{
			if ( $objResult->dtmdeliverylimitdate != "" )
			{
				$dtmDeliveryLimitDate = substr( $objResult->dtmdeliverylimitdate, 0, 7 );
			}
			$partsData["dtmDeliveryLimitDate"] = "<td nowrap align=\"left\">" . $dtmDeliveryLimitDate . "</td>";
		}
		// 卸値
		if ( $aryData["curProductPriceVisible"] )
		{
			$partsData["curProductPrice"] = "<td nowrap align=\"right\">￥" . $objResult->curproductprice . "</td>";
		}
		// 販売価格
		if ( $aryData["curRetailPriceVisible"] )
		{
			$partsData["curRetailPrice"] = "<td nowrap align=\"right\">￥" . $objResult->curretailprice . "</td>";
		}
		// カートン入数
		if ( $aryData["lngCartonQuantityCostVisible"] )
		{
			$partsData["lngCartonQuantity"] = "<td nowrap align=\"right\">" . $objResult->lngcartonquantity . "</td>";
		}
		// 生産予定数/カートン入数
		if ( $aryData["lngPlanCartonProductionVisible"] )
		{
			$partsData["lngPlanCartonProduction"] = "<td nowrap align=\"right\">" . $objResult->lngplancartonproduction . "</td>";
		}
		// 生産予定数
		if ( $aryData["lngProductionQuantityVisible"] )
		{
			$partsData["lngProductionQuantity"] = "<td nowrap align=\"right\">" . $objResult->lngproductionquantity . "</td>";
		}
		
		// 見積情報
		if ( $aryData["strEstimateVisible"] )
		{
			// 売上分類
			$partsData["lngSalesDivisionCode"] 	= "<td nowrap><table width=100% align=\"left\">".  $aryDetailHtmlSales[$objResult->lngestimateno][1] . "</table></td>";
		}

		// 固定費売上合計
		$partsData["curSubjectTotalCost"] 	= '<td nowrap align="right">￥'. number_format( $arySubjectTotalCost[$objResult->lngestimateno], 2, '.', ',' ) . '</td>';
		// 償却対象外合計金額
		$partsData["curNonFixedCost"] 		= '<td nowrap align="right">￥'. number_format( $aryNonFixedCost[$objResult->lngestimateno], 2, '.', ',' ) . '</td>';
		// 固定費利益
		$curFixedPlanProfit = ($arySubjectTotalCost[$objResult->lngestimateno] - $aryNonFixedCost[$objResult->lngestimateno]);
		$partsData["curFixedPlanProfit"] 	= '<td nowrap align="right">￥'. number_format( $curFixedPlanProfit, 2, '.', ',' ) . '</td>';
		
		
		// 見積情報
		if ( $aryData["strEstimateVisible"] )
		{
			// 固定費明細
			$partsData["curFixedCostEstimate"] = "<td nowrap><table width=100% align=\"left\">" . $aryDetailHtml[$objResult->lngestimateno][431] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][433] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][403] . "</table></td>";

			// 部材費明細
			$partsData["curMemberCostEstimate"] = "<td nowrap><table width=100% align=\"left\">" . $aryDetailHtml[$objResult->lngestimateno][402] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][401] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][420] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][1224] . "</table></td><td nowrap><table width=100%>" . $aryDetailHtml[$objResult->lngestimateno][1230] . "</table></td>";
		}
		// 固定費合計金額
		if ( $aryData["curFixedCostVisible"] )
		{
			$partsData["curFixedCost"] = "<td nowrap align=\"right\">￥" . $objResult->curfixedcost . "</td>";
		}
		// 部材費合計金額
		if ( $aryData["curMemberCostVisible"] )
		{
			$partsData["curMemberCost"] = "<td nowrap align=\"right\">￥" . $objResult->curmembercost . "</td>";
		}
		// 総製造費用
		if ( $aryData["curManufacturingCostVisible"] )
		{
			$partsData["curManufacturingCost"] = "<td nowrap align=\"right\">￥" . $objResult->curmanufacturingofcost . "</td>";
		}
		// 予定総売上高
		$curAmountOfSales = ($objResult->cursalesamount + $arySubjectTotalCost[$objResult->lngestimateno]);
		if ( $aryData["curAmountOfSalesVisible"] )
		{
			$partsData["curAmountOfSales"] = "<td nowrap align=\"right\">￥" . number_format($curAmountOfSales, 2, '.', ',' ) . "</td>";
		}
		// 企画目標利益 = 予定売上高 − 総製造費用 + 固定費利益
		$curTargetProfit = ($objResult->cursalesamount - $objResult->curmanufacturingcost + $curFixedPlanProfit);
		if ( $aryData["curTargetProfitVisible"] )
		{
			$partsData["curTargetProfit"] = "<td nowrap align=\"right\">￥" . number_format($curTargetProfit, 2, '.', ',' ) . "</td>";
		}
		// 目標利益率 = 企画目標総利益 / 予定総売上高
		$curAchievementRatio = 0;
		if( $objResult->cursalesamount != 0)
		{
			$curAchievementRatio = ($curTargetProfit / $curAmountOfSales) * 100;
		}
		if ( $aryData["curAchievementRatioVisible"] )
		{
			($curAchievementRatio >= 0) ? $strStyle = '' : $strStyle = 'style="color:red;"';	// マイナス値は赤字に
			$partsData["curAchievementRatio"] = "<td nowrap align=\"right\" $strStyle>" . number_format($curAchievementRatio, 2, '.', ',' ) . " %</td>";
		}
		// 間接製造経費 = 予定総売上高 * 標準割合
		$IndirectManufactExpenditure = $curAmountOfSales * $objResult->curstandardrate;
		if ( $aryData["curStandardRateVisible"] )
		{
			$partsData["curStandardRate"] = "<td nowrap align=\"right\">￥" . number_format($IndirectManufactExpenditure, 2, '.', ',' ) . "</td>";
		}
		// 売上総利益 = 企画目標利益 - 間接製造経費
		$curProfitOnSales = $curTargetProfit - $IndirectManufactExpenditure;
		if ( $aryData["curProfitOnSalesVisible"] )
		{
			$partsData["curProfitOnSales"] = "<td nowrap align=\"right\">￥" . number_format($curProfitOnSales, 2, '.', ',' ) . "</td>";
		}
		// 保存状態
		if ( $aryData["bytDecisionFlagVisible"] )
		{
			$partsData["bytDecisionFlag"] = "<td nowrap align=\"center\">" . $aryDecisionFlag[$objResult->bytdecisionflag] . "</td>";
		}

/*
		// ワークフロー状態
		if ( $aryData["lngWorkFlowStatusCodeVisible"] )
		{
			$partsData["lngWorkFlowStatusCode"] = "<td nowrap align=\"center\">" . $lngWorkFlowStatusCode[$objResult->lngworkflowstatuscode] . "</td>";
		}
*/
		// ワークフロー状態
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
			// ログインユーザーが入力したものかつ仮保存状態のもの、
			// または、ワークフロー承認以外のものは、削除ボタンを表示する
			if( ( $objResult->bytdecisionflag == "f" && $objResult->lnginputusercode == $objAuth->UserCode ) ||
				( $objResult->bytdecisionflag == "t" && $objResult->lngestimatestatuscode != DEF_ESTIMATE_APPLICATE ) )
			{
				// 削除
				$partsData["delete"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/estimate/regist/confirm.php?strSessionID=$aryData["strSessionID"]&lngFunctionCode=" . DEF_FUNCTION_E4 . "&lngEstimateNo=" . $objResult->lngestimateno . "&lngEstimateNoCondition=1' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></td>";
			}
			else
			{
				$partsData["delete"] = '<td bgcolor="#ffffff">&nbsp;</td>';
			}
		}



		// 見積原価番号を格納
		$lngEstimateNo = $objResult->lngestimateno;

		// データ連想配列のキーを配列に取得
		$objTemplate->replace( $partsData );

		// パーツテンプレート生成
		$baseData["tabledata"] .= $objTemplate->strTemplate;
		// テンプレートを初期のテンプレート状態に戻す
		$objTemplate->strTemplate = $strTemplate;
	}
}





$objDB->freeResult( $lngResultID );
unset ( $aryDetailHtml );
unset ( $objTemplate );
unset ( $strTemplate );
unset ( $partsData );
unset ( $objResult );


//セッションの情報をhiddenで持つ
$baseData["strSessionID"] = $aryData["strSessionID"];

/////////テストここから
// POSTされたデータをHiddenにて設定する
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
		//何もしない
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
/////////テストここまで




// ベーステンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/result/base.tmpl" );

// ベーステンプレート生成
$objTemplate->replace( $baseData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();


return TRUE;
?>
