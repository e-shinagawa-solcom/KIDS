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

function fncGetReceiveDetailHtml($aryDetail){
    $strHtml = "";
    for($i=0; $i < count($aryDetail); $i++){
        $strDisplayValue = "";
        $strHtml .= "<tr>";

        //選択チェックボックス
        $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit'></td>";
        //NO.
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngsortkey"]);
        $strHtml .= "<td class='detailSortKey'>" . $strDisplayValue . "</td>";
        //顧客発注番号
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //受注番号
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //顧客品番
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //製品コード
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductcode"]);
        $strDisplayValue .= "_";
        $strDisplayValue .= htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //製品名
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //製品名（英語）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //営業部署
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //売上区分
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //納期
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //入数
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngunitquantity"]);
        $strHtml .= "<td class='detailUnitQuantity'>" . $strDisplayValue . "</td>";
        //単価
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //単位
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //数量
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //税抜金額
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //受注番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiveno"]);
        $strHtml .= "<td class='forEdit detailReceiveNo'>" . $strDisplayValue . "</td>";
        //受注明細番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceivedetailno"]);
        $strHtml .= "<td class='forEdit detailReceiveDetailNo'>" . $strDisplayValue . "</td>";
        //リビジョン番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngrevisionno"]);
        $strHtml .= "<td class='forEdit detailRevisionNo'>" . $strDisplayValue . "</td>";
        //再販コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='forEdit detailReviseCode'>" . $strDisplayValue . "</td>";
        //売上区分コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngsalesclasscode"]);
        $strHtml .= "<td class='forEdit detailSalesClassCode'>" . $strDisplayValue . "</td>";
        //製品単位コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class='forEdit detailProductUnitCode'>" . $strDisplayValue . "</td>";
        //備考（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strnote"]);
        $strHtml .= "<td class='forEdit detailNote'>" . $strDisplayValue . "</td>";
        //通貨単位コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitCode'>" . $strDisplayValue . "</td>";
        //通貨レートコード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryratecode"]);
        $strHtml .= "<td class='forEdit detailMonetaryRateCode'>" . $strDisplayValue . "</td>";
        //通貨単位記号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strmonetaryunitsign"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitSign'>" . $strDisplayValue . "</td>";
        
        $strHtml .= "</tr>";
    }
    return $strHtml;
}




// 売上（納品書）登録メイン関数
function fncRegisterSalesAndSlip($aryHeader, $aryDetail, $objDB, $objAuth)
{
    // 現在日付
    $dtmNowDate = date( 'Y/m/d', time() );  

    // 登録する明細の数
    $totalItemCount = count($aryDetail);

    // TODO:顧客コードに紐づく帳票1ページあたりの最大明細数を取得する
    $maxItemPerPage = 999;

    // 最大ページ数の計算
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);

    for ( $page = 1; $page <= $maxPageCount; $page++ ){

        // 現在のページ数と1ページあたりの明細数から登録する明細のインデックスの最小値と最大値を求める
        $itemMinIndex = ($page-1) * $maxItemPerPage ;
        $itemMaxIndex = $page * $maxItemPerPage - 1;
        if ($itemMaxIndex > $totalItemCount - 1){
            $itemMaxIndex = $totalItemCount - 1;
        }

        // 売上番号をシーケンスより発番
        $lngSalesNo = fncGetSequence( 'm_sales.lngSalesNo', $objDB );
        
        // 新規登録時、リビジョン番号は0固定
        $lngRevisionNo = 0;

        // 当月に紐づく売上コードの発番
        $strSalesCode = fncGetDateSequence( date( 'Y', strtotime( $dtmNowDate ) ), 
                                            date( 'm',strtotime( $dtmNowDate ) ), "m_sales.lngSalesNo", $objDB );

        // TODO:当日に紐づく納品伝票コードの発番
        $strSlipCode = "";

        // 納品伝票番号をシーケンスより発番
        $lngSlipNo = fncGetSequence( 'm_slip.lngslipno', $objDB );

        // 売上マスタ登録
        if (!fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $aryHeader , $aryDetail, $objDB, $objAuth)){
            // 失敗
            return false;
        }

        // 売上明細登録
        if (!fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo, $aryHeader , $aryDetail, $objDB, $objAuth)){
            // 失敗
            return false;
        }

        // 納品伝票マスタ登録
        if (!fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $aryHeader , $aryDetail, $objDB, $objAuth)){
            // 失敗
            return false;
        }
    
        // 納品伝票明細登録
        if (!fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo, $aryHeader, $aryDetail, $objDB, $objAuth)){
            // 失敗
            return false;
        }

    }

    // 成功
    return true;
}

// 売上マスタ登録
function fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $aryHeader , $aryDetail, $objDB, $objAuth)
{
    // 登録クエリ作成
    $aryInsert = [];
    $aryInsert[] = "INSERT  ";
    $aryInsert[] = "INTO m_sales(  ";
    $aryInsert[] = "  lngsalesno ";                      //1:売上番号
    $aryInsert[] = "  , lngrevisionno ";                 //2:リビジョン番号
    $aryInsert[] = "  , strsalescode ";                  //3:売上コード
    $aryInsert[] = "  , dtmappropriationdate ";          //4:計上日
    $aryInsert[] = "  , lngcustomercompanycode ";        //5:顧客コード
    $aryInsert[] = "  , lnggroupcode ";                  //6:グループコード
    $aryInsert[] = "  , lngusercode ";                   //7:ユーザコード
    $aryInsert[] = "  , lngsalesstatuscode ";            //8:売上状態コード
    $aryInsert[] = "  , lngmonetaryunitcode ";           //9:通貨単位コード
    $aryInsert[] = "  , lngmonetaryratecode ";           //10:通貨レートコード
    $aryInsert[] = "  , curconversionrate ";             //11:換算レート
    $aryInsert[] = "  , strslipcode ";                   //12:納品書NO
    $aryInsert[] = "  , lnginvoiceno ";                  //13:請求書番号
    $aryInsert[] = "  , curtotalprice ";                 //14:合計金額
    $aryInsert[] = "  , strnote ";                       //15:備考
    $aryInsert[] = "  , lnginputusercode ";              //16:入力者コード
    $aryInsert[] = "  , bytinvalidflag ";                //17:無効フラグ
    $aryInsert[] = "  , dtminsertdate ";                 //18:登録日
    $aryInsert[] = ")  ";                                
    $aryInsert[] = "VALUES (  ";                         
    $aryInsert[] = "  :lngsalesno ";                     //1:売上番号
    $aryInsert[] = "  , :lngrevisionno ";                //2:リビジョン番号
    $aryInsert[] = "  , :strsalescode ";                 //3:売上コード
    $aryInsert[] = "  , :dtmappropriationdate ";         //4:計上日
    $aryInsert[] = "  , :lngcustomercompanycode ";       //5:顧客コード
    $aryInsert[] = "  , :lnggroupcode ";                 //6:グループコード
    $aryInsert[] = "  , :lngusercode ";                  //7:ユーザコード
    $aryInsert[] = "  , :lngsalesstatuscode ";           //8:売上状態コード
    $aryInsert[] = "  , :lngmonetaryunitcode ";          //9:通貨単位コード
    $aryInsert[] = "  , :lngmonetaryratecode ";          //10:通貨レートコード
    $aryInsert[] = "  , :curconversionrate ";            //11:換算レート
    $aryInsert[] = "  , :strslipcode ";                  //12:納品書NO
    $aryInsert[] = "  , :lnginvoiceno ";                 //13:請求書番号
    $aryInsert[] = "  , :curtotalprice ";                //14:合計金額
    $aryInsert[] = "  , :strnote ";                      //15:備考
    $aryInsert[] = "  , :lnginputusercode ";             //16:入力者コード
    $aryInsert[] = "  , :bytinvalidflag ";               //17:無効フラグ
    $aryInsert[] = "  , :dtminsertdate ";                //18:登録日
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // TODO:文字列置換による疑似パラメータバインド
    $strQuery = str_replace(":lngsalesno", $value, $strQuery);                     //1:売上番号
    $strQuery = str_replace(":lngrevisionno", $value, $strQuery);                  //2:リビジョン番号
    $strQuery = str_replace(":strsalescode", $value, $strQuery);                   //3:売上コード
    $strQuery = str_replace(":dtmappropriationdate", $value, $strQuery);           //4:計上日
    $strQuery = str_replace(":lngcustomercompanycode", $value, $strQuery);         //5:顧客コード
    $strQuery = str_replace(":lnggroupcode", $value, $strQuery);                   //6:グループコード
    $strQuery = str_replace(":lngusercode", $value, $strQuery);                    //7:ユーザコード
    $strQuery = str_replace(":lngsalesstatuscode", $value, $strQuery);             //8:売上状態コード
    $strQuery = str_replace(":lngmonetaryunitcode", $value, $strQuery);            //9:通貨単位コード
    $strQuery = str_replace(":lngmonetaryratecode", $value, $strQuery);            //10:通貨レートコード
    $strQuery = str_replace(":curconversionrate", $value, $strQuery);              //11:換算レート
    $strQuery = str_replace(":strslipcode", $value, $strQuery);                    //12:納品書NO
    $strQuery = str_replace(":lnginvoiceno", $value, $strQuery);                   //13:請求書番号
    $strQuery = str_replace(":curtotalprice", $value, $strQuery);                  //14:合計金額
    $strQuery = str_replace(":strnote", $value, $strQuery);                        //15:備考
    $strQuery = str_replace(":lnginputusercode", $value, $strQuery);               //16:入力者コード
    $strQuery = str_replace(":bytinvalidflag", $value, $strQuery);                 //17:無効フラグ
    $strQuery = str_replace(":dtminsertdate", $value, $strQuery);                  //18:登録日


    // 登録実行
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// 失敗
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// 成功
	return true;
}


// 売上明細登録
function fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo, $aryHeader , $aryDetail, $objDB, $objAuth)
{
    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        // 登録クエリ作成
        $aryInsert = [];
        $aryInsert[] ="INSERT  ";
        $aryInsert[] ="INTO t_salesdetail(  ";
        $aryInsert[] ="  lngsalesno ";                    //1:売上番号
        $aryInsert[] ="  , lngsalesdetailno ";            //2:売上明細番号
        $aryInsert[] ="  , lngrevisionno ";               //3:リビジョン番号
        $aryInsert[] ="  , strproductcode ";              //4:製品コード
        $aryInsert[] ="  , strrevisecode ";               //5:再販コード
        $aryInsert[] ="  , lngsalesclasscode ";           //6:売上区分コード
        $aryInsert[] ="  , lngconversionclasscode ";      //7:換算区分コード
        $aryInsert[] ="  , lngquantity ";                 //8:入数
        $aryInsert[] ="  , curproductprice ";             //9:製品価格
        $aryInsert[] ="  , lngproductquantity ";          //10:製品数量
        $aryInsert[] ="  , lngproductunitcode ";          //11:製品単位コード
        $aryInsert[] ="  , lngtaxclasscode ";             //12:消費税区分コード
        $aryInsert[] ="  , lngtaxcode ";                  //13:消費税率コード
        $aryInsert[] ="  , curtaxprice ";                 //14:消費税金額
        $aryInsert[] ="  , cursubtotalprice ";            //15:小計金額
        $aryInsert[] ="  , strnote ";                     //16:備考
        $aryInsert[] ="  , lngsortkey ";                  //17:表示用ソートキー
        $aryInsert[] ="  , lngreceiveno ";                //18:受注番号
        $aryInsert[] ="  , lngreceivedetailno ";          //19:受注明細番号
        $aryInsert[] ="  , lngreceiverevisionno ";        //20:受注リビジョン番号
        $aryInsert[] =")  ";                              
        $aryInsert[] ="VALUES (  ";                       
        $aryInsert[] ="  :lngsalesno ";                   //1:売上番号
        $aryInsert[] ="  , :lngsalesdetailno ";           //2:売上明細番号
        $aryInsert[] ="  , :lngrevisionno ";              //3:リビジョン番号
        $aryInsert[] ="  , :strproductcode ";             //4:製品コード
        $aryInsert[] ="  , :strrevisecode ";              //5:再販コード
        $aryInsert[] ="  , :lngsalesclasscode ";          //6:売上区分コード
        $aryInsert[] ="  , :lngconversionclasscode ";     //7:換算区分コード
        $aryInsert[] ="  , :lngquantity ";                //8:入数
        $aryInsert[] ="  , :curproductprice ";            //9:製品価格
        $aryInsert[] ="  , :lngproductquantity ";         //10:製品数量
        $aryInsert[] ="  , :lngproductunitcode ";         //11:製品単位コード
        $aryInsert[] ="  , :lngtaxclasscode ";            //12:消費税区分コード
        $aryInsert[] ="  , :lngtaxcode ";                 //13:消費税率コード
        $aryInsert[] ="  , :curtaxprice ";                //14:消費税金額
        $aryInsert[] ="  , :cursubtotalprice ";           //15:小計金額
        $aryInsert[] ="  , :strnote ";                    //16:備考
        $aryInsert[] ="  , :lngsortkey ";                 //17:表示用ソートキー
        $aryInsert[] ="  , :lngreceiveno ";               //18:受注番号
        $aryInsert[] ="  , :lngreceivedetailno ";         //19:受注明細番号
        $aryInsert[] ="  , :lngreceiverevisionno ";       //20:受注リビジョン番号
        $aryInsert[] =") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // TODO:文字列置換による疑似パラメータバインド
        $strQuery = str_replace(":lngsalesno", $value, $strQuery);                    //1:売上番号
        $strQuery = str_replace(":lngsalesdetailno", $value, $strQuery);              //2:売上明細番号
        $strQuery = str_replace(":lngrevisionno", $value, $strQuery);                 //3:リビジョン番号
        $strQuery = str_replace(":strproductcode", $value, $strQuery);                //4:製品コード
        $strQuery = str_replace(":strrevisecode", $value, $strQuery);                 //5:再販コード
        $strQuery = str_replace(":lngsalesclasscode", $value, $strQuery);             //6:売上区分コード
        $strQuery = str_replace(":lngconversionclasscode", $value, $strQuery);        //7:換算区分コード
        $strQuery = str_replace(":lngquantity", $value, $strQuery);                   //8:入数
        $strQuery = str_replace(":curproductprice", $value, $strQuery);               //9:製品価格
        $strQuery = str_replace(":lngproductquantity", $value, $strQuery);            //10:製品数量
        $strQuery = str_replace(":lngproductunitcode", $value, $strQuery);            //11:製品単位コード
        $strQuery = str_replace(":lngtaxclasscode", $value, $strQuery);               //12:消費税区分コード
        $strQuery = str_replace(":lngtaxcode", $value, $strQuery);                    //13:消費税率コード
        $strQuery = str_replace(":curtaxprice", $value, $strQuery);                   //14:消費税金額
        $strQuery = str_replace(":cursubtotalprice", $value, $strQuery);              //15:小計金額
        $strQuery = str_replace(":strnote", $value, $strQuery);                       //16:備考
        $strQuery = str_replace(":lngsortkey", $value, $strQuery);                    //17:表示用ソートキー
        $strQuery = str_replace(":lngreceiveno", $value, $strQuery);                  //18:受注番号
        $strQuery = str_replace(":lngreceivedetailno", $value, $strQuery);            //19:受注明細番号
        $strQuery = str_replace(":lngreceiverevisionno", $value, $strQuery);          //20:受注リビジョン番号
        
        // 登録実行
        if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
        {
            // 失敗
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

	// 成功
	return true;
}

// 納品伝票マスタ登録
function fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $aryHeader , $aryDetail, $objDB, $objAuth)
{
    // 登録クエリ作成
    $aryInsert = [];
    $aryInsert[] ="INSERT  ";
    $aryInsert[] ="INTO m_slip(  ";
    $aryInsert[] ="  lngslipno ";                        //1:納品伝票番号
    $aryInsert[] ="  , lngrevisionno ";                  //2:リビジョン番号
    $aryInsert[] ="  , strslipcode ";                    //3:納品伝票コード
    $aryInsert[] ="  , lngsalesno ";                     //4:売上番号
    $aryInsert[] ="  , strcustomercode ";                //5:顧客コード
    $aryInsert[] ="  , strcustomercompanyname ";         //6:顧客社名
    $aryInsert[] ="  , strcustomername ";                //7:顧客名
    $aryInsert[] ="  , strcustomeraddress1 ";            //8:顧客住所1
    $aryInsert[] ="  , strcustomeraddress2 ";            //9:顧客住所2
    $aryInsert[] ="  , strcustomeraddress3 ";            //10:顧客住所3
    $aryInsert[] ="  , strcustomeraddress4 ";            //11:顧客住所4
    $aryInsert[] ="  , strcustomerphoneno ";             //12:顧客電話番号
    $aryInsert[] ="  , strcustomerfaxno ";               //13:顧客FAX番号
    $aryInsert[] ="  , strcustomerusername ";            //14:顧客担当者名
    $aryInsert[] ="  , strshippercode ";                 //15:仕入先コード（出荷者）
    $aryInsert[] ="  , dtmdeliverydate ";                //16:納品日
    $aryInsert[] ="  , lngdeliveryplacecode ";           //17:納品場所コード
    $aryInsert[] ="  , strdeliveryplacename ";           //18:納品場所名
    $aryInsert[] ="  , strdeliveryplaceusername ";       //19:納品場所担当者名
    $aryInsert[] ="  , lngpaymentmethodcode ";           //20:支払方法コード
    $aryInsert[] ="  , dtmpaymentlimit ";                //21:支払期限
    $aryInsert[] ="  , lngtaxclasscode ";                //22:課税区分コード
    $aryInsert[] ="  , strtaxclassname ";                //23:課税区分
    $aryInsert[] ="  , curtax ";                         //24:消費税率
    $aryInsert[] ="  , strusercode ";                    //25:担当者コード
    $aryInsert[] ="  , strusername ";                    //26:担当者名
    $aryInsert[] ="  , curtotalprice ";                  //27:合計金額
    $aryInsert[] ="  , lngmonetaryunitcode ";            //28:通貨単位コード
    $aryInsert[] ="  , strmonetaryunitsign ";            //29:通貨単位
    $aryInsert[] ="  , dtminsertdate ";                  //30:作成日
    $aryInsert[] ="  , strinsertusercode ";              //31:入力者コード
    $aryInsert[] ="  , strinsertusername ";              //32:入力者名
    $aryInsert[] ="  , strnote ";                        //33:備考
    $aryInsert[] ="  , lngprintcount ";                  //34:印刷回数
    $aryInsert[] ="  , bytinvalidflag ";                 //35:無効フラグ
    $aryInsert[] =")  ";                                 
    $aryInsert[] ="VALUES (  ";                          
    $aryInsert[] ="  :lngslipno ";                       //1:納品伝票番号
    $aryInsert[] ="  , :lngrevisionno ";                 //2:リビジョン番号
    $aryInsert[] ="  , :strslipcode ";                   //3:納品伝票コード
    $aryInsert[] ="  , :lngsalesno ";                    //4:売上番号
    $aryInsert[] ="  , :strcustomercode ";               //5:顧客コード
    $aryInsert[] ="  , :strcustomercompanyname ";        //6:顧客社名
    $aryInsert[] ="  , :strcustomername ";               //7:顧客名
    $aryInsert[] ="  , :strcustomeraddress1 ";           //8:顧客住所1
    $aryInsert[] ="  , :strcustomeraddress2 ";           //9:顧客住所2
    $aryInsert[] ="  , :strcustomeraddress3 ";           //10:顧客住所3
    $aryInsert[] ="  , :strcustomeraddress4 ";           //11:顧客住所4
    $aryInsert[] ="  , :strcustomerphoneno ";            //12:顧客電話番号
    $aryInsert[] ="  , :strcustomerfaxno ";              //13:顧客FAX番号
    $aryInsert[] ="  , :strcustomerusername ";           //14:顧客担当者名
    $aryInsert[] ="  , :strshippercode ";                //15:仕入先コード（出荷者）
    $aryInsert[] ="  , :dtmdeliverydate ";               //16:納品日
    $aryInsert[] ="  , :lngdeliveryplacecode ";          //17:納品場所コード
    $aryInsert[] ="  , :strdeliveryplacename ";          //18:納品場所名
    $aryInsert[] ="  , :strdeliveryplaceusername ";      //19:納品場所担当者名
    $aryInsert[] ="  , :lngpaymentmethodcode ";          //20:支払方法コード
    $aryInsert[] ="  , :dtmpaymentlimit ";               //21:支払期限
    $aryInsert[] ="  , :lngtaxclasscode ";               //22:課税区分コード
    $aryInsert[] ="  , :strtaxclassname ";               //23:課税区分
    $aryInsert[] ="  , :curtax ";                        //24:消費税率
    $aryInsert[] ="  , :strusercode ";                   //25:担当者コード
    $aryInsert[] ="  , :strusername ";                   //26:担当者名
    $aryInsert[] ="  , :curtotalprice ";                 //27:合計金額
    $aryInsert[] ="  , :lngmonetaryunitcode ";           //28:通貨単位コード
    $aryInsert[] ="  , :strmonetaryunitsign ";           //29:通貨単位
    $aryInsert[] ="  , :dtminsertdate ";                 //30:作成日
    $aryInsert[] ="  , :strinsertusercode ";             //31:入力者コード
    $aryInsert[] ="  , :strinsertusername ";             //32:入力者名
    $aryInsert[] ="  , :strnote ";                       //33:備考
    $aryInsert[] ="  , :lngprintcount ";                 //34:印刷回数
    $aryInsert[] ="  , :bytinvalidflag ";                //35:無効フラグ
    $aryInsert[] =") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // TODO:文字列置換による疑似パラメータバインド
    $strQuery = str_replace(":lngslipno", $value, $strQuery);                        //1:納品伝票番号
    $strQuery = str_replace(":lngrevisionno", $value, $strQuery);                    //2:リビジョン番号
    $strQuery = str_replace(":strslipcode", $value, $strQuery);                      //3:納品伝票コード
    $strQuery = str_replace(":lngsalesno", $value, $strQuery);                       //4:売上番号
    $strQuery = str_replace(":strcustomercode", $value, $strQuery);                  //5:顧客コード
    $strQuery = str_replace(":strcustomercompanyname", $value, $strQuery);           //6:顧客社名
    $strQuery = str_replace(":strcustomername", $value, $strQuery);                  //7:顧客名
    $strQuery = str_replace(":strcustomeraddress1", $value, $strQuery);              //8:顧客住所1
    $strQuery = str_replace(":strcustomeraddress2", $value, $strQuery);              //9:顧客住所2
    $strQuery = str_replace(":strcustomeraddress3", $value, $strQuery);              //10:顧客住所3
    $strQuery = str_replace(":strcustomeraddress4", $value, $strQuery);              //11:顧客住所4
    $strQuery = str_replace(":strcustomerphoneno", $value, $strQuery);               //12:顧客電話番号
    $strQuery = str_replace(":strcustomerfaxno", $value, $strQuery);                 //13:顧客FAX番号
    $strQuery = str_replace(":strcustomerusername", $value, $strQuery);              //14:顧客担当者名
    $strQuery = str_replace(":strshippercode", $value, $strQuery);                   //15:仕入先コード（出荷者）
    $strQuery = str_replace(":dtmdeliverydate", $value, $strQuery);                  //16:納品日
    $strQuery = str_replace(":lngdeliveryplacecode", $value, $strQuery);             //17:納品場所コード
    $strQuery = str_replace(":strdeliveryplacename", $value, $strQuery);             //18:納品場所名
    $strQuery = str_replace(":strdeliveryplaceusername", $value, $strQuery);         //19:納品場所担当者名
    $strQuery = str_replace(":lngpaymentmethodcode", $value, $strQuery);             //20:支払方法コード
    $strQuery = str_replace(":dtmpaymentlimit", $value, $strQuery);                  //21:支払期限
    $strQuery = str_replace(":lngtaxclasscode", $value, $strQuery);                  //22:課税区分コード
    $strQuery = str_replace(":strtaxclassname", $value, $strQuery);                  //23:課税区分
    $strQuery = str_replace(":curtax", $value, $strQuery);                           //24:消費税率
    $strQuery = str_replace(":strusercode", $value, $strQuery);                      //25:担当者コード
    $strQuery = str_replace(":strusername", $value, $strQuery);                      //26:担当者名
    $strQuery = str_replace(":curtotalprice", $value, $strQuery);                    //27:合計金額
    $strQuery = str_replace(":lngmonetaryunitcode", $value, $strQuery);              //28:通貨単位コード
    $strQuery = str_replace(":strmonetaryunitsign", $value, $strQuery);              //29:通貨単位
    $strQuery = str_replace(":dtminsertdate", $value, $strQuery);                    //30:作成日
    $strQuery = str_replace(":strinsertusercode", $value, $strQuery);                //31:入力者コード
    $strQuery = str_replace(":strinsertusername", $value, $strQuery);                //32:入力者名
    $strQuery = str_replace(":strnote", $value, $strQuery);                          //33:備考
    $strQuery = str_replace(":lngprintcount", $value, $strQuery);                    //34:印刷回数
    $strQuery = str_replace(":bytinvalidflag", $value, $strQuery);                   //35:無効フラグ
        

    // 登録実行
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		// 失敗
		return false;
	}
	$objDB->freeResult( $lngResultID );

	// 成功
	return true;
}

// 納品伝票明細登録
function fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo, $aryHeader, $aryDetail, $objDB, $objAuth)
{
    for ( $i = $itemMinIndex; $i <= $itemMaxIndex; $i++ )
    {
        // 登録クエリ作成
        $aryInsert = [];
        $aryInsert[] ="INSERT  ";
        $aryInsert[] ="INTO t_slipdetail(  ";
        $aryInsert[] ="  lngslipno ";                      //1:納品伝票番号
        $aryInsert[] ="  , lngslipdetailno ";              //2:納品伝票明細番号
        $aryInsert[] ="  , lngrevisionno ";                //3:リビジョン番号
        $aryInsert[] ="  , strcustomersalescode ";         //4:顧客受注番号
        $aryInsert[] ="  , lngsalesclasscode ";            //5:売上区分コード
        $aryInsert[] ="  , strsalesclassname ";            //6:売上区分名
        $aryInsert[] ="  , strgoodscode ";                 //7:顧客品番
        $aryInsert[] ="  , strproductcode ";               //8:製品コード
        $aryInsert[] ="  , strrevisecode ";                //9:再販コード
        $aryInsert[] ="  , strproductname ";               //10:製品名
        $aryInsert[] ="  , strproductenglishname ";        //11:製品名（英語）
        $aryInsert[] ="  , curproductprice ";              //12:単価
        $aryInsert[] ="  , lngquantity ";                  //13:入数
        $aryInsert[] ="  , lngproductquantity ";           //14:数量
        $aryInsert[] ="  , lngproductunitcode ";           //15:製品単位コード
        $aryInsert[] ="  , strproductunitname ";           //16:製品単位名
        $aryInsert[] ="  , cursubtotalprice ";             //17:小計
        $aryInsert[] ="  , strnote ";                      //18:明細備考
        $aryInsert[] ="  , lngreceiveno ";                 //19:受注番号
        $aryInsert[] ="  , lngreceivedetailno ";           //20:受注明細番号
        $aryInsert[] ="  , lngreceiverevisionno ";         //21:受注リビジョン番号
        $aryInsert[] ="  , lngsortkey ";                   //22:表示用ソートキー
        $aryInsert[] =")  ";                               
        $aryInsert[] ="VALUES (  ";                        
        $aryInsert[] ="  :lngslipno ";                     //1:納品伝票番号
        $aryInsert[] ="  , :lngslipdetailno ";             //2:納品伝票明細番号
        $aryInsert[] ="  , :lngrevisionno ";               //3:リビジョン番号
        $aryInsert[] ="  , :strcustomersalescode ";        //4:顧客受注番号
        $aryInsert[] ="  , :lngsalesclasscode ";           //5:売上区分コード
        $aryInsert[] ="  , :strsalesclassname ";           //6:売上区分名
        $aryInsert[] ="  , :strgoodscode ";                //7:顧客品番
        $aryInsert[] ="  , :strproductcode ";              //8:製品コード
        $aryInsert[] ="  , :strrevisecode ";               //9:再販コード
        $aryInsert[] ="  , :strproductname ";              //10:製品名
        $aryInsert[] ="  , :strproductenglishname ";       //11:製品名（英語）
        $aryInsert[] ="  , :curproductprice ";             //12:単価
        $aryInsert[] ="  , :lngquantity ";                 //13:入数
        $aryInsert[] ="  , :lngproductquantity ";          //14:数量
        $aryInsert[] ="  , :lngproductunitcode ";          //15:製品単位コード
        $aryInsert[] ="  , :strproductunitname ";          //16:製品単位名
        $aryInsert[] ="  , :cursubtotalprice ";            //17:小計
        $aryInsert[] ="  , :strnote ";                     //18:明細備考
        $aryInsert[] ="  , :lngreceiveno ";                //19:受注番号
        $aryInsert[] ="  , :lngreceivedetailno ";          //20:受注明細番号
        $aryInsert[] ="  , :lngreceiverevisionno ";        //21:受注リビジョン番号
        $aryInsert[] ="  , :lngsortkey ";                  //22:表示用ソートキー
        $aryInsert[] =") ";

        // 文字列置換による疑似パラメータバインド
        $strQuery = str_replace(":lngslipno", $value, $strQuery);                  //1:納品伝票番号
        $strQuery = str_replace(":lngslipdetailno", $value, $strQuery);            //2:納品伝票明細番号
        $strQuery = str_replace(":lngrevisionno", $value, $strQuery);              //3:リビジョン番号
        $strQuery = str_replace(":strcustomersalescode", $value, $strQuery);       //4:顧客受注番号
        $strQuery = str_replace(":lngsalesclasscode", $value, $strQuery);          //5:売上区分コード
        $strQuery = str_replace(":strsalesclassname", $value, $strQuery);          //6:売上区分名
        $strQuery = str_replace(":strgoodscode", $value, $strQuery);               //7:顧客品番
        $strQuery = str_replace(":strproductcode", $value, $strQuery);             //8:製品コード
        $strQuery = str_replace(":strrevisecode", $value, $strQuery);              //9:再販コード
        $strQuery = str_replace(":strproductname", $value, $strQuery);             //10:製品名
        $strQuery = str_replace(":strproductenglishname", $value, $strQuery);      //11:製品名（英語）
        $strQuery = str_replace(":curproductprice", $value, $strQuery);            //12:単価
        $strQuery = str_replace(":lngquantity", $value, $strQuery);                //13:入数
        $strQuery = str_replace(":lngproductquantity", $value, $strQuery);         //14:数量
        $strQuery = str_replace(":lngproductunitcode", $value, $strQuery);         //15:製品単位コード
        $strQuery = str_replace(":strproductunitname", $value, $strQuery);         //16:製品単位名
        $strQuery = str_replace(":cursubtotalprice", $value, $strQuery);           //17:小計
        $strQuery = str_replace(":strnote", $value, $strQuery);                    //18:明細備考
        $strQuery = str_replace(":lngreceiveno", $value, $strQuery);               //19:受注番号
        $strQuery = str_replace(":lngreceivedetailno", $value, $strQuery);         //20:受注明細番号
        $strQuery = str_replace(":lngreceiverevisionno", $value, $strQuery);       //21:受注リビジョン番号
        $strQuery = str_replace(":lngsortkey", $value, $strQuery);                 //22:表示用ソートキー
                

        // 登録実行
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);
        if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
        {
            // 失敗
            return false;
        }
        $objDB->freeResult( $lngResultID );
    }

	// 成功
	return true;
}
// --------------------------------
// パラメータバインド用ヘルパ関数
// --------------------------------
// 直接置換
function bindDirect($parameterName, $bindValue, $strQuery)
{
    return str_replace($parameterName, $bindValue, $strQuery);
}
// シングルクォートで囲んでから置換
function bindWithQuote($parameterName, $bindValue, $strQuery)
{
    return str_replace($parameterName, "'".$bindValue."'", $strQuery);
}


function fncSample()
{
    // m_sales のシーケンス番号を取得
    //$sequence_m_sales = fncGetSequence( 'm_sales.lngSalesNo', $objDB );


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
