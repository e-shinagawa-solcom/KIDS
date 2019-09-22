<?php
// ----------------------------------------------------------------------------
/**
*       売上（納品書）登録関数群
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・売上（納品書）登録関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

function fncGetTaxRatePullDown($dtmDeliveryDate, $objDB)
{
    // DBからデータ取得
    $strQuery = "SELECT lngtaxcode, curtax "
        . " FROM m_tax "
        . " WHERE dtmapplystartdate <= '$dtmDeliveryDate' "
        . "   AND dtmapplyenddate >= '$dtmDeliveryDate' "
        . " ORDER BY lngpriority ";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( $lngResultNum ) {
        for ( $i = 0; $i < $lngResultNum; $i++ ) {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    } else {
        fncOutputError ( 9501, DEF_FATAL, "消費税情報の取得に失敗", TRUE, "", $objDB );
    }
    $objDB->freeResult( $lngResultID );

    // 選択項目作成
    $strHtml = "";
    for ( $i = 0; $i < count($aryResult); $i++)
	{
        $optionValue =  $aryResult[$i]["lngtaxcode"];
        $displayText =  $aryResult[$i]["curtax"];
        
        if ($i == 0)
        {
            // 1件目をデフォルトで選択
            $strHtml .= "<OPTION VALUE=\"$optionValue\" SELECTED>$displayText</OPTION>\n";
        }
        else
        {
            $strHtml .= "<OPTION VALUE=\"$optionValue\">$displayText</OPTION>\n";
        }
    }

    return $strHtml;    
}

function fncGetReceiveDetail($aryCondition, $objDB)
{
    // -------------------
    //  選択項目
    // -------------------
    $arySelect[] = " SELECT";
    $arySelect[] = "  rd.lngsortkey,";                             //No.
    $arySelect[] = "  r.strcustomerreceivecode,";                  //顧客受注番号
    $arySelect[] = "  r.strreceivecode,";                          //受注番号
    $arySelect[] = "  p2.strgoodscode,";                           //顧客品番
    $arySelect[] = "  rd.strproductcode,";                         //製品コード
    $arySelect[] = "  rd.strrevisecode,";                          //リバイズコード（再販コード）
    $arySelect[] = "  p.strproductname,";                          //製品名
    $arySelect[] = "  p.strproductenglishname,";                   //製品名（英語）
    $arySelect[] = "  g.strgroupdisplayname as strsalesdeptname,"; //営業部署（名称）
    $arySelect[] = "  rd.lngsalesclasscode,";                      //売上区分コード
    $arySelect[] = "  sc.strsalesclassname,";                      //売上区分（名称）
    $arySelect[] = "  rd.dtmdeliverydate,";                        //納期
    $arySelect[] = "  rd.lngunitquantity,";                        //入数
    $arySelect[] = "  rd.curproductprice,";                        //単価
    $arySelect[] = "  rd.lngproductunitcode,";                     //単位コード
    $arySelect[] = "  pu.strproductunitname,";                     //単位（名称）
    $arySelect[] = "  rd.lngproductquantity,";                     //数量
    $arySelect[] = "  rd.cursubtotalprice,";                       //税抜金額
    $arySelect[] = "  rd.lngreceiveno,";                           //受注番号（明細登録用）
    $arySelect[] = "  rd.lngreceivedetailno,";                     //受注明細番号（明細登録用）
    $arySelect[] = "  rd.lngrevisionno,";                          //リビジョン番号（明細登録用）
    $arySelect[] = "  rd.strnote,";                                //備考（明細登録用）
    $arySelect[] = "  r.lngmonetaryunitcode,";                     //通貨単位コード（明細登録用）
    $arySelect[] = "  r.lngmonetaryratecode,";                     //通貨レートコード（明細登録用）
    $arySelect[] = "  mu.strmonetaryunitsign";                     //通貨単位記号（明細登録用）
    $arySelect[] = " FROM";
    $arySelect[] = "  t_receivedetail rd ";
    $arySelect[] = "    LEFT JOIN m_receive r ON rd.lngreceiveno=r.lngreceiveno";
    $arySelect[] = "    LEFT JOIN m_company c ON r.lngcustomercompanycode = c.lngcompanycode";
    $arySelect[] = "    LEFT JOIN m_product p ON rd.strproductcode = p.strproductcode";
    $arySelect[] = "    LEFT JOIN m_salesclass sc ON rd.lngsalesclasscode = sc.lngsalesclasscode";
    $arySelect[] = "    LEFT JOIN m_productunit pu ON rd.lngproductunitcode = pu.lngproductunitcode";
    $arySelect[] = "    LEFT JOIN m_product p2 ON rd.strproductcode = p2.strproductcode and rd.strrevisecode = p2.strrevisecode";
    $arySelect[] = "    LEFT JOIN m_group g ON p2.lnginchargegroupcode = g.lnggroupcode";
    $arySelect[] = "    LEFT JOIN m_monetaryunit mu ON r.lngmonetaryunitcode = mu.lngmonetaryunitcode";
  
    // -------------------
    //  検索条件設定
    // -------------------
    $aryWhere[] = " WHERE";
    $aryWhere[] = "  r.lngreceivestatuscode = 2";   //受注ステータス=2:受注

    // 顧客（コードで検索）
    if ($aryCondition["strCompanyDisplayCode"]){
        $aryWhere[] = " AND c.strcompanydisplaycode = '" . $aryCondition["strCompanyDisplayCode"] . "'";
    }    

    // 顧客受注番号
    if ($aryCondition["strCustomerReceiveCode"]){
        $aryWhere[] = " AND r.strcustomerreceivecode = '" . $aryCondition["strCustomerReceiveCode"] . "'";
    }

    // 受注番号
    if ($aryCondition["lngReceiveNo"]){
        $aryWhere[] = " AND r.lngreceiveno = " . $aryCondition["lngReceiveNo"];
    }

    // 製品コード
    if ($aryCondition["strReceiveDetailProductCode"]){
        $aryWhere[] = " AND rd.strproductcode = '" . $aryCondition["strReceiveDetailProductCode"] ."'";
    }
    
    // 営業部署（コードで検索）
    if ($aryCondition["lngInChargeGroupCode"]){
        $aryWhere[] = " AND g.lnggroupcode = " . $aryCondition["lngInChargeGroupCode"];
    }

    // 売上区分（コードで検索）
    if ($aryCondition["lngSalesClassCode"]){
        $aryWhere[] = " AND rd.lngsalesclasscode = " . $aryCondition["lngSalesClassCode"];
    }

    // 顧客品番
    if ($aryCondition["strGoodsCode"]){
        $aryWhere[] = " AND p2.strgoodscode = " . $aryCondition["strGoodsCode"];
    }

    // 納品日(FROM)
    if ( $aryCondition["From_dtmDeliveryDate"] )
    {
        $dtmSearchDate = $aryCondition["From_dtmDeliveryDate"] . " 00:00:00";
        $aryWhere[] = " AND rd.dtmdeliverydate >= '" . $dtmSearchDate . "'";
    }

    // 納品日(TO)
    if ( $aryCondition["To_dtmDeliveryDate"] )
    {
        $dtmSearchDate = $aryCondition["To_dtmDeliveryDate"] . " 23:59:59";
        $aryWhere[] = " AND rd.dtmdeliverydate <= '" . $dtmSearchDate . "'";
    }

    // 明細備考
    if ( $aryCondition["strNote"] )
    {
        $aryWhere[] = " AND rd.strNote LIKE '%" . $aryCondition["strNote"] . "%'";
    }
    
    // 再販を含む（offの場合、t_receivedetail.strrevisecode='00'のみを対象）
    if ( $aryCondition["IsIncludingResale"] != "true")
    {
        $aryWhere[] = " AND rd.strrevisecode = '00'";
    }

    // -------------------
    //  並び順定義
    // -------------------
    $aryOrder[] = " ORDER BY";
    $aryOrder[] = "  rd.lngsortkey";
    
    // -------------------
    // クエリ作成
    // -------------------
    $strQuery = "";
    $strQuery .= implode("\n", $arySelect);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryWhere);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryOrder);

    // -------------------
    // クエリ実行
    // -------------------
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    // 結果を配列に格納
    $aryResult = [];    //空の配列で初期化
    if ( 0 < $lngResultNum )
    {
        for ( $j = 0; $j < $lngResultNum; $j++ )
        {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $j );
        }
    }
    $objDB->freeResult( $lngResultID );

    return $aryResult;
}

function fncGetReceiveDetailHtml($aryReceiveDetail){
    $strHtml = "";
    for($i=0; $i < count($aryReceiveDetail); $i++){
        $strDisplayValue = "";
        $strHtml .= "<tr>";

        //選択チェックボックス
        $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit'></td>";
        //NO.
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngsortkey"]);
        $strHtml .= "<td class='detailSortKey'>" . $strDisplayValue . "</td>";
        //顧客発注番号
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //受注番号
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //顧客品番
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //製品コード
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductcode"]);
        $strDisplayValue .= "_";
        $strDisplayValue .= htmlspecialchars($aryReceiveDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //製品名
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //製品名（英語）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //営業部署
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //売上区分
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //納期
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //入数
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngunitquantity"]);
        $strHtml .= "<td class='detailUnitQuantity'>" . $strDisplayValue . "</td>";
        //単価
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //単位
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //数量
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //税抜金額
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //受注番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngreceiveno"]);
        $strHtml .= "<td class='forEdit detailReceiveNo'>" . $strDisplayValue . "</td>";
        //受注明細番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngreceivedetailno"]);
        $strHtml .= "<td class='forEdit detailReceiveDetailNo'>" . $strDisplayValue . "</td>";
        //リビジョン番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngrevisionno"]);
        $strHtml .= "<td class='forEdit detailRevisionNo'>" . $strDisplayValue . "</td>";
        //再販コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='forEdit detailReviseCode'>" . $strDisplayValue . "</td>";
        //売上区分コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngsalesclasscode"]);
        $strHtml .= "<td class='forEdit detailSalesClassCode'>" . $strDisplayValue . "</td>";
        //製品単位コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class='forEdit detailProductUnitCode'>" . $strDisplayValue . "</td>";
        //備考（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strnote"]);
        $strHtml .= "<td class='forEdit detailNote'>" . $strDisplayValue . "</td>";
        //通貨単位コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitCode'>" . $strDisplayValue . "</td>";
        //通貨レートコード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["lngmonetaryratecode"]);
        $strHtml .= "<td class='forEdit detailMonetaryRateCode'>" . $strDisplayValue . "</td>";
        //通貨単位記号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryReceiveDetail[$i]["strmonetaryunitsign"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitSign'>" . $strDisplayValue . "</td>";
        
        $strHtml .= "</tr>";
    }
    return $strHtml;
}


function fncRegistSales ($aryNewData, $strProcMode, $objDB, $objAuth) {

    // 同じ「仕入科目」「仕入部品」の場合に同じ単価があるか
    // m_productPriceに同じ値があるか？在った場合は配列に行番号を記憶！！
    /*
	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

		$lngmonetaryunitcode = "";
		$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
		$strProductCode = "";
		$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "lngproductno", $aryNewData["aryPoDitail"][$i]["strProductCode"]. ":str", '', $objDB );

		$arySelect = array();
		$arySelect[] = "SELECT ";
		$arySelect[] = "lngproductpricecode ";
		$arySelect[] = "FROM ";
		$arySelect[] = "m_productprice ";
		$arySelect[] = "WHERE ";
		$arySelect[] = "lngproductno = $strProductCode AND ";
		$arySelect[] = "lngsalesclasscode = ".$aryNewData["aryPoDitail"][$i]["lngSalesClassCode"]." AND ";
		$arySelect[] = "lngmonetaryunitcode = $lngmonetaryunitcode AND ";
		$arySelect[] = "curproductprice = ".$aryNewData["aryPoDitail"][$i]["curProductPrice"];

		$strSelect = implode("\n", $arySelect );

		if ( $lngResultID = $objDB->execute( $strSelect ) )
		{
			// 同一製品価格が見つからない場合、もしくは単位計上が製品単位計上の場合のみ行番号を記憶する
			if( pg_num_rows( $lngResultID ) == 0 and $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" )
			{
				$aryM_ProductPrice[] = $i;		//行番号を記憶
			}
		}
		$objDB->freeResult( $lngResultID );

    }
    //↑成果物：$aryM_ProductPrice
    */

    //-------------------------------------------------------------------------
	// m_Sales のシーケンス番号を取得
	//-------------------------------------------------------------------------
    $sequence_m_sales = fncGetSequence( 'm_sales.lngSalesNo', $objDB );
    
    // //-------------------------------------------------------------------------
	// // 売上に紐づく受注データの取得
	// //-------------------------------------------------------------------------
	// // 受注番号
	// $strReceiveCode = $aryNewData["strReceiveCode"];

	// if( $strReceiveCode != "null" )
	// {
	// 	$aryQuery = array();
	// 	$aryQuery[] = "SELECT "; 
	// 	$aryQuery[] = "r.lngReceiveNo, ";										// 1:受注番号
	// 	$aryQuery[] = "r.lngReceiveStatusCode as lngSalesStatusCode ";			// 9:受注状態コード
	// 	$aryQuery[] = "FROM m_Receive r ";
	// 	$aryQuery[] = "WHERE r.strReceiveCode = '". $strReceiveCode . "' ";
	// 	$aryQuery[] = "AND r.bytInvalidFlag = FALSE ";
	// 	$aryQuery[] = "AND r.lngRevisionNo >= 0 ";
	// 	$aryQuery[] = "AND r.lngRevisionNo = ( ";
	// 	$aryQuery[] = "SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode  ";
	// 	$aryQuery[] = "AND r2.strReviseCode = ( ";
	// 	$aryQuery[] = "SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) ) ";
	// 	$aryQuery[] = "AND 0 <= ( ";
	// 	$aryQuery[] = "SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";

	// 	$strQuery = "";
	// 	$strQuery = implode( "\n", $aryQuery );

	// 	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	// 	if ( $lngResultNum == 1 )
	// 	{
	// 		$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
	// 	}
	// 	// 指定された発注が存在しない場合
	// 	else
	// 	{
	// 		fncOutputError ( 403, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
	// 	}
	// 	$objDB->freeResult( $lngResultID );

	// 	$lngReceiveCode = $aryReceiveResult["lngreceiveno"];
	// }
	// else
	// {
	// 	$lngReceiveCode = "null";
    // }

	//-------------------------------------------------------------------------
	// 売上マスタに登録する値の取得
	//-------------------------------------------------------------------------
    // 現在日付
    $dtmNowDate     = date( 'Y/m/d', time() );  
    // TODO:顧客コードを取得
    $lngCustomerCompanyCode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );
    // TODO:グループコードを取得
    $lngInChargeGroupCode = "";
    // TODO:ユーザーコードを取得
    $lngInChargeUserCode = "";
    // TODO:通貨単位コードの取得
    $lngMonetaryUnitCode = ""; //fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
    // TODO:通貨レートコードの取得
    $lngMonetaryRateCode = "";
    // TODO:換算レートの取得
    $curConversionRate = "";
    // 売上状態コードの取得
    $lngSalesStatusCode = DEF_SALES_ORDER;
    // TODO:合計金額の取得
    $curAllTotalPrice = 0;
	// 備考
    $strNote = ( $aryNewData["strNote"] != "null" ) ? "'".$aryNewData["strNote"]."'" : "null";
    // 入力者コード
    $lngInputUserCode = $objAuth->UserCode;

    if ($strProcMode == "new-record"){
    // ■ 処理モードが「登録」の場合
    
        // リビジョン番号を初期化
		$lngrevisionno = 0;
        
        // 当月の売上番号の取得
		$strsalsecode = fncGetDateSequence( date( 'Y', strtotime( $dtmNowDate ) ), date( 'm',strtotime( $dtmNowDate ) ), "m_sales.lngSalesNo", $objDB );
        
        // TODO:伝票番号の取得
        $strSlipCode = "";

    } else if ($strProcMode == "modify-record"){
    // ■ 処理モードが「修正」の場合
        // TODO:既存リビジョン番号+1で最大値を取得
        $lngrevisionno = 999;

        // TODO:修正対象レコードに紐づく売上番号の取得
        $strsalsecode = "";
    
        // TODO:伝票番号の取得
        $strSlipCode = "";
    }

	//-------------------------------------------------------------------------
	// 売上マスタ登録処理（m_salesへのINSERT）
	//-------------------------------------------------------------------------
	$aryQuery = array();
	$aryQuery[] = "INSERT INTO m_sales ( ";
	$aryQuery[] = "lngsalesno, ";											// 1:売上番号
	$aryQuery[] = "lngrevisionno, ";										// 2:リビジョン番号
	$aryQuery[] = "strsalescode, ";											// 3:売上コード(yymmxxx 年月連番で構成された7桁の番号)
	$aryQuery[] = "dtmappropriationdate, ";									// 4:計上日
	$aryQuery[] = "lngcustomercompanycode, ";								// 5:顧客
	$aryQuery[] = "lnggroupcode, ";									    	// 6:グループコード
	$aryQuery[] = "lngusercode, ";										    // 7:ユーザコード
	$aryQuery[] = "lngsalesstatuscode, ";									// 8:売上状態コード
	$aryQuery[] = "lngmonetaryunitcode, ";									// 9:通貨単位コード
	$aryQuery[] = "lngmonetaryratecode, ";									// 10:通貨レートコード
	$aryQuery[] = "curconversionrate, ";									// 11:換算レート
	$aryQuery[] = "strslipcode, ";											// 12:納品書NO 
	$aryQuery[] = "lnginvoiceno, ";											// 13:請求書番号
	$aryQuery[] = "curtotalprice, ";										// 14:合計金額
	$aryQuery[] = "strnote, ";												// 15:備考
	$aryQuery[] = "lnginputusercode, ";										// 16:入力者コード
	$aryQuery[] = "bytinvalidflag, ";										// 17:無効フラグ
	$aryQuery[] = "dtminsertdate";											// 18:登録日
	$aryQuery[] = " ) values ( ";
	$aryQuery[] = "$sequence_m_sales,";										// 1:売上番号
	$aryQuery[] = "$lngrevisionno, ";										// 2:リビジョン番号
	$aryQuery[] = "'$strsalsecode', ";										// 3:売上コード
	$aryQuery[] = "'".$dtmNowDate."',";										// 4:計上日
	$aryQuery[] = $lngCustomerCompanyCode.", ";						        // 5:顧客コード
	$aryQuery[] = $lngInChargeGroupCode.", ";								// 6:グループコード
	$aryQuery[] = $lngInChargeUserCode.", ";								// 7:ユーザコード
	$aryQuery[] = $lngSalesStatusCode . ", ";								// 8:売上状態コード
	$aryQuery[] = "$lngMonetaryUnitCode, ";									// 9:通貨単位コード
	$aryQuery[] = $lngMonetaryRateCode.", ";					            // 10:通貨レートコード
	$aryQuery[] = "'".$curConversionRate."', ";				                // 11:換算レート
    $aryQuery[] = "$strSlipCode, ";											// 12:納品書NO
    $aryQuery[] = "null, ";													// 13:請求書番号
	$aryQuery[] = "'".$curAllTotalPrice."', ";								// 14:合計金額
	$aryQuery[] = "$strNote, ";												// 15:備考
	$aryQuery[] = "$lngInputUserCode, ";									// 16:入力者コード
	$aryQuery[] = "false, ";												// 17:無効フラグ
	$aryQuery[] = "now() ";													// 18:登録日
	$aryQuery[] = ")";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );

	if( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
	}

	$objDB->freeResult( $lngResultID );
}




?>
