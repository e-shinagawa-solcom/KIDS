<?php

// -----------------------------------------------------------
//
// �ⷿĢɼ����������
//
// -----------------------------------------------------------

include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');

// �����ϻ��Υץ�ե��å���
const LOG_PREFIX = "[KIDS-CloseMoldReport] ";

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

// �ⷿ��Ϣ�ơ��֥�Υ�å�
pg_query("LOCK m_mold");
pg_query("LOCK m_moldreport");
pg_query("LOCK t_moldreportdetail");
pg_query("LOCK t_moldreportrelation");
pg_query("LOCK t_moldhistory");

// ̤��λ���ơ������ζⷿĢɼ�쥳���ɤμ���
$reports = $utilMold->selectUnclosedMoldReport();

// ̤��λĢɼ��0��Ǥ���Х���Хå����ƴ�λ
if (!$reports)
{
	// ����Хå�
	$objDB->transactionRollback();
	syslog(LOG_INFO, LOG_PREFIX."�������оݤζⷿĢɼ�Ϥ���ޤ���Ǥ�����");
	exit;
}

syslog(LOG_INFO, LOG_PREFIX."�ⷿĢɼ��������������");

// ̤��λ��Ģɼ���ʬ����
foreach ($reports as $report_num => $report_row)
{
	// (�����Ԥ���)�桼��ID������
	$userCode = $report_row[TableMoldReport::UpdateBy];
	$utilMold->setUserCode($userCode);

	$id = $report_row[TableMoldReport::MoldReportId];
	$revision = $report_row[TableMoldReport::Revision];

	// ɳ�դ��ⷿĢɼ�ܺ٤μ���
	$details = $utilMold->selectMoldReportDetail($id, $revision);

	// �����ⷿ����쥳����
	$newHistory = array();
	// ���̹�������
	$newHistory[TableMoldHistory::Status] = $report_row[TableMoldReport::ReportCategory];
	$newHistory[TableMoldHistory::ActionDate] = $report_row[TableMoldReport::ActionRequestDate];
	$newHistory[TableMoldHistory::SourceFactory] = $report_row[TableMoldReport::SourceFactory];
	$newHistory[TableMoldHistory::DestinationFactory] = $report_row[TableMoldReport::DestinationFactory];

	// �����ⷿĢɼ��Ϣ�쥳����
	$newRelation = array();
	// ���̹�������
	$newRelation[TableMoldReportRelation::MoldReportId] = $id;
	$newRelation[TableMoldReportRelation::Revision] = $revision;

	// �ⷿĢɼ�ܺ٤η��ʬ����
	foreach ($details as $detail_num => $detail_row)
	{
		// �ⷿ�ֹ�
		$moldNo = $detail_row[TableMoldReportDetail::MoldNo];

		// �ⷿ�ֹ������
		$newHistory[TableMoldHistory::MoldNo] = $moldNo;
		$newRelation[TableMoldReportRelation::MoldNo] = $moldNo;

		// �ⷿ����ؤ�INSERT�η�̤������ʤ��ä����
		if (!$resultHistory = $utilMold->insertMoldHistory($newHistory))
		{
			// ����Хå�
			$objDB->transactionRollback();
			// ��å���������
			$message = LOG_PREFIX.
					"�ⷿ����κ����˼��Ԥ��ޤ�����"."\n".
					"MoldReportId:".$id."\n".
					"Revision:".$revision."\n".
					"MoldNo:".$moldNo."\n";

			// ���顼������
			error_log($message, 0);
			// ���顼�᡼������
			mb_send_mail(
					ERROR_MAIL_TO,
					"K.I.D.S. Error Message from " . TOP_URL,
					$message,
					"From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
			// �ⷿ����κ�������
			syslog(LOG_INFO, $message);
			exit;
		}

		// �ⷿ�����������̤��������ֹ�����
		$historyNo = $resultHistory[TableMoldHistory::HistoryNo];

		// �ⷿĢɼ��Ϣ�ơ��֥�ؤ�INSERT
		if (!$utilMold->insertMoldReportRelation($moldNo, $historyNo, $id, $revision))
		{
			// ����Хå�
			$objDB->transactionRollback();
			// ��å���������
			$message = LOG_PREFIX.
					"�ⷿĢɼ��Ϣ�κ����˼��Ԥ��ޤ�����"."\n".
					"MoldReportId:".$id."\n".
					"Revision:".$revision."\n".
					"MoldNo:".$moldNo."\n".
					"HistoryNo:".$historyNo."\n";

			// ���顼������
			error_log($message, 0);
			// ���顼�᡼������
			mb_send_mail(
					ERROR_MAIL_TO,
					"K.I.D.S. Error Message from " . TOP_URL,
					$message,
					"From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );

			// �ⷿĢɼ��Ϣ�κ�������
			syslog(LOG_INFO, $message);
			exit;
		}
	}

	// �����оݤζⷿĢɼ�Υ��ơ�������λ(������)���ڤ��ؤ�
	if (!$utilMold->updateCloseMoldReport($id, $revision))
	{
		// ����Хå�
		$objDB->transactionRollback();

		// ��å���������
		$message = LOG_PREFIX.
		"�ⷿĢɼ���ơ������ι����˼��Ԥ��ޤ�����"."\n".
		"MoldReportId:".$id."\n".
		"Revision:".$revision."\n";

		// ���顼������
		error_log($message, 0);
		// ���顼�᡼������
		mb_send_mail(
			ERROR_MAIL_TO,
			"K.I.D.S. Error Message from " . TOP_URL,
			$message,
			"From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
		// �ⷿĢɼ���ơ������ι�������
		syslog(LOG_INFO, $message);
		exit;
	}
}

// ���ߥå�
$objDB->transactionCommit();

syslog(LOG_INFO, LOG_PREFIX."�ⷿĢɼ������������λ");

return;