<?php

// -----------------------------------------------------------
//
// �ⷿ�ޥ�������ݡ��Ƚ���
//
// -----------------------------------------------------------

include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');

// �����ϻ��Υץ�ե��å���
const LOG_PREFIX = "[KIDS-ImportMold] ";

$objDB   = new clsDB();
$objAuth = new clsAuth();

// DB�����ץ�
$objDB->open("", "", "", "");

// �ꥯ�����ȼ���
$aryData = $_REQUEST;

// �ȥ�󥶥�����󳫻�
$objDB->transactionBegin();

// Util���󥹥��󥹤μ���
$utilMold = UtilMold::getInstance();
// ���ߡ��Υ桼�������ɤ�����
$utilMold->setUserCode(99999);

// ������Ϣ�Υơ��֥��å�
pg_query("LOCK m_stock");
pg_query("LOCK t_stockdetail");

// �ⷿ��Ϣ�ơ��֥�Υ�å�
pg_query("LOCK m_mold");

syslog(LOG_INFO, LOG_PREFIX."�ⷿ�ޥ�������ݡ��Ƚ�������");

// �ȥ�󥶥�����󳫻�
$objDB->transactionBegin();

// �ⷿ�ޥ����ǡ�����̵����
$invalided = $utilMold->updateMoldToInvalid();

// ̵��������Υ�����
syslog(LOG_INFO, LOG_PREFIX.$invalided."��̵����");

// ����ݡ��ȥ�����¹�
$affected = $utilMold->importMoldFromStock();

// �����߷���Υ�����
syslog(LOG_INFO, LOG_PREFIX.$affected."�������");

// ���ߥå�
$objDB->transactionCommit();

syslog(LOG_INFO, LOG_PREFIX."�ⷿ�ޥ�������ݡ��Ƚ�����λ");

return;