<?
/** 
*	���ʡ�����Ľ�����ѡ���Хå�����
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	���ߤ����դ���κǽ�������������Ǥ���С�
*	����Ľ�����ơ��֥��Ĵ��������Ǽ���ξ��ʤ��Ф���
*	���֤��Ǽ�ʺѡפ˼�ư���ѹ�����
*
*/

// ������ɤ߹���
include_once ( "/home/kids2/lib/conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

$objDB   = new clsDB();
$objDB->open( "", "", "", "" );

// �¹������μ���
$run_date = date("d");
if ( $run_date != DEF_BATCH_DAY )
{
	$aryMessage[] = "��Хå��¹��������㤤�ޤ���";
}
else
{
	// �������դμ���
	$last_month_01 = date("Y/m/d 00:00:00", strtotime("-1 month"));
	$last_month_lastday = date("Y/m/d 23:59:59", strtotime("-1 day"));

	// echo "<br>last_month_01 = " . $last_month_01;
	// echo "<br>last_month_lastday = " . $last_month_lastday;

	// ��Ǽ���ξ��ʥǡ������������
	$aryQuery[] = "SELECT distinct on (p.lngProductNo) p.lngProductNo as lngProductNo, \n";
	$aryQuery[] = "t_gp.lngRevisionNo as lngRevisionNo, t_gp.dtmCreationDate as dtmCreationDate, \n";
	$aryQuery[] = "t_gp.lngGoodsPlanCode as lngGoodsPlanCode \n";
	$aryQuery[] = "FROM m_Product p, t_GoodsPlan t_gp \n";
//	$aryQuery[] = "WHERE To_Date( p.dtmDeliveryLimitDate, 'YYYY/MM' ) >= To_Date( '" . $last_month_01 . "', 'YYYY/MM' ) \n";	// ���ǽ����������ƹ����оݤȤ����١�������
	$aryQuery[] = "WHERE p.dtmDeliveryLimitDate <= To_Date( '" . $last_month_lastday . "', 'YYYY/MM' ) \n";
	$aryQuery[] = "AND t_gp.lngGoodsPlanProgressCode = " . DEF_GOODSPLAN_AFOOT . " \n";
//	$aryQuery[] = "AND t_gp.lngGoodsPlanProgressCode <> " . DEF_GOODSPLAN_END . " \n";
	$aryQuery[] = "AND t_gp.lngProductNo = p.lngProductNo \n";
	$aryQuery[] = "AND t_gp.lngRevisionNo = ( "
				. "SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo ) \n";
	$aryQuery[] = "AND p.bytInvalidFlag = FALSE \n";

	$strQuery = implode( "\n", $aryQuery );

	// echo "<BR>strQuery = " . $strQuery;

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryMessage[] = "����Ǽ���ǡ�Ǽ�ʺѡװʳ��ξ��֤ξ��ʤϤ���ޤ���Ǥ�����";
	}
	$objDB->freeResult( $lngResultID );

	if ( count($aryResult) )
	{
		// �Хå��桼�����Υ桼���������ɤ��������
		$lngBatchUserCode = DEF_BATCH_USERCODE;

		// ����������������Ǽ�ʺѾ��֤��ʴ��ơ��֥��INSERT
		$lngResultCount = count($aryResult);

		for ( $i = 0; $i < $lngResultCount; $i++ )
		{
			// �ȥ�󥶥�����󳫻�
			$objDB->transactionBegin();

			// ����μ���
			$lngProductNo = $aryResult[$i]["lngproductno"];
			$lngRevisionNo = $aryResult[$i]["lngrevisionno"];
			$lngRevisionNo++;
			$dtmCreationDate = $aryResult[$i]["dtmcreationdate"];
			$lngGoodsPlanCode = $aryResult[$i]["lnggoodsplancode"];

			if ( $lngGoodsPlanCode )
			{
				$Flag = FALSE;

				// �����Ԥιԥ�٥��å�
				$strQuery = "SELECT lngGoodsPlanCode FROM t_GoodsPlan WHERE lngGoodsPlanCode = " . $lngGoodsPlanCode . " FOR UPDATE";

// echo "SELECT FOR UPDATE Query = " . $strQuery . "<BR>";

				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					$aryMessage[] = "�����ƥ२�顼�������ֹ� " . $lngProductNo . " �ξ��ʴ��ơ��֥�Υ�å������˼��Ԥ��ޤ�����";
					$Flag = TRUE;
				}
				$objDB->freeResult( $lngResultID );

				// �����Ԥ�UPDATE
				$strQuery = "UPDATE t_GoodsPlan SET lngGoodsPlanProgressCode = " . DEF_GOODSPLAN_END . " WHERE lngGoodsPlanCode = " . $lngGoodsPlanCode;

				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					$aryMessage[] = "�����ƥ२�顼�������ֹ� " . $lngProductNo . " �ξ��ʴ��ơ��֥��UPDATE�˼��Ԥ��ޤ�����";
					$Flag = TRUE;
				}
				$objDB->freeResult( $lngResultID );

				if ( !$Flag )
				{
					$aryMessage[] = "�����������ֹ� " . $lngProductNo . " �ξ�������ʥޥ��������ʴ��ơ��֥����Ͽ���������ޤ�����";
				}
			}

		}
		// t_ProcessInformation�Υ������󥹤����
		$sequence_t_processinformation = fncGetSequence( 't_processinformation.lngprocessinformationcode', $objDB );

		// ����������Ǽ
		$strQuery = "INSERT INTO t_ProcessInformation ( lngProcessInformationCode, lngFunctionCode, "
				. "dtmInsertDate, lngInputUserCode, dtmStartDate, dtmEndDate ) "
				. " VALUES ( " . $sequence_t_processinformation . ", "
				. DEF_FUNCTION_SYS5 . ", "
				. "now(), "
				. DEF_BATCH_USERCODE . ", "
				. "'" . $last_month_01 . "', "
				. "'" . $last_month_lastday . "' ) ";

		if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
		{
			$aryMessage[] = "�����ƥ२�顼����������ơ��֥��INSERT�˼��Ԥ��ޤ�����";
			$Flag = TRUE;
		}
		$objDB->freeResult( $lngResultID );

		// ���ߥå�
		$objDB->transactionCommit();
	}
}

$strMessage = "\n\n".implode( "\n", $aryMessage );

$aryData["strMessage"] = $strMessage;

// ��å�������������԰��˥᡼������
list ( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_SYS5, $aryData, $objDB );
$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

mb_language("Japanese");
// �᡼�������
mb_send_mail( ERROR_MAIL_TO, $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
//mail ( "k-suzukaze@wiseknot.co.jp", $strSubject, $strBody, "From: $strFromMail\nReturn-Path: k-suzukaze@wiseknot.co.jp\n" );

$objDB->close();


return TRUE;
?>
