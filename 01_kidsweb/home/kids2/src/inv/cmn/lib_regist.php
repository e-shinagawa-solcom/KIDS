<?
// ----------------------------------------------------------------------------
/**
*       ��������  ������Ϣ�ؿ���
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
*       ��������
*         ��������Ϣ�δؿ�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



/**
* Ǽ�ʽ񸡺��θ������ܤ�����פ���ǿ���Ǽ����ɼ�ޥ����ǡ������������SQLʸ�κ����ؿ�
*
*    Ǽ�ʽ񸡺��θ������ܤ��� SQLʸ���������
*
*    @param  Array     $arySearchDataColumn     �������Ƥ�����
*    @param  bool      $renew                   �����������ޥ�������Ͽ�ϸ��ʤ�
*    @param  Object    $objDB                   DB���֥�������
*    @param    String    $strSlipCode            Ǽ����ɼ������    ��������:������̽���    Ǽ����ɼ�����ɻ����:�����ѡ�Ʊ��Ǽ�ʽ�ΣϤΰ�������
*    @param    Integer    $lngSlipNo                Ǽ����ɼ�ֹ�    0:������̽���    Ǽ����ɼ�ֹ�����:�����ѡ�Ʊ��Ǽ����ɼ�����ɤȤ�������оݳ�Ǽ����ɼ�ֹ�
*    @return Array     $strSQL ������SQLʸ OR Boolean FALSE
*    @access public
*/
function fncGetSearchMSlipSQL ( $aryCondtition = array(), $renew = false, $objDB)
{
    // -----------------------------
    //  ��������ưŪ����
    // -----------------------------
    // ������
    // ��ӥ�����ֹ�
    $revisionNo = 1;


    $aryOutQuery = array();
//     //���ٹ�NO
//     $aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
    //Ǽ����ɼ�ֹ�
    $aryOutQuery[] = " SELECT ms.lngslipno ";
    //��ӥ�����ֹ�
    $aryOutQuery[] = ",ms.lngrevisionno ";
    // Ǽ����ɼ������
    $aryOutQuery[] = ", ms.strslipcode ";
    // ����ֹ�
    $aryOutQuery[] = ", ms.lngsalesno ";
    // �ܵҥ�����
    $aryOutQuery[] = ", ms.lngcustomercode ";
    // �ܵ�̾
    $aryOutQuery[] = ", ms.strcustomername ";
    // ����ֹ�
    $aryOutQuery[] = ", ms.lngsalesno ";
    // �ܵҥ�����
    $aryOutQuery[] = ", ms.lngcustomercode ";
    // ɽ���Ѹܵҥ�����
    $aryOutQuery[] = ", mc.strcompanydisplaycode ";
    // �ܵ�̾
    $aryOutQuery[] = ", ms.strcustomername ";
    // �ܵ�ô����̾
    $aryOutQuery[] = ", ms.strcustomerusername ";
    // Ǽ����
    $aryOutQuery[] = ", ms.dtmdeliverydate ";
    // Ǽ�ʾ�ꥳ����
    // $aryOutQuery[] = ", ms.lngdeliveryplacecode ";
    $aryOutQuery[] = ", mc2.strcompanydisplaycode as lngdeliveryplacecode";
    // Ǽ�ʾ��̾
    $aryOutQuery[] = ", ms.strdeliveryplacename ";
    // Ǽ�ʾ��ô����̾
    $aryOutQuery[] = ", ms.strdeliveryplaceusername ";
    // ��ʧ����
    $aryOutQuery[] = ", ms.dtmpaymentlimit ";
    // ���Ƕ�ʬ������]
    $aryOutQuery[] = ", ms.lngtaxclasscode ";
    // ���Ƕ�ʬ
    $aryOutQuery[] = ", ms.strtaxclassname ";
    // ������Ψ
    $aryOutQuery[] = ", ms.curtax ";
    // ô���ԥ�����
    $aryOutQuery[] = ", ms.lngusercode ";
    // ô����̾
    $aryOutQuery[] = ", ms.strusername ";
    // ��׶��
    $aryOutQuery[] = ", ms.curtotalprice";
    // �̲�ñ�̥�����
    $aryOutQuery[] = ", ms.lngmonetaryunitcode ";
    // �̲�ñ��
    $aryOutQuery[] = ", ms.strmonetaryunitsign ";
    // ������
    $aryOutQuery[] = ", ms.dtminsertdate ";
    // ���ϼԥ�����
    $aryOutQuery[] = ", ms.lnginsertusercode ";
    // ���ϼ�̾
    $aryOutQuery[] = ", ms.strinsertusername ";
    // ����
    $aryOutQuery[] = ", ms.strnote ";
    // �������
    $aryOutQuery[] = ", ms.lngprintcount ";
    // ̵���ե饰
    $aryOutQuery[] = ", ms.bytinvalidflag ";
    // From��
    $aryOutQuery[] = " FROM m_slip ms ";
    $aryOutQuery[] = " INNER JOIN (select lngslipno, MAX(lngrevisionno) as lngrevisionno from m_slip group by lngslipno)";
    $aryOutQuery[] = " rev_max on rev_max.lngslipno = ms.lngslipno and rev_max.lngrevisionno = ms.lngrevisionno";
    // JOIN
    $aryOutQuery[] = " LEFT JOIN m_company mc ON mc.lngcompanycode = ms.lngcustomercode ";
    $aryOutQuery[] = " LEFT JOIN m_company mc2 ON mc2.lngcompanycode = ms.lngdeliveryplacecode ";
    $aryOutQuery[] = " LEFT JOIN m_user mu1 ON mu1.lngusercode = ms.lngusercode ";
    $aryOutQuery[] = " LEFT JOIN m_user mu2 ON mu2.lngusercode = ms.lnginsertusercode ";

    // Where��
    $aryOutQuery[] = " WHERE not exists (select lngslipno from m_slip ms1 where ms1.lngslipno = ms.lngslipno and lngslipno < 0) " ; // ����Ѥߤ��оݳ�

    foreach($aryCondtition as $column => $value) {
        $value = trim($value);
        if(empty($value)) {
            continue;
        }

        // �ܵҥ�����(ɽ����)
        if($column == 'customerCode') {
            $aryOutQuery[] = " AND mc.strcompanydisplaycode = '" .$value ."' " ;
        }

        // �ܵ�̾
/*
        if($column == 'customerName') {
            $aryOutQuery[] = " AND strcustomername LIKE '%" .$value ."%' " ;
        }
*/
        // Ǽ�ʽ��ֹ�
        if($column == 'strSlipCode') {
            // ����޶��ڤ�������ͤ�OR����Ÿ��
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

        // Ǽ���� FROM
        if($column == 'deliveryFrom') {
            $aryOutQuery[] = " AND dtmdeliverydate >= '" .$value ." 00:00:00" ."' " ;
        }

        // Ǽ���� To
        if($column == 'deliveryTo') {
            $aryOutQuery[] = " AND dtmdeliverydate <= '" .$value ." 23:59:59" ."' " ;
        }

        // Ǽ�ʾ�ꥳ����
        if($column == 'deliveryPlaceCode') {
            $aryOutQuery[] = " AND ms.lngdeliveryplacecode = ( SELECT lngcompanycode FROM m_company WHERE strcompanydisplaycode = '" .$value ."') ";
        }
        // Ǽ�ʾ��̾
        if($column == 'deliveryPlaceName') {
            $aryOutQuery[] = " AND ms.strdeliveryplacename LIKE '%" .$value ."%' " ;
        }
        // �̲�
        if($column == 'moneyClassCode') {
            $aryOutQuery[] = " AND lngmonetaryunitcode = " .$value ." " ;
        }

        // ���Ƕ�ʬ
        if($column == 'taxClassCode') {
            $aryOutQuery[] = " AND lngtaxclasscode = " .$value ." " ;
        }

        // ô���ԥ�����
        if($column == 'inChargeUserCode') {
            $aryOutQuery[] = " AND mu1.struserdisplaycode LIKE '%" .$value ."%' " ;
        }

        // ô����
        if($column == 'inChargeUserName') {
            $aryOutQuery[] = " AND strusername LIKE '%" .$value ."%' " ;
        }

        // �����ԥ�����
        if($column == 'inputUserCode') {
            $aryOutQuery[] = " AND mu2.struserdisplaycode LIKE '%" .$value ."%' " ;
        }

        // ������
        if($column == 'inputUserName') {
            $aryOutQuery[] = " AND strinsertusername LIKE '%" .$value ."%' " ;
        }
    }

    if($renew == false)
    {
        $aryOutQuery[] = " AND  ms.lngsalesno NOT IN( SELECT m_sales.lngsalesno FROM m_sales WHERE lnginvoiceno IS NOT NULL) " ;
    }

    // OrderBy��
    $aryOutQuery[] = " ORDER BY ms.lngslipno ASC , ms.lngrevisionno DESC ";


    return implode("\n", $aryOutQuery);
}



/**
 * ������ֹ椫����������٤�ɳ�Ť�Ǽ����ɼ�ޥ����ǡ������������SQLʸ�κ����ؿ�
 *
 *
 *    @param    Integer    $lnginvoiceno            �����No
 *    @return Array     $strSQL ������SQLʸ OR Boolean FALSE
 *    @access public
 */
function fncGetSearchMSlipInvoiceNoSQL ( $lnginvoiceno, $lngrevisionno )
{

    $aryOutQuery = array();
    //Ǽ����ɼ�ֹ�
    $aryOutQuery[] = " SELECT DISTINCT ON (ms.lngSlipNo) ms.lngSlipNo ";
    //��ӥ�����ֹ�
    $aryOutQuery[] = ", ms.lngrevisionno ";
    // Ǽ����ɼ������
    $aryOutQuery[] = ", ms.strslipcode ";
    // ����ֹ�
    $aryOutQuery[] = ", ms.lngsalesno ";
    // �ܵҥ�����
    $aryOutQuery[] = ", ms.lngcustomercode ";
    // ɽ���Ѹܵҥ�����
    $aryOutQuery[] = ", mc.strcompanydisplaycode ";
    // �ܵ�̾
    $aryOutQuery[] = ", ms.strcustomername ";
    // �ܵ�ô����̾
    $aryOutQuery[] = ", ms.strcustomerusername ";
    // Ǽ����
    $aryOutQuery[] = ", ms.dtmdeliverydate ";
    // Ǽ�ʾ�ꥳ����
    $aryOutQuery[] = ", mc2.strcompanydisplaycode as lngdeliveryplacecode ";
    // Ǽ�ʾ��̾
    $aryOutQuery[] = ", ms.strdeliveryplacename ";
    // Ǽ�ʾ��ô����̾
    $aryOutQuery[] = ", ms.strdeliveryplaceusername ";
    // ��ʧ����
    $aryOutQuery[] = ", ms.dtmpaymentlimit ";
    // ���Ƕ�ʬ������]
    $aryOutQuery[] = ", ms.lngtaxclasscode ";
    // ���Ƕ�ʬ
    $aryOutQuery[] = ", ms.strtaxclassname ";
    // ������Ψ
    $aryOutQuery[] = ", ms.curtax ";
    // ô���ԥ�����
    $aryOutQuery[] = ", ms.lngusercode ";
    // ô����̾
    $aryOutQuery[] = ", ms.strusername ";
    // ��׶��
    $aryOutQuery[] = ", ms.curtotalprice ";
    // �̲�ñ�̥�����
    $aryOutQuery[] = ", ms.lngmonetaryunitcode ";
    // �̲�ñ��
    $aryOutQuery[] = ", ms.strmonetaryunitsign ";
    // ������
    $aryOutQuery[] = ", ms.dtminsertdate ";
    // ���ϼԥ�����
    $aryOutQuery[] = ", ms.lnginsertusercode ";
    // ���ϼ�̾
    $aryOutQuery[] = ", ms.strinsertusername ";
    // ����
    $aryOutQuery[] = ", ms.strnote ";
    // �������
    $aryOutQuery[] = ", ms.lngprintcount ";
    // ̵���ե饰
    $aryOutQuery[] = ", ms.bytinvalidflag ";
    // From��
    $aryOutQuery[] = " FROM m_slip ms ";
    // JOIN
    $aryOutQuery[] = " LEFT JOIN m_company mc ON (mc.lngcompanycode = ms.lngcustomercode ) ";
    $aryOutQuery[] = " LEFT JOIN m_company mc2 ON (mc2.lngcompanycode = ms.lngdeliveryplacecode ) ";

    // Where��
    // strslipcode �򸡺����륵�֥�����
    $subQuery  = "select DISTINCT lngslipno from t_invoicedetail where lnginvoiceno = "  .$lnginvoiceno ."  ";
    $subQuery .= "AND lngrevisionno = " .$lngrevisionno. " ";

    $aryOutQuery[] = " WHERE lngrevisionno >= 0 " ;    // �о�Ǽ����ɼ�ֹ�λ���
    $aryOutQuery[] = " AND ms.lngslipno IN ( " .$subQuery ." ) " ;

    // order��
    $aryOutQuery[] = " ORDER BY  ms.lngSlipNo ASC , ms.lngrevisionno DESC " ;

    // �������ʿ�פ�ʸ������Ѵ�
    $query = implode("\n",$aryOutQuery);

    return $query;

}



/**
 * ���ꤷ��������ֹ椫������񥳡��ɤ��������(�ʤ���Х����ɤ�����)
 *
 *
 *    @param  String    $lnginvoiceno     ������ֹ�
 *    @param  date      $isDummy          true  : ��������̤ȯ��
 *                                         false : ��������ȯ��
 *    @param  Object    $objDB            DB���֥�������
 *    @return str       strinvoicecode    TT-BMMnnn
 *                                         TT������4/1�˥�����ȥ��åס�2019ǯ�٤�42����
 *                                         MM����(01���12��
 *                                         nnn������Ǥ�Ϣ�֡�001���999�ˤ�ư����
 *    @access public
 */
function fncGetStrInvoiceCode( $lnginvoiceno = null, $isDummy=true , $objDB )
{
    // ��Ͽ�Ѥʤ���Ͽ����Ƥ��륳���ɤ��֤�
    if ( !empty($lnginvoiceno) )
    {
        $strQuery = " SELECT DISTINCT strinvoicecode FROM m_invoice WHERE lnginvoiceno = " . $lnginvoiceno . " ";

        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
        // �쥳���ɤ�����Х����ɤ��֤�
        if ( $lngResultNum )
        {
            $result = pg_fetch_assoc($lngResultID);
            $objDB->freeResult($lngResultID);
            return $result['strinvoicecode'];
        }
        $objDB->freeResult($lngResultID);
    }

    // ������ֹ�(strinvoicecode)��ȯ��
    $format = '%02d-B%02d%03d';
    // ������
    $basePeriod = 42;
    $baseDate   = '2019-04-01';

    $dateTimeBase = new DateTime($baseDate);
    $dateTimeNow  = new DateTime(date('Y-m-d'));
    $diff   = $dateTimeBase->diff($dateTimeNow);
    $period = $basePeriod + (int)$diff->format('%Y');
    $thisMonth = $dateTimeNow->format('m');

    // dummy�ν���(̵�̤ʥ�������ȯ�Ԥ��ɤ�)
    if($isDummy)
    {
        // �������ȯ�Ԥ��줿����񥫥���Ȥ��������
        $start = date('Y-m-01', strtotime('first day of ' .$dateTimeNow->format('Y-m-d')));
        $end   = date('Y-m-01', strtotime($start. '+1 month'));
        $strQuery = "SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE dtminsertdate >= '" . $start . "' AND dtminsertdate < '" . $end . "' " ;

        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
        // ������ߤ����ΤǷ�̤Ϥʤ��Ƥ�����ʤ�
        $num = $lngResultNum+1;
        // format�˽��ä�����񥳡��ɤ��֤�
        $strinvoicecode = sprintf($format, $period, $thisMonth, $num );
    }
    else
    {
        // ��������ȯ��
        $sequenceInvoiceCode = fncGetDateSequence($period, $thisMonth, 'm_invoice.strinvoicecode', $objDB);
        // format�˽��ä�����񥳡��ɤ��֤�
        $strinvoicecode = sprintf($format, substr($sequenceInvoiceCode,0,2), substr($sequenceInvoiceCode,2,2), substr($sequenceInvoiceCode,5,3) );
    }

    return $strinvoicecode;
}

/**
 * ���ꤷ��ɽ���Ѹܵҥ����ɤ��� �ܵ�̾/�ܵҼ�̾���֤�
 *
 *
 *    @param  String    $companyDisplayCode  ɽ���Ѹܵҥ�����
 *    @param  Object    $objDB               DB���֥�������
 *    @return int       $lngCustomerCode     �ܵҥ�����
 *    @return string    printCustomerName    ɽ���Ѹܵ�̾
 *    @return string    printCompanyName     ɽ���ѸܵҼ�̾
 *    @return string    $result['strcompanydisplayname']    DB��ɽ���Ѳ��̾��
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

    // �������ʿ�פ�ʸ������Ѵ�
    $query = implode("\n",$strQuery);

    // ������¹�
    list ( $lngResultID, $lngResultNum ) = fncQuery( $query, $objDB );
    // �쥳���ɤ���иܵ�̾���ܵҼ�̾�����ꤹ��
    if ( $lngResultNum )
    {
        // �������Ϣ����������
        $result =  pg_fetch_assoc($lngResultID);
        $lngCustomerCode = $result['lngcompanycode'];

        if( !empty($result['strprintcompanyname']) )
        {
            // �����Ѳ�ҥޥ���.�����Ѳ��̾���ߤĤ��ä����
            // �ܵҼ�̾
            $printCompanyName  = $result['strprintcompanyname'];
            // �ܵ�̾
            $printCustomerName = $result['strcompanydisplayname'];
        }
        else
       {
           $organizationName = ($result['strorganizationname'] == '-') ? '' : $result['strorganizationname'];
            // �ܵҼ�̾
            if($records['bytorganizationfront'] == 't')
            {
                $printCompanyName = $organizationName .$result['strcompanydisplayname'];
            }
            else

           {
               $printCompanyName  = $result['strcompanydisplayname'] .$organizationName;
            }
            // �ܵ�̾(���ꤷ�ʤ�)
            $printCustomerName = "";
        }
        $objDB->freeResult($lngResultID);
    }

    return [ $printCustomerName, $printCompanyName, $lngCustomerCode, $result['strcompanydisplayname'] ];
}


/**
 * ���ꤷ��ɽ���Ѹܵҥ����ɡ����դ��� ���������֤�
 *
 *
 *    @param  String    $companyDisplayCode  ɽ���Ѹܵҥ�����
 *    @param  date      $targetDate          ������(�ʤ���Х����ƥ�DATE)
 *    @param  Object    $objDB               DB���֥�������
 *    @return date      $closedDay           ������
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

    // �������ʿ�פ�ʸ������Ѵ�
    $query = implode("\n",$strQuery);
    // ������¹�
    list ( $lngResultID, $lngResultNum ) = fncQuery( $query, $objDB );

    // �쥳���ɤ���иܵ�̾���ܵҼ�̾�����ꤹ��
    if ( $lngResultNum )
    {
        // �������Ϣ����������
        $result = pg_fetch_assoc($lngResultID);
        $lngClosedDay = (int)$result['lngclosedday'];
        if($lngClosedDay < 0){
            return $closedDay;
        }
        // ���դ����
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
                // ���
                $dateTime->add(DateInterval::createFromDateString('1 month'));
            }
        }
        $objDB->freeResult($lngResultID);
        $closedDay = $dateTime->format('Y-m-').$lngClosedDay;
    }
    return $closedDay;
}



/**
 * ���ꤷ���̲�ñ�̥����ɤ��� �̲�ñ��̾���֤�
 *
 *
 *    @param  int      $monetaryUnitCode  �̲�ñ�̥�����
 *    @param  Object   $objDB               DB���֥�������
 *    @return string   $monetaryUnitSign    �̲�ñ��̾
 *    @access public
 */
function fncGetMonetaryunitSign( $monetaryUnitCode ,$objDB)
{
    // �̲�ñ�̥ޥ��������
    $query = "SELECT strmonetaryunitsign FROM m_monetaryunit WHERE lngmonetaryunitcode = " .$monetaryUnitCode;

    // ������¹�
    list ( $lngResultID, $lngResultNum ) = fncQuery( $query, $objDB );
    // �쥳���ɤ�����̲�ñ��̾�Τ����ꤹ��
    $monetaryUnitSign = "";
    if ( $lngResultNum )
    {
        // �������Ϣ����������
        $result =  pg_fetch_assoc($lngResultID);
        $monetaryUnitSign = $result['strmonetaryunitsign'];
    }
    return $monetaryUnitSign;

}


/**
 * ����񸡺��θ������ܤ�����פ���ǿ��������ǡ������������SQLʸ�κ����ؿ�
 *
 *    ����񸡺��θ������ܤ��� SQLʸ���������
 *
 *    @param  Array     $arySearchColumn         �����оݥ����̾������
 *    @param  Array     $arySearchDataColumn     �������Ƥ�����
 *    @param  Object    $objDB                   DB���֥�������
 *    @return Array     $strSQL ������SQLʸ OR Boolean FALSE
 *    @access public
 */
function fncGetSearchInvoiceSQL ( $arySearchColumn, $arySearchDataColumn, $objDB, $strSessionID)
{
    // -----------------------------
    //  ��������ưŪ����
    // -----------------------------
    // ���پ���ɲúѤߥե饰
    $detailFlag = FALSE;

    // Ʊ��Ǽ����ɼ�����ɤΥǡ��������������
    if ( $strSlipCode )
    {
        // Ʊ��Ǽ����ɼ�����ɤ��Ф��ƻ����Ǽ����ɼ�ֹ�Υǡ����Ͻ�������
        if ( $lngSlipNo )
        {
            $aryQuery[] = " WHERE inv.bytInvalidFlag = FALSE AND s.strSlipCode = '" . $strSlipCode . "'";
        }
        else
        {
            fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "../inv/search/index.php?strSessionID=".$strSessionID, $objDB );
        }
    }
    // �����⡼�ɤǤ�Ʊ��Ǽ����ɼ�����ɤ��Ф��븡���⡼�ɰʳ��ξ��ϸ��������ɲä���
    else
    {
        // ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ����Τ�
        $aryQuery[] = " WHERE inv.bytinvalidflag = FALSE AND inv.lngrevisionno >= 0";
        // ���������å��ܥå�����ON�ι��ܤΤ߸��������ɲ�
        for ( $i = 0; $i < count($arySearchColumn); $i++ )
        {
            $strSearchColumnName = $arySearchColumn[$i];

            // ----------------------------------------------
            //   Ǽ�ʽ�ޥ����ʥإå����ˤθ������
            // ----------------------------------------------
            // �ܵҡ�������
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

            // ���Ƕ�ʬ�ʾ����Ƕ�ʬ��
            if ( $strSearchColumnName == "lngTaxClassCode" )
            {
                if ( $arySearchDataColumn["lngTaxClassCode"] )
                {
                    $aryQuery[] = " AND inv.lngtaxclasscode = '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
                }
            }

            // Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO����������٥ơ��֥뤫�����
            if ( $strSearchColumnName == "strInvoiceCode" )
            {
                if ( $arySearchDataColumn["strInvoiceCode"] )
                {
                    // ����޶��ڤ�������ͤ�OR����Ÿ��
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

            // ������
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

            // ������
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

//             // Ǽ����
//             if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
//             {
//                 if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
//                 {
//                     //��ҥޥ�����ɳ�Ť����ͤ����
//                     $aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
//                 }
//                 if ( $arySearchDataColumn["strDeliveryPlaceName"] )
//                 {
//                     $aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//                 }
//             }

            // ��ɼ��
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
            // ���ϼ�
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
            //   Ǽ����ɼ���٥ơ��֥���������ˤθ������
            // ----------------------------------------------
            // ��ʸ��NO.
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

                    // ����޶��ڤ�������ͤ�OR����Ÿ��
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


            // ����ʬ
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
    //   SQLʸ�κ���
    // ---------------------------------
    $aryOutQuery = array();
    $aryOutQuery[] = "SELECT distinct inv.lnginvoiceno as lnginvoiceno";    // ������ֹ��ֹ�
    $aryOutQuery[] = "    ,inv.lngrevisionno as lngrevisionno";                // ��ӥ�����ֹ�
    $aryOutQuery[] = "    ,inv.lnginvoiceno as lngpkno";                // ������ֹ��ֹ�
    $aryOutQuery[] = "    ,inv.dtminsertdate as dtminsertdate";                // ������

    // �ܵ�
    $arySelectQuery[] = ", cust_c.strcompanydisplaycode as strcustomerdisplaycode";               // �ܵҥ�����
    $arySelectQuery[] = ", inv.strcustomername as strcustomername";               // �ܵ�̾
    $arySelectQuery[] = ", inv.strcustomercompanyname as strcustomerdisplayname"; // �ܵҼ�̾
    // �ܵҤι�
    $arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
    // ����񥳡���
    $arySelectQuery[] = ", inv.strinvoicecode as strinvoicecode";
    // ������
    $arySelectQuery[] = ", to_char( inv.dtminvoicedate, 'YYYY/MM/DD' ) as dtminvoicedate";
    // ������� ��
    $arySelectQuery[] = ", to_char( inv.dtmchargeternstart, 'YYYY/MM/DD' ) as dtmchargeternstart";
    // ������� ��
    $arySelectQuery[] = ", to_char( inv.dtmchargeternend, 'YYYY/MM/DD' ) as dtmchargeternend";
    // ��������ĳ�
    $arySelectQuery[] = ", To_char( inv.curlastmonthbalance, '9,999,999,990.99' ) as curlastmonthbalance";
    // ��������
    $arySelectQuery[] = ", To_char( inv.curthismonthamount, '9,999,999,990.99' ) as curthismonthamount";
    // �̲�ñ�̥�����
    $arySelectQuery[] = ", inv.lngmonetaryunitcode as lngmonetaryunitcode";
    // �̲�ñ��
    $arySelectQuery[] = ", inv.strmonetaryunitsign as strmonetaryunitsign";
    // ���Ƕ�ʬ������
    $arySelectQuery[] = ", inv.lngtaxclasscode as lngtaxclasscode";
    // ���Ƕ�ʬ̾
    $arySelectQuery[] = ", inv.strtaxclassname as strtaxclassname";
    // ��ȴ���1
    $arySelectQuery[] = ", To_char( inv.cursubtotal1, '9,999,999,990.99' ) as cursubtotal";
    // ������Ψ1
    $arySelectQuery[] = ", inv.curtax1 as curtax";
    // �����ǳ�1
    $arySelectQuery[] = ", To_char( inv.curtaxprice1, '9,999,999,990.99' ) as curtaxprice";
    // ô����
    $arySelectQuery[] = ", u.struserdisplaycode as strusercode";
    $arySelectQuery[] = ", inv.strusername as strusername";
    // ������
    $arySelectQuery[] = ", insert_u.struserdisplaycode as strinsertusercode";
    $arySelectQuery[] = ", inv.strinsertusername as strinsertusername";
    // ������
    $arySelectQuery[] = ", to_char( inv.dtminsertdate, 'YYYY/MM/DD' ) as dtminsertdate";
    // ����
    $arySelectQuery[] = ", inv.strnote as strnote";
    // â����
    $arySelectQuery[] = ", inv.description as description";
    // �������
    $arySelectQuery[] = ", inv.lngprintcount as lngprintcount";


//     // ���Ƕ�ʬ
//     $arySelectQuery[] = ", s.strtaxclassname as strtaxclassname";
//     // Ǽ����ɼ�����ɡ�Ǽ�ʽ�NO��
//     $arySelectQuery[] = ", s.strSlipCode as strSlipCode";
//     // Ǽ����
//     $arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
//     // ��׶��
//     $arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
    //// ���Σ�
    //$arySelectQuery[] = ", s.strSalesCode as strSalesCode";
    // �����֥�����
    $arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
    $arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
//     // �̲�ñ��
//     $arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";

    // select�� �����꡼Ϣ��
    $aryOutQuery[] = implode("\n", $arySelectQuery);

    // From�� ������
    $aryFromQuery = array();
    $aryFromQuery[] = " FROM m_invoice inv";
    $aryFromQuery[] = " LEFT JOIN m_sales sa ON inv.lnginvoiceno = sa.lnginvoiceno";
    $aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";
    $aryFromQuery[] = " LEFT JOIN m_Company cust_c ON inv.lngcustomercode = cust_c.lngcompanycode";
    $aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON inv.lngmonetaryunitcode = mu.lngMonetaryUnitCode";
    $aryFromQuery[] = " LEFT JOIN m_User insert_u ON inv.lngInsertUserCode = insert_u.lngusercode";
    $aryFromQuery[] = " LEFT JOIN m_User u ON inv.lngusercode = u.lngusercode";
    // $aryFromQuery[] = " LEFT JOIN m_Company delv_c ON inv.strcustomercode = delv_c.strcompanydisplaycode";
    //����������٤��ʤ�����������ΰٽ���
    $aryFromQuery[] = " INNER JOIN t_invoicedetail inv_d ON inv.lnginvoiceno = inv_d.lnginvoiceno";

    // From�� �����꡼Ϣ��
    $aryOutQuery[] = implode("\n", $aryFromQuery);


    // Where�� �����꡼Ϣ��
//     $aryOutQuery[] = $strDetailQuery;
    $aryOutQuery[] = implode("\n", $aryQuery);

//     // ���ٹ��Ѥξ��Ϣ��
//     $aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


    /////////////////////////////////////////////////////////////
    //// �ǿ����ʥ�ӥ�����ֹ椬���硢��Х����ֹ椬���硢     ////
    //// ���ĥ�ӥ�����ֹ�����ͤ�̵���ե饰��FALSE��           ////
    //// Ʊ��Ǽ����ɼ�����ɤ���ĥǡ�����̵�����ǡ���          ////
    /////////////////////////////////////////////////////////////
    $aryOutQuery[] = " AND inv.lngrevisionno = ( "
                    . "SELECT MAX( inv1.lngrevisionno ) FROM m_invoice inv1 WHERE inv1.strinvoicecode = inv.strinvoicecode AND inv1.bytinvalidflag = false )";

    // ����ǡ������оݳ�
    $aryOutQuery[] = " AND 0 <= ( "
            . "SELECT MIN( inv2.lngrevisionno ) FROM m_invoice inv2 WHERE inv2.bytinvalidflag = false AND inv2.strinvoicecode = inv.strinvoicecode )";

    // �����Ⱦ������
    $aryOutQuery[] = " ORDER BY inv.lnginvoiceno DESC";

    return implode("\n", $aryOutQuery);
}



/**
* ������ֹ椫�������ޥ�����ɳ�Ť����������٤��������SQLʸ�κ����ؿ�
*
*
*    @param    Integer    $lnginvoiceno            �����No
*    @param    Integer    $lngrevisionno         ��ӥ����No
*    @return Array     $strSQL ������SQLʸ OR Boolean FALSE
*    @access public
*/
function fncGetSearchInvoiceDetailSQL ( $lnginvoiceno, $lngrevisionno=null )
{
    // -----------------------------
    //  ��������ưŪ����
    // -----------------------------

    // ���о�� ̵���ե饰�����ꤵ��Ƥ��餺���ǿ���������٤Τ�
    $aryQuery[] = " WHERE inv_d.lngrevisionno >= 0 ";
    $aryQuery[] = " AND  inv_d.lnginvoiceno = " . (int)$lnginvoiceno ." ";
    
    if($lngrevisionno !== null){
        $aryQuery[] = " AND  inv_d.lngrevisionno = " . (int)$lngrevisionno ." ";
    } else {
        $aryQuery[] = " AND  inv_d.lngrevisionno = (SELECT MAX(inv.lngrevisionno) FROM m_invoice inv WHERE inv.lnginvoiceno = " . (int)$lnginvoiceno .") ";
    }
    // ����Ѥߤ��ӽ�
    $aryQuery[] = " AND inv_d.lnginvoiceno NOT IN ( ";
    $aryQuery[] = " SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE lngrevisionno = -1";
    $aryQuery[] = " ) ";

    // order by
    $aryQuery[] = " ORDER BY inv_d.lnginvoicedetailno ASC";

    //     $aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCode"] . "'";


    // ---------------------------------
    //   SQLʸ�κ���
    // ---------------------------------
    $aryOutQuery = array();
    // ������ֹ�
    $arySelectQuery[] = "SELECT inv_d.lnginvoiceno as lnginvoiceno";
    // ��ӥ�����ֹ�
    $arySelectQuery[] = ", inv_d.lngrevisionno as lngrevisionno";
    // ����������ֹ�
    $arySelectQuery[] = ", inv_d.lnginvoicedetailno";
    // Ǽ����
    $arySelectQuery[] = ", to_char( inv_d.dtmdeliverydate, 'YYYY/MM/DD HH:MI:SS' ) as dtmdeliverydate";
    // Ǽ�ʾ�ꥳ����
    $arySelectQuery[] = ", delv_c.strcompanydisplaycode as lngdeliveryplacecode";
    // Ǽ�ʾ��̾
    $arySelectQuery[] = ", inv_d.strdeliveryplacename as strdeliveryplacename";
    // ����
    $arySelectQuery[] = ", To_char( inv_d.cursubtotalprice, '9,999,999,990.99' ) as cursubtotalprice";
    // ���Ƕ�ʬ������
    $arySelectQuery[] = ", inv_d.lngtaxclasscode as lngtaxclasscode";
    // ���Ƕ�ʬ̾
    $arySelectQuery[] = ", inv_d.strtaxclassname as strtaxclassname";
    // ������Ψ
    $arySelectQuery[] = ", inv_d.curtax as curtax";
    // ����
    $arySelectQuery[] = ", inv_d.strnote as strnote";
    // Ǽ�ʽ��ֹ�
    $arySelectQuery[] = ", inv_d.lngslipno as lngslipno";
    // Ǽ�ʽ��ӥ�����ֹ�
    $arySelectQuery[] = ", inv_d.lngsliprevisionno as lngsliprevisionno";
    // Ǽ����ɼ������
    $arySelectQuery[] = ", slip_m.strslipcode as strslipcode";

    // select�� �����꡼Ϣ��
    $aryOutQuery[] = implode("\n", $arySelectQuery);

    // From�� ������
    $aryFromQuery = array();
    $aryFromQuery[] = " FROM t_invoicedetail inv_d ";
    $aryFromQuery[] = " LEFT JOIN m_slip slip_m ON inv_d.lngslipno = slip_m.lngslipno";
//     $aryFromQuery[] = " LEFT JOIN m_Company cust_c ON inv_d.lngdeliveryplacecode = cust_c.lngdeliveryplacecode";
    $aryFromQuery[] = " LEFT JOIN m_Company delv_c ON inv_d.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
    // From�� �����꡼Ϣ��
    $aryOutQuery[] = implode("\n", $aryFromQuery);

    // Where�� �����꡼Ϣ��
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
 * �������Ͽ�ؿ�
 *
 *    �Ϥ��줿POST�ͤ������ޥ���������Ͽ��ɬ�פ�������֤�
 *
 *    @param  Array     $aryData          �Уϣӣԥǡ�����
 *    @param  Array     $aryResult        ������ֹ��ɳ�Ť������ޥ�������Ǽ���줿����
 *    @param  Object    $objDB               DB���֥�������
 *    @access public
 */
function fncInvoiceInsertReturnArray($aryData, $aryResult=null, $objAuth, $objDB)
{
    $insertAry = [];

    // �������ٰ�����Ǽ�ʽ��ֹ�
    $slipCodeArray = explode(',' ,$aryData['slipCodeList']);
    $insertAry['slipCodeArray']  = $slipCodeArray;

    // �����No
    // ��Ͽ�� : MAX(������ֹ���������ޥ���.������ֹ�)+1
    // ������ : �������������ޥ���.������ֹ�
    $insertAry['lnginvoiceno'] = empty($aryResult['lnginvoiceno'])
                                    ? null
                                : $aryResult['lnginvoiceno'];

    // ��ӥ����No
    // ��Ͽ�� : 0
    // ������ : �������������ޥ���.��ӥ�����ֹ� + 1
    $insertAry['lngrevisionno'] = !isset($aryResult['lngrevisionno'])
                                    ? 0
                                    : (int)$aryResult['lngrevisionno']+1;

    // ������
    $insertAry['dtminvoicedate'] = $aryData['dtminvoicedate'];

    // ����񥳡���
    // ��Ͽ�� : �롼��˴�Ť�������������
    // ������ : �������������ޥ���.����񥳡���
    $insertAry['strinvoicecode'] = empty($aryResult['strinvoicecode'])
                                    ? fncGetStrInvoiceCode(null, false, $objDB)
                                    : $aryResult['strinvoicecode'];

    // �ܵҥ�����(DISPLAY)
    $insertAry['strcustomercode'] = $aryData['strcustomercode'];

    // �ܵ�̾
    $insertAry['strcustomername'] = $aryData['strcustomercompanyname'];

    // �ܵҼ�̾
    $insertAry['strcustomercompanyname'] = $aryData['strcustomercompanyname'];

    // ������� ��
    $insertAry['dtmchargeternstart'] = $aryData['dtmchargeternstart'];

    // ������� ��
    $insertAry['dtmchargeternend'] = $aryData['dtmchargeternend'];

    // �������
//     $dtmchargeternend = $aryData['dtmchargeternend'];

    // ��������ĳ�
    $insertAry['curlastmonthbalance'] = $aryData['curlastmonthbalance'];

    // ��������
    $insertAry['curthismonthamount']   = $aryData['curthismonthamount'];

    // �̲�ñ�̥����� //�߰ʳ��ϡ�
    $insertAry['lngmonetaryunitcode'] = 1;
//     $insertAry['lngmonetaryunitcode']  = $aryData['lngmonetaryunitcode'];
    // �̲�ñ��̾��
    $insertAry['strmonetaryunitsign']  = fncGetMonetaryunitSign($insertAry['lngmonetaryunitcode'] ,$objDB);

    // ���Ƕ�ʬ������
    // ���Ƕ�ʬ̾
    $insertAry['lngtaxclasscode']  = $aryData['lngtaxclasscode'];
    $insertAry['strtaxclassname']  = $aryData['strtaxclassname'];

    // ��ȴ���1(����������)
    $insertAry['cursubtotal1'] = $aryData['cursubtotal1'];
    // ������Ψ1
    $insertAry['curtax1'] = (int)$aryData['curtax1'];
    // �����ǳ�1
    $insertAry['curtaxprice1'] = $aryData['curtaxprice'];

    // ������
    $insertAry['dtminsertdate'] = 'now()';

    // ô���ԥ�����
    $insertAry['strusercode'] = $aryData['strusercode'];
    // ô����̾
    $insertAry['strusername'] = $aryData['strusername'];

    // �����ԥ�����
    $insertAry['strinsertusercode'] = $objAuth->UserCode;
    // ������̾
    $insertAry['strinsertusername'] = $objAuth->UserDisplayName;
    // ����
    $insertAry['strnote'] = $aryData['strnote'];
    // ����
    $insertAry['description'] = $aryData['description'];

    return $insertAry;
}



/**
 * �������Ͽ�ؿ�
 *
 *    �Ϥ��줿�ǡ����������ޥ�������Ͽ����
 *
 *    @param  Array    $insertAry  ��Ͽ�ǡ���
 *    @param  Object   $objDB      DB���֥�������
 *    @access public
 */
function fncInvoiceInsert( $insertAry ,$objDB, $objAuth)
{
    // �����ޥ����˥ǡ�������Ͽ����
    // ������ֹ�����
    if($insertAry['lnginvoiceno'] > 0){
        $sequence_m_lnginvoice = $insertAry['lnginvoiceno'];
    }
    else
    {
        // ��������ȯ��
        $sequence_m_lnginvoice = fncGetSequence('m_invoice.lnginvoiceno', $objDB);
    }

    $aryQuery    = array();
    $aryQuery[] = "INSERT INTO m_invoice (";
    $aryQuery[] = "lnginvoiceno, ";             // ������ֹ�
    $aryQuery[] = "lngrevisionno, ";            // ��ӥ�����ֹ� //��Ͽ����0
    $aryQuery[] = "strinvoicecode, ";           // ����񥳡���
    $aryQuery[] = "dtminvoicedate, ";           // ������
    $aryQuery[] = "lngcustomercode, ";          // �ܵҥ�����
    $aryQuery[] = "strcustomername, ";          // �ܵ�̾
    $aryQuery[] = "strcustomercompanyname, ";   // �ܵҼ�̾
    $aryQuery[] = "dtmchargeternstart, ";       // �������(FROM)
    $aryQuery[] = "dtmchargeternend, ";         // �������(TO)
    $aryQuery[] = "curlastmonthbalance, ";      // ��������ĳ�
    $aryQuery[] = "curthismonthamount, ";       // ��������
    $aryQuery[] = "lngmonetaryunitcode, ";      // �̲�ñ�̥�����
    $aryQuery[] = "strmonetaryunitsign, ";      // �̲�ñ��
    $aryQuery[] = "lngtaxclasscode, ";          // ���Ƕ�ʬ������
    $aryQuery[] = "strtaxclassname, ";          // ���Ƕ�ʬ̾
    $aryQuery[] = "cursubtotal1, ";             // ��ȴ�����1
    $aryQuery[] = "curtax1, ";                  // ������Ψ1
    $aryQuery[] = "curtaxprice1, ";             // �����ǳ�1
    $aryQuery[] = "dtminsertdate, ";            // ������
    $aryQuery[] = "lngusercode, ";              // ô���ԥ�����
    $aryQuery[] = "strusername, ";              // ô����̾
    $aryQuery[] = "lnginsertusercode, ";        // �����ԥ�����
    $aryQuery[] = "strinsertusername, ";        // ������̾
    $aryQuery[] = "strnote, ";                  // ����
     $aryQuery[] = "lngprintcount, ";         // �������
    $aryQuery[] = "bytinvalidflag, ";            // ̵���ե饰
    $aryQuery[] = "description ";            // ����
    $aryQuery[] = ") values (";
    // ������ֹ�
    $aryQuery[] = $sequence_m_lnginvoice ." ,";
    $aryQuery[] = $insertAry['lngrevisionno'] ." ,";                                        // ��ӥ�����ֹ�
    $aryQuery[] = "'" .$insertAry['strinvoicecode'] ."' ,";                                 // ����񥳡���
    $aryQuery[] = "'" .$insertAry['dtminvoicedate'] ."' ,";                                 // ������
    $aryQuery[] = "(select lngcompanycode from m_company where strcompanydisplaycode='". $insertAry['strcustomercode'] ."')  ,";                               // �ܵҥ�����(ɽ����)
    $aryQuery[] = "'" .$insertAry['strcustomername']."' , " ;                               // �ܵ�̾
    $aryQuery[] = "'" .$insertAry['strcustomercompanyname']."' , " ;                        // �ܵҼ�̾
    $aryQuery[] = "'". $insertAry['dtmchargeternstart'] ."'  ,";                            // �������(FROM)
    $aryQuery[] = "'". $insertAry['dtmchargeternend'] ."'  ,";                              // �������(TO)
    $aryQuery[] = $insertAry['curlastmonthbalance'] ." ,";                                  // ��������ĳ�
    $aryQuery[] = (int)$insertAry['cursubtotal1']." ,";                                     // ��������
    $aryQuery[] = $insertAry['lngmonetaryunitcode'] ." ,";                                  // �̲�ñ�̥����� default ?
    $aryQuery[] = "'". preg_replace('/\\\/','��',$insertAry['strmonetaryunitsign']) ."'  ,";// �̲�ñ�� \�Υ��󥵡��Ȥ��Ǥ��ʤ��Τ������б�
    $aryQuery[] = (int)$insertAry['lngtaxclasscode'] ." , ";                                // ���Ƕ�ʬ������
    $aryQuery[] = "'" .$insertAry['strtaxclassname']."' , ";                                // ���Ƕ�ʬ̾
    $aryQuery[] =  $insertAry['curthismonthamount'] .",";                                   // ��ȴ�����1
    $aryQuery[] = (int)$insertAry['curtax1'] .",";                                          // ������Ψ1
    $aryQuery[] = (int)$insertAry['curtaxprice1'] .",";                                     // �����ǳ�1
    $aryQuery[] = "now() ,";                                                                // ������
    $aryQuery[] = "(select lngusercode from m_user where struserdisplaycode = '". $insertAry['strusercode'] ."')  ,";                                   // ô���ԥ�����
    $aryQuery[] = "'". $insertAry['strusername'] ."'  ,";                                   // ô����̾
    $aryQuery[] = $objAuth->UserCode . " ,";                              // �����ԥ�����
    $aryQuery[] = "'" .$objAuth->UserDisplayName ."' ,";                              // ������̾
    $aryQuery[] = "'" .$insertAry['strnote'] ."', ";                                        // ����
    $aryQuery[] = "0 ,";                                                                 // �������
    $aryQuery[] = "FALSE, ";                                                                 // ̵���ե饰
    $aryQuery[] = "'" .$insertAry['description'] ."'";                                        // ����
    $aryQuery[] = ") ";

    $strQuery = implode("\n",  $aryQuery );
    
    if( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
    }

    $objDB->freeResult( $lngResultID );

    // �����������Ͽ
    $salesNoList = [];      // ���No
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
        $aryQuery[] = "lnginvoiceno , ";                    // ������ֹ�
        $aryQuery[] = "lnginvoicedetailno , ";              // ����������ֹ�
        $aryQuery[] = "lngrevisionno , ";                   // ��ӥ�����ֹ�
        $aryQuery[] = "dtmdeliverydate , ";                 // Ǽ����
        $aryQuery[] = "lngdeliveryplacecode , ";            // Ǽ�ʾ�ꥳ����
        $aryQuery[] = "strdeliveryplacename , ";            // Ǽ�ʾ��̾
        $aryQuery[] = "cursubtotalprice , ";                // ����
        $aryQuery[] = "lngtaxclasscode , ";                 // ���Ƕ�ʬ������
        $aryQuery[] = "strtaxclassname , ";                 // ���Ƕ�ʬ
        $aryQuery[] = "curtax , ";                          // ������Ψ
        $aryQuery[] = "strnote , ";                         // ����
        $aryQuery[] = "lngslipno , ";                       // Ǽ�ʽ��ֹ�
        $aryQuery[] = "lngsliprevisionno  ";                // Ǽ�ʽ��ӥ�����ֹ�
        $aryQuery[] = " ) VALUES ( ";
        $aryQuery[] = $sequence_m_lnginvoice ." ,";                      // ������ֹ�
        $aryQuery[] = (int)$no+1 ." ,";                                 // ����������ֹ�
        $aryQuery[] =  $insertAry['lngrevisionno']." ,";                // ��ӥ�����ֹ�
        $aryQuery[] =  "'"  .$insertAry['dtminvoicedate'] ."' ,";       // Ǽ����
        $aryQuery[] = "(select lngcompanycode from m_company where strcompanydisplaycode='". $result['lngdeliveryplacecode'] ."')  ,"; // Ǽ�ʾ�ꥳ����
        $aryQuery[] =  "'"  .$result['strdeliveryplacename'] ."' ,";    // Ǽ�ʾ��̾
        $aryQuery[] =  "'"  .$result['curtotalprice'] ."' ,";           // ����
        $aryQuery[] =  "'"  .$result['lngtaxclasscode'] ."' ,";         // ���Ƕ�ʬ������
        $aryQuery[] =  "'"  .$result['strtaxclassname'] ."' ,";         // ���Ƕ�ʬ
        $aryQuery[] =  "'"  .$result['curtax'] ."' ,";                  // ������Ψ
        $aryQuery[] =  "'"  .$result['strnote'] ."' ,";                 // ����
        $aryQuery[] =  $result['lngslipno'] ." ,";                      // Ǽ�ʽ��ֹ�
        $aryQuery[] =  $result['lngrevisionno'] ." ";                   // Ǽ�ʽ��ӥ�����ֹ�
        $aryQuery[] =  " ) ";

        $strQuery = "";
        $strQuery = implode( $aryQuery );

        // ��������٤���Ͽ
        if( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
        }

        if(!empty($result['lngsalesno'])) {
            $salesNoList[] = (int)$result['lngsalesno'];
        }
    }
    $objDB->freeResult( $lngResultID );

    // ���ޥ�����������ֹ�򹹿�
    // Ǽ����ɼ�ޥ�����ɳ�Ť����ޥ���
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

    // ���ޥ�������
    if( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
    }

    $aryResult["result"] = true;
    $aryResult["strReportKeyCode"] = $sequence_m_lnginvoice;
    return $aryResult;

}

/**
 * ���ꤵ�줿������ֹ椫�������ޥ���������������ӣѣ�ʸ�����
 *
 *    ����������ֹ�������ޥ�������μ����ѣӣѣ�ʸ�����ؿ�
 *
 *    @param  Integer     $lngInvoiceNo             ��������������ֹ�
 *    @return strQuery     $strQuery ������SQLʸ
 *    @access public
 */
function fncGetInvoiceMSQL ( $lngInvoiceNo, $lngRevisionNo)
{
    // ������ֹ��ֹ�
    $aryQuery[] = "SELECT distinct on (inv.lnginvoiceno) inv.lnginvoiceno as lnginvoiceno ";
    // ��ӥ�����ֹ�
    $aryQuery[] = ", inv.lngrevisionno as lngrevisionno";
    // �ܵҥ�����
    $aryQuery[] = ", cust_c.strcompanydisplaycode as strcustomercode";
    // �ܵ�̾
    $aryQuery[] = ", inv.strcustomername as strcustomername";
    // �ܵҼ�̾
    $aryQuery[] = ", inv.strcustomercompanyname as strcustomercompanyname";
    // ����񥳡���
    $aryQuery[] = ", inv.strinvoicecode as strinvoicecode";
    // ������
    $aryQuery[] = ", to_char( inv.dtminvoicedate, 'YYYY/MM/DD' ) as dtminvoicedate";
    // ������� ��
    $aryQuery[] = ", to_char( inv.dtmchargeternstart, 'YYYY/MM/DD' ) as dtmchargeternstart";
    // ������� ��
    $aryQuery[] = ", to_char( inv.dtmchargeternend, 'YYYY/MM/DD' ) as dtmchargeternend";
    // ��������ĳ�
    $aryQuery[] = ", To_char( inv.curlastmonthbalance, '9,999,999,990' ) as curlastmonthbalance";
    // ��������
    $aryQuery[] = ", To_char( inv.curthismonthamount, '9,999,999,990' ) as curthismonthamount";
    // �̲�ñ�̥�����
    $aryQuery[] = ", inv.lngmonetaryunitcode as lngmonetaryunitcode";
    // �̲�ñ��
    $aryQuery[] = ", inv.strmonetaryunitsign as strmonetaryunitsign";
    // ���Ƕ�ʬ������
    $aryQuery[] = ", inv.lngtaxclasscode as lngtaxclasscode";
    // ���Ƕ�ʬ̾
    $aryQuery[] = ", inv.strtaxclassname as strtaxclassname";
    // ��ȴ���1
    $aryQuery[] = ", To_char( inv.cursubtotal1, '9,999,999,990' ) as cursubtotal1";
    // ������Ψ1
    $aryQuery[] = ", inv.curtax1 as curtax1";
    // �����ǳ�1
    $aryQuery[] = ", To_char( inv.curtaxprice1, '9,999,999,990' ) as curtaxprice1";
    // ô����
    $aryQuery[] = ", u.struserdisplaycode as strusercode";
    $aryQuery[] = ", inv.strusername as strusername";
    // ������
    $aryQuery[] = ", insert_u.struserdisplaycode as strinsertusercode";
    $aryQuery[] = ", inv.strinsertusername as strinsertusername";
    // ������
    $aryQuery[] = ", to_char( inv.dtminsertdate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";
    // ����
    $aryQuery[] = ", inv.strnote as strnote";
    // �������
    $aryQuery[] = ", inv.lngprintcount as lngprintcount";
    // ����
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

    // ����Ѥߤ��ӽ�
    $aryQuery[] = " AND inv.lnginvoiceno NOT IN ( ";
    $aryQuery[] = " SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE lngrevisionno = -1";
    $aryQuery[] = " ) ";

    // order by
    $aryQuery[] = " ORDER BY inv.lnginvoiceno ASC , inv.lngrevisionno DESC ";

    $strQuery = implode( "\n", $aryQuery );

    return $strQuery;
}



// --------------------------------
//  �����ɬ�פʥ��顼�����å�
// --------------------------------

/**
 *    ��������٤�ɳ�Ť�Ǽ�ʽ�ޥ�����ɳ�Ť����ޥ�������她�ơ����������Ѥ�(=99)���ɤ��������å�
 *  ���ꤵ�줿������ֹ椫�����ޥ�������
 *
 *    @param  Integer     $lngInvoiceNo         ������ֹ�
 *    @return boolean    true : ���Ѥߤ��ޤޤ�Ƥ���
 *                      false:������ѡפ����٤�̵��
 */

function fncSalesStatusIsClosed($lngInvoiceNo, $objDB)
{
    // ������ֹ��ɳ�Ť����ޥ����ǡ����μ���
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
        // ������ֹ��ɳ�Ť����ޥ��������Ĥ���ʤ���DB���顼
        fncOutputError ( 9061, DEF_FATAL, "����������å�������ȼ�����ޥ�����������", TRUE, "", $objDB );
    }

    //  ���ޥ�����ɳ�Ť������֤Υ��ơ�������������ѡפ��ɤ���
    for ( $i = 0; $i < count($aryDetailResult); $i++)
    {
        // �����֥�����
        $lngSalesStatusCode = $aryDetailResult[$i]["lngsalesstatuscode"];

        if ($lngSalesStatusCode == DEF_SALES_CLOSED){
            // �����֥����ɤ�������ѡפΥޥ�����1��ʾ�¸��
            return true;
        }
    }

    // �����֥����ɤ�������ѡפ�1���̵��
    return false;
}



/**
 * �����ޥ����Υǡ����κ��
 *
 *    @param  Integer     $lngInvoiceNo ������ֹ�
 *    @param  Object        $objDB        DB���֥�������
 *    @return Boolean     true        �¹�����
 *                        false        �¹Լ��� �����������
 */
function fncDeleteInvoice($lngInvoiceNo, $lngRevisionNo, $objDB, $objAuth)
{
    // ������ֹ��֤�ͭ���ʥޥ����ǡ��������뤫��ǧ
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
        // �����ޥ��������˼���
        return false;
    }
    $objDB->freeResult( $lngResultID );

    // ��ӥ�����ֹ��-1����ʻ��ͽ�˽ऺ���
    $lngMinRevisionNo = -1;

    // �����ޥ����˥�ӥ�����ֹ椬 -1 �Υ쥳���ɤ��ɲ�
    $aryQuery[] = "INSERT INTO m_invoice (";
    $aryQuery[] = " lnginvoiceno,";                     // 1:������ֹ�
    $aryQuery[] = " lngrevisionno, ";                   // 2:��ӥ�����ֹ�
    $aryQuery[] = " strinvoicecode, ";                  // 3:����񥳡���
    $aryQuery[] = " lnginsertusercode, ";               // 4:���ϼԥ�����
    $aryQuery[] = " strinsertusername, ";               // 3:���ϼ�̾
    $aryQuery[] = " bytinvalidflag, ";                  // 5:̵���ե饰
    $aryQuery[] = " dtminsertdate";                     // 6:��Ͽ��
    $aryQuery[] = ") values (";
    $aryQuery[] = $lngInvoiceNo . ", ";                 // 1:������ֹ�
    $aryQuery[] = $lngMinRevisionNo . ", ";             // 2:��ӥ�����ֹ�
    $aryQuery[] = "'" .$strInvoiceCode . "', ";         // 3:����񥳡���
    $aryQuery[] = "" .$objAuth->UserCode . ", ";      // 4:���ϼԥ�����
    $aryQuery[] = "'" .$objAuth->UserFullName . "', ";  // 4:���ϼ�̾
    $aryQuery[] = "false, ";                            // 5:̵���ե饰
    $aryQuery[] = "now()";                              // 6:��Ͽ��
    $aryQuery[] = ")";

    unset($strQuery);
    $strQuery = implode("\n", $aryQuery );

    if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
    {
        // �쥳�����ɲü���
        return false;
    }
    $objDB->freeResult( $lngResultID );

    // ��������
    return true;
}


/**
 * ���ꤵ�줿������ֹ��ɳ�Ť��Ƥ������ޥ�����������ֹ����ˤ���
 *
 *    @param  integer     $lngInvoiceNo   ������ֹ�
 *    @param  Object        $objDB          DB���֥�������
 *    @return Boolean     true            �¹�����
 *                        false           �¹Լ��� �����������
 */
function fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB)
{
    // ������ֹ��ɳ�Ť����ޥ����ǡ����μ���
    $strQuery = fncGetSalesMSQL ( $lngInvoiceNo );
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( !$lngResultNum )
    {
        // ������ֹ��ɳ�Ť����ޥ��������Ĥ���ʤ���DB���顼
        return false;
    }


    // �����оݥ쥳���ɤ�������ֹ��NULL�˹���
    $strWhere  = "WHERE ";
    $strWhere .= "lnginvoiceno = " . $lngInvoiceNo . " ";
    $strWhere .= "and lngrevisionno = (SELECT MAX(lngrevisionno) FROM m_sales WHERE lnginvoiceno = " . $lngInvoiceNo . ")";
    $strUpdateQuery  = "UPDATE m_sales ";
    $strUpdateQuery .= "SET lnginvoiceno = NULL " ;
    $strUpdateQuery .= $strWhere;

    list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
    if (!$lngUpdateResultID){ return false; }
    $objDB->freeResult( $lngUpdateResultID );

    // ��������
    return true;

}


/**
 * ���ꤵ�줿������ֹ椫�����ޥ���������������ӣѣ�ʸ�����
 *
 *    ����������ֹ椬��Ͽ����Ƥ������ޥ�����������ѣӣѣ�ʸ�����ؿ�
 *
 *    @param  Integer     $lngInvoiceNo �����Ȥʤ�������ֹ�
 *    @return strQuery     $strQuery     ������SQLʸ
 *    @access public
 */
function fncGetSalesMSQL ( $lngInvoiceNo )
{
    // ����ֹ�
    $aryQuery[] = "SELECT distinct on (lngsalesno) lngsalesno ";
    // ��ӥ�����ֹ�
    $aryQuery[] = ", lngrevisionno";
    // ��女����
    $aryQuery[] = ", strsalescode";
    // �׾���
    $aryQuery[] = ", to_char( dtmappropriationdate, 'YYYY/MM/DD' ) as dtmappropriationdate";
    // �ܵҥ�����
    $aryQuery[] = ", lngcustomercompanycode";
    // ���롼�ץ�����
    $aryQuery[] = ", lnggroupcode";
    // �桼��������
    $aryQuery[] = ", lngusercode";
    // �����֥�����
    $aryQuery[] = ", lngsalesstatuscode";
    // �̲�ñ�̥�����
    $aryQuery[] = ", lngmonetaryunitcode";
    // �̲ߥ졼�ȥ�����
    $aryQuery[] = ", lngmonetaryratecode";
    // �̻��졼��
    $aryQuery[] = ", curconversionrate";
    // Ǽ�ʽ�NO
    $aryQuery[] = ", strslipcode";
    // ������ֹ�
    $aryQuery[] = ", lnginvoiceno";
    // ��׶��
    $aryQuery[] = ", To_char( curtotalprice, '9,999,999,990.9999' )  as curtotalprice";
    // ����
    $aryQuery[] = ", strnote";
    // ���ϼԥ�����
    $aryQuery[] = ", lnginputusercode";
    // ��Ͽ��
    $aryQuery[] = ", to_char( dtminsertdate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";

    // FROM��
    $aryQuery[] = " FROM m_sales ";

    $aryQuery[] = " WHERE lnginvoiceno = " . $lngInvoiceNo . "";

    $aryQuery[] = " ORDER BY lngsalesno ASC , lngrevisionno DESC ";

    $strQuery = implode( "\n", $aryQuery );

    return $strQuery;
}


/**
 * �إå����ǡ����ù�
 *
 *    SQL�Ǽ��������إå������ͤ�ɽ���Ѥ˲ù�����
 *    ��SQL������̤Υ���̾�Ϥ��٤ƾ�ʸ���ˤʤ뤳�Ȥ����
 *
 *    @param  Array     $aryResult                 �إå��Ԥθ�����̤���Ǽ���줿����
 *    @access public
 */
function fncSetInvoiceHeadTableData ( $aryResult )
{
    // �����No
    $aryNewResult["lngInvoiceNo"]    = $aryResult["lnginvoiceno"];
    // ��ӥ�����ֹ�
    $aryNewResult["lngRevisionNo"]   = $aryResult["lngrevisionno"];
    // �ܵҥ�����
    $aryNewResult["strCustomerCode"] = $aryResult["strcustomercode"];
    // �ܵ�̾
    $aryNewResult["strCustomerName"] = $aryResult["strcustomername"];
    // �ܵ�
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
    // �ܵҼ�̾
    $aryNewResult["strCustomerCompanyName"] = $aryResult["strcustomercompanyname"];
    // ����񥳡���
    $aryNewResult["strInvoiceCode"]         = $aryResult["strinvoicecode"];
    // ������
    $aryNewResult["dtmInvoiceDate"]         = $aryResult["dtminvoicedate"];
    // ������� ��
    $aryNewResult["dtmChargeternStart"]     = $aryResult["dtmchargeternstart"];
    // ������� ��
    $aryNewResult["dtmChargeternEnd"]       = $aryResult["dtmchargeternend"];
    // ��������ĳ�
    $aryNewResult["curLastMonthBalance"]    = $aryResult["curlastmonthbalance"];
    // ��������
    $aryNewResult["curThisMonthAmount"]     = $aryResult["curthismonthamount"];
    // �̲�ñ�̥�����
    $aryNewResult["lngMonetaryUnitCode"]    = $aryResult["lngmonetaryunitcode"];
    // �̲�ñ��
    $aryNewResult["strMonetaryUnitSign"]    = $aryResult["strmonetaryunitsign"];
    // ���Ƕ�ʬ������
    $aryNewResult["lngTaxClassCode"]        = $aryResult["lngtaxclasscode"];
    // ���Ƕ�ʬ̾
    $aryNewResult["strTaxClassName"]        = $aryResult["strtaxclassname"];
    // ��ȴ���1
    $aryNewResult["curSubTotal1"]           = $aryResult["cursubtotal1"];
    // ������Ψ1
    $aryNewResult["curTax1"]                = $aryResult["curtax1"];
    // �����ǳ�1
    $aryNewResult["curTaxPrice1"]           = $aryResult["curtaxprice1"];
    // ô����
    $aryNewResult["strUserCode"]            = $aryResult["strusercode"];
    $aryNewResult["strUserName"]            = $aryResult["strusername"];
    // ɽ����ô����
    if ( $aryResult["strusercode"] )
    {
        $aryNewResult["strUser"] = "[" . $aryResult["strusercode"] ."]";
    }
    else
    {
        $aryNewResult["strUser"] = "      ";
    }
    $aryNewResult["strUser"]               .= " " . $aryResult["strusername"];

    // ������
    $aryNewResult["strInsertUserCode"]      = $aryResult["strinsertusercode"];
    $aryNewResult["strInsertUserName"]      = $aryResult["strinsertusername"];
    // ɽ���Ѻ�����
    if ( $aryResult["strinsertusercode"] )
    {
        $aryNewResult["strInsertUser"] = "[" . $aryResult["strinsertusercode"] ."]";
    }
    else
    {
        $aryNewResult["strInsertUser"] = "      ";
    }
    $aryNewResult["strInsertUser"]         .= " " . $aryResult["strinsertusername"];

    // ������
    $aryNewResult["dtmInsertDate"]          = $aryResult["dtminsertdate"];
    // ����
    $aryNewResult["strNote"]                = $aryResult["strnote"];
    // ����
    $aryNewResult["description"]                = $aryResult["description"];
    // �������
    $aryNewResult["lngPrintCount"]          = $aryResult["lngprintcount"];

    return $aryNewResult;
}



/**
 * ������������ǡ����ù�
 *
 *    SQL�Ǽ���������������٤��ͤ�ɽ���Ѥ˲ù�����
 *    ��SQL������̤Υ���̾�Ϥ��٤ƾ�ʸ���ˤʤ뤳�Ȥ����
 *
 *    @param  Array     $aryDetailResult     ���ٹԤθ�����̤���Ǽ���줿����ʣ��ǡ���ʬ��
 *    @param  Array     $aryHeadResult         �إå��Ԥθ�����̤���Ǽ���줿����ʻ����ѡ�
 *    @access public
 */
function fncSetInvoiceDetailTableData ( $aryDetailResult, $aryHeadResult )
{
    // �����ȥ���
    $aryNewDetailResult["lngInvoiceDetailNo"]    = $aryDetailResult["lnginvoicedetailno"];
    // ������ֹ�
    $aryNewDetailResult["lngInvoiceNo"]          = $aryDetailResult["lnginvoiceno"];
    // ��ӥ�����ֹ�
    $aryNewDetailResult["lngRevisionNo"]         = $aryDetailResult["lngrevisionno"];
    // Ǽ����
    $aryNewDetailResult["dtmDeliveryDate"]       = date('Y-m-d', strtotime($aryDetailResult["dtmdeliverydate"]));
    // // Ǽ�ʾ�ꥳ����
    // $aryNewDetailResult["lngDeliveryPlaceCode"]  = $aryDetailResult["lngdeliveryplacecode"];
    // // Ǽ�ʾ��
    // if ( $aryDetailResult["lngdeliveryplacecode"] )
    // {
    //     $aryNewResult["c"] = "[" . $aryResult["lngdeliveryplacecode"] ."]";
    // }
    // else
    // {
    //     $aryNewResult["strDeliveryPlaceName"] = "      ";
    // }
    // $aryNewDetailResult["strDeliveryPlaceName"]  = $aryDetailResult["c"];
    // Ǽ�ʾ�ꥳ����
    $aryNewDetailResult["lngDeliveryPlaceCode"]            = $aryDetailResult["lngdeliveryplacecode"];
    $aryNewDetailResult["strDeliveryPlaceName"]            = $aryDetailResult["lngdeliveryplacecode"];
    // ɽ����Ǽ�ʾ��
    if ( $aryDetailResult["lngdeliveryplacecode"] )
    {
        $aryNewDetailResult["strDeliveryPlace"] = "[" . $aryDetailResult["lngdeliveryplacecode"] ."]";
    }
    else
    {
        $aryNewDetailResult["strDeliveryPlace"] = "      ";
    }
    $aryNewDetailResult["strDeliveryPlace"]               .= " " . $aryDetailResult["strdeliveryplacename"];
    // ��ȴ���
    if ( !$aryDetailResult["cursubtotalprice"] )
    {
        $aryNewDetailResult["curSubTotalPrice"] .= "0.00";
    }
    else
    {
        $aryNewDetailResult["curSubTotalPrice"] .= $aryDetailResult["cursubtotalprice"];
    }
    // ���Ƕ�ʬ������
    $aryNewDetailResult["lngTaxClassCode"]       = $aryDetailResult["lngtaxclasscode"];
    // ���Ƕ�ʬ̾
    $aryNewDetailResult["strTaxClassName"]       = $aryDetailResult["strtaxclassname"];
    // ������Ψ
    $curTax = (float)$aryDetailResult["curtax"];
    $aryNewDetailResult["curTax"]                = (int)($curTax*100);
    // �����ǳ�
    $curSubTotalPrice = preg_replace('/,/', '', $aryDetailResult["cursubtotalprice"]);
    $aryNewDetailResult["taxPrice"]              = (int)($aryDetailResult["lngtaxclasscode"]) == 1
                                                    ? 0
                                                    : (float)$curSubTotalPrice*$curTax;
    // ���Ƕ�ʬ����Ψ
    $aryNewDetailResult["strTax"]                = $aryDetailResult["strtaxclassname"] ."��" . (int)($curTax*100) . '%';
    // ��������
    $aryNewDetailResult["strDetailNote"]         = nl2br($aryDetailResult["strnote"]);
    // Ǽ����ɼ�ֹ�
    $aryNewDetailResult["lngSlipNo"]             = $aryDetailResult["lngslipno"];
    // ��ӥ�����ֹ�
    $aryNewDetailResult["lngSlipRevisionNo"]     = $aryDetailResult["lngsliprevisionno"];
    // Ǽ����ɼ������
    $aryNewDetailResult["lngSlipCode"]           = $aryDetailResult["strslipcode"];

    return $aryNewDetailResult;
}



/**
 * �ץ�ӥ塼�ѥǡ����ù�
 *
 *    POST�ǡ�����ץ�ӥ塼ɽ���Ѥ˲ù�����
 *
 *    @param  Array       $aryResult         POST����Ǽ���줿����
 *  @param  integer  $lngInvoiceNo      ������ֹ�
 *    @access public
 */
function fncSetPreviewTableData ( $aryResult , $lngInvoiceNo, $objDB)
{
	require_once (LIB_DEBUGFILE);
    // ��������٤�Ǽ�ʽ�No
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
        // ���Ƕ�ʬ������
        $aryPrevResult["lngTaxClassCode"] = $taxclasscode;
        // ���Ƕ�ʬ̾
        $aryPrevResult["strTaxClassName"] = $taxclassname;
    }


    // �ܵҥ�����
    $aryPrevResult["strCustomerCode"] = $aryResult["lngCustomerCode"];
    // �ܵ�̾
    $aryPrevResult["strCustomerName"] = $aryResult["strCustomerName"];

    // ��������ĳ�
    $aryPrevResult['curLastMonthBalance_desc'] = preg_match('/,/',$aryResult["curlastmonthbalance"]) ? $aryResult["curlastmonthbalance"] : number_format($aryResult["curlastmonthbalance"]);
    // ������ȴ�����
    $aryPrevResult['curSubTotal1_desc']        = preg_match('/,/',$aryResult["curthismonthamount"]) ? $aryResult["curthismonthamount"] : number_format($aryResult["curthismonthamount"]);
    // �����ǳ�
    $aryPrevResult['curTaxPrice1_desc']        = preg_match('/,/',$aryResult["curtaxprice"]) ? $aryResult["curtaxprice"] : number_format($aryResult["curtaxprice"]);
    // �������
    $aryPrevResult['curThisMonthAmount_desc']  = preg_match('/,/',$aryResult["notaxcurthismonthamount"]) ? $aryResult["notaxcurthismonthamount"] : number_format($aryResult["notaxcurthismonthamount"]);
    // ��������ĳ�
    $aryPrevResult['curLastMonthBalance'] = preg_replace('/,/', '', $aryResult["curlastmonthbalance"]);
    // ������ȴ�����
    $aryPrevResult['curSubTotal1']        = preg_replace('/,/', '', $aryResult["curthismonthamount"]);
    // �����ǳ�
    $aryPrevResult['curTaxPrice1']        = preg_replace('/,/', '', $aryResult["curtaxprice"]);
    // �������
    $aryPrevResult['curThisMonthAmount']  = preg_replace('/,/', '', $aryResult["notaxcurthismonthamount"]);

    // ������Ψ1
    $curtax1 = preg_replace('/[^0-9]/', '', $aryResult["tax"]);
    $aryPrevResult['curTax1'] = (int)$curtax1;

    // ������
    $dtmInvoiceDate = $aryResult['ActionDate'];
    $aryPrevResult['dtmInvoiceDate'] = $dtmInvoiceDate;
    // ɽ����������
    $printInvDate = fncGetJapaneseDate($dtmInvoiceDate);
    $aryPrevResult['printInvDate']  = $printInvDate[0] . $printInvDate[1] .'ǯ ' .$printInvDate[2] .'��' .$printInvDate[3] .'��';

    // ����񥳡���
    $aryPrevResult['strInvoiceCode'] = fncGetStrInvoiceCode($lngInvoiceNo, true, $objDB);

    // �� dtmchargeternstart
    $printTernStart = fncGetJapaneseDate($aryResult['dtmchargeternstart']);
    $aryPrevResult['dtmChargeternStart'] = $aryResult['dtmchargeternstart'];
    $aryPrevResult['printTernStartM'] = $printTernStart[2];
    $aryPrevResult['printTernStartD'] = $printTernStart[3];

    // �� dtmchargeternend
    $printTernEnd = fncGetJapaneseDate($aryResult['dtmchargeternend']);
    $aryPrevResult['dtmChargeternEnd'] = $aryResult['dtmchargeternend'];
    $aryPrevResult['printTernEndM'] = $printTernEnd[2];
    $aryPrevResult['printTernEndD'] = $printTernEnd[3];

    // �ܵҥ����ɡ��ܵ�̾���ܵҼ�̾
    list ($aryPrevResult['printCustomerName'], $aryPrevResult['printCompanyName'], $aryPrevResult['customerCode'], $strcompanydisplayname ) = fncGetCompanyPrintName($aryResult["lngCustomerCode"], $objDB);
    // �ܵ�
    if ( $aryResult["lngCustomerCode"] )
    {
        $aryNewResult["strCustomer"] = "[" . $aryResult["lngCustomerCode"] ."]";
    }
    else
    {
        $aryNewResult["strCustomer"] = "      ";
    }
    $aryNewResult["strCustomer"] .= " " . $aryPrevResult["printCompanyName"];

    // ô����
    $aryPrevResult["strUserCode"] = $aryResult["lngInputUserCode"];
    $aryPrevResult["strUserName"] = $aryResult["strInputUserName"];
    // �̲�ñ��̾��
    $monetaryUnitCode = 1;
    $aryPrevResult['strMonetaryUnitName'] = fncGetMonetaryunitSign( $monetaryUnitCode ,$objDB);

    // ����
    $aryPrevResult['description'] = $aryResult['description'];
    // ����
    $aryPrevResult['strNote'] = $aryResult['strnote'];
    // �ư���
    if( !empty($aryResult['strnotecheck']) && $aryResult['strnotecheck'] == 'on') {
        $space =  !empty($aryPrevResult['strNote']) ? '  ' : '';
        $aryPrevResult['strNote'] .= $space . "�ư���";
    }

    // ������(R Y.M.D)
    $aryPrevResult['prevDate'] = "R." .((int)date('Y')-2018) . "." .(int)date('m') ."." .(int)date('d');

    // �桼����̾����
    $aryPrevResult['lngUserName'] = $objAuth->UserFullName;

    return $aryPrevResult;
}


/**
 * �����̾���Ǽ��������Υ�����"CN"����Ϳ����
 *
 *    @param  Array     $aryColumnNames         �����̾����Ǽ���줿����
 *    @access public
 */
function fncAddColumnNameArrayKeyToCN ($aryColumnNames)
{
    $arrayKeys = array_keys($aryColumnNames);

    // ɽ���оݥ������������̤ν���
    for ( $i = 0; $i < count($arrayKeys); $i++ )
    {
        $key = $arrayKeys[$i];
        $strNewColumnName = "CN" . $key;
        $aryNames[$strNewColumnName] = $aryColumnNames[$key];
    }

    return $aryNames;
}


/**
 * ����    �����Ѵ��ؿ�
 *
 *
 *    @param  Date    $date       ���� Y/m/d
 *    @access public
 *    return   Array   $jdate      [0] ǯ�� [1] ǯ [2] �� [3] ��
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
        $result[0] = '����';
        $result[1] = $retY;
    }
    $result[2] = (int)$m;
    $result[3] = (int)$d;

    return $result;

}


/**
 *
 *    ����񽸷פ�ɬ�פʥǡ��������ѣӣѣ�ʸ�����ؿ�
 *
 *    @param  date        $invoiceMonth
 *    @return strQuery    $strQuery ������SQLʸ
 *    @access public
 */
function fncGetInvoiceAggregateSQL ( $invoiceMonth )
{
    $start = new DateTime($invoiceMonth);
    $end =   new DateTime($invoiceMonth);
    // ���
    $end->add(DateInterval::createFromDateString('1 month'));

    // ������ֹ��ֹ�
    $aryQuery[] = "SELECT distinct on (inv.lnginvoiceno) inv.lnginvoiceno as lnginvoiceno ";
    // ��ӥ�����ֹ�
    $aryQuery[] = ", inv.lngrevisionno as lngrevisionno";
    // �ܵҥ�����
    $aryQuery[] = ", cust_c.strCompanyDisplayCode as strcustomercode";
    // �ܵ�̾
    $aryQuery[] = ", inv.strcustomername as strcustomername";
    // �ܵҼ�̾
    $aryQuery[] = ", inv.strcustomercompanyname as strcustomercompanyname";
    // ����񥳡���
    $aryQuery[] = ", inv.strinvoicecode as strinvoicecode";
    // ������
    $aryQuery[] = ", to_char( inv.dtminvoicedate, 'YYYY/MM/DD' ) as dtminvoicedate";
    // ������� ��
    $aryQuery[] = ", to_char( inv.dtmchargeternstart, 'YYYY/MM/DD' ) as dtmchargeternstart";
    // ������� ��
    $aryQuery[] = ", to_char( inv.dtmchargeternend, 'YYYY/MM/DD' ) as dtmchargeternend";
    // ��������ĳ�
    $aryQuery[] = ", inv.curlastmonthbalance as curlastmonthbalance";
    // ��������
    $aryQuery[] = ", inv.curthismonthamount as curthismonthamount";
    // �̲�ñ�̥�����
    $aryQuery[] = ", inv.lngmonetaryunitcode as lngmonetaryunitcode";
    // �̲�ñ��
    $aryQuery[] = ", inv.strmonetaryunitsign as strmonetaryunitsign";
    // ���Ƕ�ʬ������
    $aryQuery[] = ", inv.lngtaxclasscode as lngtaxclasscode";
    // ���Ƕ�ʬ̾
    $aryQuery[] = ", inv.strtaxclassname as strtaxclassname";
    // ��ȴ���1
    $aryQuery[] = ", inv.cursubtotal1 as cursubtotal1";
    // ������Ψ1
    $aryQuery[] = ", inv.curtax1 as curtax1";
    // �����ǳ�1
    $aryQuery[] = ", inv.curtaxprice1 as curtaxprice1";
    // ô����
    $aryQuery[] = ", u.struserdisplaycode as strusercode";
    $aryQuery[] = ", inv.strusername as strusername";
    // ������
    $aryQuery[] = ", insert_u.struserdisplaycode as strinsertusercode";
    $aryQuery[] = ", inv.strinsertusername as strinsertusername";
    // ������
    $aryQuery[] = ", to_char( inv.dtminsertdate, 'YYYY/MM/DD HH:MI:SS' ) as dtminsertdate";
    // ����
    $aryQuery[] = ", inv.strnote as strnote";
    // �������
    $aryQuery[] = ", inv.lngprintcount as lngprintcount";

    $aryQuery[] = " FROM m_invoice inv ";
    $aryQuery[] = " LEFT JOIN m_Company cust_c ON inv.lngcustomercode = cust_c.lngcompanycode";
    $aryQuery[] = " LEFT JOIN m_User insert_u ON inv.lngInsertUserCode = insert_u.lngusercode";
    $aryQuery[] = " LEFT JOIN m_User u ON inv.lngusercode = u.lngusercode";

    // WHERE  dtminvoicedate
    $aryQuery[] = " WHERE inv.dtminvoicedate >= '" .$start->format('Y-m-d') ."'  AND inv.dtminvoicedate < '"  .$end->format('Y-m-d') ."' ";
    // ����Ѥߤ��ӽ�
    $aryQuery[] = " AND inv.lnginvoiceno NOT IN ( ";
    $aryQuery[] = " SELECT DISTINCT(lnginvoiceno) FROM m_invoice WHERE lngrevisionno = -1";
    $aryQuery[] = " ) ";

    $aryQuery[] = " ORDER BY inv.lnginvoiceno ASC , inv.lngrevisionno DESC , inv.lngmonetaryunitcode ASC, inv.lngcustomercode ASC, inv.strinvoicecode ASC ";

    $strQuery = implode( "\n", $aryQuery );

    return $strQuery;
}



// /**
//  * ���ᥳ���ɤˤ��ǡ����ξ��֤��ǧ����
//  *
//  * @param [type] $strinvoicecode
//  * @param [type] $objDB
//  * @return void [0��̤����ǡ�����1������ѥǡ���]
//  */
// function fncCheckData($strinvoicecode, $objDB) {
//     $result = 0;
//     unset($aryQuery);
//     $aryQuery[] = "SELECT";
//     $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, strinvoicecode ";
//     $aryQuery[] = "FROM m_invoice ";
//     $aryQuery[] = "WHERE strinvoicecode='" . $strinvoicecode . "'";
//     $aryQuery[] = "group by strinvoicecode, bytInvalidFlag";

//     // �������ʿ�פ�ʸ������Ѵ�
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
//  * ���٥ǡ����μ���
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
//     // �������ʿ�פ�ʸ������Ѵ�
//     $strQuery = implode("\n", $aryQuery);

//     list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
//     // �������������ξ��
//     if ($lngResultNum > 0) {
//         // ���������Ǥ�����̾����
//         for ($i = 0; $i < $lngResultNum; $i++) {
//             $detailData = pg_fetch_all($lngResultID);
//         }
//     }
//     $objDB->freeResult($lngResultID);

//     return $detailData;
// }

// /**
//  * �إå������ǡ���������
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
//         // �����̤�ɽ���ƥ����Ȥ�����
//         switch ($key) {
//             // �ܵ�
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
//             // �����NO.
//             case "strInvoiceCode":
//                 $td = $doc->createElement("td", $record["strinvoicecode"]);
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // ������.
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
//             // �������ĳ�
//             case "curLastMonthBalance":
//                 $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curlastmonthbalance"]));
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // ����������.
//             case "curThisMonthAmount":
//                 $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curthismonthamount"]));
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // �����ǳ�
//             case "curSubTotal1":
//                 $td = $doc->createElement("td", toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotal1"]));
//                 $td->setAttribute("style", $bgcolor);
//                 $td->setAttribute("rowspan", $rowspan);
//                 $trBody->appendChild($td);
//                 break;
//             // ������
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
//             // [ô����] ô����ɽ��̾
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
//             // ���ϼ�
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
//             // �������
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
//             // ����
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
//  * ���ٹԥǡ���������
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
//     // ���ꤵ�줿�ơ��֥���ܤΥ�����������
//     foreach ($aryTableDetailHeaderName as $key => $value) {
//             // �����̤�ɽ���ƥ����Ȥ�����
//             switch ($key) {                
//                 // ����������ֹ�
//                 case "lngInvoiceDetailNo":
//                     $td = $doc->createElement("td", $detailData["lnginvoicedetailno"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // Ǽ����
//                 case "dtmDeliveryDate":
//                     if ($toUTF8Flag) {
//                         $td = $doc->createElement("td", str_replace( "-", "/", toUTF8(substr( $detailData["dtmdeliverydate"], 0, 19 ))));
//                     } else {
//                         $td = $doc->createElement("td", str_replace( "-", "/", substr( $detailData["dtmdeliverydate"], 0, 19 )));
                       
//                     }
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // Ǽ�ʽ�NO
//                 case "strSlipCode":
//                     $td = $doc->createElement("td", $detailData["lngslipno"]);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // [Ǽ����ɽ��������] Ǽ����ɽ��̾
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
//                 // ��ȴ���
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
//                 // ���Ƕ�ʬ
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
//                 // ��Ψ
//                 case "curDetailTax":
//                     $textContent = round($detailData["curtax"] * 100) . '%';                    
//                     if ($toUTF8Flag) {
//                         $textContent = toUTF8($textContent);
//                     }
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // �����
//                 case "curTaxPrice":
//                     $cursubtotalprice = preg_replace('/,/','', $detailData["cursubtotalprice"]);
//                     $strText = number_format((int)($detailData["curtax"] * (int)$cursubtotalprice) ,2);
//                     $textContent = toMoneyFormat($headerData["lngmonetaryunitcode"], $headerData["strmonetaryunitsign"], $strText);
//                     $td = $doc->createElement("td", $textContent);
//                     $td->setAttribute("style", $bgcolor);
//                     $trBody->appendChild($td);
//                     break;
//                 // ��������
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