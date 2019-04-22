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
// 登録、修正実行
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
// 削除実行
// confirm.php -> strSessionID   -> action.php
// confirm.php -> lngActionCode  -> action.php
// confirm.php -> lngcompanycode -> action.php

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

// 属性コードに関するチェック(文字列チェック、本社・顧客チェック)
$aryAttributeCode = explode ( ":", $aryData["strattributecode"] );
for ( $i = 0; $i < count ( $aryAttributeCode ); $i++ )
{
	// 属性数値チェック
	if ( fncCheckString( $aryAttributeCode[$i], "number(0,2147483647)" ) == "" )
	{
		// 本社または顧客属性だった場合、それぞれのフラグ真
		if ( $aryAttributeCode[$i] == DEF_ATTRIBUTE_HEADOFFICE )
		{
			$bytHeadOfficeFlag = TRUE;
		}
		elseif ( $aryAttributeCode[$i] == DEF_ATTRIBUTE_CLIENT )
		{
			$bytClientFlag = TRUE;
		}
	}
}
// 本社と顧客双方の属性を指定されていた場合、エラー
if ( $bytHeadOfficeFlag && $bytClientFlag )
{
	fncOutputError ( 9056, DEF_WARNING, "本社、顧客双方の属性を付加することはできません。", TRUE, "", $objDB );
}


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]  = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
$aryCheck["lngcompanycode"] = "null:number(0,2147483647)";

if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	$aryCheck["lngcompanycode"]        = "null:number(0,2147483647)";
	$aryCheck["lngcountrycode"]        = "null:number(0,2147483647)";
	$aryCheck["lngorganizationcode"]   = "null:number(0,2147483647)";
	$aryCheck["bytorganizationfront"]  = "english(4,5)";
	$aryCheck["strcompanyname"]        = "null:length(1,100)";
	$aryCheck["bytcompanydisplayflag"] = "english(4,5)";
	$aryCheck["strcompanydisplaycode"] = "null:numenglish(0,10)";
	$aryCheck["strcompanyomitname"] = "length(1,100)";
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
	$aryCheck["strattributecode"]      = "null";
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
//echo getArrayTable( $aryData, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) エラーがない 場合、
// 新規登録、修正チェック実行
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
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
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 9056, DEF_WARNING, "会社コードが重複しています。", TRUE, "", $objDB );
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

	// 登録処理(INSERT)
	if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{
		// シーケンステーブルより会社コードを取得
		//$aryData["lngcompanycode"] = fncGetSequence( "m_company.lngcompanycode", $objDB );
		// インクリメント後のシーケンスが9999だった場合さらに取得
		//if ( $aryData["lngcompanycode"] == 9999 )
		//{
		//	$aryData["lngcompanycode"] = fncGetSequence( "m_company.lngcompanycode", $objDB );
		//}

		$aryQuery[] = "INSERT INTO m_Company VALUES ( " .
                       $aryData["lngcompanycode"] . ", " .
                       $aryData["lngcountrycode"]. ", " .
                       $aryData["lngorganizationcode"] . ", " .
                       $aryData["bytorganizationfront"]. ", " .
                 "'" . $aryData["strcompanyname"]. "', " .
                       $aryData["bytcompanydisplayflag"] . ", " .
                 "'" . $aryData["strcompanydisplaycode"] . "', " .
                 "'" . $aryData["strcompanydisplayname"] . "', " .
                 "'" . $aryData["strcompanyomitname"] . "', " .
                 "'" . $aryData["strpostalcode"] . "', " .
                 "'" . $aryData["straddress1"] . "', " .
                 "'" . $aryData["straddress2"] . "', " .
                 "'" . $aryData["straddress3"] . "', " .
                 "'" . $aryData["straddress4"] . "', " .
                 "'" . $aryData["strtel1"] . "', " .
                 "'" . $aryData["strtel2"] . "', " .
                 "'" . $aryData["strfax1"] . "', " .
                 "'" . $aryData["strfax2"] . "', " .
                 "'" . $aryData["strdistinctcode"] . "', " .
                       $aryData["lngcloseddaycode"].
                    " )";

		for ( $i = 0; $i < count ( $aryAttributeCode ); $i++ )
		{
			$aryQuery[] = "INSERT INTO m_AttributeRelation VALUES ( " .
                           fncGetSequence( "m_AttributeRelation.lngAttributeRelationCode", $objDB ) . ", " .
                           $aryData["lngcompanycode"] . ", " .
                           $aryAttributeCode[$i] .
                          " )";
		}
	}

	// 修正処理(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		// ロック
		$aryQuery[] = "SELECT * FROM m_Company WHERE lngcompanycode = " . $aryData["lngcompanycode"];

		// UPDATE クエリ
		$aryQuery[] = "UPDATE m_Company SET " .
                       "lngcountrycode = " . $aryData["lngcountrycode"]. ", " .
                       "lngorganizationcode = " . $aryData["lngorganizationcode"] . ", " .
                       "bytorganizationfront = " . $aryData["bytorganizationfront"]. ", " .
                       "strcompanyname = '" . $aryData["strcompanyname"]. "', " .
                       "bytcompanydisplayflag = " . $aryData["bytcompanydisplayflag"] . ", " .
                       "strcompanydisplaycode = '" . $aryData["strcompanydisplaycode"] . "', " .
                       "strcompanydisplayname = '" . $aryData["strcompanydisplayname"] . "', " .
                       "strcompanyomitname = '" . $aryData["strcompanyomitname"] . "', " .
                       "strpostalcode = '" . $aryData["strpostalcode"] . "', " .
                       "straddress1 = '" . $aryData["straddress1"] . "', " .
                       "straddress2 = '" . $aryData["straddress2"] . "', " .
                       "straddress3 = '" . $aryData["straddress3"] . "', " .
                       "straddress4 = '" . $aryData["straddress4"] . "', " .
                       "strtel1 = '" . $aryData["strtel1"] . "', " .
                       "strtel2 = '" . $aryData["strtel2"] . "', " .
                       "strfax1 = '" . $aryData["strfax1"] . "', " .
                       "strfax2 = '" . $aryData["strfax2"] . "', " .
                       "strdistinctcode = '" . $aryData["strdistinctcode"] . "', " .
                       "lngcloseddaycode = " . $aryData["lngcloseddaycode"] .
                       " WHERE lngcompanycode = " . $aryData["lngcompanycode"];

		// 属性の変更チェック(変更のあった場合のみ、変更クエリ生成)
		// 現状の属性を取得
		$strQuery = "SELECT lngAttributeCode FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
		$objAttribute = new clsMaster();
		$objAttribute->setMasterTableData( $strQuery, $objDB );

		// 今回登録された属性をコピー
		$aryAttributeCopy = $aryAttributeCode;

		// それぞれの数を取得
		$countDB  = count ( $objAttribute->aryData );
		$countGET = count ( $aryAttributeCode );

		// 属性の現状と修正の比較
		for ( $i = 0; $i < $countDB; $i++ )
		{
			for ( $j = 0; $j < $countGET; $j++ )
			{
				// 同じ属性が存在した場合、
				// チェックフラグを偽、新規登録分を削除し、ループを抜ける
				if ( $objAttribute->aryData[$i]["lngattributecode"] == $aryAttributeCopy[$j] )
				{
					$bytCheckFlag = FALSE;
					$aryAttributeCopy[$j] = "";
					break;
				}
				$bytCheckFlag = TRUE;
			}

			// チェックフラグが真の場合、削除属性が存在するということなので、
			// 属性修正クエリ生成フラグを真とし、ループを抜ける
			if ( $bytCheckFlag )
			{
				$bytAttributeChangeFlag = TRUE;
				break;
			}
		}

		// 入力された属性のコピー配列を文字列として結合した結果、
		// 値が存在した場合、追加された属性が存在するということなので、
		// 属性修正クエリ生成フラグを真とする
		if ( join ( "", $aryAttributeCopy ) )
		{
			$bytAttributeChangeFlag = TRUE;
		}

		// 属性修正クエリ生成フラグが真の場合、属性修正クエリを生成
		if ( $bytAttributeChangeFlag )
		{
			$aryQuery[] = "DELETE FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];

			for ( $i = 0; $i < $countGET; $i++ )
			{
				$aryQuery[] = "INSERT INTO m_AttributeRelation VALUES ( " .
                               fncGetSequence( "m_AttributeRelation.lngAttributeRelationCode", $objDB ) . ", " .
                               $aryData["lngcompanycode"] . ", " .
                               $aryAttributeCode[$i] .
                              " )";
			}
		}
	}
}

// 削除 かつ エラーがない 場合、
// 削除チェック実行
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( $aryCheckResult ) )
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
	// if ( $lngResultNum > 0 )
	// {
	// 	$objDB->freeResult( $lngResultID );
	// 	fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
	// }

	// 削除処理(DELETE)
	$aryQuery[] = "DELETE FROM m_Company WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
	$aryQuery[] = "DELETE FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
}



////////////////////////////////////////////////////////////////////////////
// クエリ実行
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();


$objDB->close();



//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
?>
<html>
<body>
<script language="javascript">window.returnValue=true;window.open('about:blank','_parent').close();
</script>
</body>
</html>
<?


return TRUE;
?>


