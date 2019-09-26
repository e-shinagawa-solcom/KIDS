<?
/**
 *	�ޥ��������ѥ饤�֥��
 *
 *	�ޥ��������Ѵؿ��饤�֥��
 *	��) ��������(m_StockSubject)����������(m_StockItem)�ޥ������ü����ͭ��
 *
 *	@package   KIDS
 *	@license   http://www.wiseknot.co.jp/
 *	@copyright Copyright &copy; 2003, Wiseknot
 *	@author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *	@access    public
 *	@version   1.00
 *
 */

//////////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////////
// ����ID���
define ( "DEF_ACTION_INSERT", 1 ); // �ɲ�
define ( "DEF_ACTION_UPDATE", 2 ); // �ѹ�
define ( "DEF_ACTION_DELETE", 3 ); // ���


// �����ޥ������ơ��֥����
$aryListTableName = Array (
    "m_StockClass"       => "������ʬ�ޥ�������",
    "m_StockSubject"     => "�������ܥޥ�������",
    "m_StockItem"        => "�������ʥޥ�������",
    "m_AccessIPAddress"  => "��������IP���ɥ쥹�ޥ�������",
    "m_CertificateClass" => "�ڻ����ޥ�������",
    "m_Country"          => "��ޥ�������",
    "m_Copyright"        => "�Ǹ����ޥ�������",
    "m_Organization"     => "�ȿ��ޥ�������",
    "m_ProductForm"      => "���ʷ��֥ޥ�������",
    "m_SalesClass"       => "����ʬ�ޥ�������",
    "m_TargetAge"        => "�о�ǯ��ޥ�������",
    "m_DeliveryMethod"   => "������ˡ�ޥ�������"
);


// �����ޥ������ơ��֥����
$arySearchTableName = Array (
    "m_Company"          => "��ҥޥ�������",
    "m_Group"            => "���롼�ץޥ�������",
    "m_MonetaryRate"     => "�̲ߥ졼�ȥޥ�������"
);


/**
 *	�ޥ������ơ��֥륯�饹
 *
 *	setMasterTable     �ơ��֥����μ���������
 *	setAryMasterInfo   �ƥޥ����Υ��������å�ˡ���ɲá���������å������ꡢ�ץ�����󥫥�������
 *	getColumnHtmlTable ������HTML�����(<td>���</td>������)
 *
 *	@package k.i.d.s.
 *	@license http://www.wiseknot.co.jp/
 *	@copyright Copyright &copy; 2003, Wiseknot
 *	@author Kenji Chiba <k-chiba@wiseknot.co.jp>
 *	@access public
 *	@version 0.1
 */
class clsMaster
{
    /**
     *	�ơ��֥�̾
     *	@var string
     */
    var $strTableName;

    /**
     *	�ԥǡ��� $aryData[���ֹ�][�����̾]
     *	@var array
     */
    var $aryData;

    /**
     *	�Կ�
     *	@var integer
     */
    var $lngRecordRow;

    /**
     *	�����̾ $aryColumnName[�ե�������ֹ�]
     *	@var array
     */
    var $aryColumnName;

    /**
     *	�����η����� $aryType[�ե�������ֹ�]
     *	@var array
     */
    var $aryType;

    /**
     *	�����Υ����å����� $aryCheck[�����̾]
     *	@var array
     */
    var $aryCheck;

    /**
     *	�����Υץ�������˥塼���� $aryMasterMenu[�����̾][���ֹ�][(VALUE|TEXT)]
     *	@var array
     */
    var $aryMasterMenu;

    /**
     *	�����å����������� $aryCheckQuery[(INSERT|UPDATE|DELETE)][�������ֹ�]
     *	@var array
     */
    var $aryCheckQuery;

    /**
     *	�����ơ��֥� $aryDeleteTable[Ϣ��]
     *	@var array
     */
    var $aryDeleteTable;

    /**
     *	���饹��ν������Ԥ�
     *
     *	@return void
     *	@access public
     */
    function __construct()
    {
        $this->strTableName   = "";
        $this->lngRecordRow   = 0;
        $this->aryColumnName  = Array();
        $this->aryType        = Array();
        $this->aryCheck       = Array();
        $this->aryMasterMenu  = Array();
        $this->aryCheckQuery  = Array();
        $this->aryDeleteTable = Array();
    }

    // -----------------------------------------------------------------
    /**
     *	�ޥ����ơ��֥�˴ؤ��륯��������������������������
     *	@param	string  $strTableName  �ޥ����ơ��֥�̾
     *	@param	string  $strKeyName    �����Υ����̾
     *	@param	int     $lngKeyCode    ��������
     *	@param	Array   $arySubCode    FORM VALUE
     *	@param	object  $objDB         DB���֥�������
     *	@return	boolean ���ԡ�����
     */
    // -----------------------------------------------------------------
    function setMasterTable( $strTableName, $strKeyName, $lngKeyCode, $arySubCode, $objDB )
    {
        // �ޥ����ơ��֥�̾�Υ��å�
        $this->strTableName = $strTableName;

        // ��������������
        $strQuery = "SELECT * FROM " . $this->strTableName;

        // �����ɻ���(��������������ξ��)
        if ( $strKeyName && $lngKeyCode != "" )
        {
            $strQuery .= " WHERE $strKeyName = $lngKeyCode";

            // �������ʥޥ����ξ�硢�����ɲ�
            if ( $this->strTableName == "m_StockItem" )
            {
                $strQuery .= " AND lngStockSubjectCode = " . $arySubCode["lngstocksubjectcode"];
            }

            // �̲ߥ졼�ȥޥ����ξ�硢�����ɲ�
			elseif ( $this->strTableName == "m_MonetaryRate" )
            {
                $strQuery .= " AND lngMonetaryUnitCode = " . $arySubCode["lngmonetaryunitcode"] .
                    " AND dtmApplyStartDate = '" . $arySubCode["dtmapplystartdate"] . "'" .
                    " AND dtmApplyEndDate > now()";
            }
        }

        // �������ʥޥ����ξ�硢2������ܤǥ����Ȥ����ü����
        if ( $this->strTableName == "m_StockItem" )
        {
            $strQuery .= " ORDER BY 2, 1";
        }
        else
        {
            $strQuery .= " ORDER BY 1";
        }
        // echo $strQuery;
        // return;
        $this->setMasterTableData( $strQuery, $objDB );
        return TRUE;
    }



    // -----------------------------------------------------------------
    /**
     *	�ޥ����ơ��֥�˴ؤ����������������
     *	@param	string  $strQuery     ������
     *	@param	object  $objDB        DB���֥�������
     *	@return	int     $lngResultNum ��̹Կ�
     */
    // -----------------------------------------------------------------
    function setMasterTableData( $strQuery, $objDB )
    {
        // �ǡ����μ����ȥ��å�
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

        // ������Ϣ�ǡ����μ����ȥ��å�
        $lngColumnNum = $objDB->getFieldsCount ( $lngResultID );

        for ( $i = 0; $i < $lngColumnNum; $i++ )
        {
            // �����̾���ɤ߹��ߤȥ��å�
            $this->aryColumnName[$i] = pg_field_name ( $lngResultID, $i);

            // �����ɤ߹��ߤȥ��å�
            $this->aryType[$i]       = pg_field_type ( $lngResultID, $i);
        }

        if ( $lngResultNum )
        {
            $this->aryData = pg_fetch_all ( $lngResultID );

            // ���󥯥���ȥ��ꥢ�륳���ɼ�������
            // �쥳���ɿ�����1���������(�Ǹ�������ֹ�)�����
            $lngRecordRow = ( count ( $this->aryData ) - 1 );

            for ( $i = 0; $i < $lngRecordRow; $i++ )
            {
                // �ǽ��쥳���ɤ���1����ब99�ʳ�����9999�ʲ��ξ�硢
                // ���ο��ͤ򥤥󥯥���Ȥ�����Τ� $this->lngRecordRow �˥��å�
                if ( $this->aryData[$lngRecordRow - $i][$this->aryColumnName[0]] != 99 && $this->aryData[$lngRecordRow - $i][$this->aryColumnName[0]] < 9999 )
                {
                    $this->lngRecordRow = $this->aryData[$lngRecordRow - $i][$this->aryColumnName[0]] + 1;
                    break;
                }
            }
            $objDB->freeResult( $lngResultID );
        }

        return $lngResultNum;
    }



    // -----------------------------------------------------------------
    /**
     *	�����å����������
     *	@param	int     ���������ɤ���
     *	@param	Array   ���֥��������ɤ���(�����Υơ��֥�ˤ�ɬ��)
     *	@return	boolean ���ԡ�����
     */
    // -----------------------------------------------------------------
    function setAryMasterInfo( $lngKeyCodeValue, $lngSubCode )
    {
        $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue";

        // �ƥޥ���������
        switch ( $this->strTableName )
        {
            //////////////////////////////////////////////////////
            // �̲ߥ졼�ȥޥ�������
            case "m_MonetaryRate":

                // ��Ͽ�����å�����������
                $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND " . $this->aryColumnName[1] . " = $lngSubCode";

                // ���������å�����������
                $this->aryCheckQuery["UPDATE"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND " . $this->aryColumnName[1] . " = $lngSubCode";

                break;

            //////////////////////////////////////////////////////
            // ��ʧ�ͼ��ޥ�������
            case "m_PayCondition":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Order WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // ������ʬ�ޥ�������
            case "m_StockClass":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_StockSubject WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // �������ܥޥ�������
            case "m_StockSubject":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[2]] = "length(1,100)";

                // ��Ͽ�����å�����������
                $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_StockItem WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][1] = "SELECT * FROM m_ProductPrice WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][2] = "SELECT * FROM t_StockDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][3] = "SELECT * FROM t_OrderDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][4] = "SELECT * FROM t_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // �������ʥޥ�������
            case "m_StockItem":
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,214748				// ʸ��������å�����
3647)";
                $this->aryCheck[$this->aryColumnName[1]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[2]] = "length(1,100)";

                // ��Ͽ�����å�����������
                $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND " . $this->aryColumnName[1] . " = $lngSubCode";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_ProductPrice WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                $this->aryCheckQuery["DELETE"][1] = "SELECT * FROM t_OrderDetail WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                $this->aryCheckQuery["DELETE"][2] = "SELECT * FROM t_StockDetail WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                $this->aryCheckQuery["DELETE"][3] = "SELECT * FROM t_Product WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                // $this->aryCheckQuery["DELETE"][4] = "SELECT * FROM m_StockSubject WHERE lngStockSubjectCode = $lngSubCode";


                break;

            //////////////////////////////////////////////////////
            // ��������IP���ɥ쥹�ޥ�������
            case "m_AccessIPAddress":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(-1,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "IP(1,100,',')";
                $this->aryCheck[$this->aryColumnName[2]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_User WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // �ڻ����ޥ�������
            case "m_CertificateClass":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // ��ޥ�������
            case "m_Country":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";
                $this->aryCheck[$this->aryColumnName[2]] = "english(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Company WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // �Ǹ����ޥ�������
            case "m_Copyright":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // �ȿ��ޥ�������
            case "m_Organization":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Company WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // ���ʷ��֥ޥ�������
            case "m_ProductForm":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // ����ʬ�ޥ�������
            case "m_SalesClass":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM t_ReceiveDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][1] = "SELECT * FROM t_SalesDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // �о�ǯ��ޥ�������
            case "m_TargetAge":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // ������ˡ�ޥ�������
            case "m_DeliveryMethod":
                // ʸ��������å�����
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // ��������å�����������
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM t_OrderDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;
        }

        return TRUE;
    }



    // -----------------------------------------------------------------
    /**
     *	������HTML�����(<td>���</td>������)
     *	@param	Int    $lngColumnNum  ������(���꤬�ʤ���硢°���������)
     *	@return	String $strColumnHtml ������(<td>���</td>������)
     */
    // -----------------------------------------------------------------
    function getColumnHtmlTable( $lngColumnNum )
    {
        // ���������꤬�ʤ���硢°���������
        if ( !$lngColumnNum )
        {
            $lngColumnNum = count ( $this->aryColumnName );
        }

        // ������ʬ<td></td>������
        for ( $i = 0; $i < $lngColumnNum; $i++ )
        {
            $strColumnHtml .= "		<td id=\"Column$i\" nowrap>Column$i</td>\n";
        }

        // ����ե����֥ޥ����ʳ��ξ�硢���������ɽ��
        if ( $this->strTableName != "m_WorkflowOrder" )
        {
            $strColumnHtml .= "		<td id=\"FixColumn\" nowrap>����</td>\n";
        }
        // �̲ߥ졼�ȥޥ����ʳ��ξ�硢��������ɽ��
        if ( $this->strTableName != "m_MonetaryRate" )
        {
            $strColumnHtml .= "		<td id=\"DeleteColumn\" nowrap>���</td>\n";
        }
        return $strColumnHtml;
    }
}



// -----------------------------------------------------------------
/**
 *	���󤫤�GET���Ϥ������URLʸ���������
 *	@param	Array  $aryData �ѿ�̾�򥭡��˻���Ϣ������
 *	@return	String $strURL  URL(&***=***������)
 */
// -----------------------------------------------------------------
function fncGetUrl( $aryData )
{
    if ( count ( $aryData ) > 0 )
    {
        $aryKeys = array_keys ( $aryData );
        foreach ( $aryKeys as $strKey )
        {
            $strURL .= "&" . $strKey . "=" . $aryData["$strKey"];
        }
    }
    return $strURL;
}



return TRUE;
?>
