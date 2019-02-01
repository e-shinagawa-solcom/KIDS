<?
/** 
*	帳票出力 PO 印刷完了画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.03.05	海外の会社の宛先に付ける TO の横に : を追加するように修正する
*	2004.04.19	このソースにて商品化企画書対応されている箇所を発注書に変更
*
*/
// 印刷プレビュー画面( * は指定帳票のファイル名 )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "/list/cmn/lib_lo.php");
require (SRC_ROOT . "m/cmn/lib_m.php");
require (LIB_DEBUGFILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

// 文字列チェック
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strReportKeyCode"]   = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 指定キーコードの帳票データを取得
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum === 1 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strListOutputPath = $objResult->strreportpathname;
	unset ( $objResult );
	$objDB->freeResult( $lngResultID );
	//echo "コピーファイル有り。";
}

// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ( $lngResultNum === 0 )
{
	// 指定した発注データの承認状態が「承認」ではなかった場合、エラー
	if ( fncCheckApprovalProductOrder( $aryData["strReportKeyCode"], $objDB ) == FALSE )
	{
		fncOutputError ( 502, DEF_WARNING, "未承認案件の可能性があります。", TRUE, "", $objDB );
	}

	// データ取得クエリ
	$strQuery = fncGetListOutputQuery( DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $objDB );


	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryParts =& $objMaster->aryData[0];

	/////////////////////////////////////////////////////////////////
	// 特殊処理
	/////////////////////////////////////////////////////////////////
	// 海外・国内用宛名処理
	if ( $aryParts["lngorganizationcode"] == DEF_ORGANIZATION_FOREIGN )
	{
		// 宛名
// 2004.03.04 suzukaze update start
		$aryParts["strcustomerdisplayname"] = "TO: " . $aryParts["strcustomerdisplayname"];
// 2004.03.04 suzukaze update end

		// 仕入先住所
		$aryParts["strAddress"] = $objMaster->aryData[0]["strcustomeraddress4"] . $objMaster->aryData[0]["strcustomeraddress3"] . "<br>" . $objMaster->aryData[0]["strcustomeraddress2"] . $objMaster->aryData[0]["strcustomeraddress1"];
	}
	else
	{
		// 宛名
		$aryParts["strcustomerdisplayname"] .= " 様";

		// 仕入先住所
		$aryParts["strAddress"] = $objMaster->aryData[0]["strcustomeraddress1"] . $objMaster->aryData[0]["strcustomeraddress2"] . "<br>" . $objMaster->aryData[0]["strcustomeraddress3"] . $objMaster->aryData[0]["strcustomeraddress4"];
	}

	// 詳細取得
	$aryQuery[] = "SELECT p.strProductName, p.strProductEnglishName,";
	$aryQuery[] = " p.strProductCode, p.lngCartonQuantity,";
// 2004.03.30 suzukaze update start
	$aryQuery[] = " od.lngSortKey AS lngOrderDetailNo, od.strNote,";
// 2004.03.30 suzukaze update end
	$aryQuery[] = " od.lngConversionClassCode,";
	$aryQuery[] = " TO_CHAR( od.lngProductQuantity, '9,999,999,990' ) AS lngProductQuantity,";
	$aryQuery[] = " TO_CHAR( od.dtmDeliveryDate, 'YYYY/MM/DD' ) AS dtmDeliveryDate,";
	$aryQuery[] = " TO_CHAR( od.curProductPrice, '9,999,999,990.9999' ) AS curProductPrice,";
	$aryQuery[] = " TO_CHAR( od.curSubTotalPrice, '9,999,999,990.99' ) AS curSubTotalPrice,";
	$aryQuery[] = " si.strStockItemName, dm.strDeliveryMethodName,";
	$aryQuery[] = " pu.strProductUnitName, od.strMoldNo ";
	$aryQuery[] = "FROM t_OrderDetail od, m_Product p,";
	$aryQuery[] = " m_StockItem si, m_DeliveryMethod dm, m_ProductUnit pu ";
	$aryQuery[] = "WHERE od.lngOrderNo = " . $aryData["strReportKeyCode"];
	$aryQuery[] = " AND od.lngRevisionNo = ";
	$aryQuery[] = "(";
	$aryQuery[] = "  SELECT MAX ( od2.lngRevisionNo )";
	$aryQuery[] = "  FROM t_OrderDetail od2";
	$aryQuery[] = "  WHERE od.lngOrderNo = od2.lngOrderNo";
	$aryQuery[] = "   AND od.lngOrderDetailNo = od2.lngOrderDetailNo";
	$aryQuery[] = ")";
	$aryQuery[] = " AND od.strProductCode = p.strProductCode";
	$aryQuery[] = " AND od.lngStockItemCode = si.lngStockItemCode";
	$aryQuery[] = " AND od.lngStockSubjectCode = si.lngStockSubjectCode";
	$aryQuery[] = " AND od.lngDeliveryMethodCode = dm.lngDeliveryMethodCode";
	$aryQuery[] = " AND od.lngProductUnitCode = pu.lngProductUnitCode ";
// 2004.03.30 suzukaze update start
	$aryQuery[] = "ORDER BY od.lngSortKey";
// 2004.03.30 suzukaze update end

	$strQuery = join ( "", $aryQuery );
	unset ( $aryQuery );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );


	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 9051, DEF_FATAL, "帳票詳細データが存在しませんでした。", TRUE, "", $objDB );
	}

	// フィールド名取得
	for ( $i = 0; $i < pg_num_fields ( $lngResultID ); $i++ )
	{
		$aryKeys[] = pg_field_name ( $lngResultID, $i );
	}

	// 製品コード、製品名、英語製品名取得
	$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	$aryParts[$aryKeys[2]] = $aryResult[2];
	$aryParts[$aryKeys[0]] = $aryResult[0];
	$aryParts[$aryKeys[1]] = $aryResult[1];

	// 行数だけデータ取得、配列に代入
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, $i );
		for ( $j = 3; $j < count ( $aryKeys ); $j++ )
		{
			$aryDetail[$i][$aryKeys[$j] . ( ( $i + 5 ) % 5 )] = $aryResult[$j];
		}
	}
	$objDB->freeResult( $lngResultID );

	// 合計金額処理(最後のページだけに表示)別変数に保存
	$curTotalPrice = $aryParts["strmonetaryunitsign"] . " " . $aryParts["curtotalprice"];
	//$aryParts["curtotalprice"] = NULL;
	unset ( $aryParts["curtotalprice"] );

	// ページ処理
	$aryParts["lngNowPage"] = 1;
	$aryParts["lngAllPage"] = ceil ( $lngResultNum / 5 );
	//$aryParts["lngAllPage"] = 2;


	// HTML出力
	// ---------------------------------------- added by Kazushi Saito 2004/04/22 ↓
	$objTemplateHeader = new clsTemplate();
	$objTemplateHeader->getTemplate( "list/result/po_header.tmpl" );
	$strTemplateHeader = $objTemplateHeader->strTemplate;

	$objTemplateFooter = new clsTemplate();
	$objTemplateFooter->getTemplate( "list/result/po_footer.tmpl" );
	$strTemplateFooter = $objTemplateFooter->strTemplate;
	// ---------------------------------------- added by Kazushi Saito 2004/04/22 ↑
	
	//echo getArrayTable( $aryDetail[1], "TABLE" );exit;
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/po.tmpl" );
	$strTemplate = $objTemplate->strTemplate;

	// ページ数分テンプレートを繰り返し読み込み
	for ( ; $aryParts["lngNowPage"] < ( $aryParts["lngAllPage"] + 1 ); $aryParts["lngNowPage"]++ )
	{
		$objTemplate->strTemplate = $strTemplate;

		// 表示しようとしているページが最後のページの場合、
		// 合計金額を代入(発注書出力特別処理)
		if ( $aryParts["lngNowPage"] == $aryParts["lngAllPage"]  )
		{
			$aryParts["curTotalPrice"] = $curTotalPrice;
			$aryParts["strTotalAmount"] = "Total Amount";
		}

		// 置き換え
		$objTemplate->replace( $aryParts );

		// 詳細行を５行表示(発注書出力特別処理)
		$lngRecordCount = 0;
		for ( $j = ( $aryParts["lngNowPage"] - 1 ) * 5; $j < ( $aryParts["lngNowPage"] * 5 ); $j++ )
		{
			$aryDetail[$j]["record" . $lngRecordCount] = $j + 1;

			// 単価が存在すれば、それに通貨単位をつける
			if ( $aryDetail[$j]["curproductprice" . ( ( $j + 5 ) % 5 )] > 0 )
			{
				$aryDetail[$j]["curproductprice" . ( ( $j + 5 ) % 5 )] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["curproductprice" . ( ( $j + 5 ) % 5 )];
			}

			// 小計が存在すれば、それに通貨単位をつける
			if ( $aryDetail[$j]["cursubtotalprice" . ( ( $j + 5 ) % 5 )] > 0 )
			{
				$aryDetail[$j]["cursubtotalprice" . ( ( $j + 5 ) % 5 )] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["cursubtotalprice" . ( ( $j + 5 ) % 5 )];
			}

			// 製品数量が存在すれば、それに製品単位をつける
			if ( $aryDetail[$j]["lngproductquantity" . ( ( $j + 5 ) % 5 )] > 0 )
			{
				$aryDetail[$j]["lngproductquantity" . ( ( $j + 5 ) % 5 )] .= "(" . $aryDetail[$j]["strproductunitname" . ( ( $j + 5 ) % 5 )] . ")";
			}

			// カートン入数が存在すれば、それに製品単位をつける
			if ( $aryDetail[$j]["lngconversionclasscode" . ( ( $j + 5 ) % 5 )] == 2 )
			{
				$aryDetail[$j]["lngcartonquantity" . ( ( $j + 5 ) % 5 )] = "1(c/t) = " . $aryDetail[$j]["lngcartonquantity" . ( ( $j + 5 ) % 5 )] . "(pcs)";
			}
			else
			{
				unset ( $aryDetail[$j]["lngcartonquantity" . ( ( $j + 5 ) % 5 )] );
			}

			// 金型番号が存在すれば、それに()をつける
			if ( $aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] != "" )
			{
				$aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] = "(" . $aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] . ")";
			}
			else
			{
				unset ( $aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] );
			}

			$objTemplate->replace( $aryDetail[$j] );
			$lngRecordCount++;
		}

		$objTemplate->complete();
		$aryHtml[] = $objTemplate->strTemplate;

	}

	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ↓
	$strBodyHtml = join ( "<br style=\"page-break-after:always;\">\n", $aryHtml );
	
	$strHtml = $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ↑

	$objDB->transactionBegin();

	// シーケンス発行
	$lngSequence = fncGetSequence( "t_Report.lngReportCode", $objDB );


	// 帳票テーブルにINSERT
	$strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_ORDER . ", " . $aryParts["lngorderno"] . ", '', '$lngSequence' )";

//fncDebug("list_action.txt", SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", __FILE__ , __LINE__, "w" );
//fncDebug("list_action.txt", $strQuery, __FILE__ , __LINE__, "a" );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );



	// 帳票ファイルオープン
	if ( !$fp = fopen ( SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w" ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "帳票ファイルのオープンに失敗しました。", TRUE, "", $objDB );
	}

	// 帳票ファイルへの書き込み
	if ( !fwrite ( $fp, $strHtml ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "帳票ファイルの書き込みに失敗しました。", TRUE, "", $objDB );
	}

	$objDB->transactionCommit();
	//echo "コピーファイル作成";
}
//echo "<script language=javascript>window.form1.submit();window.returnValue=true;window.close();</script>";
echo "<script language=javascript>parent.window.close();</script>";


$objDB->close();



return TRUE;
?>
