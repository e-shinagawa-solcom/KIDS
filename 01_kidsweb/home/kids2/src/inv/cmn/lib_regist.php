<?
// ----------------------------------------------------------------------------
/**
*       請求書管理  請求書関連関数群
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
*         ・請求書関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



/**
* 納品書検索の検索項目から一致する最新の納品伝票マスタデータを取得するSQL文の作成関数
*
*    納品書検索の検索項目から SQL文を作成する
*
*    @param  Array     $arySearchDataColumn     検索内容の配列
*    @param  bool      $renew                   更新時は売上マスタの登録は見ない
*    @param  Object    $objDB                   DBオブジェクト
*    @param    String    $strSlipCode            納品伝票コード    空白指定時:検索結果出力    納品伝票コード指定時:管理用、同じ納品書ＮＯの一覧取得
*    @param    Integer    $lngSlipNo                納品伝票番号    0:検索結果出力    納品伝票番号指定時:管理用、同じ納品伝票コードとする時の対象外納品伝票番号
*    @return Array     $strSQL 検索用SQL文 OR Boolean FALSE
*    @access public
*/
function fncGetSearchMSlipSQL ( $aryCondtition = array(), $renew = false, $objDB)
{
    // -----------------------------
    //  検索条件の動的設定
    // -----------------------------
    // 規定値
    // リビジョン番号
    $revisionNo = 1;


    $aryOutQuery = array();
//     //明細行NO
//     $aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
    //納品伝票番号
    $aryOutQuery[] = " SELECT ms.lngslipno ";
    //リビジョン番号
    $aryOutQuery[] = ",ms.lngrevisionno ";
    // 納品伝票コード
    $aryOutQuery[] = ", ms.strslipcode ";
    // 売上番号
    $aryOutQuery[] = ", ms.lngsalesno ";
    // 顧客コード
    $aryOutQuery[] = ", ms.lngcustomercode ";
    // 顧客名
    $aryOutQuery[] = ", ms.strcustomername ";
    // 売上番号
    $aryOutQuery[] = ", ms.lngsalesno ";
    // 顧客コード
    $aryOutQuery[] = ", ms.lngcustomercode ";
    // 表示用顧客コード
    $aryOutQuery[] = ", mc.strcompanydisplaycode ";
    // 顧客名
    $aryOutQuery[] = ", ms.strcustomername ";
    // 顧客担当者名
    $aryOutQuery[] = ", ms.strcustomerusername ";
    // 納品日
    $aryOutQuery[] = ", ms.dtmdeliverydate ";
    // 納品場所コード
    // $aryOutQuery[] = ", ms.lngdeliveryplacecode ";
    $aryOutQuery[] = ", mc2.strcompanydisplaycode as lngdeliveryplacecode";
    // 納品場所名
    $aryOutQuery[] = ", ms.strdeliveryplacename ";
    // 納品場所担当者名
    $aryOutQuery[] = ", ms.strdeliveryplaceusername ";
    // 支払期限
    $aryOutQuery[] = ", ms.dtmpaymentlimit ";
    // 課税区分コード]
    $aryOutQuery[] = ", ms.lngtaxclasscode ";
    // 課税区分
    $aryOutQuery[] = ", ms.strtaxclassname ";
    // 消費税率
    $aryOutQuery[] = ", ms.curtax ";
    // 担当者コード
    $aryOutQuery[] = ", ms.lngusercode ";
    // 担当者名
    $aryOutQuery[] = ", ms.strusername ";
    // 合計金額
    $aryOutQuery[] = ", ms.curtotalprice";
    // 通貨単位コード
    $aryOutQuery[] = ", ms.lngmonetaryunitcode ";
    // 通貨単位
    $aryOutQuery[] = ", ms.strmonetaryunitsign ";
    // 作成日
    $aryOutQuery[] = ", ms.dtminsertdate ";
    // 入力者コード
    $aryOutQuery[] = ", ms.lnginsertusercode ";
    // 入力者名
    $aryOutQuery[] = ", ms.strinsertusername ";
    // 備考
    $aryOutQuery[] = ", ms.strnote ";
    // 印刷回数
    $aryOutQuery[] = ", ms.lngprintcount ";
    // 無効フラグ
    $aryOutQuery[] = ", ms.bytinvalidflag ";
    // From句
    $aryOutQuery[] = " FROM m_slip ms ";
    $aryOutQuery[] = " INNER JOIN (select lngslipno, MAX(lngrevisionno) as lngrevisionno from m_slip group by lngslipno)";
    $aryOutQuery[] = " rev_max on rev_max.lngslipno = ms.lngslipno and rev_max.lngrevisionno = ms.lngrevisionno";
    // JOIN
    $aryOutQuery[] = " LEFT JOIN m_company mc ON mc.lngcompanycode = ms.lngcustomercode ";
    $aryOutQuery[] = " LEFT JOIN m_company mc2 ON mc2.lngcompanycode = ms.lngdeliveryplacecode ";
    $aryOutQuery[] = " LEFT JOIN m_user mu1 ON mu1.lngusercode = ms.lngusercode ";
    $aryOutQuery[] = " LEFT JOIN m_user mu2 ON mu2.lngusercode = ms.lnginsertusercode ";

    // Where句
    $aryOutQuery[] = " WHERE not exists (select lngslipno from m_slip ms1 where ms1.lngslipno = ms.lngslipno and lngslipno < 0) " ; // 削除済みは対象外

    foreach($aryCondtition as $column => $value) {
        $value = trim($value);
        if(empty($value)) {
            continue;
        }

        // 顧客コード(表示用)
        if($column == 'customerCode') {
            $aryOutQuery[] = " AND mc.strcompanydisplaycode = '" .$value ."' " ;
        }

        // 顧客名
/*
        if($column == 'customerName') {
            $aryOutQuery[] = " AND strcustomername LIKE '%" .$value ."%' " ;
        }
*/
        // 納品書番号
        if($column == 'strSlipCode') {
            // カンマ区切りの入力値をOR条件に展開
            $arySCValue = preg_split('/[,\s]/', $value);
            foreach($arySCValue as $strSCValue){
                if(empty(trim($strSCValue)))
                    continue;

                    $arySCOr[] = "UPPER(strslipcode) LIKE UPPER('%" . trim($strSCValue) . "%')";
            }
            $aryOutQuery[] = " AND (";
            $aryOutQuery[] = implode(" OR ", $arySCOr);
            $aryOutQuery[] = ") ";
        }

        // 納品日 FROM
        if($column == 'deliveryFrom') {
            $aryOutQuery[] = " AND dtmdeliverydate >= '" .$value ." 00:00:00" ."' " ;
        }

        // 納品日 To
        if($column == 'deliveryTo') {
            $aryOutQuery[] = " AND dtmdeliverydate <= '" .$value ." 23:59:59" ."' " ;
        }

        // 納品場所コード
        if($column == 'deliveryPlaceCode') {
            $aryOutQuery[] = " AND ms.lngdeliveryplacecode = ( SELECT lngcompanycode FROM m_company WHERE strcompanydisplaycode = '" .$value ."') ";
        }
        // 納品場所名
        if($column == 'deliveryPlaceName') {
            $aryOutQuery[] = " AND ms.strdeliveryplacename LIKE '%" .$value ."%' " ;
        }
        // 通貨
        if($column == 'moneyClassCode') {
            $aryOutQuery[] = " AND lngmonetaryunitcode = " .$value ." " ;
        }

        // 課税区分
        if($column == 'taxClassCode') {
            $aryOutQuery[] = " AND lngtaxclasscode = " .$value ." " ;
        }

        // 担当者コード
        if($column == 'inChargeUserCode') {
            $aryOutQuery[] = " AND mu1.struserdisplaycode LIKE '%" .$value ."%' " ;
        }

        // 担当者
        if($column == 'inChargeUserName') {
            $aryOutQuery[] = " AND strusername LIKE '%" .$value ."%' " ;
        }

        // 作成者コード
        if($column == 'inputUserCode') {
            $aryOutQuery[] = " AND mu2.struserdisplaycode LIKE '%" .$value ."%' " ;
        }

        // 作成者
        if($column == 'inputUserName') {
            $aryOutQuery[] = " AND strinsertusername LIKE '%" .$value ."%' " ;
        }
    }

    if($renew == false)
    {
        $aryOutQuery[] = " AND  ms.lngsalesno NOT IN( SELECT m_sales.lngsalesno FROM m_sales WHERE lnginvoiceno IS NOT NULL) " ;
    }

    // OrderBy句
    $aryOutQuery[] = " ORDER BY ms.lngslipno ASC , ms.lngrevisionno DESC ";


    return implode("\n", $aryOutQuery);
}



/**
 * 請求書番号から請求書明細に紐づく納品伝票マスタデータを取得するSQL文の作成関数
 *
 *
 *    @param    Integer    $lnginvoiceno            請求書No
 *    @return Array     $strSQL 検索用SQL文 OR Boolean FALSE
 *    @access public
 */
function fncGetSearchMSlipInvoiceNoSQL ( $lnginvoiceno, $lngrevisionno )
{

    $aryOutQuery = array();
    //納品伝票番号
    $aryOutQuery[] = " SELECT DISTINCT ON (ms.lngSlipNo) ms.lngSlipNo ";
    //リビジョン番号
    $aryOutQuery[] = ", ms.lngrevisionno ";
    // 納品伝票コード
    $aryOutQuery[] = ", ms.strslipcode ";
    // 売上番号
    $aryOutQuery[] = ", ms.lngsalesno ";
    // 顧客コード
    $aryOutQuery[] = ", ms.lngcustomercode ";
    // 表示用顧客コード
    $aryOutQuery[] = ", mc.strcompanydisplaycode ";
    // 顧客名
    $aryOutQuery[] = ", ms.strcustomername ";
    // 顧客担当者名
    $aryOutQuery[] = ", ms.strcustomerusername ";
    // 納品日
    $aryOutQuery[] = ", ms.dtmdeliverydate ";
    // 納品場所コード
    $aryOutQuery[] = ", mc2.strcompanydisplaycode as lngdeliveryplacecode ";
    // 納品場所名
    $aryOutQuery[] = ", ms.strdeliveryplacename ";
    // 納品場所担当者名
    $aryOutQuery[] = ", ms.strdeliveryplaceusername ";
    // 支払期限
    $aryOutQuery[] = ", ms.dtmpaymentlimit ";
    // 課税区分コード]
    $aryOutQuery[] = ", ms.lngtaxclasscode ";
    // 課税区分
    $aryOutQuery[] = ", ms.strtaxclassname ";
    // 消費税率
    $aryOutQuery[] = ", ms.curtax ";
    // 担当者コード
    $aryOutQuery[] = ", ms.lngusercode ";
    // 担当者名
    $aryOutQuery[] = ", ms.strusername ";
    // 合計金額
    $aryOutQuery[] = ", ms.curtotalprice ";
    // 通貨単位コード
    $aryOutQuery[] = ", ms.lngmonetaryunitcode ";
    // 通貨単位
    $aryOutQuery[] = ", ms.strmonetaryunitsign ";
    // 作成日
    $aryOutQuery[] = ", ms.dtminsertdate ";
    // 入力者コード
    $aryOutQuery[] = ", ms.lnginsertusercode ";
    // 入力者名
    $aryOutQuery[] = ", ms.strinsertusername ";
    // 備考
    $aryOutQuery[] = ", ms.strnote ";
    // 印刷回数
    $aryOutQuery[] = ", ms.lngprintcount ";
    // 無効フラグ
    $aryOutQuery[] = ", ms.bytinvalidflag ";
    // From句
    $aryOutQuery[] = " FROM m_slip ms ";
    // JOIN
    $aryOutQuery[] = " LEFT JOIN m_company mc ON (mc.lngcompanycode = ms.lngcustomercode ) ";
    $aryOutQuery[] = " LEFT JOIN m_company mc2 ON (mc2.lngcompanycode = ms.lngdeliveryplacecode ) ";

    // Where句
    // strslipcode を検索するサブクエリ
    $subQuery  = "select DISTINCT lngslipno from t_invoicedetail where lnginvoiceno = "  .$lnginvoiceno ."  ";
    $subQuery .= "AND lngrevisionno = " .$lngrevisionno. " ";

    $aryOutQuery[] = " WHERE lngrevisionno >= 0 " ;    // 対象納品伝票番号の指定
    $aryOutQuery[] = " AND ms.lngslipno IN ( " .$subQuery ." ) " ;

    // order句
    $aryOutQuery[] = " ORDER BY  ms.lngSlipNo ASC , ms.lngrevisionno DESC " ;

    // クエリを平易な文字列に変換
    $query = implode("\n",$aryOutQuery);

    return $query;

}



/**
 * 指定した請求書番号から請求書コードを取得する(なければコードを生成)
 *
 *
 *    @param  String    $lnginvoiceno     請求書番号
 *    @param  date      $isDummy          true  : シーケンス未発行
 *                                         false : シーケンス発行
 *    @param  Object    $objDB            DBオブジェクト
 *    @return str       strinvoicecode    TT-BMMnnn
 *                                         TT：期（4/1にカウントアップ。2019年度は42期）
 *                                         MM：月(01~12）
 *                                         nnn：月内での連番（001~999）を自動採番
 *    @access public
 */
function fncGetStrInvoiceCode( $lnginvoiceno = null, $isDummy=true , $objDB )
{
    // 登録済なら登録されているコードを返す
    if ( !empty($lnginvoiceno) )
    {
        $strQuery = " SELECT DISTINCT strinvoicecode FROM m_invoice WHERE lnginvoiceno = " . $lnginvoiceno . " ";

        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
        // レコードがあればコードを返す
        if ( $lngResultNum )
        {
            $result = pg_fetch_assoc($lngResultID);
            $objDB->freeResult($lngResultID);
            return $result['strinvoicecode'];
        }
        $objDB->freeResult($lngResultID);
    }

    // 請求書番号(strinvoicecode)を発行
    $format = '%02d-B%02d%03d';
    // 期取得
    $basePeriod = 42;
    $baseDate   = '2019-04-01';

    $dateTimeBase = new DateTime($baseDate);
    $dateTimeNow  = new DateTime(date('Y-m-d'));
    $diff   = $dateTimeBase->diff($dateTimeNow);
    $period = $basePeriod + (int)$diff->format('%Y');
    $thisMonth = $dateTimeNow->format('m');

    // dummyの処理(無駄なシーケンス発行を防ぐ)
    if($isDummy)
    {
        // 当月内に発行された請求書カウントを取得する
        $start = date('Y-m-01', strtotime('first day of ' .$dateTimeNow->format('Y-m-d')));
        $end   = date('Y-m-01', strtotime($start. '+1 month'));
        $strQuery = "SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE dtminsertdate >= '" . $start . "' AND dtminsertdate < '" . $end . "' " ;

        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
        // 件数が欲しいので結果はなくても問題ない
        $num = $lngResultNum+1;
        // formatに従って請求書コードを返す
        $strinvoicecode = sprintf($format, $period, $thisMonth, $num );
    }
    else
    {
        // シーケンス発行
        $sequenceInvoiceCode = fncGetDateSequence($period, $thisMonth, 'm_invoice.strinvoicecode', $objDB);
        // formatに従って請求書コードを返す
        $strinvoicecode = sprintf($format, substr($sequenceInvoiceCode,0,2), substr($sequenceInvoiceCode,2,2), substr($sequenceInvoiceCode,5,3) );
    }

    return $strinvoicecode;
}

/**
 * 指定した表示用顧客コードから 顧客名/顧客社名を返す
 *
 *
 *    @param  String    $companyDisplayCode  表示用顧客コード
 *    @param  Object    $objDB               DBオブジェクト
 *    @return int       $lngCustomerCode     顧客コード
 *    @return string    printCustomerName    表示用顧客名
 *    @return string    printCompanyName     表示用顧客社名
 *    @return string    $result['strcompanydisplayname']    DBの表示用会社名称
 *    @access public
 */
function fncGetCompanyPrintName( $companyDisplayCode ,$objDB)
{
    $printCustomerName = $printCompanyName = $lngCustomerCode= "";
    if( empty($companyDisplayCode) )
    {
        return [$printCustomerName, $printCompanyName, $lngCustomerCode, null];
    }

    $strQuery = [];
    $strQuery[] = "select ";
    $strQuery[] = "mc.lngcompanycode ,";
    $strQuery[] = "mc.strcompanyname ,";
    $strQuery[] = "mc.bytcompanydisplayflag ,";
    $strQuery[] = "mc.bytorganizationfront ,";
    $strQuery[] = "mc.strcompanydisplaycode ,";
    $strQuery[] = "mc.strcompanydisplayname ,";
    $strQuery[] = "mcp.strprintcompanyname , ";
    $strQuery[] = "oz.strorganizationname";
    $strQuery[] = "FROM m_company mc ";
    $strQuery[] = "LEFT JOIN m_companyprintname mcp";
    $strQuery[] = "ON (mc.lngcompanycode = mcp.lngcompanycode) ";
    $strQuery[] = "LEFT JOIN m_organization oz";
    $strQuery[] = "ON(mc.lngorganizationcode = oz.lngorganizationcode)";
    $strQuery[] = "WHERE mc.strcompanydisplaycode = '"  .$companyDisplayCode ."' ";

    // クエリを平易な文字列に変換
    $query = implode("\n",$strQuery);

    // クエリ実行
    list ( $lngResultID, $lngResultNum ) = fncQuery( $query, $objDB );
    // レコードあれば顧客名・顧客社名を設定する
    if ( $lngResultNum )
    {
        // 検索結果連想配列を取得
        $result =  pg_fetch_assoc($lngResultID);
        $lngCustomerCode = $result['lngcompanycode'];

        if( !empty($result['strprintcompanyname']) )
        {
            // 印字用会社マスタ.印字用会社名がみつかった場合
            // 顧客社名
            $printCompanyName  = $result['strprintcompanyname'];
            // 顧客名
            $printCustomerName = $result['strcompanydisplayname'];
        }
        else
       {
           $organizationName = ($result['strorganizationname'] == '-') ? '' : $result['strorganizationname'];
            // 顧客社名
            if($records['bytorganizationfront'] == 't')
            {
                $printCompanyName = $organizationName .$result['strcompanydisplayname'];
            }
            else

           {
               $printCompanyName  = $result['strcompanydisplayname'] .$organizationName;
            }
            // 顧客名(設定しない)
            $printCustomerName = "";
        }
        $objDB->freeResult($lngResultID);
    }

    return [ $printCustomerName, $printCompanyName, $lngCustomerCode, $result['strcompanydisplayname'] ];
}


/**
 * 指定した表示用顧客コード・日付から 締め日を返す
 *
 *
 *    @param  String    $companyDisplayCode  表示用顧客コード
 *    @param  date      $targetDate          指定日(なければシステムDATE)
 *    @param  Object    $objDB               DBオブジェクト
 *    @return date      $closedDay           締め日
 *    @access public
 */
function fncGetCompanyClosedDay($companyDisplayCode , $targetDate=null, $objDB)
{
    $closedDay = null;
    if( empty($companyDisplayCode) )
    {
        return false;
    }
    if(empty($targetDate)) {
        $targetDate = date('Y-m-d');
    }

    $strQuery = [];
    $strQuery[] = "select ";
    $strQuery[] = "close.strcloseddaycode ,";
    $strQuery[] = "close.lngclosedday ";
    $strQuery[] = "FROM m_company mc ";
    $strQuery[] = "LEFT JOIN m_closedday close ";
    $strQuery[] = "ON (mc.lngcloseddaycode = close.lngcloseddaycode) ";
    $strQuery[] = "WHERE mc.strcompanydisplaycode = '"  .$companyDisplayCode ."' ";

    // クエリを平易な文字列に変換
    $query = implode("\n",$strQuery);
    // クエリ実行
    list ( $lngResultID, $lngResultNum ) = fncQuery( $query, $objDB );

    // レコードあれば顧客名・顧客社名を設定する
    if ( $lngResultNum )
    {
        // 検索結果連想配列を取得
        $result = pg_fetch_assoc($lngResultID);
        $lngClosedDay = (int)$result['lngclosedday'];
        if($lngClosedDay < 0){
            return $closedDay;
        }
        // 日付の比較
        $dateTime = new DateTime($targetDate);

        if( $lngClosedDay == 0 )
        {
        	$year = (int)($dateTime->format('Y'));
        	$month = (int)($dateTime->format('m'));
            $dateTime->setDate($year,$month,1);
            $dateTime->add(DateInterval::createFromDateString('1 month'));
            $dateTime->add(DateInterval::createFromDateString('-1 day'));
        }
        else
        {
            $day = (int)$dateTime->format('d');
            if($day > $lngClosedDay)
            {
                // 来月
                $dateTime->add(DateInterval::createFromDateString('1 month'));
            }
        }
        $objDB->freeResult($lngResultID);
        $closedDay = $dateTime->format('Y-m-').$lngClosedDay;
    }
    return $closedDay;
}



/**
 * 指定した通貨単位コードから 通貨単位名を返す
 *
 *
 *    @param  int      $monetaryUnitCode  通貨単位コード
 *    @param  Object   $objDB               DBオブジェクト
 *    @return string   $monetaryUnitSign    通貨単位名
 *    @access public
 */
function fncGetMonetaryunitSign( $monetaryUnitCode ,$objDB)
{
    // 通貨単位マスタを取得
    $query = "SELECT strmonetaryunitsign FROM m_monetaryunit WHERE lngmonetaryunitcode = " .$monetaryUnitCode;

    // クエリ実行
    list ( $lngResultID, $lngResultNum ) = fncQuery( $query, $objDB );
    // レコードあれば通貨単位名称を設定する
    $monetaryUnitSign = "";
    if ( $lngResultNum )
    {
        // 検索結果連想配列を取得
        $result =  pg_fetch_assoc($lngResultID);
        $monetaryUnitSign = $result['strmonetaryunitsign'];
    }
    return $monetaryUnitSign;

}


/**
 * 請求書検索の検索項目から一致する最新の請求書データを取得するSQL文の作成関数
 *
 *    請求書検索の検索項目から SQL文を作成する
 *
 *    @param  Array     $arySearchColumn         検索対象カラム名の配列
 *    @param  Array     $arySearchDataColumn     検索内容の配列
 *    @param  Object    $objDB                   DBオブジェクト
 *    @return Array     $strSQL 検索用SQL文 OR Boolean FALSE
 *    @access public
 */
function fncGetSearchInvoiceSQL ( $arySearchColumn, $arySearchDataColumn, $objDB, $strSessionID)
{
    // -----------------------------
    //  検索条件の動的設定
    // -----------------------------
    // 明細条件追加済みフラグ
    $detailFlag = FALSE;

    // 同じ納品伝票コードのデータを取得する場合
    if ( $strSlipCode )
    {
        // 同じ納品伝票コードに対して指定の納品伝票番号のデータは除外する
        if ( $lngSlipNo )
        {
            $aryQuery[] = " WHERE inv.bytInvalidFlag = FALSE AND s.strSlipCode = '" . $strSlipCode . "'";
        }
        else
        {
            fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../inv/search/index.php?strSessionID=".$strSessionID, $objDB );
        }
    }
    // 管理モードでの同じ納品伝票コードに対する検索モード以外の場合は検索条件を追加する
    else
    {
        // 絶対条件 無効フラグが設定されておらず、最新売上のみ
        $aryQuery[] = " WHERE inv.bytinvalidflag = FALSE AND inv.lngrevisionno >= 0";
        // 検索チェックボックスがONの項目のみ検索条件に追加
        for ( $i = 0; $i < count($arySearchColumn); $i++ )
        {
            $strSearchColumnName = $arySearchColumn[$i];

            // ----------------------------------------------
            //   納品書マスタ（ヘッダ部）の検索条件
            // ----------------------------------------------
            // 顧客（売上先）
            if ( $strSearchColumnName == "lngCustomerCompanyCode" )
            {
                if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
                {
                    $aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'";
                }
                if ( $arySearchDataColumn["strCustomerCompanyName"] )
                {
                    $aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerCompanyName"] . "%')";
                }
            }

            // 課税区分（消費税区分）
            if ( $strSearchColumnName == "lngTaxClassCode" )
            {
                if ( $arySearchDataColumn["lngTaxClassCode"] )
                {
                    $aryQuery[] = " AND inv.lngtaxclasscode = '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
                }
            }

            // 納品伝票コード（納品書NO）請求書明細テーブルから引く
            if ( $strSearchColumnName == "strInvoiceCode" )
            {
                if ( $arySearchDataColumn["strInvoiceCode"] )
                {
                    // カンマ区切りの入力値をOR条件に展開
                    $arySCValue = preg_split('/[,\s]/', $arySearchDataColumn["strInvoiceCode"]);
                    foreach($arySCValue as $strSCValue){
                        if(empty(trim($strSCValue)))
                            continue;

                        $arySCOr[] = "UPPER(inv.strinvoicecode) LIKE UPPER('%" . trim($strSCValue) . "%')";
                    }
                    $aryQuery[] = " AND (";
                    $aryQuery[] = implode(" OR ", $arySCOr);
                    $aryQuery[] = ") ";
                }
            }

            // 請求日
            if ( $strSearchColumnName == "dtmInvoiceDate" )
            {
                if ( $arySearchDataColumn["dtmInvoiceDateFrom"] )
                {
                    $dtmSearchDate = $arySearchDataColumn["dtmInvoiceDateFrom"] . " 00:00:00";
                    $aryQuery[] = " AND inv.dtminvoicedate >= '" . $dtmSearchDate . "'";
                }
                if ( $arySearchDataColumn["dtmInvoiceDateTo"] )
                {
                    $dtmSearchDate = $arySearchDataColumn["dtmInvoiceDateTo"] . " 23:59:59";
                    $aryQuery[] = " AND inv.dtminvoicedate <= '" . $dtmSearchDate . "'";
                }
            }

            // 入力日
            if ( $strSearchColumnName == "dtmInsertDate" )
            {
                if ( $arySearchDataColumn["dtmInsertDateFrom"] )
                {
                    $dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
                    $aryQuery[] = " AND inv.dtminsertdate >= '" . $dtmSearchDate . "'";
                }
                if ( $arySearchDataColumn["dtmInsertDateTo"] )
                {
                    $dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
                    $aryQuery[] = " AND inv.dtminsertdate <= '" . $dtmSearchDate . "'";
                }
            }

//             // 納品先
//             if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
//             {
//                 if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
//                 {
//                     //会社マスタと紐づけた値と比較
//                     $aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
//                 }
//                 if ( $arySearchDataColumn["strDeliveryPlaceName"] )
//                 {
//                     $aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//                 }
//             }

            // 起票者
            if ( $strSearchColumnName == "lngInsertUserCode" )
            {
                if ( $arySearchDataColumn["lngInsertUserCode"] )
                {
                    $aryQuery[] = " AND u.struserdisplaycode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
                }
                if ( $arySearchDataColumn["strInsertUserName"] )
                {
                    $aryQuery[] = " AND UPPER(inv.strusername) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
                }
            }
            // 入力者
            if ( $strSearchColumnName == "lngInputUserCode" )
            {
                if ( $arySearchDataColumn["lngInputUserCode"] )
                {
                    $aryQuery[] = " AND insert_u.struserdisplaycode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'";
                }
                if ( $arySearchDataColumn["strInputUserName"] )
                {
                    $aryQuery[] = " AND UPPER(inv.strinsertusername) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')";
                }
            }

            // ----------------------------------------------
            //   納品伝票明細テーブル（明細部）の検索条件
            // ----------------------------------------------
            // 注文書NO.
            if ( $strSearchColumnName == "strCustomerSalesCode" )
            {
                if ( $arySearchDataColumn["strCustomerSalesCode"] )
                {
                    if ( !$detailFlag )
                    {
                        $aryDetailTargetQuery[] = " where";
                    }
                    else
                    {
                        unset( $aryDetailTargetQuery );
                        $aryDetailTargetQuery[] = " where";

                        $aryDetailWhereQuery[] = "AND ";
                    }

                    // カンマ区切りの入力値をOR条件に展開
                    $aryCSCValue = explode(",",$arySearchDataColumn["strCustomerSalesCode"]);
                    foreach($aryCSCValue as $strCSCValue){
                        $aryCSCOr[] = "UPPER(sd1.strCustomerSalesCode) LIKE UPPER('%" . $strCSCValue . "%')";
                    }
                    $aryDetailWhereQuery[] = " (";
                    $aryDetailWhereQuery[] = implode(" OR ", $aryCSCOr);
                    $aryDetailWhereQuery[] = ") ";

                    $detailFlag = TRUE;
                }
            }


            // 売上区分
            if ( $strSearchColumnName == "lngSalesClassCode" )
            {
                if ( $arySearchDataColumn["lngSalesClassCode"] )
                {
                    if ( !$detailFlag )
                    {
                        $aryDetailTargetQuery[] = " where";
                    }
                    else
                    {
                        $aryDetailWhereQuery[] = "AND ";
                    }
                    $aryDetailWhereQuery[] = "sd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
                    $detailFlag = TRUE;
                }
            }
        }
    }

    // ---------------------------------
    //   SQL文の作成
    // ---------------------------------
    $aryOutQuery = array();
    $aryOutQuery[] = "SELECT distinct inv.lnginvoiceno as lnginvoiceno";    // 請求書番号番号
    $aryOutQuery[] = "    ,inv.lngrevisionno as lngrevisionno";                // リビジョン番号
    $aryOutQuery[] = "    ,inv.lnginvoiceno as lngpkno";                // 請求書番号番号
    $aryOutQuery[] = "    ,inv.dtminsertdate as dtminsertdate";                // 作成日

    // 顧客
    $arySelectQuery[] = ", cust_c.strcompanydisplaycode as strcustomerdisplaycode";               // 顧客コード
    $arySelectQuery[] = ", inv.strcustomername as strcustomername";               // 顧客名
    $arySelectQuery[] = ", inv.strcustomercompanyname as strcustomerdisplayname"; // 顧客社名
    // 顧客の国
    $arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
    // 請求書コード
    $arySelectQuery[] = ", inv.strinvoicecode as strinvoicecode";
    // 請求日
    $arySelectQuery[] = ", to_char( inv.dtminvoicedate, 'YYYY/MM/DD' ) as dtminvoicedate";
    // 請求期間 自
    $arySelectQuery[] = ", to_char( inv.dtmchargeternstart, 'YYYY/MM/DD' ) as dtmchargeternstart";
    // 請求期間 至
    $arySelectQuery[] = ", to_char( inv.dtmchargeternend, 'YYYY/MM/DD' ) as dtmchargeternend";
    // 前月請求残額
    $arySelectQuery[] = ", To_char( inv.curlastmonthbalance, '9,999,999,990.99' ) as curlastmonthbalance";
    // 御請求金額
    $arySelectQuery[] = ", To_char( inv.curthismonthamount, '9,999,999,990.99' ) as curthismonthamount";
    // 通貨単位コード
    $arySelectQuery[] = ", inv.lngmonetaryunitcode as lngmonetaryunitcode";
    // 通貨単位
    $arySelectQuery[] = ", inv.strmonetaryunitsign as strmonetaryunitsign";
    // 課税区分コード
    $arySelectQuery[] = ", inv.lngtaxclasscode as lngtaxclasscode";
    // 課税区分名
    $arySelectQuery[] = ", inv.strtaxclassname as strtaxclassname";
    // 税抜金額1
    $arySelectQuery[] = ", To_char( inv.cursubtotal1, '9,999,999,990.99' ) as cursubtotal";
    // 消費税率1
    $arySelectQuery[] = ", inv.curtax1 as curtax";
    // 消費税額1
    $arySelectQuery[] = ", To_char( inv.curtaxprice1, '9,999,999,990.99' ) as curtaxprice";
    // 担当者
    $arySelectQuery[] = ", u.struserdisplaycode as strusercode";
    $arySelectQuery[] = ", inv.strusername as strusername";
    // 作成者
    $arySelectQuery[] = ", insert_u.struserdisplaycode as strinsertusercode";
    $arySelectQuery[] = ", inv.strinsertusername as strinsertusername";
    // 作成日
    $arySelectQuery[] = ", to_char( inv.dtminsertdate, 'YYYY/MM/DD' ) as dtminsertdate";
    // 備考
    $arySelectQuery[] = ", inv.strnote as strnote";
    // 但し書き
    $arySelectQuery[] = ", inv.description as description";
    // 印刷回数
    $arySelectQuery[] = ", inv.lngprintcount as lngprintcount";


//     // 課税区分
//     $arySelectQuery[] = ", s.strtaxclassname as strtaxclassname";
//     // 納品伝票コード（納品書NO）
//     $arySelectQuery[] = ", s.strSlipCode as strSlipCode";
//     // 納品先
//     $arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
//     // 合計金額
//     $arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
    //// 売上Ｎｏ
    //$arySelectQuery[] = ", s.strSalesCode as strSalesCode";
    // 売上状態コード
    $arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
    $arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
//     // 通貨単位
//     $arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";

    // select句 クエリー連結
    $aryOutQuery[] = implode("\n", $arySelectQuery);

    // From句 の生成
    $aryFromQuery = array();
    $aryFromQuery[] = " FROM m_invoice inv";
    $aryFromQuery[] = " LEFT JOIN m_sales sa ON inv.lnginvoiceno = sa.lnginvoiceno";
    $aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";
    $aryFromQuery[] = " LEFT JOIN m_Company cust_c ON inv.lngcustomercode = cust_c.lngcompanycode";
    $aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON inv.lngmonetaryunitcode = mu.lngMonetaryUnitCode";
    $aryFromQuery[] = " LEFT JOIN m_User insert_u ON inv.lngInsertUserCode = insert_u.lngusercode";
    $aryFromQuery[] = " LEFT JOIN m_User u ON inv.lngusercode = u.lngusercode";
    // $aryFromQuery[] = " LEFT JOIN m_Company delv_c ON inv.strcustomercode = delv_c.strcompanydisplaycode";
    //　請求書明細がない場合は不整合の為除外
    $aryFromQuery[] = " INNER JOIN t_invoicedetail inv_d ON inv.lnginvoiceno = inv_d.lnginvoiceno";

    // From句 クエリー連結
    $aryOutQuery[] = implode("\n", $aryFromQuery);


    // Where句 クエリー連結
//     $aryOutQuery[] = $strDetailQuery;
    $aryOutQuery[] = implode("\n", $aryQuery);

//     // 明細行用の条件連結
//     $aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


    /////////////////////////////////////////////////////////////
    //// 最新売上（リビジョン番号が最大、リバイズ番号が最大、     ////
    //// かつリビジョン番号負の値で無効フラグがFALSEの           ////
    //// 同じ納品伝票コードを持つデータが無い売上データ          ////
    /////////////////////////////////////////////////////////////
    $aryOutQuery[] = " AND inv.lngrevisionno = ( "
                    . "SELECT MAX( inv1.lngrevisionno ) FROM m_invoice inv1 WHERE inv1.strinvoicecode = inv.strinvoicecode AND inv1.bytinvalidflag = false )";

    // 削除データは対象外
    $aryOutQuery[] = " AND 0 <= ( "
            . "SELECT MIN( inv2.lngrevisionno ) FROM m_invoice inv2 WHERE inv2.bytinvalidflag = false AND inv2.strinvoicecode = inv.strinvoicecode )";

    // ソート条件設定
    $aryOutQuery[] = " ORDER BY inv.lnginvoiceno DESC";

    return implode("\n", $aryOutQuery);
}



/**
* 請求書番号から請求書マスタに紐づく請求祖明細を取得するSQL文の作成関数
*
*
*    @param    Integer    $lnginvoiceno            請求書No
*    @param    Integer    $lngrevisionno         リビジョンNo
*    @return Array     $strSQL 検索用SQL文 OR Boolean FALSE
*    @access public
*/
function fncGetSearchInvoiceDetailSQL ( $lnginvoiceno, $lngrevisionno=null )
{
    // -----------------------------
    //  検索条件の動的設定
    // -----------------------------

    // 絶対条件 無効フラグが設定されておらず、最新請求書明細のみ
    $aryQuery[] = " WHERE inv_d.lngrevisionno >= 0 ";
    $aryQuery[] = " AND  inv_d.lnginvoiceno = " . (int)$lnginvoiceno ." ";
    
    if($lngrevisionno !== null){
        $aryQuery[] = " AND  inv_d.lngrevisionno = " . (int)$lngrevisionno ." ";
    } else {
        $aryQuery[] = " AND  inv_d.lngrevisionno = (SELECT MAX(inv.lngrevisionno) FROM m_invoice inv WHERE inv.lnginvoiceno = " . (int)$lnginvoiceno .") ";
    }
    // 削除済みは排除
    $aryQuery[] = " AND inv_d.lnginvoiceno NOT IN ( ";
    $aryQuery[] = " SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE lngrevisionno = -1";
    $aryQuery[] = " ) ";

    // order by
    $aryQuery[] = " ORDER BY inv_d.lnginvoicedetailno ASC";

    //     $aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCode"] . "'";


    // ---------------------------------
    //   SQL文の作成
    // ---------------------------------
    $aryOutQuery = array();
    // 請求書番号
    $arySelectQuery[] = "SELECT inv_d.lnginvoiceno as lnginvoiceno";
    // リビジョン番号
    $arySelectQuery[] = ", inv_d.lngrevisionno as lngrevisionno";
    // 請求書明細番号
    $arySelectQuery[] = ", inv_d.lnginvoicedetailno";
    // 納品日
    $arySelectQuery[] = ", to_char( inv_d.dtmdeliverydate, 'YYYY/MM/DD HH:MI:SS' ) as dtmdeliverydate";
    // 納品場所コード
    $arySelectQuery[] = ", delv_c.strcompanydisplaycode as lngdeliveryplacecode";
    // 納品場所名
    $arySelectQuery[] = ", inv_d.strdeliveryplacename as strdeliveryplacename";
    // 小計
    $arySelectQuery[] = ", To_char( inv_d.cursubtotalprice, '9,999,999,990.99' ) as cursubtotalprice";
    // 課税区分コード
    $arySelectQuery[] = ", inv_d.lngtaxclasscode as lngtaxclasscode";
    // 課税区分名
    $arySelectQuery[] = ", inv_d.strtaxclassname as strtaxclassname";
    // 消費税率
    $arySelectQuery[] = ", inv_d.curtax as curtax";
    // 備考
    $arySelectQuery[] = ", inv_d.strnote as strnote";
    // 納品書番号
    $arySelectQuery[] = ", inv_d.lngslipno as lngslipno";
    // 納品書リビジョン番号
    $arySelectQuery[] = ", inv_d.lngsliprevisionno as lngsliprevisionno";
    // 納品伝票コード
    $arySelectQuery[] = ", slip_m.strslipcode as strslipcode";

    // select句 クエリー連結
    $aryOutQuery[] = implode("\n", $arySelectQuery);

    // From句 の生成
    $aryFromQuery = array();
    $aryFromQuery[] = " FROM t_invoicedetail inv_d ";
    $aryFromQuery[] = " LEFT JOIN m_slip slip_m ON inv_d.lngslipno = slip_m.lngslipno";
//     $aryFromQuery[] = " LEFT JOIN m_Company cust_c ON inv_d.lngdeliveryplacecode = cust_c.lngdeliveryplacecode";
    $aryFromQuery[] = " LEFT JOIN m_Company delv_c ON inv_d.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
    // From句 クエリー連結
    $aryOutQuery[] = implode("\n", $aryFromQuery);

    // Where句 クエリー連結
    $aryOutQuery[] = implode("\n", $aryQuery);

    return implode("\n", $aryOutQuery);
}

function fncGetSearchInvoiceByInvoiceCodeSQL($strInvoiceCode, $lngRevisionNo)
{
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  inv.lnginvoiceno as lnginvoiceno";
    $aryQuery[] = "  , inv.lngrevisionno as lngrevisionno";
    $aryQuery[] = "  , inv.dtminsertdate as dtminsertdate";
    $aryQuery[] = "  , cust_c.strcompanydisplaycode as strcustomercode";
    $aryQuery[] = "  , inv.strcustomername as strcustomername";
    $aryQuery[] = "  , inv.strcustomercompanyname as strcustomercompanyname";
    $aryQuery[] = "  , cust_c.lngCountryCode as lngcountrycode";
    $aryQuery[] = "  , inv.strinvoicecode as strinvoicecode";
    $aryQuery[] = "  , to_char(inv.dtminvoicedate, 'YYYY/MM/DD') as dtminvoicedate";
    $aryQuery[] = "  , to_char(inv.dtmchargeternstart, 'YYYY/MM/DD') as dtmchargeternstart";
    $aryQuery[] = "  , to_char(inv.dtmchargeternend, 'YYYY/MM/DD') as dtmchargeternend";
    $aryQuery[] = "  , To_char(inv.curlastmonthbalance, '9,999,999,990.99') as curlastmonthbalance";
    $aryQuery[] = "  , To_char(inv.curthismonthamount, '9,999,999,990.99') as curthismonthamount";
    $aryQuery[] = "  , inv.lngmonetaryunitcode as lngmonetaryunitcode";
    $aryQuery[] = "  , inv.strmonetaryunitsign as strmonetaryunitsign";
    $aryQuery[] = "  , inv.lngtaxclasscode as lngtaxclasscode";
    $aryQuery[] = "  , inv.strtaxclassname as strtaxclassname";
    $aryQuery[] = "  , To_char(inv.cursubtotal1, '9,999,999,990.99') as cursubtotal1";
    $aryQuery[] = "  , inv.curtax1 as curtax1";
    $aryQuery[] = "  , To_char(inv.curtaxprice1, '9,999,999,990.99') as curtaxprice1";
    $aryQuery[] = "  , u.struserdisplaycode as strusercode";
    $aryQuery[] = "  , inv.strusername as strusername";
    $aryQuery[] = "  , insert_u.struserdisplaycode as strinsertusercode";
    $aryQuery[] = "  , inv.strinsertusername as strinsertusername";
    $aryQuery[] = "  , to_char(inv.dtminsertdate, 'YYYY/MM/DD') as dtminsertdate";
    $aryQuery[] = "  , inv.strnote as strnote";
    $aryQuery[] = "  , inv.lngprintcount as lngprintcount";
    $aryQuery[] = "  , sa.lngSalesStatusCode as lngSalesStatusCode";
    $aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_invoice inv ";
    $aryQuery[] = "  LEFT JOIN m_sales sa ";
    $aryQuery[] = "    ON inv.lnginvoiceno = sa.lnginvoiceno ";
    $aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
    $aryQuery[] = "    ON sa.lngSalesStatusCode = ss.lngSalesStatusCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON inv.lngcustomercode = cust_c.lngcompanycode ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
    $aryQuery[] = "    ON inv.lngmonetaryunitcode = mu.lngMonetaryUnitCode ";
    $aryQuery[] = "  LEFT JOIN m_User insert_u ";
    $aryQuery[] = "    ON inv.lngInsertUserCode = insert_u.lngusercode ";
    $aryQuery[] = "  LEFT JOIN m_User u ";
    $aryQuery[] = "    ON inv.lngusercode = u.lngusercode ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  inv.bytinvalidflag = FALSE ";
    $aryQuery[] = "  AND inv.lngrevisionno <> ". $lngRevisionNo. " ";
    $aryQuery[] = "  AND inv.strinvoicecode = '". $strInvoiceCode. "'";
    $aryQuery[] = " ORDER BY inv.lngrevisionno DESC";

    return implode("\n", $aryQuery);
}

/**
 * 請求書登録関数
 *
 *    渡されたPOST値と請求書マスタから登録に必要な配列を返す
 *
 *    @param  Array     $aryData          ＰＯＳＴデータ群
 *    @param  Array     $aryResult        請求書番号に紐づく請求書マスタが格納された配列
 *    @param  Object    $objDB               DBオブジェクト
 *    @access public
 */
function fncInvoiceInsertReturnArray($aryData, $aryResult=null, $objAuth, $objDB)
{
    $insertAry = [];

    // 出力明細一覧の納品書番号
    $slipCodeArray = explode(',' ,$aryData['slipCodeList']);
    $insertAry['slipCodeArray']  = $slipCodeArray;

    // 請求書No
    // 登録時 : MAX(請求書番号内の請求書マスタ.請求書番号)+1
    // 更新時 : 更新元の請求書マスタ.請求書番号
    $insertAry['lnginvoiceno'] = empty($aryResult['lnginvoiceno'])
                                    ? null
                                : $aryResult['lnginvoiceno'];

    // リビジョンNo
    // 登録時 : 0
    // 更新時 : 更新元の請求書マスタ.リビジョン番号 + 1
    $insertAry['lngrevisionno'] = !isset($aryResult['lngrevisionno'])
                                    ? 0
                                    : (int)$aryResult['lngrevisionno']+1;

    // 請求日
    $insertAry['dtminvoicedate'] = $aryData['dtminvoicedate'];

    // 請求書コード
    // 登録時 : ルールに基づいたコード生成
    // 更新時 : 更新元の請求書マスタ.請求書コード
    $insertAry['strinvoicecode'] = empty($aryResult['strinvoicecode'])
                                    ? fncGetStrInvoiceCode(null, false, $objDB)
                                    : $aryResult['strinvoicecode'];

    // 顧客コード(DISPLAY)
    $insertAry['strcustomercode'] = $aryData['strcustomercode'];

    // 顧客名
    $insertAry['strcustomername'] = $aryData['strcustomercompanyname'];

    // 顧客社名
    $insertAry['strcustomercompanyname'] = $aryData['strcustomercompanyname'];

    // 請求期間 自
    $insertAry['dtmchargeternstart'] = $aryData['dtmchargeternstart'];

    // 請求期間 至
    $insertAry['dtmchargeternend'] = $aryData['dtmchargeternend'];

    // 請求期間
//     $dtmchargeternend = $aryData['dtmchargeternend'];

    // 前月請求残額
    $insertAry['curlastmonthbalance'] = $aryData['curlastmonthbalance'];

    // 御請求金額
    $insertAry['curthismonthamount']   = $aryData['curthismonthamount'];

    // 通貨単位コード //円以外は？
    $insertAry['lngmonetaryunitcode'] = 1;
//     $insertAry['lngmonetaryunitcode']  = $aryData['lngmonetaryunitcode'];
    // 通貨単位名称
    $insertAry['strmonetaryunitsign']  = fncGetMonetaryunitSign($insertAry['lngmonetaryunitcode'] ,$objDB);

    // 課税区分コード
    // 課税区分名
    $insertAry['lngtaxclasscode']  = $aryData['lngtaxclasscode'];
    $insertAry['strtaxclassname']  = $aryData['strtaxclassname'];

    // 税抜金額1(当月請求金額)
    $insertAry['cursubtotal1'] = $aryData['cursubtotal1'];
    // 消費税率1
    $insertAry['curtax1'] = (int)$aryData['curtax1'];
    // 消費税額1
    $insertAry['curtaxprice1'] = $aryData['curtaxprice'];

    // 作成日
    $insertAry['dtminsertdate'] = 'now()';

    // 担当者コード
    $insertAry['strusercode'] = $aryData['strusercode'];
    // 担当者名
    $insertAry['strusername'] = $aryData['strusername'];

    // 作成者コード
    $insertAry['strinsertusercode'] = $objAuth->UserCode;
    // 作成者名
    $insertAry['strinsertusername'] = $objAuth->UserDisplayName;
    // 備考
    $insertAry['strnote'] = $aryData['strnote'];
    // 説明
    $insertAry['description'] = $aryData['description'];

    return $insertAry;
}



/**
 * 請求書登録関数
 *
 *    渡されたデータで請求書マスタに登録する
 *
 *    @param  Array    $insertAry  登録データ
 *    @param  Object   $objDB      DBオブジェクト
 *    @access public
 */
function fncInvoiceInsert( $insertAry ,$objDB, $objAuth)
{
    // 請求書マスタにデータを登録する
    // 請求書番号を取得
    if($insertAry['lnginvoiceno'] > 0){
        $sequence_m_lnginvoice = $insertAry['lnginvoiceno'];
    }
    else
    {
        // シーケンス発行
        $sequence_m_lnginvoice = fncGetSequence('m_invoice.lnginvoiceno', $objDB);
    }

    $aryQuery    = array();
    $aryQuery[] = "INSERT INTO m_invoice (";
    $aryQuery[] = "lnginvoiceno, ";             // 請求書番号
    $aryQuery[] = "lngrevisionno, ";            // リビジョン番号 //登録時は0
    $aryQuery[] = "strinvoicecode, ";           // 請求書コード
    $aryQuery[] = "dtminvoicedate, ";           // 請求日
    $aryQuery[] = "lngcustomercode, ";          // 顧客コード
    $aryQuery[] = "strcustomername, ";          // 顧客名
    $aryQuery[] = "strcustomercompanyname, ";   // 顧客社名
    $aryQuery[] = "dtmchargeternstart, ";       // 請求期間(FROM)
    $aryQuery[] = "dtmchargeternend, ";         // 請求期間(TO)
    $aryQuery[] = "curlastmonthbalance, ";      // 前月請求残額
    $aryQuery[] = "curthismonthamount, ";       // 御請求金額
    $aryQuery[] = "lngmonetaryunitcode, ";      // 通貨単位コード
    $aryQuery[] = "strmonetaryunitsign, ";      // 通貨単位
    $aryQuery[] = "lngtaxclasscode, ";          // 課税区分コード
    $aryQuery[] = "strtaxclassname, ";          // 課税区分名
    $aryQuery[] = "cursubtotal1, ";             // 税抜き金額1
    $aryQuery[] = "curtax1, ";                  // 消費税率1
    $aryQuery[] = "curtaxprice1, ";             // 消費税額1
    $aryQuery[] = "dtminsertdate, ";            // 作成日
    $aryQuery[] = "lngusercode, ";              // 担当者コード
    $aryQuery[] = "strusername, ";              // 担当者名
    $aryQuery[] = "lnginsertusercode, ";        // 作成者コード
    $aryQuery[] = "strinsertusername, ";        // 作成者名
    $aryQuery[] = "strnote, ";                  // 備考
     $aryQuery[] = "lngprintcount, ";         // 印刷回数
    $aryQuery[] = "bytinvalidflag, ";            // 無効フラグ
    $aryQuery[] = "description ";            // 説明
    $aryQuery[] = ") values (";
    // 請求書番号
    $aryQuery[] = $sequence_m_lnginvoice ." ,";
    $aryQuery[] = $insertAry['lngrevisionno'] ." ,";                                        // リビジョン番号
    $aryQuery[] = "'" .$insertAry['strinvoicecode'] ."' ,";                                 // 請求書コード
    $aryQuery[] = "'" .$insertAry['dtminvoicedate'] ."' ,";                                 // 請求日
    $aryQuery[] = "(select lngcompanycode from m_company where strcompanydisplaycode='". $insertAry['strcustomercode'] ."')  ,";                               // 顧客コード(表示用)
    $aryQuery[] = "'" .$insertAry['strcustomername']."' , " ;                               // 顧客名
    $aryQuery[] = "'" .$insertAry['strcustomercompanyname']."' , " ;                        // 顧客社名
    $aryQuery[] = "'". $insertAry['dtmchargeternstart'] ."'  ,";                            // 請求期間(FROM)
    $aryQuery[] = "'". $insertAry['dtmchargeternend'] ."'  ,";                              // 請求期間(TO)
    $aryQuery[] = $insertAry['curlastmonthbalance'] ." ,";                                  // 前月請求残額
    $aryQuery[] = (int)$insertAry['cursubtotal1']." ,";                                     // 御請求金額
    $aryQuery[] = $insertAry['lngmonetaryunitcode'] ." ,";                                  // 通貨単位コード default ?
    $aryQuery[] = "'". preg_replace('/\\\/','￥',$insertAry['strmonetaryunitsign']) ."'  ,";// 通貨単位 \のインサートができないので全角対応
    $aryQuery[] = (int)$insertAry['lngtaxclasscode'] ." , ";                                // 課税区分コード
    $aryQuery[] = "'" .$insertAry['strtaxclassname']."' , ";                                // 課税区分名
    $aryQuery[] =  $insertAry['curthismonthamount'] .",";                                   // 税抜き金額1
    $aryQuery[] = (int)$insertAry['curtax1'] .",";                                          // 消費税率1
    $aryQuery[] = (int)$insertAry['curtaxprice1'] .",";                                     // 消費税額1
    $aryQuery[] = "now() ,";                                                                // 作成日
    $aryQuery[] = "(select lngusercode from m_user where struserdisplaycode = '". $insertAry['strusercode'] ."')  ,";                                   // 担当者コード
    $aryQuery[] = "'". $insertAry['strusername'] ."'  ,";                                   // 担当者名
    $aryQuery[] = $objAuth->UserCode . " ,";                              // 作成者コード
    $aryQuery[] = "'" .$objAuth->UserDisplayName ."' ,";                              // 作成者名
    $aryQuery[] = "'" .$insertAry['strnote'] ."', ";                                        // 備考
    $aryQuery[] = "0 ,";                                                                 // 印刷回数
    $aryQuery[] = "FALSE, ";                                                                 // 無効フラグ
    $aryQuery[] = "'" .$insertAry['description'] ."'";                                        // 備考
    $aryQuery[] = ") ";

    $strQuery = implode("\n",  $aryQuery );
    
    if( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
    }

    $objDB->freeResult( $lngResultID );

    // 請求書明細登録
    $salesNoList = [];      // 売上No
    foreach($insertAry['slipCodeArray'] as $no => $strslipcode)
    {
        $condtition = [];
        $condtition['strSlipCode'] = $strslipcode;
        $strQuery = fncGetSearchMSlipSQL ($condtition, true, $objDB);
        if( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
        }
        $result = pg_fetch_assoc($lngResultID);

        $aryQuery   = [];
        $aryQuery[] = "INSERT INTO t_invoicedetail ( ";
        $aryQuery[] = "lnginvoiceno , ";                    // 請求書番号
        $aryQuery[] = "lnginvoicedetailno , ";              // 請求書明細番号
        $aryQuery[] = "lngrevisionno , ";                   // リビジョン番号
        $aryQuery[] = "dtmdeliverydate , ";                 // 納品日
        $aryQuery[] = "lngdeliveryplacecode , ";            // 納品場所コード
        $aryQuery[] = "strdeliveryplacename , ";            // 納品場所名
        $aryQuery[] = "cursubtotalprice , ";                // 小計
        $aryQuery[] = "lngtaxclasscode , ";                 // 課税区分コード
        $aryQuery[] = "strtaxclassname , ";                 // 課税区分
        $aryQuery[] = "curtax , ";                          // 消費税率
        $aryQuery[] = "strnote , ";                         // 備考
        $aryQuery[] = "lngslipno , ";                       // 納品書番号
        $aryQuery[] = "lngsliprevisionno  ";                // 納品書リビジョン番号
        $aryQuery[] = " ) VALUES ( ";
        $aryQuery[] = $sequence_m_lnginvoice ." ,";                      // 請求書番号
        $aryQuery[] = (int)$no+1 ." ,";                                 // 請求書明細番号
        $aryQuery[] =  $insertAry['lngrevisionno']." ,";                // リビジョン番号
        $aryQuery[] =  "'"  .$insertAry['dtminvoicedate'] ."' ,";       // 納品日
        $aryQuery[] = "(select lngcompanycode from m_company where strcompanydisplaycode='". $result['lngdeliveryplacecode'] ."')  ,"; // 納品場所コード
        $aryQuery[] =  "'"  .$result['strdeliveryplacename'] ."' ,";    // 納品場所名
        $aryQuery[] =  "'"  .$result['curtotalprice'] ."' ,";           // 小計
        $aryQuery[] =  "'"  .$result['lngtaxclasscode'] ."' ,";         // 課税区分コード
        $aryQuery[] =  "'"  .$result['strtaxclassname'] ."' ,";         // 課税区分
        $aryQuery[] =  "'"  .$result['curtax'] ."' ,";                  // 消費税率
        $aryQuery[] =  "'"  .$result['strnote'] ."' ,";                 // 備考
        $aryQuery[] =  $result['lngslipno'] ." ,";                      // 納品書番号
        $aryQuery[] =  $result['lngrevisionno'] ." ";                   // 納品書リビジョン番号
        $aryQuery[] =  " ) ";

        $strQuery = "";
        $strQuery = implode( $aryQuery );

        // 請求書明細に登録
        if( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
        }

        if(!empty($result['lngsalesno'])) {
            $salesNoList[] = (int)$result['lngsalesno'];
        }
    }
    $objDB->freeResult( $lngResultID );

    // 売上マスタの請求書番号を更新
    // 納品伝票マスタに紐づく売上マスタ
    $where = "";
    foreach($salesNoList as $salesno)
    {
        if(!empty($where))
        {
            $where .= ', ';
        }
        $where .= $salesno;
    }

    $aryQuery = [];
    $aryQuery[] = "UPDATE m_sales set lnginvoiceno = " .$sequence_m_lnginvoice ;
    $aryQuery[] = " WHERE lngsalesno IN ( " .$where .")";
    $strQuery = "";
    $strQuery = implode( $aryQuery );

    // 売上マスタ更新
    if( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
    }

    $aryResult["result"] = true;
    $aryResult["strReportKeyCode"] = $sequence_m_lnginvoice;
    return $aryResult;

}

/**
 * 指定された請求書番号から請求書マスタ情報を取得するＳＱＬ文を作成
 *
 *    指定請求書番号の請求書マスタ情報の取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngInvoiceNo             取得する請求書番号
 *    @return strQuery     $strQuery 検索用SQL文
 *    @access public
 */
function fncGetInvoiceMSQL ( $lngInvoiceNo, $lngRevisionNo)
{
    // 請求書番号番号
    $aryQuery[] = "SELECT distinct on (inv.lnginvoiceno) inv.lnginvoiceno as lnginvoiceno ";
    // リビジョン番号
    $aryQuery[] = ", inv.lngrevisionno as lngrevisionno";
    // 顧客コード
    $aryQuery[] = ", cust_c.strcompanydisplaycode as strcustomercode";
    // 顧客名
    $aryQuery[] = ", inv.strcustomername as strcustomername";
    // 顧客社名
    $aryQuery[] = ", inv.strcustomercompanyname as strcustomercompanyname";
    // 請求書コード
    $aryQuery[] = ", inv.strinvoicecode as strinvoicecode";
    // 請求日
    $aryQuery[] = ", to_char( inv.dtminvoicedate, 'YYYY/MM/DD' ) as dtminvoicedate";
    // 請求期間 自
    $aryQuery[] = ", to_char( inv.dtmchargeternstart, 'YYYY/MM/DD' ) as dtmchargeternstart";
    // 請求期間 至
    $aryQuery[] = ", to_char( inv.dtmchargeternend, 'YYYY/MM/DD' ) as dtmchargeternend";
    // 前月請求残額
    $aryQuery[] = ", To_char( inv.curlastmonthbalance, '9,999,999,990' ) as curlastmonthbalance";
    // 御請求金額
    $aryQuery[] = ", To_char( inv.curthismonthamount, '9,999,999,990' ) as curthismonthamount";
    // 通貨単位コード
    $aryQuery[] = ", inv.lngmonetaryunitcode as lngmonetaryunitcode";
    // 通貨単位
    $aryQuery[] = ", inv.strmonetaryunitsign as strmonetaryunitsign";
    // 課税区分コード
    $aryQuery[] = ", inv.lngtaxclasscode as lngtaxclasscode";
    // 課税区分名
    $aryQuery[] = ", inv.strtaxclassname as strtaxclassname";
    // 税抜金額1
    $aryQuery[] = ", To_char( inv.cursubtotal1, '9,999,999,990' ) as cursubtotal1";
    // 消費税率1
    $aryQuery[] = ", inv.curtax1 as curtax1";
    // 消費税額1
    $aryQuery[] = ", To_char( inv.curtaxprice1, '9,999,999,990' ) as curtaxprice1";
    // 担当者
    $aryQuery[] = ", u.struserdisplaycode as strusercode";
    $aryQuery[] = ", inv.strusername as strusername";
    // 作成者
    $aryQuery[] = ", insert_u.struserdisplaycode as strinsertusercode";
    $aryQuery[] = ", inv.strinsertusername as strinsertusername";
    // 作成日
    $aryQuery[] = ", to_char( inv.dtminsertdate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";
    // 備考
    $aryQuery[] = ", inv.strnote as strnote";
    // 印刷回数
    $aryQuery[] = ", inv.lngprintcount as lngprintcount";
    // 説明
    $aryQuery[] = ", inv.description as description";

    $aryQuery[] = " FROM m_invoice inv ";
    $aryQuery[] = " LEFT JOIN m_Company cust_c ON inv.lngcustomercode = cust_c.lngcompanycode";
    $aryQuery[] = " LEFT JOIN m_User insert_u ON inv.lngInsertUserCode = insert_u.lngusercode";
    $aryQuery[] = " LEFT JOIN m_User u ON inv.lngusercode = u.lngusercode";
    // WHERE
    $aryQuery[] = " WHERE inv.lnginvoiceno = ".$lngInvoiceNo. " ";
    if ($lngRevisionNo == null) {
        $aryQuery[] = " and inv.lngrevisionno = (SELECT MAX(lngrevisionno) FROM m_invoice WHERE lnginvoiceno = " . $lngInvoiceNo.")";
    } else {        
        $aryQuery[] = " AND inv.lngrevisionno = ".$lngRevisionNo. " ";
    }

    // 削除済みは排除
    $aryQuery[] = " AND inv.lnginvoiceno NOT IN ( ";
    $aryQuery[] = " SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE lngrevisionno = -1";
    $aryQuery[] = " ) ";

    // order by
    $aryQuery[] = " ORDER BY inv.lnginvoiceno ASC , inv.lngrevisionno DESC ";

    $strQuery = implode( "\n", $aryQuery );

    return $strQuery;
}



// --------------------------------
//  削除に必要なエラーチェック
// --------------------------------

/**
 *    請求書明細に紐づく納品書マスタに紐づく売上マスタの売上ステータスが締済み(=99)かどうかチェック
 *  指定された請求書番号から売上マスタ情報
 *
 *    @param  Integer     $lngInvoiceNo         請求書番号
 *    @return boolean    true : 締済みが含まれている
 *                      false:「締め済」の明細は無し
 */

function fncSalesStatusIsClosed($lngInvoiceNo, $objDB)
{
    // 請求書番号に紐づく売上マスタデータの取得
    $strQuery = fncGetSalesMSQL ( $lngInvoiceNo );
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum )
    {
        for ( $i = 0; $i < $lngResultNum; $i++ )
        {
            $aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    }
    else
    {
        // 請求書番号に紐づく売上マスタが見つからない⇒DBエラー
        fncOutputError ( 9061, DEF_FATAL, "削除前チェック処理に伴う売上マスタ取得失敗", TRUE, "", $objDB );
    }

    //  売上マスタに紐づく売上状態のステータスが「締め済」かどうか
    for ( $i = 0; $i < count($aryDetailResult); $i++)
    {
        // 売上状態コード
        $lngSalesStatusCode = $aryDetailResult[$i]["lngsalesstatuscode"];

        if ($lngSalesStatusCode == DEF_SALES_CLOSED){
            // 売上状態コードが「締め済」のマスタが1件以上存在
            return true;
        }
    }

    // 売上状態コードが「締め済」は1件も無い
    return false;
}



/**
 * 請求書マスタのデータの削除
 *
 *    @param  Integer     $lngInvoiceNo 請求書番号
 *    @param  Object        $objDB        DBオブジェクト
 *    @return Boolean     true        実行成功
 *                        false        実行失敗 情報取得失敗
 */
function fncDeleteInvoice($lngInvoiceNo, $lngRevisionNo, $objDB, $objAuth)
{
    // 請求書番号番の有効なマスタデータがあるか確認
    $strQuery = fncGetInvoiceMSQL($lngInvoiceNo, $lngRevisionNo);
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB);
    if ( $lngResultNum )
    {
        $objResult = $objDB->fetchObject( $lngResultID, 0 );
        $strSalesCode   = $objResult->strsalescode;
        $strInvoiceCode = $objResult->strinvoicecode;
    }
    else
    {
        // 請求書マスタ取得に失敗
        return false;
    }
    $objDB->freeResult( $lngResultID );

    // リビジョン番号は-1固定（仕様書に準ずる）
    $lngMinRevisionNo = -1;

    // 請求書マスタにリビジョン番号が -1 のレコードを追加
    $aryQuery[] = "INSERT INTO m_invoice (";
    $aryQuery[] = " lnginvoiceno,";                     // 1:請求書番号
    $aryQuery[] = " lngrevisionno, ";                   // 2:リビジョン番号
    $aryQuery[] = " strinvoicecode, ";                  // 3:請求書コード
    $aryQuery[] = " lnginsertusercode, ";               // 4:入力者コード
    $aryQuery[] = " strinsertusername, ";               // 3:入力者名
    $aryQuery[] = " bytinvalidflag, ";                  // 5:無効フラグ
    $aryQuery[] = " dtminsertdate";                     // 6:登録日
    $aryQuery[] = ") values (";
    $aryQuery[] = $lngInvoiceNo . ", ";                 // 1:請求書番号
    $aryQuery[] = $lngMinRevisionNo . ", ";             // 2:リビジョン番号
    $aryQuery[] = "'" .$strInvoiceCode . "', ";         // 3:請求書コード
    $aryQuery[] = "" .$objAuth->UserCode . ", ";      // 4:入力者コード
    $aryQuery[] = "'" .$objAuth->UserFullName . "', ";  // 4:入力者名
    $aryQuery[] = "false, ";                            // 5:無効フラグ
    $aryQuery[] = "now()";                              // 6:登録日
    $aryQuery[] = ")";

    unset($strQuery);
    $strQuery = implode("\n", $aryQuery );

    if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
    {
        // レコード追加失敗
        return false;
    }
    $objDB->freeResult( $lngResultID );

    // 処理成功
    return true;
}


/**
 * 指定された請求書番号に紐づいている売上マスタの請求書番号を空にする
 *
 *    @param  integer     $lngInvoiceNo   請求書番号
 *    @param  Object        $objDB          DBオブジェクト
 *    @return Boolean     true            実行成功
 *                        false           実行失敗 情報取得失敗
 */
function fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB)
{
    // 請求書番号に紐づく売上マスタデータの取得
    $strQuery = fncGetSalesMSQL ( $lngInvoiceNo );
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( !$lngResultNum )
    {
        // 請求書番号に紐づく売上マスタが見つからない⇒DBエラー
        return false;
    }


    // 更新対象レコードの請求書番号をNULLに更新
    $strWhere  = "WHERE ";
    $strWhere .= "lnginvoiceno = " . $lngInvoiceNo . " ";
    $strWhere .= "and lngrevisionno = (SELECT MAX(lngrevisionno) FROM m_sales WHERE lnginvoiceno = " . $lngInvoiceNo . ")";
    $strUpdateQuery  = "UPDATE m_sales ";
    $strUpdateQuery .= "SET lnginvoiceno = NULL " ;
    $strUpdateQuery .= $strWhere;

    list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
    if (!$lngUpdateResultID){ return false; }
    $objDB->freeResult( $lngUpdateResultID );

    // 処理成功
    return true;

}


/**
 * 指定された請求書番号から売上マスタ情報を取得するＳＱＬ文を作成
 *
 *    指定請求書番号が登録されている売上マスタ情報取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngInvoiceNo キーとなる請求書番号
 *    @return strQuery     $strQuery     検索用SQL文
 *    @access public
 */
function fncGetSalesMSQL ( $lngInvoiceNo )
{
    // 売上番号
    $aryQuery[] = "SELECT distinct on (lngsalesno) lngsalesno ";
    // リビジョン番号
    $aryQuery[] = ", lngrevisionno";
    // 売上コード
    $aryQuery[] = ", strsalescode";
    // 計上日
    $aryQuery[] = ", to_char( dtmappropriationdate, 'YYYY/MM/DD' ) as dtmappropriationdate";
    // 顧客コード
    $aryQuery[] = ", lngcustomercompanycode";
    // グループコード
    $aryQuery[] = ", lnggroupcode";
    // ユーザコード
    $aryQuery[] = ", lngusercode";
    // 売上状態コード
    $aryQuery[] = ", lngsalesstatuscode";
    // 通貨単位コード
    $aryQuery[] = ", lngmonetaryunitcode";
    // 通貨レートコード
    $aryQuery[] = ", lngmonetaryratecode";
    // 通算レート
    $aryQuery[] = ", curconversionrate";
    // 納品書NO
    $aryQuery[] = ", strslipcode";
    // 請求書番号
    $aryQuery[] = ", lnginvoiceno";
    // 合計金額
    $aryQuery[] = ", To_char( curtotalprice, '9,999,999,990.9999' )  as curtotalprice";
    // 備考
    $aryQuery[] = ", strnote";
    // 入力者コード
    $aryQuery[] = ", lnginputusercode";
    // 登録日
    $aryQuery[] = ", to_char( dtminsertdate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";

    // FROM句
    $aryQuery[] = " FROM m_sales ";

    $aryQuery[] = " WHERE lnginvoiceno = " . $lngInvoiceNo . "";

    $aryQuery[] = " ORDER BY lngsalesno ASC , lngrevisionno DESC ";

    $strQuery = implode( "\n", $aryQuery );

    return $strQuery;
}


/**
 * ヘッダ部データ加工
 *
 *    SQLで取得したヘッダ部の値を表示用に加工する
 *    ※SQL取得結果のキー名はすべて小文字になることに注意
 *
 *    @param  Array     $aryResult                 ヘッダ行の検索結果が格納された配列
 *    @access public
 */
function fncSetInvoiceHeadTableData ( $aryResult )
{
    // 請求書No
    $aryNewResult["lngInvoiceNo"]    = $aryResult["lnginvoiceno"];
    // リビジョン番号
    $aryNewResult["lngRevisionNo"]   = $aryResult["lngrevisionno"];
    // 顧客コード
    $aryNewResult["strCustomerCode"] = $aryResult["strcustomercode"];
    // 顧客名
    $aryNewResult["strCustomerName"] = $aryResult["strcustomername"];
    // 顧客
    if ( $aryResult["strcustomercode"] )
    {
        $aryNewResult["strCustomer"] = "[" . $aryResult["strcustomercode"] ."]";
    }
    else
    {
        $aryNewResult["strCustomer"] = "      ";
    }
    $aryNewResult["strCustomer"] .= " " . $aryResult["printCompanyName"];
    if( $aryResult["printCustomerName"] ) {
        $aryNewResult["strCustomer"] .= "  " . $aryResult["printCustomerName"];
    }
    // 顧客社名
    $aryNewResult["strCustomerCompanyName"] = $aryResult["strcustomercompanyname"];
    // 請求書コード
    $aryNewResult["strInvoiceCode"]         = $aryResult["strinvoicecode"];
    // 請求日
    $aryNewResult["dtmInvoiceDate"]         = $aryResult["dtminvoicedate"];
    // 請求期間 自
    $aryNewResult["dtmChargeternStart"]     = $aryResult["dtmchargeternstart"];
    // 請求期間 至
    $aryNewResult["dtmChargeternEnd"]       = $aryResult["dtmchargeternend"];
    // 前月請求残額
    $aryNewResult["curLastMonthBalance"]    = $aryResult["curlastmonthbalance"];
    // 御請求金額
    $aryNewResult["curThisMonthAmount"]     = $aryResult["curthismonthamount"];
    // 通貨単位コード
    $aryNewResult["lngMonetaryUnitCode"]    = $aryResult["lngmonetaryunitcode"];
    // 通貨単位
    $aryNewResult["strMonetaryUnitSign"]    = $aryResult["strmonetaryunitsign"];
    // 課税区分コード
    $aryNewResult["lngTaxClassCode"]        = $aryResult["lngtaxclasscode"];
    // 課税区分名
    $aryNewResult["strTaxClassName"]        = $aryResult["strtaxclassname"];
    // 税抜金額1
    $aryNewResult["curSubTotal1"]           = $aryResult["cursubtotal1"];
    // 消費税率1
    $aryNewResult["curTax1"]                = $aryResult["curtax1"];
    // 消費税額1
    $aryNewResult["curTaxPrice1"]           = $aryResult["curtaxprice1"];
    // 担当者
    $aryNewResult["strUserCode"]            = $aryResult["strusercode"];
    $aryNewResult["strUserName"]            = $aryResult["strusername"];
    // 表示用担当者
    if ( $aryResult["strusercode"] )
    {
        $aryNewResult["strUser"] = "[" . $aryResult["strusercode"] ."]";
    }
    else
    {
        $aryNewResult["strUser"] = "      ";
    }
    $aryNewResult["strUser"]               .= " " . $aryResult["strusername"];

    // 作成者
    $aryNewResult["strInsertUserCode"]      = $aryResult["strinsertusercode"];
    $aryNewResult["strInsertUserName"]      = $aryResult["strinsertusername"];
    // 表示用作成者
    if ( $aryResult["strinsertusercode"] )
    {
        $aryNewResult["strInsertUser"] = "[" . $aryResult["strinsertusercode"] ."]";
    }
    else
    {
        $aryNewResult["strInsertUser"] = "      ";
    }
    $aryNewResult["strInsertUser"]         .= " " . $aryResult["strinsertusername"];

    // 作成日
    $aryNewResult["dtmInsertDate"]          = $aryResult["dtminsertdate"];
    // 備考
    $aryNewResult["strNote"]                = $aryResult["strnote"];
    // 備考
    $aryNewResult["description"]                = $aryResult["description"];
    // 印刷回数
    $aryNewResult["lngPrintCount"]          = $aryResult["lngprintcount"];

    return $aryNewResult;
}



/**
 * 請求書明細部データ加工
 *
 *    SQLで取得した請求書明細の値を表示用に加工する
 *    ※SQL取得結果のキー名はすべて小文字になることに注意
 *
 *    @param  Array     $aryDetailResult     明細行の検索結果が格納された配列（１データ分）
 *    @param  Array     $aryHeadResult         ヘッダ行の検索結果が格納された配列（参照用）
 *    @access public
 */
function fncSetInvoiceDetailTableData ( $aryDetailResult, $aryHeadResult )
{
    // ソートキー
    $aryNewDetailResult["lngInvoiceDetailNo"]    = $aryDetailResult["lnginvoicedetailno"];
    // 請求書番号
    $aryNewDetailResult["lngInvoiceNo"]          = $aryDetailResult["lnginvoiceno"];
    // リビジョン番号
    $aryNewDetailResult["lngRevisionNo"]         = $aryDetailResult["lngrevisionno"];
    // 納品日
    $aryNewDetailResult["dtmDeliveryDate"]       = date('Y-m-d', strtotime($aryDetailResult["dtmdeliverydate"]));
    // // 納品場所コード
    // $aryNewDetailResult["lngDeliveryPlaceCode"]  = $aryDetailResult["lngdeliveryplacecode"];
    // // 納品場所
    // if ( $aryDetailResult["lngdeliveryplacecode"] )
    // {
    //     $aryNewResult["c"] = "[" . $aryResult["lngdeliveryplacecode"] ."]";
    // }
    // else
    // {
    //     $aryNewResult["strDeliveryPlaceName"] = "      ";
    // }
    // $aryNewDetailResult["strDeliveryPlaceName"]  = $aryDetailResult["c"];
    // 納品場所コード
    $aryNewDetailResult["lngDeliveryPlaceCode"]            = $aryDetailResult["lngdeliveryplacecode"];
    $aryNewDetailResult["strDeliveryPlaceName"]            = $aryDetailResult["lngdeliveryplacecode"];
    // 表示用納品場所
    if ( $aryDetailResult["lngdeliveryplacecode"] )
    {
        $aryNewDetailResult["strDeliveryPlace"] = "[" . $aryDetailResult["lngdeliveryplacecode"] ."]";
    }
    else
    {
        $aryNewDetailResult["strDeliveryPlace"] = "      ";
    }
    $aryNewDetailResult["strDeliveryPlace"]               .= " " . $aryDetailResult["strdeliveryplacename"];
    // 税抜金額
    if ( !$aryDetailResult["cursubtotalprice"] )
    {
        $aryNewDetailResult["curSubTotalPrice"] .= "0.00";
    }
    else
    {
        $aryNewDetailResult["curSubTotalPrice"] .= $aryDetailResult["cursubtotalprice"];
    }
    // 課税区分コード
    $aryNewDetailResult["lngTaxClassCode"]       = $aryDetailResult["lngtaxclasscode"];
    // 課税区分名
    $aryNewDetailResult["strTaxClassName"]       = $aryDetailResult["strtaxclassname"];
    // 消費税率
    $curTax = (float)$aryDetailResult["curtax"];
    $aryNewDetailResult["curTax"]                = (int)($curTax*100);
    // 消費税額
    $curSubTotalPrice = preg_replace('/,/', '', $aryDetailResult["cursubtotalprice"]);
    $aryNewDetailResult["taxPrice"]              = (int)($aryDetailResult["lngtaxclasscode"]) == 1
                                                    ? 0
                                                    : (float)$curSubTotalPrice*$curTax;
    // 課税区分・税率
    $aryNewDetailResult["strTax"]                = $aryDetailResult["strtaxclassname"] ."・" . (int)($curTax*100) . '%';
    // 明細備考
    $aryNewDetailResult["strDetailNote"]         = nl2br($aryDetailResult["strnote"]);
    // 納品伝票番号
    $aryNewDetailResult["lngSlipNo"]             = $aryDetailResult["lngslipno"];
    // リビジョン番号
    $aryNewDetailResult["lngSlipRevisionNo"]     = $aryDetailResult["lngsliprevisionno"];
    // 納品伝票コード
    $aryNewDetailResult["lngSlipCode"]           = $aryDetailResult["strslipcode"];

    return $aryNewDetailResult;
}



/**
 * プレビュー用データ加工
 *
 *    POSTデータをプレビュー表示用に加工する
 *
 *    @param  Array       $aryResult         POSTが格納された配列
 *  @param  integer  $lngInvoiceNo      請求書番号
 *    @access public
 */
function fncSetPreviewTableData ( $aryResult , $lngInvoiceNo, $objDB)
{
	require_once (LIB_DEBUGFILE);
    // 請求書明細の納品書No
    $slipCodeArray = explode(',' ,$aryResult['slipCodeList']);
    $aryPrevResult['slipCodeList']  = $aryResult['slipCodeList'];
    $aryPrevResult['slipCodeArray'] = $slipCodeArray;
    $aryPrevResult['slipCodeCount'] = COUNT($slipCodeArray);
    $i = 0;
    foreach ($slipCodeArray as $slipCode) {
        $aryPrevResult['strslipcode' . $i] = $slipCode;
    }
    if(isset($aryResult['taxclass'])) {
        $taxclass = explode(' ' ,$aryResult['taxclass']);
        $taxclasscode = preg_replace('/[^0-9]/', '', $taxclass[0]);
        $taxclassname = $taxclass[1];
        // 課税区分コード
        $aryPrevResult["lngTaxClassCode"] = $taxclasscode;
        // 課税区分名
        $aryPrevResult["strTaxClassName"] = $taxclassname;
    }


    // 顧客コード
    $aryPrevResult["strCustomerCode"] = $aryResult["lngCustomerCode"];
    // 顧客名
    $aryPrevResult["strCustomerName"] = $aryResult["strCustomerName"];

    // 前月請求残額
    $aryPrevResult['curLastMonthBalance_desc'] = preg_match('/,/',$aryResult["curlastmonthbalance"]) ? $aryResult["curlastmonthbalance"] : number_format($aryResult["curlastmonthbalance"]);
    // 今月税抜き金額
    $aryPrevResult['curSubTotal1_desc']        = preg_match('/,/',$aryResult["curthismonthamount"]) ? $aryResult["curthismonthamount"] : number_format($aryResult["curthismonthamount"]);
    // 消費税額
    $aryPrevResult['curTaxPrice1_desc']        = preg_match('/,/',$aryResult["curtaxprice"]) ? $aryResult["curtaxprice"] : number_format($aryResult["curtaxprice"]);
    // ご請求額
    $aryPrevResult['curThisMonthAmount_desc']  = preg_match('/,/',$aryResult["notaxcurthismonthamount"]) ? $aryResult["notaxcurthismonthamount"] : number_format($aryResult["notaxcurthismonthamount"]);
    // 前月請求残額
    $aryPrevResult['curLastMonthBalance'] = preg_replace('/,/', '', $aryResult["curlastmonthbalance"]);
    // 今月税抜き金額
    $aryPrevResult['curSubTotal1']        = preg_replace('/,/', '', $aryResult["curthismonthamount"]);
    // 消費税額
    $aryPrevResult['curTaxPrice1']        = preg_replace('/,/', '', $aryResult["curtaxprice"]);
    // ご請求額
    $aryPrevResult['curThisMonthAmount']  = preg_replace('/,/', '', $aryResult["notaxcurthismonthamount"]);

    // 消費税率1
    $curtax1 = preg_replace('/[^0-9]/', '', $aryResult["tax"]);
    $aryPrevResult['curTax1'] = (int)$curtax1;

    // 請求日
    $dtmInvoiceDate = $aryResult['ActionDate'];
    $aryPrevResult['dtmInvoiceDate'] = $dtmInvoiceDate;
    // 表示用請求日
    $printInvDate = fncGetJapaneseDate($dtmInvoiceDate);
    $aryPrevResult['printInvDate']  = $printInvDate[0] . $printInvDate[1] .'年 ' .$printInvDate[2] .'月' .$printInvDate[3] .'日';

    // 請求書コード
    $aryPrevResult['strInvoiceCode'] = fncGetStrInvoiceCode($lngInvoiceNo, true, $objDB);

    // 自 dtmchargeternstart
    $printTernStart = fncGetJapaneseDate($aryResult['dtmchargeternstart']);
    $aryPrevResult['dtmChargeternStart'] = $aryResult['dtmchargeternstart'];
    $aryPrevResult['printTernStartM'] = $printTernStart[2];
    $aryPrevResult['printTernStartD'] = $printTernStart[3];

    // 至 dtmchargeternend
    $printTernEnd = fncGetJapaneseDate($aryResult['dtmchargeternend']);
    $aryPrevResult['dtmChargeternEnd'] = $aryResult['dtmchargeternend'];
    $aryPrevResult['printTernEndM'] = $printTernEnd[2];
    $aryPrevResult['printTernEndD'] = $printTernEnd[3];

    // 顧客コード・顧客名・顧客社名
    list ($aryPrevResult['printCustomerName'], $aryPrevResult['printCompanyName'], $aryPrevResult['customerCode'], $strcompanydisplayname ) = fncGetCompanyPrintName($aryResult["lngCustomerCode"], $objDB);
    // 顧客
    if ( $aryResult["lngCustomerCode"] )
    {
        $aryNewResult["strCustomer"] = "[" . $aryResult["lngCustomerCode"] ."]";
    }
    else
    {
        $aryNewResult["strCustomer"] = "      ";
    }
    $aryNewResult["strCustomer"] .= " " . $aryPrevResult["printCompanyName"];

    // 担当者
    $aryPrevResult["strUserCode"] = $aryResult["lngInputUserCode"];
    $aryPrevResult["strUserName"] = $aryResult["strInputUserName"];
    // 通貨単位名称
    $monetaryUnitCode = 1;
    $aryPrevResult['strMonetaryUnitName'] = fncGetMonetaryunitSign( $monetaryUnitCode ,$objDB);

    // 備考
    $aryPrevResult['description'] = $aryResult['description'];
    // 備考
    $aryPrevResult['strNote'] = $aryResult['strnote'];
    // 再印刷
    if( !empty($aryResult['strnotecheck']) && $aryResult['strnotecheck'] == 'on') {
        $space =  !empty($aryPrevResult['strNote']) ? '  ' : '';
        $aryPrevResult['strNote'] .= $space . "再印刷";
    }

    // 作成日(R Y.M.D)
    $aryPrevResult['prevDate'] = "R." .((int)date('Y')-2018) . "." .(int)date('m') ."." .(int)date('d');

    // ユーザー名取得
    $aryPrevResult['lngUserName'] = $objAuth->UserFullName;

    return $aryPrevResult;
}


/**
 * カラム名を格納する配列のキーに"CN"を付与する
 *
 *    @param  Array     $aryColumnNames         カラム名が格納された配列
 *    @access public
 */
function fncAddColumnNameArrayKeyToCN ($aryColumnNames)
{
    $arrayKeys = array_keys($aryColumnNames);

    // 表示対象カラムの配列より結果の出力
    for ( $i = 0; $i < count($arrayKeys); $i++ )
    {
        $key = $arrayKeys[$i];
        $strNewColumnName = "CN" . $key;
        $aryNames[$strNewColumnName] = $aryColumnNames[$key];
    }

    return $aryNames;
}


/**
 * 和暦    西暦変換関数
 *
 *
 *    @param  Date    $date       西暦 Y/m/d
 *    @access public
 *    return   Array   $jdate      [0] 年号 [1] 年 [2] 月 [3] 日
 */
function fncGetJapaneseDate($date)
{
    $result = [];
    $_date = explode('/',$date);
    $y = (int)$_date[0];
    $m = (int)$_date[1];
    $d = (int)$_date[2];
    $retY = $y - 2018;
    if($retY > 0) {
        $result[0] = '令和';
        $result[1] = $retY;
    }
    $result[2] = (int)$m;
    $result[3] = (int)$d;

    return $result;

}


/**
 *
 *    請求書集計に必要なデータ取得用ＳＱＬ文作成関数
 *
 *    @param  date        $invoiceMonth
 *    @return strQuery    $strQuery 検索用SQL文
 *    @access public
 */
function fncGetInvoiceAggregateSQL ( $invoiceMonth )
{
    $start = new DateTime($invoiceMonth);
    $end =   new DateTime($invoiceMonth);
    // 来月
    $end->add(DateInterval::createFromDateString('1 month'));

    // 請求書番号番号
    $aryQuery[] = "SELECT distinct on (inv.lnginvoiceno) inv.lnginvoiceno as lnginvoiceno ";
    // リビジョン番号
    $aryQuery[] = ", inv.lngrevisionno as lngrevisionno";
    // 顧客コード
    $aryQuery[] = ", cust_c.strCompanyDisplayCode as strcustomercode";
    // 顧客名
    $aryQuery[] = ", inv.strcustomername as strcustomername";
    // 顧客社名
    $aryQuery[] = ", inv.strcustomercompanyname as strcustomercompanyname";
    // 請求書コード
    $aryQuery[] = ", inv.strinvoicecode as strinvoicecode";
    // 請求日
    $aryQuery[] = ", to_char( inv.dtminvoicedate, 'YYYY/MM/DD' ) as dtminvoicedate";
    // 請求期間 自
    $aryQuery[] = ", to_char( inv.dtmchargeternstart, 'YYYY/MM/DD' ) as dtmchargeternstart";
    // 請求期間 至
    $aryQuery[] = ", to_char( inv.dtmchargeternend, 'YYYY/MM/DD' ) as dtmchargeternend";
    // 前月請求残額
    $aryQuery[] = ", inv.curlastmonthbalance as curlastmonthbalance";
    // 御請求金額
    $aryQuery[] = ", inv.curthismonthamount as curthismonthamount";
    // 通貨単位コード
    $aryQuery[] = ", inv.lngmonetaryunitcode as lngmonetaryunitcode";
    // 通貨単位
    $aryQuery[] = ", inv.strmonetaryunitsign as strmonetaryunitsign";
    // 課税区分コード
    $aryQuery[] = ", inv.lngtaxclasscode as lngtaxclasscode";
    // 課税区分名
    $aryQuery[] = ", inv.strtaxclassname as strtaxclassname";
    // 税抜金額1
    $aryQuery[] = ", inv.cursubtotal1 as cursubtotal1";
    // 消費税率1
    $aryQuery[] = ", inv.curtax1 as curtax1";
    // 消費税額1
    $aryQuery[] = ", inv.curtaxprice1 as curtaxprice1";
    // 担当者
    $aryQuery[] = ", u.struserdisplaycode as strusercode";
    $aryQuery[] = ", inv.strusername as strusername";
    // 作成者
    $aryQuery[] = ", insert_u.struserdisplaycode as strinsertusercode";
    $aryQuery[] = ", inv.strinsertusername as strinsertusername";
    // 作成日
    $aryQuery[] = ", to_char( inv.dtminsertdate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";
    // 備考
    $aryQuery[] = ", inv.strnote as strnote";
    // 印刷回数
    $aryQuery[] = ", inv.lngprintcount as lngprintcount";

    $aryQuery[] = " FROM m_invoice inv ";
    $aryQuery[] = " LEFT JOIN m_Company cust_c ON inv.lngcustomercode = cust_c.lngcompanycode";
    $aryQuery[] = " LEFT JOIN m_User insert_u ON inv.lngInsertUserCode = insert_u.lngusercode";
    $aryQuery[] = " LEFT JOIN m_User u ON inv.lngusercode = u.lngusercode";

    // WHERE  dtminvoicedate
    $aryQuery[] = " WHERE inv.dtminvoicedate >= '" .$start->format('Y-m-d') ."'  AND inv.dtminvoicedate < '"  .$end->format('Y-m-d') ."' ";
    // 削除済みは排除
    $aryQuery[] = " AND inv.lnginvoiceno NOT IN ( ";
    $aryQuery[] = " SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE lngrevisionno = -1";
    $aryQuery[] = " ) ";

    $aryQuery[] = " ORDER BY inv.lnginvoiceno ASC , inv.lngrevisionno DESC , inv.lngmonetaryunitcode ASC, inv.lngcustomercode ASC, inv.strinvoicecode ASC ";

    $strQuery = implode( "\n", $aryQuery );

    return $strQuery;
}



// /**
//  * 請求コードによりデータの状態を確認する
//  *
//  * @param [type] $strinvoicecode
//  * @param [type] $objDB
//  * @return void [0：未削除データ　1：削除済データ]
//  */
// function fncCheckData($strinvoicecode, $objDB) {
//     $result = 0;
//     unset($aryQuery);
//     $aryQuery[] = "SELECT";
//     $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, strinvoicecode ";
//     $aryQuery[] = "FROM m_invoice ";
//     $aryQuery[] = "WHERE strinvoicecode='" . $strinvoicecode . "'";
//     $aryQuery[] = "group by strinvoicecode, bytInvalidFlag";

//     // クエリを平易な文字列に変換
//     $strQuery = implode("\n", $aryQuery);
    
//     list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

//     if ($lngResultNum) {
//         $resultObj = $objDB->fetchArray($lngResultID, 0);
//     }

//     $objDB->freeResult($lngResultID);

//     if ($resultObj["lngrevisionno"] < 0) {
//         $result = 1;
//     }
//     return $result;
// }

// /**
//  * 明細データの取得
//  *
//  * @param [type] $lngSalesNo
//  * @param [type] $lngRevisionNo
//  * @param [type] $objDB
//  * @return void
//  */
// function fncGetDetailData($lngInvoiceNo, $lngRevisionNo, $objDB)
// {
//     $detailData = array();
//     unset($aryQuery);
//     $aryQuery[] = "SELECT";
//     $aryQuery[] = "  inv_d.lnginvoiceno as lnginvoiceno";
//     $aryQuery[] = "  , inv_d.lngrevisionno as lngrevisionno";
//     $aryQuery[] = "  , inv_d.lnginvoicedetailno";
//     $aryQuery[] = "  , to_char(inv_d.dtmdeliverydate, 'YYYY/MM/DD HH:MI:SS') as dtmdeliverydate";
//     $aryQuery[] = "  , delv_c.strcompanydisplaycode as lngdeliveryplacecode";
//     $aryQuery[] = "  , inv_d.strdeliveryplacename as strdeliveryplacename";
//     $aryQuery[] = "  , To_char(inv_d.cursubtotalprice, '9,999,999,990.99') as cursubtotalprice";
//     $aryQuery[] = "  , inv_d.lngtaxclasscode as lngtaxclasscode";
//     $aryQuery[] = "  , inv_d.strtaxclassname as strtaxclassname";
//     $aryQuery[] = "  , inv_d.curtax as curtax";
//     $aryQuery[] = "  , inv_d.strnote as strnote";
//     $aryQuery[] = "  , inv_d.lngslipno as lngslipno";
//     $aryQuery[] = "  , inv_d.lngsliprevisionno as lngsliprevisionno";
//     $aryQuery[] = "  , slip_m.strslipcode as strslipcode ";
//     $aryQuery[] = "FROM";
//     $aryQuery[] = "  t_invoicedetail inv_d ";
//     $aryQuery[] = "  LEFT JOIN m_slip slip_m ";
//     $aryQuery[] = "    ON inv_d.lngslipno = slip_m.lngslipno ";
//     $aryQuery[] = "    and inv_d.lngsliprevisionno = slip_m.lngrevisionno ";
//     $aryQuery[] = "  LEFT JOIN m_Company delv_c ";
//     $aryQuery[] = "    ON inv_d.lngDeliveryPlaceCode = delv_c.lngCompanyCode ";
//     $aryQuery[] = "WHERE";
//     $aryQuery[] = "  inv_d.lnginvoiceno = " . $lngInvoiceNo;
//     $aryQuery[] = "  AND inv_d.lngrevisionno = " . $lngRevisionNo;
//     $aryQuery[] = "ORDER BY";
//     $aryQuery[] = "  inv_d.lnginvoicedetailno ASC";
//     // クエリを平易な文字列に変換
//     $strQuery = implode("\n", $aryQuery);

//     list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
//     // 検索件数がありの場合
//     if ($lngResultNum > 0) {
//         // 指定数以内であれば通常処理
//         for ($i = 0; $i < $lngResultNum; $i++) {
//             $detailData = pg_fetch_all($lngResultID);
//         }
//     }
//     $objDB->freeResult($lngResultID);

//     return $detailData;
// }

// /**
//  * ヘッダー部データの生成
//  *
//  * @param [type] $doc
//  * @param [type] $trBody
//  * @param [type] $bgcolor
//  * @param [type] $aryTableHeaderName
//  * @param [type] $record
//  * @param [type] $toUTF8Flag
//  * @return void
//  */
// function fncSetHeaderDataToTr($doc, $trBody, $bgcolor, $rowspan, $aryTableHeaderName, $record, $toUTF8Flag)
// {
//     foreach ($aryTableHeaderName as $key => $value) {
//         // 項目別に表示テキストを設定
//         switch ($key) {
//             // 顧客
//             case "lngCustomerCode":
//                 if ($record["strcustomercode"] != '') {
//                     $textContent = "[" . $record["strcustomercode"] . "]" . " " . $record["strcustomername"];
//                 } else {
//                     $textContent .= "     ";
//                 }
//                 if ($toUTF8Flag) {
//                     $textContent = toUTF8($textContent);
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 請求書NO.
//             case "strInvoiceCode":
//                 $td = $doc->createElement("td", $record["strinvoicecode"]);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 請求日.
//             case "dtmInvoiceDate":
//                 $textContent = str_replace("-", "/", substr($record["dtminvoicedate"], 0, 19));
//                 if ($toUTF8Flag) {
//                     $textContent = toUTF8($textContent);
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 先月請求残額
//             case "curLastMonthBalance":
//                 $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curlastmonthbalance"]));
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 当月請求金額.
//             case "curThisMonthAmount":
//                 $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curthismonthamount"]));
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 消費税額
//             case "curSubTotal1":
//                 $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotal1"]));
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 作成日
//             case "dtmInsertDate":            
//                 $textContent = str_replace("-", "/", substr($record["dtminsertdate"], 0, 19));
//                 if ($toUTF8Flag) {
//                     $textContent = toUTF8($textContent);
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // [担当者] 担当者表示名
//             case "lngUserCode":
//                 if ($record["strusercode"] != '') {
//                     $textContent = "[" . $record["strusercode"] . "]" . " " . $record["strusername"];
//                 } else {
//                     $textContent .= "     ";
//                 };
//                 if ($toUTF8Flag) {
//                     $textContent = toUTF8($textContent);
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 入力者
//             case "lngInsertUserCode":

//                 if ($record["strinsertusercode"] != '') {
//                     $textContent = "[" . $record["strinsertusercode"] . "]" . " " . $record["strinsertusername"];
//                 } else {
//                     $textContent .= "     ";
//                 }
//                 if ($toUTF8Flag) {
//                     $textContent = toUTF8($textContent);
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 印刷回数
//             case "lngPrintCount":
//                 if (empty($record["lngprintcount"])) {
//                     $textContent = '0';
//                 } else {
//                     $textContent = $record["lngprintcount"];
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // 備考
//             case "strNote":          
//                 $textContent = $record["strnote"];
//                 if ($toUTF8Flag) {
//                     $textContent = toUTF8($textContent);
//                 }
//                 $td = $doc->createElement("td", $textContent);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//         }
//     }
// }
// /**
//  * 明細行データの生成
//  *
//  * @param [type] $doc
//  * @param [type] $trBody
//  * @param [type] $bgcolor
//  * @param [type] $aryTableDetailHeaderName
//  * @param [type] $displayColumns
//  * @param [type] $detailData
//  * @return void
//  */
// function fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $detailData, $headerData, $toUTF8Flag)
// {
//     // 指定されたテーブル項目のセルを作成する
//     foreach ($aryTableDetailHeaderName as $key => $value) {
//             // 項目別に表示テキストを設定
//             switch ($key) {                
//                 // 請求書明細番号
//                 case "lngInvoiceDetailNo":
//                     $td = $doc->createElement("td", $detailData["lnginvoicedetailno"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 納品日
//                 case "dtmDeliveryDate":
//                     if ($toUTF8Flag) {
//                         $td = $doc->createElement("td", str_replace( "-", "/", toUTF8(substr( $detailData["dtmdeliverydate"], 0, 19 ))));
//                     } else {
//                         $td = $doc->createElement("td", str_replace( "-", "/", substr( $detailData["dtmdeliverydate"], 0, 19 )));
                       
//                     }
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 納品書NO
//                 case "strSlipCode":
//                     $td = $doc->createElement("td", $detailData["lngslipno"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // [納品先表示コード] 納品先表示名
//                 case "lngDeliveryPlaceCode":
//                     if ($detailData["lngdeliveryplacecode"] != '') {
//                         $textContent = "[" . $detailData["lngdeliveryplacecode"] . "]" . " " . $detailData["strdeliveryplacename"];
//                     } else {
//                         $textContent = "     ";
//                     }
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 税抜金額
//                 case "curSubTotalPrice":
//                     if ($detailData["cursubtotalprice"] != '') {
//                         $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], "0.00");;
//                     } else {
//                         $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $detailData["cursubtotalprice"]);;
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 課税区分
//                 case "lngTaxClassCode":
//                     if ($detailData["lngtaxclasscode"] != '') {
//                         $textContent = "[" . $detailData["lngtaxclasscode"] . "]" . " " . $detailData["strtaxclassname"];
//                     } else {
//                         $textContent = "     ";
//                     }                    
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 税率
//                 case "curDetailTax":
//                     $textContent = round($detailData["curtax"] * 100) . '%';                    
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 消費額
//                 case "curTaxPrice":
//                     $cursubtotalprice = preg_replace('/,/','', $detailData["cursubtotalprice"]);
//                     $strText = number_format((int)($detailData["curtax"] * (int)$cursubtotalprice) ,2);
//                     $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $strText);
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // 明細備考
//                 case "strDetailNote":
//                     $textContent = $detailData["strnote"];
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//             }

//     }
//     return $trBody;
// }

// function fncGetInvoicesByStrInvoiceCodeSQL($strinvoicecode, $lngrevisionno)
// {
//     $aryQuery[] = "SELECT distinct";
//     $aryQuery[] = "  inv.lnginvoiceno as lnginvoiceno";
//     $aryQuery[] = "  , inv.lngrevisionno as lngrevisionno";
//     $aryQuery[] = "  , inv.dtminsertdate as dtminsertdate";
//     $aryQuery[] = "  , cust_c.strcompanydisplaycode as strcustomercode";
//     $aryQuery[] = "  , inv.strcustomername as strcustomername";
//     $aryQuery[] = "  , inv.strcustomercompanyname as strcustomercompanyname";
//     $aryQuery[] = "  , cust_c.lngCountryCode as lngcountrycode";
//     $aryQuery[] = "  , inv.strinvoicecode as strinvoicecode";
//     $aryQuery[] = "  , to_char(inv.dtminvoicedate, 'YYYY/MM/DD') as dtminvoicedate";
//     $aryQuery[] = "  , to_char(inv.dtmchargeternstart, 'YYYY/MM/DD') as dtmchargeternstart";
//     $aryQuery[] = "  , to_char(inv.dtmchargeternend, 'YYYY/MM/DD') as dtmchargeternend";
//     $aryQuery[] = "  , To_char(inv.curlastmonthbalance, '9,999,999,990.99') as curlastmonthbalance";
//     $aryQuery[] = "  , To_char(inv.curthismonthamount, '9,999,999,990.99') as curthismonthamount";
//     $aryQuery[] = "  , inv.lngmonetaryunitcode as lngmonetaryunitcode";
//     $aryQuery[] = "  , inv.strmonetaryunitsign as strmonetaryunitsign";
//     $aryQuery[] = "  , inv.lngtaxclasscode as lngtaxclasscode";
//     $aryQuery[] = "  , inv.strtaxclassname as strtaxclassname";
//     $aryQuery[] = "  , To_char(inv.cursubtotal1, '9,999,999,990.99') as cursubtotal1";
//     $aryQuery[] = "  , inv.curtax1 as curtax1";
//     $aryQuery[] = "  , To_char(inv.curtaxprice1, '9,999,999,990.99') as curtaxprice1";
//     $aryQuery[] = "  , u.struserdisplaycode as strusercode";
//     $aryQuery[] = "  , inv.strusername as strusername";
//     $aryQuery[] = "  , insert_u.struserdisplaycode as strinsertusercode";
//     $aryQuery[] = "  , inv.strinsertusername as strinsertusername";
//     $aryQuery[] = "  , to_char(inv.dtminsertdate, 'YYYY/MM/DD') as dtminsertdate";
//     $aryQuery[] = "  , inv.strnote as strnote";
//     $aryQuery[] = "  , inv.lngprintcount as lngprintcount";
//     $aryQuery[] = "  , sa.lngSalesStatusCode as lngSalesStatusCode";
//     $aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName ";
//     $aryQuery[] = "FROM";
//     $aryQuery[] = "  m_invoice inv ";
//     $aryQuery[] = "  LEFT JOIN m_sales sa ";
//     $aryQuery[] = "    ON inv.lnginvoiceno = sa.lnginvoiceno ";
//     $aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
//     $aryQuery[] = "    ON sa.lngSalesStatusCode = ss.lngSalesStatusCode ";
//     $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
//     $aryQuery[] = "    ON inv.lngcustomercode = cust_c.lngcompanycode ";
//     $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
//     $aryQuery[] = "    ON inv.lngmonetaryunitcode = mu.lngMonetaryUnitCode ";
//     $aryQuery[] = "  LEFT JOIN m_User insert_u ";
//     $aryQuery[] = "    ON inv.lngInsertUserCode = insert_u.lngusercode ";
//     $aryQuery[] = "  LEFT JOIN m_User u ";
//     $aryQuery[] = "    ON inv.lngusercode = u.lngusercode ";
//     $aryQuery[] = "  INNER JOIN t_invoicedetail inv_d ";
//     $aryQuery[] = "    ON inv.lnginvoiceno = inv_d.lnginvoiceno ";
//     $aryQuery[] = "WHERE";
//     $aryQuery[] = "  inv.bytinvalidflag = FALSE ";
//     $aryQuery[] = "  AND inv.lngrevisionno <> " .$lngrevisionno. "";
//     $aryQuery[] = "  AND inv.strinvoicecode = '". $strinvoicecode."'";
//     $aryQuery[] = "ORDER BY";
//     $aryQuery[] = "  inv.lngrevisionno DESC";

//     return implode("\n", $aryQuery);
// }
?>