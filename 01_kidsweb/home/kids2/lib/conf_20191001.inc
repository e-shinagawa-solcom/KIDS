<?php
/**
*	�����ƥ�������
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*/

// HTTP/1.1
//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
//header("Pragma: no-cache");

// �ۥ���̾��ư����
$strHost="";
if(isset($_SERVER['HTTPS']))
{
	$_SERVER["HTTPS"]=="on" ? $strHost="https://".$_SERVER["HTTP_HOST"] : $strHost="http://".$_SERVER["HTTP_HOST"];
}
else
{
	$strHost="http://".$_SERVER["HTTP_HOST"];
}

// �¹ԥ����С�̾
define ( "EXECUTE_HOST_NAME_DEV",	"kids.devs.biz" );
//define ( "EXECUTE_HOST_NAME_KWG_WEBv2",       "192.168.10.229" );
define ( "EXECUTE_HOST_NAME_KWG_WEBv2",	"192.168.10.228" );
define ( "EXECUTE_HOST_NAME_KWG_WEBv1",	"192.168.10.235" );
define ( "EXECUTE_HOST_NAME_KWG_BACK",	"192.168.10.230" );
// define ( "EXECUTE_HOST_NAME_KWG_DB",	"192.168.1.82" );
//define ( "EXECUTE_HOST_NAME_KWG_DB",	"192.168.1.113" );
define ( "EXECUTE_HOST_NAME_KWG_DB",	"127.0.0.1" ); // ���������ǧ��

// �¹ԥ����С�̾�Τμ���
$strExcuteHttpHostName = $_SERVER["HTTP_HOST"];


define ( "TOP_URL",	$strHost."/" );

// ��ȯ�Ķ������ִĶ���ۥ���̾�ǿ���ʬ��
switch( $strExcuteHttpHostName )
{
	case EXECUTE_HOST_NAME_DEV:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_DEV."/" );
		// DB����
		define ( "POSTGRESQL_HOSTNAME", "localhost" );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		// ���顼�᡼��������
		define ( "ERROR_MAIL_TO", "devs.biz@gmail.com" );
		//define ( "ERROR_MAIL_TO", "miyata@testdomain.local" );
		define ( "SEND_MAIL_FLAG", true);
		// �ƥ롼�ȥѥ�
		define ( "PATH_HOME",	"/home/kids2/" );
		// �̲�(���ܱ�)���楳����
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;

	case EXECUTE_HOST_NAME_KWG_WEBv2:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_KWG_WEBv2."/" );
		// DB����
//		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "POSTGRESQL_HOSTNAME", "localhost" );
//                define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "POSTGRESQL_HOSTPORT", "9999" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		// ���顼�᡼��������
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", true);
		// �ƥ롼�ȥѥ�
		define ( "PATH_HOME",	"/home/kids2" );
		// �̲�(���ܱ�)���楳����
		//define ( "DEF_MONETARY_CODE_YEN", "\\\\" );
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;

	case EXECUTE_HOST_NAME_KWG_WEBv1:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_KWG_WEBv1."/" );
		// DB����
		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore" );
		// ���顼�᡼��������
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", false);
		// �ƥ롼�ȥѥ�
		define ( "PATH_HOME",	"/home/kids2" );
		// �̲�(���ܱ�)���楳����
		//define ( "DEF_MONETARY_CODE_YEN", "\\\\" );
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;
	case EXECUTE_HOST_NAME_KWG_BACK:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_KWG_BACK."/" );
		// DB����
		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_BACK );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		// ���顼�᡼��������
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", true);
		// �ƥ롼�ȥѥ�
		define ( "PATH_HOME",	"/home/kids2" );
		// �̲�(���ܱ�)���楳����
		//define ( "DEF_MONETARY_CODE_YEN", "\\\\" );
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;
	default:
		// DB����
		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		define ( "LC_POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "LC_POSTGRESQL_HOSTPORT", "5432" );
		define ( "LC_DB_LOGIN_USERNAME", "ackids" );
		define ( "LC_DB_LOGIN_PASSWORD", "kids" );
		define ( "LC_DB_NAME", "ackids" );
		// ���顼�᡼��������
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", true);
		// �ƥ롼�ȥѥ�
		// define ( "PATH_HOME",	"/Users/solcom/Desktop/Git/KIDS/01_kidsweb/home/kids2" );
		// define ( "PATH_HOME",	"/Users/solcom-ad/Desktop/Git/KIDS/01_kidsweb/home/kids2" ); // ���������ǧ��
		// define ( "PATH_HOME",	"E:/Source/Repos/solcom-net/KIDS/01_kidsweb/home/kids2" );
        define ( "PATH_HOME",    "/Users/esash/Desktop/solcom/git/KIDS2/01_kidsweb/home/kids2" ); // ���������ǧ��
        define ( "LOG_FILE",     PATH_HOME ."/src/logs/debug.log" ); // ���������ǧ��

		break;
}


// ����������
define ("DEF_DEBUG_DIR",	PATH_HOME . "/log/");

// ��������URL
define ( "LOGIN_URL",	TOP_URL );

// �����ƥ�С������
define ( "DEF_SYSTEM_VERSION",	"2.1" );

// ľ����Ͽ��ǽ���¥��롼�ץ�����
define ( "DEF_DIRECT_REGIST_AUTHORITY_CODE",	4 );

// ����
// �����ॢ���Ȼ���(ʬ)
define ( "REMINDER_LIMIT",	1440 );

// �����ॢ���ȷٹ�ɽ��(��)
define ( "LIMIT_REMAIN_SECONDS",	300 );


define ( "IMAGE_LIMIT",	102400 );
define ( "LAYOUT_CODE",	"type01" );

define ( "TMP_ROOT",	PATH_HOME . "/tmp/" );
define ( "LIB_ROOT",	PATH_HOME . "/lib/" );
define ( "CLS_ROOT",	PATH_HOME . "/lib/" );
define ( "SRC_ROOT",	PATH_HOME . "/src/" );
define ( "SQLLOG_ROOT",	PATH_HOME . "/temp/" );
define ( "VENDOR_ROOT",	PATH_HOME . "/vendor/" );

// ���饹���饤�֥��ѥ�
define ( "LIB_FILE",			LIB_ROOT . "lib.php" );
define ( "LIB_DEBUGFILE",		LIB_ROOT . "libdebug.php" );			// added by saito 20051017
define ( "LIB_EXCELPARSER_FILE",	LIB_ROOT . "excelparser/excelparser.php" );	// Excel Parser Module
define ( "CLS_DB_FILE",			LIB_ROOT . "clsdb.php" );
define ( "CLS_AUTH_FILE",		LIB_ROOT . "clsauth.php" );
define ( "CLS_TEMPLATE_FILE",		LIB_ROOT . "clstemplate.php" );
define ( "CLS_CACHE_FILE",		LIB_ROOT . "clscache.php" );
define ( "CLS_EXCELMAP_FILE",		LIB_ROOT . "mapping/clsMapping.php" );		// Excel Mapping Object
define ( "CLS_TABLETEMP_FILE",		LIB_ROOT . "tabletemp/clstabletemp.php" );	// Temporary DB Object
define ( "CLS_IMAGELO_FILE",		LIB_ROOT . "clsImageLo.php" );
define ( "VENDOR_AUTOLOAD_FILE",	VENDOR_ROOT . "autoload.php" ); // Composer��autoload�Υѥ�

// �����ǥ��쥯�ȥ�ѥ�
define ( "USER_IMAGE_TMPDIR",		PATH_HOME . "/image_tmp/" );	// ����¸���
define ( "USER_IMAGE_DIR",		SRC_ROOT . "face/" );		// �����֤���
define ( "USER_IMAGE_TYPE",		"pjpeg|jpeg|gif" );		// ���� MIME-TYPE
define ( "USER_IMAGE_URL",		TOP_URL . "face/" );
define ( "USER_IMAGE_DEFAULT_URL",	USER_IMAGE_URL . "default.gif" );

// ���ʻ��;ܺ١�����UP��DEST���
define ( "DEF_PEDIT_IMGTMP", 		"/p/edit/image_tmp/" );			// URL�����ѥѥ�
define ( "USER_IMAGE_PEDIT_TMPDIR",	SRC_ROOT . "p/edit/image_tmp/" );	// �����ƥ�ե����뻲���ѥѥ�

// �ե����륢�åץ����ɥǥ��쥯�ȥ�ѥ�
define ( "FILE_UPLOAD_TMPDIR",	PATH_HOME . "/upload_tmp/" );	// �ե����륢�åץ����ɲ���¸���


// ����ե������֥����ɤ��ؤ��������������
$aryWorkflowStatus = Array ( "0"=>"�����", "1"=>"������", "2"=>"��Ƥ��", "3"=>"���ᤷ", "4"=>"�������", "10"=>"��ǧ", "99"=>"��ǧ" );

// t_Workflow.lngWorkflowStatusCode
define ( "DEF_STATUS_VOID",		0 );	// �����
define ( "DEF_STATUS_ORDER",	1 );	// ������
define ( "DEF_STATUS_CONSIDER",	2 );	// ��Ƥ��
define ( "DEF_STATUS_BACK",		3 );	// �����ᤷ
define ( "DEF_STATUS_CANCELL",	4 );	// �����������
define ( "DEF_STATUS_APPROVE",	10 );	// ��ǧ
define ( "DEF_STATUS_DENIAL",	99 );	// ��ǧ

// m_WorkflowOrder.lngWorkflowStatusCode
define ( "DEF_APPROVE_VOID",	0 );	// �����
define ( "DEF_APPROVER",		1 );	// ��ǧ��
define ( "DEF_FINAL_APPROVER",	2 );	// �ǽ���ǧ��

// m_Product.lngProductStatusCode
define ( "DEF_PRODUCT_NORMAL",		0 );	// �ޥ�������
define ( "DEF_PRODUCT_APPLICATE",	1 );	// ������

// t_GoodsPlan.lngGoodsPlanProgressCode
define ( "DEF_GOODSPLAN_AFOOT",	1 );	// ���ʹ���
define ( "DEF_GOODSPLAN_HOLD",	2 );	// ��α
define ( "DEF_GOODSPLAN_HALT",	3 );	// ���
define ( "DEF_GOODSPLAN_END",	4 );	// Ǽ�ʺ�

// m_Estimate.lngEstimateStatusCode
define ( "DEF_ESTIMATE_TEMPORARY",	1 );	// ����¸
define ( "DEF_ESTIMATE_APPLICATE",	2 );	// ������
define ( "DEF_ESTIMATE_CANCELL",	3 );	// �������
define ( "DEF_ESTIMATE_APPROVE",	4 );	// ��ǧ
define ( "DEF_ESTIMATE_DENIAL",		5 );	// ��ǧ

// m_receivestatus.lngReceiveStatusCode
define ( "DEF_RECEIVE_APPLICATE",	1 );	// ������
define ( "DEF_RECEIVE_ORDER",		2 );	// ����
define ( "DEF_RECEIVE_DELIVER",		3 );	// Ǽ����
define ( "DEF_RECEIVE_END",			4 );	// Ǽ�ʺ�
define ( "DEF_RECEIVE_CLOSED",		99 );	// �����

// m_Sales.lngSalesStatusCode
//ľ�����Ͽ��Ǽ�ʺѤߤˤ��뽤��By��kou��2007/10/2
define ( "DEF_SALES_APPLICATE",	1 );	// ������
define ( "DEF_SALES_ORDER",		4 );	// ���
define ( "DEF_SALES_DELIVER",	3 );	// Ǽ����
define ( "DEF_SALES_END",		4 );	// Ǽ�ʺ�
define ( "DEF_SALES_DENIAL",	5 );	// ��ǧ
define ( "DEF_SALES_CLOSED",	99 );	// �����

// m_orderstatus.lngOrderStatusCode
define ( "DEF_ORDER_APPLICATE",	1 );	// ������
define ( "DEF_ORDER_ORDER",		2 );	// ȯ��
define ( "DEF_ORDER_DELIVER",	3 );	// Ǽ����
define ( "DEF_ORDER_END",		4 );	// Ǽ�ʺ�
define ( "DEF_ORDER_CLOSED",	99 );	// �����

// m_Stock.lngStockStatusCode
define ( "DEF_STOCK_APPLICATE",	1 );	// ������
define ( "DEF_STOCK_ORDER",		2 );	// ����
define ( "DEF_STOCK_DELIVER",	3 );	// Ǽ����
define ( "DEF_STOCK_END",		4 );	// Ǽ�ʺ�
define ( "DEF_STOCK_DENIAL",	5 );	// ��ǧ
define ( "DEF_STOCK_CLOSED",	99 );	// �����

// m_AuthorityGroup.lngAuthorityLevel
define ( "DEF_AUTHG_ADMIN",		10 );	// �����ƥ������
define ( "DEF_AUTHG_OPERATOR",	20 );	// �����ƥ౿�Ѽ�
define ( "DEF_AUTHG_DIRECTOR",	100 );	// �ǥ��쥯����
define ( "DEF_AUTHG_MANAGER",	110 );	// �ޥ͡����㡼
define ( "DEF_AUTHG_USER",		120 );	// �桼����
define ( "DEF_AUTHG_GUEST",		130 );	// ������

// m_Attribute.lngAttributeCode
define ( "DEF_ATTRIBUTE_HEADOFFICE",	1 );	// �ܼ�
define ( "DEF_ATTRIBUTE_CLIENT",		2 );	// �ܵ�
define ( "DEF_ATTRIBUTE_SUPPLIER",      3 );    // ������
define ( "DEF_ATTRIBUTE_DELIVERY",      5 );    // Ǽ�ʾ�ꡦ�Ҹ�

// m_ReportClass.lngReportClassCode
define ( "DEF_REPORT_PRODUCT",	1 );	// ���ʲ�����
define ( "DEF_REPORT_ORDER",	2 );	// PurchaseOrder(ȯ��)
define ( "DEF_REPORT_ESTIMATE",	3 );	// ���Ѹ����׻�
define ( "DEF_REPORT_MOLD",	4 );		// �ⷿ�����
define ( "DEF_REPORT_SLIP",	5 );		// Ǽ�ʽ�

// m_Organization.lngOrganizationCode
define ( "DEF_ORGANIZATION_FOREIGN",	11 ); // ����

// m_Function.lngFunctionCode
define ( "DEF_FUNCTION_LOGIN0",	100 );	//  100 ��������
define ( "DEF_FUNCTION_LOGIN1",	101 );	//  101 ���������
define ( "DEF_FUNCTION_LOGIN2",	102 );	//  102 �ѥ���ɥ�ޥ������
define ( "DEF_FUNCTION_MENU0",	200 );	//  200 ��˥塼ɽ��

define ( "DEF_FUNCTION_P0",	300 );      //  300 ���ʴ���
define ( "DEF_FUNCTION_P1",	301 );      //  301 ���ʴ����ʾ�����Ͽ��
define ( "DEF_FUNCTION_P2",	302 );      //  302 ���ʴ����ʾ��ʸ�����
define ( "DEF_FUNCTION_P3",	303 );      //  303 ���ʴ����ʾ��ʸ���������ǡ�����ɽ����
define ( "DEF_FUNCTION_P4",	304 );      //  304 ���ʴ����ʾ��ʾ���ܺ١�
define ( "DEF_FUNCTION_P5",	305 );      //  305 ���ʴ����ʾ��ʾ���ܺ١�����ǡ�����ɽ����
define ( "DEF_FUNCTION_P6",	306 );      //  306 ���ʴ����ʾ��ʽ�����
define ( "DEF_FUNCTION_P7",	307 );      //  307 ���ʴ����ʾ��ʺ����

define ( "DEF_FUNCTION_SO0",	400 );     //  400 ��������
define ( "DEF_FUNCTION_SO1",	401 );     //  401 ���������ʼ���������
define ( "DEF_FUNCTION_SO2",	402 );     //  402 ���������ʼ��������������⡼�ɡ�
define ( "DEF_FUNCTION_SO3",	403 );     //  403 ���������ʼ����ܺ�ɽ����
define ( "DEF_FUNCTION_SO4",	404 );     //  404 ���������ʼ��������
define ( "DEF_FUNCTION_SO5",	405 );     //  405 ���������ʼ��������á�

define ( "DEF_FUNCTION_PO0",	500 );     //  500 ȯ������
define ( "DEF_FUNCTION_PO1",	501 );     //  501 ȯ��������ȯ����Ͽ��
define ( "DEF_FUNCTION_PO2",	502 );     //  502 ȯ��������ȯ��������
define ( "DEF_FUNCTION_PO3",	503 );     //  503 ȯ��������ȯ�������������⡼�ɡ�
define ( "DEF_FUNCTION_PO4",	504 );     //  504 ȯ��������ȯ���ܺ�ɽ����
define ( "DEF_FUNCTION_PO5",	505 );     //  505 ȯ��������ȯ��������
define ( "DEF_FUNCTION_PO6",	506 );     //  506 ȯ��������ȯ�������
define ( "DEF_FUNCTION_PO7",	507 );     //  507 ȯ��������ȯ����̵����������
define ( "DEF_FUNCTION_PO8",	508 );     //  508 ȯ�������ʾ��ʥޥ��������쥯�Ƚ�����
define ( "DEF_FUNCTION_PO9",	509 );     //  509 ȯ��������ȯ�������
define ( "DEF_FUNCTION_PO10",	510 );     //  510 ȯ��������ȯ���񸡺���
define ( "DEF_FUNCTION_PO11",	511 );     //  511 ȯ��������ȯ���񸡺��������⡼�ɡ�
define ( "DEF_FUNCTION_PO12",	512 );     //  512 ȯ��������ȯ��������

define ( "DEF_FUNCTION_SC0",	600 );     //  600 ������
define ( "DEF_FUNCTION_SC1",	601 );     //  601 �������������Ͽ��
define ( "DEF_FUNCTION_SC2",	602 );     //  602 ����������帡����
define ( "DEF_FUNCTION_SC3",	603 );     //  602 ����������帡���������⡼�ɡ�
define ( "DEF_FUNCTION_SC4",	604 );     //  604 �����������ܺ�ɽ����
define ( "DEF_FUNCTION_SC5",	605 );     //  603 ����������彤����
define ( "DEF_FUNCTION_SC6",	606 );     //  604 ���������������
define ( "DEF_FUNCTION_SC7",	607 );     //  607 ������������̵����������
define ( "DEF_FUNCTION_SC8",	608 );     //  608 �����������μ���No����ꤷ�ʤ���Ͽ��
define ( "DEF_FUNCTION_SC9",	609 );     //  609 �����������μ���No����ꤷ�ʤ������ۡ�DEF_MONEY_NO_CODE�˰ʾ����Ͽ��
define ( "DEF_FUNCTION_SC10",	610 );     //  610 �������ʹ��ɲá��Ժ����

define ( "DEF_FUNCTION_PC0",	700 );     //  700 ��������
define ( "DEF_FUNCTION_PC1",	701 );     //  701 ���������ʻ�����Ͽ��
define ( "DEF_FUNCTION_PC2",	702 );     //  702 ���������ʻ���������
define ( "DEF_FUNCTION_PC3",	703 );     //  703 ���������ʻ��������������⡼�ɡ�
define ( "DEF_FUNCTION_PC4",	704 );     //  704 ���������ʻ����ܺ�ɽ����
define ( "DEF_FUNCTION_PC5",	705 );     //  705 ���������ʻ���������
define ( "DEF_FUNCTION_PC6",	706 );     //  706 ���������ʻ��������
define ( "DEF_FUNCTION_PC7",	707 );     //  707 ���������ʻ�����̵����������
define ( "DEF_FUNCTION_PC8",	708 );     //  708 ���������ʻ�����ȯ��No����ꤷ�ʤ���Ͽ��
define ( "DEF_FUNCTION_PC9",	709 );     //  709 ���������ʻ�����ȯ��No����ꤷ�ʤ������ۡ�DEF_MONEY_NO_CODE�˰ʾ����Ͽ��
define ( "DEF_FUNCTION_PC10",	710 );     //  710 ���������ʹ��ɲá��Ժ����

define ( "DEF_FUNCTION_WF0",	800 );     //  800 ����ե�������
define ( "DEF_FUNCTION_WF1",	801 );     //  801 ����ե��������ʰƷ������
define ( "DEF_FUNCTION_WF2",	802 );     //  802 ����ե��������ʰƷ︡����
define ( "DEF_FUNCTION_WF3",	803 );     //  803 ����ե��������ʰƷ︡������
define ( "DEF_FUNCTION_WF4",	804 );     //  804 ����ե��������ʰƷ����ܺ١�
define ( "DEF_FUNCTION_WF5",	805 );     //  805 ����ե��������ʰƷ����ܺ٣���
define ( "DEF_FUNCTION_WF6",	806 );     //  806 ����ե��������ʰƷ�ν�����

define ( "DEF_FUNCTION_LO0",	900 );     //  900 Ģɼ����
define ( "DEF_FUNCTION_LO1",	901 );     //  901 Ģɼ����(���ʲ�����)
define ( "DEF_FUNCTION_LO2",	902 );     //  902 Ģɼ����(ȯ����У�)
define ( "DEF_FUNCTION_LO3",	903 );     //  903 Ģɼ����(���ʲ�����)���ԡ��������
define ( "DEF_FUNCTION_LO4",	904 );     //  904 Ģɼ����(ȯ����У�)���ԡ��������
define ( "DEF_FUNCTION_LO5",	905 );     //  905 Ģɼ����(Ǽ�ʽ�)
define ( "DEF_FUNCTION_LO6",	906 );     //  906 Ģɼ����(Ǽ�ʽ�)���ԡ��������

define ( "DEF_FUNCTION_DE0",	1000 );    // 1000 �ǡ����������ݡ���
define ( "DEF_FUNCTION_DE1",	1001 );    // 1001 �ǡ����������ݡ���(ȯ������/L/C ͽ��ɽ)
define ( "DEF_FUNCTION_DE2",	1002 );    // 1002 �ǡ����������ݡ���(������/���쥷��)
define ( "DEF_FUNCTION_DE3",	1003 );    // 1003 �ǡ����������ݡ���(��������/��������ɽ)
define ( "DEF_FUNCTION_DE4",	1004 );    // 1004 �ǡ����������ݡ���(��������/Purchase recipe file)
define ( "DEF_FUNCTION_DE5",	1005 );    // 1005 ���Ѹ�����
define ( "DEF_FUNCTION_DE6",	1006 );    // 1006 �������ץǡ�������師����
define ( "DEF_FUNCTION_DE7",	1007 );    // 1007 �������ץǡ����ʳ�������
define ( "DEF_FUNCTION_DE8",	1008 );    // 1008 �ǡ����������ݡ���(���ʷײ��)

define ( "DEF_FUNCTION_UC0",	1100 );    // 1100 �桼��������
define ( "DEF_FUNCTION_UC1",	1101 );    // 1101 �桼���������ʥ桼���������
define ( "DEF_FUNCTION_UC2",	1102 );    // 1102 �桼���������ʥ桼������Ͽ��
define ( "DEF_FUNCTION_UC3",	1103 );    // 1103 �桼���������ʥ桼����������
define ( "DEF_FUNCTION_UC4",	1104 );    // 1104 �桼���������ʥ桼�����ܺ١�
define ( "DEF_FUNCTION_UC5",	1105 );    // 1105 �桼���������ʥ桼����������

define ( "DEF_FUNCTION_M0" ,	1200 );   // 1200 �ޥ�������
define ( "DEF_FUNCTION_SYS0",	1300 );   // 1300 �����ƥ����
define ( "DEF_FUNCTION_SYS1",	1301 );   // 1301 �����ƥ����(���Τ餻)
define ( "DEF_FUNCTION_SYS2",	1302 );   // 1302 �����ƥ����(�����С�����)
define ( "DEF_FUNCTION_SYS3",	1303 );   // 1303 �����ƥ����(�����ԥ᡼��)
define ( "DEF_FUNCTION_SYS4",	1304 );   // 1304 �����ƥ����(���å����)
define ( "DEF_FUNCTION_SYS5",	1305 );   // 1305 �����ƥ����(��Хå�����)

define ( "DEF_FUNCTION_CLD0",	1400 );	// 1400 �������

define ( "DEF_FUNCTION_E0",	1500 );	// 1500 ���Ѹ�������
define ( "DEF_FUNCTION_E1",	1501 );	// 1501 ���Ѹ�����������Ͽ��
define ( "DEF_FUNCTION_E2",	1502 );	// 1502 ���Ѹ��������ʸ�����
define ( "DEF_FUNCTION_E3",	1503 );	// 1503 ���Ѹ��������ʽ�����
define ( "DEF_FUNCTION_E4",	1504 );	// 1504 ���Ѹ��������ʾܺ١�
define ( "DEF_FUNCTION_E5",	1505 );	// 1505 ���Ѹ��������ʺ����

define ( "DEF_FUNCTION_UP0",	1600 );	// 1600 ���åץ����ɴ���
define ( "DEF_FUNCTION_UP1",	1601 );	// 1601 ���åץ����ɴ�������Ͽ��

define ( "DEF_FUNCTION_BG02",	1702 );	// 1702 �Хå����饦��ɽ����ʸ��������ƥࡦ��Ľ������ǽϢ�ȡ�WF��ǧ��᡼��������

define ( "DEF_FUNCTION_MM0", 1800 );	//�ⷿ����
define ( "DEF_FUNCTION_MM1", 1801 );	//�ⷿ����(��Ͽ)
define ( "DEF_FUNCTION_MM2", 1802 );	//�ⷿ����(����)
define ( "DEF_FUNCTION_MM3", 1803 );	//�ⷿ����(�ܺ�)
define ( "DEF_FUNCTION_MM4", 1804 );	//�ⷿ����(����)
define ( "DEF_FUNCTION_MM5", 1805 );	//�ⷿ����(���)

define ( "DEF_FUNCTION_MR0", 1900 );	//�ⷿĢɼ����
define ( "DEF_FUNCTION_MR1", 1901 );	//�ⷿĢɼ����(��Ͽ)
define ( "DEF_FUNCTION_MR2", 1902 );	//�ⷿĢɼ����(����)
define ( "DEF_FUNCTION_MR3", 1903 );	//�ⷿĢɼ����(�ܺ�)
define ( "DEF_FUNCTION_MR4", 1904 );	//�ⷿĢɼ����(����)
define ( "DEF_FUNCTION_MR5", 1905 );	//�ⷿĢɼ����(���)

define ( "DEF_FUNCTION_DWN",	2001 );	// 2001 ����������ɴ���

define ( "DEF_FUNCTION_LC0",	2100 );	// 2100 L/C����
define ( "DEF_FUNCTION_LC1",	2101 );	// 2101 L/C����
define ( "DEF_FUNCTION_LC2",	2102 );	// 2102 L/C�����ѹ�
define ( "DEF_FUNCTION_LC3",	2103 );	// 2103 L/C�Խ�
define ( "DEF_FUNCTION_LC4",	2104 );	// 2104 L/CĢɼ����

define ( "DEF_FUNCTION_INV0",	2200 );	// 2200 �������
define ( "DEF_FUNCTION_INV1",	2201 );	//  �����ȯ��
define ( "DEF_FUNCTION_INV2",	2202 );	// 2202 ����񸡺�
define ( "DEF_FUNCTION_INV3",	2203 );	// 2203 ���ὸ��

// ���顼����
define ( "DEF_WARNING",		1 );           //  1 ���ե�٥�����ϥߥ��ʤɡ�
define ( "DEF_ERROR",		2 );           //  2 ���顼��٥�
define ( "DEF_FATAL",		3 );           //  3 �����ƥ२�顼��٥�
define ( "DEF_ANNOUNCE",	99 );          //  99 �����ƥॢ�ʥ���

// �ǥХå��⡼��
define ( "DEF_DEBUG_MODE",	0 );         //  �ܲ�ư�⡼�ɡ������顼����ɽ��
//define ( "DEF_DEBUG_MODE",	1 );      //  �ǥХå��⡼�ɡ����顼������ɽ��

// ȯ����Ͽ
define ("DEF_M_WORKFLOW_FUNCTIONCODE",	1);		// ��ǽ������
define ("DEF_T_WORKFLOW_SUBCODE",		1);		// ����ե������֥�����
define ("DEF_T_WORKFLOW_ORDERNO",		1);		// ����ե�������ֹ�
define ("DEF_T_WORKFLOW_STATUS",		1);		// ����ե������֥�����

// �̲ߥ�����
define ("DEF_MONETARY_YEN",	1);				// ���ܱ�
define ("DEF_MONETARY_USD",	2);				// US�ɥ�
define ("DEF_MONETARY_HKD",	3);				// HK�ɥ�

// ����졼�����
define ("DEF_MONETARYCLASS_SHANAI", 	2);		// ����

define ("DEF_MONEY_USER",		0);			// �桼���Ϥʤ�
define ("DEF_MONEY_MANAGER",	50000);		// �ޥ͡����㡼��5���ޤ�
define ("DEF_MONEY_DIRECTOR",	200000);	// �ǥ��쥯������20���ޤ� 

// 2004.03.01 suzukaze update start
define ("DEF_MONEY_NO_CODE",	50000);		// �������Ǥ�ȯ������No�ʤ��Ǥ���Ͽ��ǽ���  �嵭�����ɤ���Ѥ��ʤ��褦�˽���
// 2004.03.01 suzukaze update end

// ���ʤβ��ʤΥǥե�����̲�
define ("DEF_PRODUCT_MONETARYSIGN",	"\\");	// ���ʾ���ɽ�������̲�ɽ��

// ���������ɽ���ͣ��ط��
define ("DEF_SEARCH_MAX",	1000);			// �ǥե���Ȥ�1000��ʾ�ξ��ϥ��顼ɽ������

// ���顼����ư��
define ("DEF_ERROR_CLOSE",		0);				// ���顼���˥�����ɥ����Ĥ���
define ("DEF_ERROR_BACK",		1);				// ���顼�������β��̤����

// ��Хå�����
define ("DEF_BATCH_DAY",	   	"01");		// ��Хå������¹�����
define ("DEF_BATCH_USERID",		"batsys");	// ��̤���ѡ˥Хå������¹ԥ桼����
define ("DEF_BATCH_USERCODE",	 0);		// �Хå������¹ԥ桼�����Υ桼����������

// 2004/03/12 suzukaze update start
// ������ʬ
define ("DEF_CONVERSION_SEIHIN",		1);		// ����ñ�̷׾�
define ("DEF_CONVERSION_NISUGATA",		2);		// �ٻ�ñ�̷׾�

// ñ��
define ("DEF_PRODUCTUNIT_PCS",		1);			// Piece
define ("DEF_PRODUCTUNIT_CTN",		2);			// Carton
define ("DEF_PRODUCTUNIT_SET",		3);			// Set
// 2004/03/12 suzukaze update end

// 2004/03/12 watanabe update start
define ( "DEF_EN_MARK",	"\\");				// ������Ͽ�γ�ǧ���̤ǡ֡�פ�ɽ��
// watanabe update end

// 2004.03.24 suzukaze update start
// ����������˼���No���ɲä���ʸ��
define ( "DEF_RECEIVE_DEL_START",	"*");		// ����ʸ��
define ( "DEF_RECEIVE_DEL_END",		"*");		// ��λʸ��
// 2004.03.24 suzukaze update end

// 2004.03.25 suzukaze update start
// 2004.05.31 suzukaze update start
// �ⷿ�ֹ���������������ܡ���������
define ( "DEF_MOLD_STOCK_SUBJECT",	433);		// �������ܡ��������ʶⷿ�������ѡ�
define ( "DEF_MOLD_STOCK_ITEM",		1);			// �������ʡ�����Injection Mold��
// �ⷿ�ֹ���������������ܡ��������� �ɲ�ʬ
define ( "DEF_MOLD_STOCK_SUBJECT_ADD",	431);	// �������ܡ��������ʶⷿ���ѹ��
define ( "DEF_MOLD_STOCK_ITEM_ADD",		8);		// �������ʡ����ʶⷿ��
// 2004.05.31 suzukaze update end
// 2004.03.25 suzukaze update end

// 2004.03.30 suzukaze update start
// �Ƕ�ʬ
define ("DEF_TAXCLASS_HIKAZEI",	1);				// �����
define ("DEF_TAXCLASS_SOTOZEI",	2);				// ����
define ("DEF_TAXCLASS_UCHIZEI",	3);				// ����
// 2004.03.30 suzukaze update end

// 2004.04.08 suzukaze update start
// ü��������ˡ
define ("DEF_CALC_SISHAGONYU",	0);				// �ͼθ���
define ("DEF_CALC_KIRISUTE",	1);				// �ڼΤ�
define ("DEF_CALC_KIRIAGE",		2);				// �ھ夲
// 2004.04.08 suzukaze update end

// 2004.04.09 suzukaze update start
// �̲ߤ��Ф���ü�������оݷ��
define ("DEF_CALCMONETARY_YEN",		0);				// �ߤξ�硢ü�������ϣ��� Ex. 1234.56-->1234.00
define ("DEF_CALCMONETARY_OTHER",	2);				// �߰ʳ��ξ�硢ü�������Ͼ������ʲ����� Ex. 1234.5678-->1234.58
// 2004.04.09 suzukaze update end

// 2004.05.13 suzukaze update start
// ��ʧ���
define ("DEF_PAYCONDITION_LC",		1);		// LC
define ("DEF_PAYCONDITION_TT",		2);		// TT
// 2004.05.13 suzukaze update end

// ���ʲ�����ե����ޥåȥ�����
define ( "DEF_P_FORMAT_CODE",	"2004-12-1" );

// �оݥ��ꥢ������
define ( "DEF_AREA_PRODUCT_SALES",     1);  // �������
define ( "DEF_AREA_FIXED_COST_SALES",  2);  // ���������
define ( "DEF_AREA_FIXED_COST_ORDER",  3);  // ������
define ( "DEF_AREA_PARTS_COST_ORDER",  4);  // ������
define ( "DEF_AREA_OTHER_COST_ORDER",  5);  // ����¾����

// �̲ߥ졼�ȶ�ʬ
define ( "DEF_MONETARY_RATE_CODE_TTM",           1); // TTM
define ( "DEF_MONETARY_RATE_CODE_COMPANY_LOCAL", 2); // ����

// // ��å�����������
define ( "DEF_MESSAGE_CODE_RATE_UNCAUGHT_WARNING",    9201); // �̲ߥ졼�ȼ����Բ�
define ( "DEF_MESSAGE_CODE_RATE_DIFFER",              9202); // �̲ߥ졼�Ⱥ���
define ( "DEF_MESSAGE_CODE_NOT_ENTRY_ERROR",          9203); // ̤���ϥ��顼
define ( "DEF_MESSAGE_CODE_FORMAT_ERROR",             9204); // �������顼
define ( "DEF_MESSAGE_CODE_MASTER_CHECK_ERROR",       9205); // �ޥ����������å����顼
define ( "DEF_MESSAGE_CODE_PRODUCT_CODE_ERROR",       9206); // ���ʥ����ɥ����å����顼
define ( "DEF_MESSAGE_CODE_FILE_UPLOAD_ERROR",        9207); // �ե����륢�åץ����ɥ��顼
define ( "DEF_MESSAGE_CODE_VALID_SHEET_ABSENT_ERROR", 9208); // ͭ����������¸�ߥ��顼

// ������ʬ�ޥ���������
define ("DEF_STOCK_CLASS_CODE_PARTS",      1); // ������
define ("DEF_STOCK_CLASS_CODE_PRODUCTION", 2); // ��¤����
define ("DEF_STOCK_CLASS_CODE_TEST",       3); // �ƥ�������

// Ǽ����ɼ����ޥ���������
define ("DEF_SLIP_KIND_EXCLUSIVE", 1);   // ����Ģɼ
define ("DEF_SLIP_KIND_COMM",      2);   // ����Ģɼ
define ("DEF_SLIP_KIND_DEBIT",     3);   // DEBIT NOTE

// ���ʬ��ޥ���������
define ("DEF_SALES_DIVISION_CODE_FIXED_COST_SALES",    1); // ���������
define ("DEF_SALES_DIVISION_CODE_PRODUCT_SALES",       2); // �������

// ����ʬ�ޥ���������
define ("DEF_SALES_CLASS_CODE_MAIN_PRODUCT",    1); // �ܲ�

// �������ܥޥ���������
define ("DEF_STOCK_SUBJECT_CODE_OVERSEA_MANUFACTURE",       420); // �����ù���
define ("DEF_STOCK_SUBJECT_CODE_MOLD_DEPRECIATION",         431); // �ⷿ���ѹ�
define ("DEF_STOCK_SUBJECT_CODE_OVERSEA_MOLD_DEPRECIATION", 433); // �ⷿ��������
define ("DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST",       401); // �����ѡ��Ļ�����
define ("DEF_STOCK_SUBJECT_CODE_IMPORT_PARTS_COST",         402); // ͢���ѡ��Ļ�����
define ("DEF_STOCK_SUBJECT_CODE_CHARGE",                   1224); // ���㡼��
define ("DEF_STOCK_SUBJECT_CODE_EXPENSE" ,                 1230); // ����
define ("DEF_STOCK_SUBJECT_CODE_MATERIAL_TOOLS_COST",       403); // �����ġ��������

// �������ʥޥ���������
define ("DEF_STOCK_ITEM_CODE_IMPORT_COST",  2); // ͢������
define ("DEF_STOCK_ITEM_CODE_TARIFF",       3); // ����
// Ģɼ�ƥ�ץ졼�ȥǥ��쥯�ȥ�ѥ�
define ( "REPORT_TMPDIR",	PATH_HOME . "/report_tmp/" );
// L/CĢɼ�ƥ�ץ졼�ȥե�����
define ( "REPORT_LC_TMPFILE",	"Ģɼ�ƥ�ץ졼��.xls" );
// L/CĢɼ�ƥ�ץ졼�ȥե�����
define ( "REPORT_LC_OUTPUTFILE",	"Ģɼ�ƥ�ץ졼��_copy.xls" );

// ��������ȥƥ�ץ졼�ȥǥ��쥯�ȥ�ѥ�
define ( "EXCEL_TMP_ROOT", PATH_HOME . "/excel_tmp/");

// ��ҥ�����
define ( "DEF_COMPANY_CODE_OTHERS", 0); // ����¾

// ɽ���Ѳ�ҥ�����
define ( "DEF_DISPLAY_COMPANY_CODE_OTHERS", '0000'); // ����¾

// ���롼��°��������
define ( "DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP",              1);  // �Ķ�����
define ( "DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP",            2);  // ��ȯ����
define ( "DEF_GROUP_ATTRIBUTE_CODE_SALES_SUPPORT",            3);  // �Ķȥ��ݡ���
define ( "DEF_GROUP_ATTRIBUTE_CODE_ACCOUNTING",               4);  // ����
define ( "DEF_GROUP_ATTRIBUTE_CODE_QUALITY_ASSURANCE",        5);  // �ʼ��ݾ�

?>