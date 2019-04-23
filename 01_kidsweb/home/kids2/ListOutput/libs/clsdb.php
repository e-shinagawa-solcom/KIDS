<?
	// DB����
	// define ( "POSTGRESQL_HOSTNAME", "192.168.0.160" );
	// define ( "POSTGRESQL_HOSTPORT", "" );
	// define ( "DB_LOGIN_USERNAME", "kuwagata" );
	// define ( "DB_LOGIN_PASSWORD", "kabutomushi" );
	// define ( "DB_NAME", "kids" );

	// ��³Ū����������ͭ���ˤ��뤫��
	// define ( "ENABLE_COUNTINUE_OPEN", FALSE );

/**
*	�ǡ����١����������饹
*
*	isOpen             ��³�椫�ɤ����μ���
*	open               DB����³
*	close              DB��������
*	Execute            SQL�μ¹�
*	transactionBegin   �ȥ�󥶥�����󳫻�(BEGIN)
*	transactionCommit  �ȥ�󥶥������λ(COMMIT)
*	FetchArray         �������λ���Ԥ�Ϣ������˼�������
*	FreeResult         ���ID�ʷ�̡ˤ��������
*	getFieldsCount     ��̤Υե�����ɿ����������
*	getFieldName       ��̤Υե������̾���������
*	fetchObject        �������λ���Ԥ򥪥֥������ȤǼ�������
*	replaceLineFeed    �о�ʸ����β��ԥ����ɤ�<LF>�����줹��
*
*	@package k.i.d.s.
*	@license http://www./
*	@copyright Copyright &copy; 2003, kids
*	@author a a <a@a>
*	@access public
*	@version   1.00
*
*	��������
*	2004.04.08	Query�¹Ի��˥��顼���ä����˥��顼�᡼�����������褦���ѹ�
*
*/
class clsDB
{
	/**
	*	��³ID
	*	@var string
	*/
	var $ConnectID;
	/**
	*	�ȥ�󥶥������ե饰(TRUE:SQL���顼��ROLLBACK��¹�)
	*	@var boolean
	*/
	var $Transaction;

	// ---------------------------------------------------------------
	/**
	*	���󥹥ȥ饯��
	*	���饹��ν������Ԥ�
	*	
	*	@return void
	*	@access public
	*/
	// ---------------------------------------------------------------
	function __construct()
	{
		// ��³ID�ν����
		$this->ConnectID   = FALSE;

		// �ȥ�󥶥������ե饰�ν����
		$this->Transaction = FALSE;

	}

	// ---------------------------------------------------------------
	/**
	*	��³�椫�ɤ����μ���
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function isOpen()
	{
		if ( $this->ConnectID == FALSE )
		{
			$res = FALSE;
		}
		else
		{
			$res = TRUE;
		}
		return $res;
	}

	// ---------------------------------------------------------------
	/**
	*	DB����³
	*	@param  string  $strUserID  �桼����ID
	*	@param  string  $strPasswd  �ѥ����
	*	@param  string  $strDBName  �ǡ����١���̾
	*	@param  string  $strOptions �ǡ����١�����³���ץ����ѥ�᡼��
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function open( $strUserID, $strPasswd, $strDBName, $strOptions )
	{
		$lngConID = 0;

		// ���꤬�ʤ���Хǥե���Ȥ�����
		if ( trim($strOptions["POSTGRESQL_HOSTNAME"]) == "" )
		{
			$strOptions["POSTGRESQL_HOSTNAME"] = POSTGRESQL_HOSTNAME;
		}
		if ( trim($strOptions["POSTGRESQL_HOSTPORT"]) == "" )
		{
			$strOptions["POSTGRESQL_HOSTPORT"] = POSTGRESQL_HOSTPORT;
		}
		if ( $strUserID == "" )
		{
			$strUserID = DB_LOGIN_USERNAME;
		}
		if ( $strPasswd == "" )
		{
			$strPasswd = DB_LOGIN_PASSWORD;
		}
		if ( $strDBName == "" )
		{
			$strDBName = DB_NAME;
		}

		// ��³����
		$strConnectionConfig = "";
		if ( $strOptions["POSTGRESQL_HOSTNAME"] )
		{
			$strConnectionConfig .= "host=" . $strOptions["POSTGRESQL_HOSTNAME"];
		}
		if ( $strOptions["POSTGRESQL_HOSTPORT"] )
		{
			$strConnectionConfig .= " port=" . $strOptions["POSTGRESQL_HOSTPORT"];
		}
		if ( $strUserID )
		{
			$strConnectionConfig .= " user=" . $strUserID;
		}
		if ( $strPasswd )
		{
			$strConnectionConfig .= " password=" . $strPasswd;
		}
		if ( $strDBName )
		{
			$strConnectionConfig .= " dbname=" . $strDBName;
		}

		// ��³��³��Ƚ��
		//if ( ENABLE_COUNTINUE_OPEN )
		//{
		//	$lngConID = pg_pconnect( "$strConnectionConfig" );
		//}
		//else
		//{
			$lngConID = pg_connect( "$strConnectionConfig" );
		//}

		// ���ｪλ�ʤ���³ID�����
		if ( $lngConID != FALSE )
		{
			$this->ConnectID = $lngConID;
			$ret = TRUE;
		}
		else
		{
			$ret = FALSE;
		}

		return $ret;
	}

	// ---------------------------------------------------------------
	/**
	*	DB��������
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function close()
	{
		// ��³�����å�
		if ( !$this->isOpen() )
		{
			$res = FALSE;
		}
		else
		{
			pg_close( $this->ConnectID );
			$this->ConnectID = FALSE;
			$res = TRUE;
		}
		return $res;
	}

	// ---------------------------------------------------------------
	/**
	*	SQL�μ¹�
	*	@param  string  $strQuery  �¹Ԥ���SQLʸ
	*	@return long    ��̥Хåե�ID
	*	        boolean FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function Execute( $strQuery )
	{
		// ��³�����å�
		if ( !$this->isOpen() ){
			return FALSE;
		}

		$lngResultID = FALSE;
		// OutputDebugString($SQL . "<br>\n");

		// 2001/10/16 Added by saito
		// ���ԥ����ɤ����졢�������Ƚ���
		$strQuery = $this->replaceLineFeed( $strQuery );
		//$strQuery    = fncGetSafeSQLString($strQuery);

		// ������¹�
		if ( !$lngResultID = pg_query( $this->ConnectID, $strQuery ) )
		{
			// SQL���顼
			// �ȥ�󥶥������ե饰�γ�ǧ
			if ( $this->Trasaction )
			{
				if ( !pg_query( $this->ConnectID, "ROLLBACK" ) )
				{
					return FALSE;
				}
				$this->Trasaction = FALSE;
			}

// 2004.04.08 suzukaze update start
			// timestamp for the error entry
			$dt = date ( "Y-m-d H:i:s (T)" );

			$strMailMessage = "DATE $dt\n";
			$strMailMessage .= "Query -->\n" . $strQuery . "\n";

			mb_send_mail( ERROR_MAIL_TO,"K.I.D.S. Error Message from " . TOP_URL, $strMailMessage, "From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
// 2004.04.08 suzukaze update start

			return FALSE;
		}

		// echo "SQL:$SQL<br>\n";
		return $lngResultID;
	}

	// ---------------------------------------------------------------
	/**
	*	�ȥ�󥶥�����󳫻�(BEGIN)
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function transactionBegin()
	{
		// ��³�����å�
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// �ȥ�󥶥�����󥹥�����
		if ( !$this->Execute( "BEGIN" ) )
		{
			return FALSE;
		}
		$this->Trasaction = TRUE;

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	�ȥ�󥶥������λ(COMMIT)
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function transactionCommit()
	{
		// ��³�����å�
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// �ȥ�󥶥�����󥳥ߥå�
		if ( !$this->Execute( "COMMIT" ) )
		{
			return FALSE;
		}
		$this->Trasaction = FALSE;

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	�������λ���Ԥ�Ϣ������˼�������
	*	@param  string  $lngResultID ���ID
	*	@param  long    $lngResLine  ���������̹�
	*	@return array   �����ǡ�������
	*	@access public
	*/
	// ---------------------------------------------------------------
	function FetchArray( $lngResultID, $lngResLine )
	{
		// ��³�����å�
		if ( !$this->isOpen() ) {
			// OutputDebugString("CDatabase::fetchArray::isOpen Failed");
			return FALSE;
		}

		// ���ID�����å�
		if ( $lngResultID == 0 ) {
			// OutputDebugString("CDatabase::fetchArray::ResId Failed");
			return FALSE;
		}

		return pg_Fetch_Array( $lngResultID, $lngResLine );
	}


	//	�ؿ�̾��		SafeFetch
	//
	//	���ס�		�������μ��ιԤ�Ϣ������˼�������ʰ����ǡ�
	//
	//	������
	//				$ResId		���ID
	//				$ResArray	�ǡ����������������
	//				$ResLine	���������̹�(PostgreSQL�Τ�ͭ��)
	//
	//	����͡�
	//				TRUE��	���ιԤ⤢��
	//				FALSE:	���Υǡ������ǽ���
	//
	function SafeFetch($ResId, &$ResArray, $ResLine)
	{

		if($ResLine < pg_NumRows($ResId))
		{
			$ret = TRUE;
			$ResArray = pg_fetch_array($ResId, $ResLine);
		}
		else
		{
			$ret = FALSE;
		}
		if( is_array($ResArray) )
		{
			reset($ResArray);
		}
		return $ret;
	}
	

	// ---------------------------------------------------------------
	/**
	*	�������Υ����̾������˼�������
	*	@param  string  $lngResultID ���ID
	*	@param  long    $lngFieldNum �ե�����ɿ�
	*	@return array   $aryColumns  �����ǡ�������
	*	@access public
	*/
	// ---------------------------------------------------------------
	function fncColumnsArray( $lngResultID, $lngFieldNum )
	{
		// ��³�����å�
		if ( !$this->isOpen() ) {
			// OutputDebugString("CDatabase::fncColumnsArray::isOpen Failed");
			return FALSE;
		}

		// ���ID�����å�
		if ( $lngResultID == 0 ) {
			// OutputDebugString("CDatabase::fncColumnsArray::ResId Failed");
			return FALSE;
		}

		$aryColumns = Array();

		// �ե������̾�����
		for ( $i = 0; $i < $lngFieldNum; $i++ )
		{
			$aryColumns[$i] = pg_field_name ( $lngResultID, $i );
		}

		return $aryColumns;
	}

	// ---------------------------------------------------------------
	/**
	*	���ID�ʷ�̡ˤ��������
	*	@param  string  $lngResultID ���ID
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function FreeResult($lngResultID)
	{
		// ��³�����å�
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// ���ID�����å�
		if ( $lngResultID == 0)
		{
			return FALSE;
		}

		$ret = 0;

		$ret = pg_Free_Result($lngResultID);

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	��̤Υե�����ɿ����������
	*	@param  string  $lngResultID ���ID
	*	@return long �ե�����ɿ�
	*	        boolean FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function getFieldsCount($lngResultID)
	{
		// ��³�����å�
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// ���ID�����å�
		if ( $lngResultID == 0 )
		{
			return FALSE;
		}

		$ret = 0;

		$ret = pg_Num_Fields( $lngResultID );

		return $ret;
	}

	// ---------------------------------------------------------------
	/**
	*	��̤Υե������̾���������
	*	@param  string  $lngResultID ���ID
	*	@param  string  $lngResultID �ե������ID(0����Ϥޤ륤��ǥå���)
	*	@return array   $ret         �ե������̾
	*	        boolean FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function getFieldName( $lngResultID, $lngFieldNum )
	{
		// ��³�����å�
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// ���ID�����å�
		if ( $lngResultID == 0 )
		{
			return FALSE;
		}

		$ret = FALSE;

		$ret = pg_Field_Name( $lngResultID, $lngFieldNum );

		return $ret;
	}

	// ---------------------------------------------------------------
	/**
	*	�������λ���Ԥ򥪥֥������ȤǼ�������
	*	@param  string $lngResultID ���ID
	*	@param  long   $lngResLine  ���������̹�
	*	@return object              �ե������̾
	*	@access public
	*/
	// ---------------------------------------------------------------
	function fetchObject( $lngResultID, $lngResLine )
	{
		// ��³�����å�
		if ( !$this->isOpen() ) {
			// OutputDebugString("CDatabase::fetchArray::isOpen Failed");
			return FALSE;
		}

		// ���ID�����å�
		if ( $lngResultID == 0 ) {
			// OutputDebugString("CDatabase::fetchArray::ResId Failed");
			return FALSE;
		}

		return pg_fetch_object( $lngResultID, $lngResLine );
	}

	// ---------------------------------------------------------------
	/**
	*	�о�ʸ����β��ԥ����ɤ�<LF>�����줹��
	*	@param  string $strTarget �о�ʸ����
	*	@return string $strTarget �����Ѥ�ʸ����
	*	@access public
	*/
	// ---------------------------------------------------------------
	function replaceLineFeed( $strTarget ){
		return preg_replace( '/\x0D\x0A|\x0A|\x0D/', "\x0A", $strTarget );
	}

}



// ---------------------------------------------------------------
//
// clsDBLock ���饹
//
// DB��Ȥä���å������ѥ��饹
//
// ---------------------------------------------------------------
class clsDBLock {
	var $LockKey;         // ��å��˻��Ѥ��륻�å����ID
	var $ServerTimeout;   // �����С�¦��å����ԤΥ����ॢ���ȡ��á�
	var $ClientTimeout;   // ���饤�����¦��å����ԤΥ����ॢ���ȡ��á�
	var $ServerCheckSpan; // �����С�¦��å��ƻ�Դֳ֡ʥߥ��á�

	// ���󥹥ȥ饯��
	function __construct()
	{
		global $SERVER_NAME;
		global $REMOTE_ADDR;
		// ���å����ID������
		$this->LockKey = fncGetSafeSQLString(uniqid($SERVER_NAME . $REMOTE_ADDR . ((string)rand())));
		// �����С�¦�����ॢ���Ȼ��֤�����(20��)
		$this->ServerTimeout = 20;
		// ���饤�����¦�����ॢ���Ȼ��֤�����(5��)
		$this->ClientTimeout = 5;
		// �����С�¦��å��ƻ�Դֳ֡ʥߥ��á�
		$this->ServerCheckSpan = 100;
	}

	// ---------------------------------------------------------------
	// �ؿ�̾: fncLock
	//
	// ����:   ��å�����
	//
	// ����:
	//         &$dbconn   DB��³���֥�������(Open�ѤߤǤ��뤳��)
	//         $locktable ��å��ơ��֥�
	//         $lockid    ��å�ID�ե������
	//         $lockkey   ��å������ե������
	//         $locktime  ��å����֥ե������
	//         $lockidval ��å�ID��
	//
	// �����:
	//         TRUE       ��å���λ
	//         FALSE      ��å�����
	// ---------------------------------------------------------------
	function fncLock(&$dbconn, $locktable, $lockid, $lockkey, $locktime, $lockidval = 1)
	{
		// �����ॢ���Ȼ��֤μ���
		$timeout = time() + $this->ClientTimeout;
		while(1) {
			// �����ॢ���Ȥ�Ƚ��
			if ( $timeout < time()) break;
			// �����Ƥ����顢�⤷���ϥ����ॢ���ȸ�ʤ��å�������񤭹���
			$SQL = "UPDATE $locktable SET ";
			$SQL = $SQL . "$lockkey = '" . $this->LockKey . "', ";
			$SQL = $SQL . "$locktime=(SYSDATE + (" . $this->ServerTimeout . " / 86400)) ";
			$SQL = $SQL . "WHERE ($lockkey IS NULL or $locktime < SYSDATE) and $lockid = $lockidval";
			$resid = $dbconn->Execute($SQL);
			if ( $resid == FALSE) {
				break;
			}
			$dbconn->FreeResult($resid);
			// ��å�����λ��������ǧ����
			$SQL = "SELECT $lockkey as KEYVALUE ";
			$SQL = $SQL . "FROM $locktable ";
			$SQL = $SQL . "WHERE $lockid = $lockidval";
			$resid = $dbconn->Execute($SQL);
			if ( $resid == FALSE) {
				break;
			}
			if ( !$dbconn->FetchArray($resid, $ResCount, 0)) {
				break;
			}
			// ���ID���Ĥ���
			$dbconn->FreeResult($resid);
			if ( $ResCount["KEYVALUE"] == $this->LockKey) return TRUE;
			// ����ä��Ԥ�
			usleep($this->ServerCheckSpan);
		};

		return FALSE;
	}

	// ---------------------------------------------------------------
	// �ؿ�̾: fncUnlock
	//
	// ����:   ��å��������
	//
	// ����:
	//         &$dbconn   DB��³���֥�������(Open�ѤߤǤ��뤳��)
	//         $locktable ��å��ơ��֥�
	//         $lockid    ��å�ID�ե������
	//         $lockkey   ��å������ե������
	//         $lockidval ��å�ID��
	//
	// �����:
	//         TRUE       ��å���λ
	//         FALSE      ��å�����
	// ---------------------------------------------------------------
	function fncUnlock(&$dbconn, $locktable, $lockid, $lockkey, $lockidval = 1)
	{
		// �����ॢ���Ȼ��֤μ���
		$timeout = time() + $this->ClientTimeout;
		do {
			// ��å����������ˤ���
			$SQL = "UPDATE $locktable SET ";
			$SQL = $SQL . "$lockkey = NULL ";
			$SQL = $SQL . "WHERE $lockkey = '" . $this->LockKey . "' and $lockid = $lockidval";
			$resid = $dbconn->Execute($SQL);
			if ( $resid == FALSE) {
				break;
			}
			// ��λ
			return TRUE;
		} while(0);

		return FALSE;
	}


}

	// ---------------------------------------------------------------
	// �ؿ�̾: fncGetSafeSQLString
	//
	// ����:   SQLʸ�ǻ��ѤǤ���ʸ������Ѵ�
	//
	// ����:
	//         $strVal ʸ����
	//
	// �����:
	//         �Ѵ����ʸ����
	// ---------------------------------------------------------------
	function fncGetSafeSQLString( $strVal )
	{
		return pg_escape_string( $strVal );
	}
?>
