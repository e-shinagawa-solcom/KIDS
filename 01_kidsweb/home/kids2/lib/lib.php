<?php
// ----------------------------------------------------------------------------
/**
*       �ؿ��饤�֥��
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
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// ���饹���ɤ߹���
require ( CLS_DB_FILE );
require ( CLS_AUTH_FILE );
require ( CLS_TEMPLATE_FILE );

// ��𥨥顼����
error_reporting ( E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING );
// ���顼�ؿ������
set_error_handler ( "fncError" );

$strBaseTemplate = "base.tmpl";
$aryConfigName = array ( "bodyonload", "header1", "header2", "header3" );



// -----------------------------------------------------------------
/**
*	�ѿ������å��ؿ�
*
*	�����¸ʸ����ʸ��������å�
*	��:����      Boolean = fncCheckString( "2003/01/01", "null:date" )
*	��:����0�ʾ� Boolean = fncCheckString( 1920, "null:number(0,)" )
*
*	@param  String  $str          �Ѵ��оݤȤʤ�ǡ���
*	@param  String  $strCheckMode �����å��⡼��[(����)]
*	                              number(min,max)    : ����
*	                              english(minlength,maxlength)    : �ѻ�
*	                              numenglish(minlength,maxlength) : �ѿ���
*	                              ascii(minlength,maxlength)      : �ѿ�������
*	                              ID(minlength,maxlength)         : ID
*	                              password(minlength,maxlength)   : �ѥ����
*	                              email(minlength,maxlength)      : �᡼��
*	                              date(string)                    : YYYY/MM/DD
*	                              file(minlength,maxlength)       : �ե�����
*	                              length(minlength,maxlength)     : ʸ����
*	                              money(min,max)                  : ���
*	                              IP(min,max,plural,asterisk)     : IP���ɥ쥹
*	                              color                           : ��
*	@return String                ���顼����
*	        Boolean               FALSE ���顼̵��
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckString( $str, $strCheckMode )
{
	// Ⱦ�ѥ��� -> ���ѥ���
	//$str = mb_convert_kana ( $str, "K" );

	// ����ζ������
	//$str = mb_ereg_replace ( "^[��\s]+", "", $str );
	//$str = mb_ereg_replace ( "[��\s]+$", "", $str );

	// ������������å�( ���̥����å����� )
	//if ( mb_ereg ( "[!\"\$&\'()*<>?\[\]\\\\]", $str ) ) {
	//	errorExit ( "�����������Ѥ��Ƥ��ޤ�������Τ���ʸ���� \"$str\"" );
	//}

	$aryCheck = explode ( ":", $strCheckMode );
	foreach ( $aryCheck as $strCheckType )
	{
		$lngRange[1] = "";
		$lngRange[2] = "";
		// ɬ�ܥ����å�
//		if ( $strCheckType == "null" && $str == "" && $str != 0 )
		if ( $strCheckType == "null" && ( $str === "" || !isset ( $str ) ) )
		{
			return "9001:$str";
		}

/*
		// ����ʸ���������¸ʸ�������å�( ���̥����å����� )
		if ( mb_ereg ( "(ad[a1-fc]|[00-1f])", bin2hex ( $str ) ) )
		{
			for ( $i = 0; $i < mb_strlen ( $str, "EUC" ); $i++ )
			{
				$str = preg_replace ( "/[\r\n]/", "", $str );
				$dec = hexdec ( bin2hex ( mb_substr ( $str, $i, 1, "EUC" ) ) );
//				if ( ( $dec > 0 && $dec < 32 ) || ( $dec > 44448 && $dec < 44540 ) )
				//if ( ( $dec > 0 && $dec < 32 ) || ( $dec > 44478 && $dec < 44522 ) || ( $dec > 44524 && $dec < 45217 ) )
				if ( $dec > 0 && $dec < 32 )
				{
					return "9002:$str";
				}
			}
		}

		// ���������å�
		if ( ereg ( "^number", $strCheckType ) && $str != "" )
		{
			// ���ڡ�������
			$str = mb_ereg_replace ( "\s", "", $str );

			// ����Ǿ�����μ���
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*)\)/", $strCheckType , $lngRange );

			// ���ͥ����å�
			if ( !ereg ( "^-?[0-9]*\.?[0-9]+$", $str ) || ereg ( "^(\.|-\.)", $str ) ) {
				return "9003:$str";
			}

			// �Ǿ��ͥ����å�
			if ( $lngRange[1] != "" && $str < $lngRange[1] ) {
				return "9004:$str";
			}

			// �����ͥ����å�
			if ( $lngRange[2] != "" && $str > $lngRange[2] ) {
				return "9005:$str";
			}

		}
*/

		// ���������å�(���顼��å����������ǽ�����å��ƥ��ȱ���)
		if ( preg_match ( "/^number/", $strCheckType ) && $str != "" )
		{
			// ���ڡ�������
			$str = mb_ereg_replace ( "[\s,]", "", $str );

			// ����Ǿ����ꡢ���顼��å������μ���
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*),?(.*?)?\)/", $strCheckType , $lngRange );

			// ��3���������ä���硢����򥨥顼��å������Ȥ���
			if ( $lngRange[3] )
			{
				$lngRange[3] = "ORIGINAL" . $lngRange[3];
			}
			else
			{
				$lngRange[3] = $str;
			}

			// ���ͥ����å�
			if ( !preg_match ( "/^-?[0-9]*\.?[0-9]+$/", $str ) || preg_match ( "/^(\.|-\.)/", $str ) ) {
				return "9003:$lngRange[3]";
			}

			// ���ͷ��Ȥ���
			settype ( $str, "float" );

			// �Ǿ��ͥ����å�
			if ( $lngRange[1] != "" && $str < $lngRange[1] ) {
				return "9004:$lngRange[3]";
			}

			// �����ͥ����å�
			if ( $lngRange[2] != "" && $str > $lngRange[2] ) {
				return "9005:$lngRange[3]";
			}

		}
		// �ѻ������å�
		elseif ( preg_match ( "/^english/", $strCheckType ) && $str != "" )
		{
			// ���ڡ�������
			$str = mb_ereg_replace ( "\s", "", $str );

			// ʸ��������å�
			if ( !mb_ereg ( "^[a-zA-Z]+$", $str ) )
			{
				return "9008:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// �ѿ��������å�
		elseif ( preg_match ( "/^numenglish/", $strCheckType ) && $str != "" )
		{
			// ���ڡ�������
			$str = mb_ereg_replace ( "\s", "", $str );

			// ʸ��������å�
			if ( !mb_ereg ( "^[a-zA-Z0-9]+$", $str ) )
			{
				return "9009:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// �ѿ�����������å�
		elseif ( preg_match ( "/^ascii/", $strCheckType ) && $str != "" )
		{
			// ���ڡ�������
			$str = mb_ereg_replace ( "\s", "", $str );
			if ( !mb_ereg ( "^[0-9a-zA-Z\"#%&\+-\/=^_`\{\}\|~@\.:]+$", $str ) ) {
				return "9010:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// ID�����å�
		elseif ( preg_match ( "/^ID/", $strCheckType ) && $str != "" )
		{
			if ( !mb_ereg ( "^[0-9a-zA-Z\"#%\+-\/=^_`\{\}\|~@\.]+$", $str ) || strlen ( $str ) < 3 || strlen ( $str ) > 64 ) {
				return "9011:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// �ѥ���ɥ����å�
		elseif ( preg_match ( "/^password/", $strCheckType ) && $str != "" )
		{
			if ( !mb_ereg ( "^[0-9a-zA-Z]+$", $str ) || strlen ( $str ) > 64 ) {
				return "9012:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// �᡼�륢�ɥ쥹�����å�
		elseif ( preg_match ( "/^e?mail/", $strCheckType ) && $str != "" )
		{
			if ( !mb_ereg ( "^[0-9a-zA-Z!\"#\$%&\'\(\)\=\~\|\`\{\+\*\}\<\>\?\_\-\^\@\[\;\:\]\,\.\/\\\\]+$", $str ) || !mb_ereg ( "^[^@.\-][^@]*@[^@.\-][^@]*\..+[a-z]$", $str ) ) {
				return "9013:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// ���ե����å�
		if ( preg_match ( "/^date/", $strCheckType ) && $str != "" )
		{
			if ( !preg_match ( "/^[0-9\-\/]+$/", $str ) )
			{
				return "9014:$str";
			}

			list ( $year, $mon, $date ) = explode ( "[-\/]", $str );

			// ����̤�����ʾ�硢1���˶�������
			if ( !$date )
			{
				$date = 1;
			}

			// �̤�����ʾ�硢1��˶�������
			if ( !$mon )
			{
				$mon = 1;
			}

			// ���ե����å�
			if ( !checkdate ( $mon, $date, $year) || $year < 1601 ) {
				return "9014:$str";
			}
		}
		// �ե�����
		elseif ( preg_match ( "/^file/", $strCheckType ) && $str != "" )
		{
			if ( mb_ereg ( "^\.\.", $str ) || !mb_ereg ( "^[0-9a-zA-Z\"#%\+-\/=^_`\{\}\|~@\.:]+$", $str ) ) {
				return "9016:$str";
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// ʸ���������å�
		elseif ( preg_match ( "/^length/", $strCheckType ) && $str != "" )
		{
			// ʸ���������å�
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// ��ۥ����å�
		elseif ( preg_match ( "/^money/", $strCheckType ) && $str != "" )
		{
			// ���ڡ����ȥ���ޡ�\��$ ����
			$str = mb_ereg_replace ( "[\s,]", "", $str );
			$str = mb_ereg_replace ( "^[\\\\$]", "", $str );

			// ����Ǿ�����μ���
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*)\)/", $strCheckType , $lngRange );

			// ���ͥ����å�
			if ( !preg_match ( "/^-?[0-9]*\.?[0-9]+$/", $str ) || preg_match ( "/^(\.|-\.)/", $str ) ) {
				return "9017:$str";
			}

			// �Ǿ��ͥ����å�
			if ( $lngRange[1] != "" && $str < $lngRange[1] ) {
				return "9018:$str";
			}

			// �����ͥ����å�
			if ( $lngRange[2] != "" && $str > $lngRange[2] ) {
				return "9019:$str";
			}

		}
		// IP���ɥ쥹�����å�
		elseif ( preg_match ( "/^IP/", $strCheckType ) && $str != "" )
		{
			// ���ڡ�������
			$str = mb_ereg_replace ( " ", "", $str );

			// ����Ǿ���ʣ��������ġ��������ꥹ�����Ļ���μ���
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*),(\'.?\')/", $strCheckType , $lngRange );
			$strCheckType = "length($lngRange[1],$lngRange[2])";

			// ʣ���������Ĥ��Ƥ����硢IP���ɥ쥹��ʬ�򤹤�
			if ( $lngRange[3] != "''" )
			{
				$lngRange[3] = str_replace ( "'", "", $lngRange[3] );
				$aryStr = explode ( $lngRange[3], $str );
			}
			else
			{
				$aryStr[0] = $str;
			}

			// �����å�
			$count = count ( $aryStr );
			for ( $i = 0; $i < $count; $i++ )
			{
				if ( $aryStr[$i] && !preg_match ( "/^[0-9\*\.]+$/", $aryStr[$i] ) || preg_match ( "/(\.\.|\*\*)/", $aryStr[$i] ) )
				{
					return "9017:$str";
				}
			}

			// ʸ���������å�
			$strError = fncCheckStringLength( "($lngRange[1],$lngRange[2])", $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// ����������å�
		elseif ( $strCheckType == "color" && $str != "" )
		{
			if ( !preg_match ( "/^#[0-9a-fA-F]{6}$/", $str ) )
			{
				return "9010:$str";
			}
		}
	}
	return FALSE;
}



// -----------------------------------------------------------------
/**
*	Ϣ��������Ϥ��줿�ǡ����򤹤٤ƥ����å�����ؿ�
*
*	�ѿ��Υ����å���¹Ԥ���
*
*	@param  Array $aryData   �����å��оݥǡ���(�ѿ�̾�򥭡��Ȥ���Ϣ������)
*	@param  Array $aryCheck  �����å�����(�ѿ�̾�򥭡��Ȥ���Ϣ������)
*	@return Array $aryResult �����å�����(�ѿ�̾�򥭡��Ȥ���Ϣ������)
*	@access public
*/
// -----------------------------------------------------------------
function fncAllCheck( $aryData, $aryCheck )
{
	// �ѿ�̾�Ȥʤ륭�������
	$aryKey = array_keys( $aryCheck );

	// �����ο����������å�
	foreach ( $aryKey as $strKey )
	{
		// $aryData[$strKey]  : �����å��оݥǡ���
		// $aryCheck[$strKey] : �����å�����(���͡��ѿ���������������)
		$aryResult[$strKey . "_Error"] = fncCheckString( $aryData[$strKey], $aryCheck[$strKey] );
	}
	return $aryResult;
}



// -----------------------------------------------------------------
/**
*	ʸ����Ĺ�����å��ؿ�
*
*	ʸ�����Ĺ��������å�����
*
*	@param  String $strCheckType �����å�����
*	@param  Atring $str          �����å�ʸ����
*	@return Boolean
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckStringLength( $strCheckType, $str )
{
	// ʸ�����μ���
	if ( !preg_match ( "/\(([0-9]*),([0-9]*)\)/", $strCheckType , $lngRange ) )
	{
		return FALSE;
	}

	// ʸ���������å�
	if ( $lngRange[1] != "" && mb_strlen ( $str ) < $lngRange[1] )
	{
		return "9006:$str";
	}
	if ( $lngRange[2] != "" && mb_strlen ( $str ) > $lngRange[2] )
	{
		return "9007:$str";
	}
	return FALSE;
}



// -----------------------------------------------------------------
/**
*	ʸ��������å����顼���ϴؿ�
*
*	Ϣ��������Ϥ��줿�ǡ����򤹤٤ƥ����å�����ؿ��Ǽ���������̤���
*	ʸ��������å����顼����Ϥ���
*
*	@param  Array  $aryResult ʸ��������å����顼���
*	@param  Object $objDB     DB���֥�������
*	@return Boolean
*	@access public
*/
// -----------------------------------------------------------------
function fncPutStringCheckError( $aryResult, $objDB )
{
	$flag = TRUE;
	$aryKeys = array_keys ( $aryResult );
	foreach ( $aryKeys as $strKey )
	{
		if ( $aryResult[$strKey] )
		{
			list ( $lngErrorNo, $strErrorMessage ) = explode ( ":", $aryResult[$strKey] );
			fncOutputError ( $lngErrorNo, DEF_ERROR, $strErrorMessage, TRUE, "", $objDB );
//			echo $strErrorMessage . "<BR>";
			$flag = FALSE;
			exit;
		}
	}
	return $flag;
}



// -----------------------------------------------------------------
/**
*	������¹Դؿ�
*
*	������¹Ԥ���
*
*	@param  String $strQuery ������
*	@param  Object $objDB    DB���֥�������
*	@return $lngResultID     ���ID
*	        $lngResultNum    �Կ�
*	@access public
*/
// -----------------------------------------------------------------
function fncQuery( $strQuery, $objDB )
{
	$strQuery = html_entity_decode ( $strQuery, ENT_QUOTES );
	
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
//		$strErrorMessage = fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
//echo "Query = " . $strQuery . "<BR>";
//echo $strErrorMessage . "<BR>";
	}
	$lngResultNum = pg_num_rows ( $lngResultID );
	return array ( $lngResultID, $lngResultNum );
}



// -----------------------------------------------------------------
/**
*	���̵�ǽ�ޥ����ǡ��������ؿ�
*
*	���̵�ǽ�ޥ���������ꤵ�줿������ͤ��������
*
*	@param  String $strClass ����
*	@param  Object $objDB    DB���֥�������
*	@return $strVAlue        ��
*	@access public
*/
// -----------------------------------------------------------------
function fncGetCommonFunction( $strClass, $strTable, $objDB )
{
	$strQuery = "SELECT strValue " .
	            "FROM $strTable " .
	            "WHERE strClass = '$strClass'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "���̵�ǽ�ޥ���", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strValue  = strtolower ( "strValue" );
	$strValue = $objResult->$strValue;
	$objDB->freeResult( $lngResultID );

	return $strValue;
}



// -----------------------------------------------------------------
/**
*	�����Ե�ǽ�ޥ����ǡ��������ؿ�
*
*	�����Ե�ǽ�ޥ���������ꤵ�줿������ͤ��������
*
*	@param  String $strClass ����
*	@param  Object $objDB    DB���֥�������
*	@return $strVAlue        ��
*	@access public
*/
// -----------------------------------------------------------------
function fncGetAdminFunction( $strClass, $objDB )
{
	$strQuery = "SELECT strValue " .
	            "FROM m_AdminFunction " .
	            "WHERE strClass = '$strClass'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_ERROR, "�����Ե�ǽ�ޥ���", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strValue  = strtolower ( strValue );
	$strValue = $objResult->$strValue;
	$objDB->freeResult( $lngResultID );

	return $strValue;
}



// -----------------------------------------------------------------
/**
*	�������󥹴ؿ�
*
*	���ߤΥ������󥹤μ���(���󥯥���ȡ�UPDATE̵��)
*
*	@param  String $strSequenceName ����
*	@param  Object $objDB           DB���֥�������
*	@return Long   $lngSequence     �ֹ�
*	@access public
*/
// -----------------------------------------------------------------
function fncIsSequence( $strSequenceName, $objDB )
{
	// ���������ֹ����
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE lower ( ltrim( strSequenceName, ' ' ) ) = lower ( '$strSequenceName' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "�������󥹥ơ��֥�", TRUE, "", $objDB );
	}
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	$lngSequence = $objResult->lngsequence;

	$objDB->freeResult( $lngResultID );

	return $lngSequence;
}



// -----------------------------------------------------------------
/**
*	�������󥹴ؿ�
*
*	�������󥹤Υ��󥯥���Ȥ���Ӽ���
*
*	@param  String $strSequenceName ����
*	@param  Object $objDB           DB���֥�������
*	@return Long   $lngSequence     �ֹ�
*	@access public
*/
// -----------------------------------------------------------------
function fncGetSequence( $strSequenceName, $objDB )
{

	// �ȥ�󥶥�����󳫻�
	//$objDB->transactionBegin();

	// ��å�����
	//$strQuery = "LOCK TABLE t_Sequence IN EXCLUSIVE MODE";
	//list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ���������ֹ����
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE lower ( ltrim( strSequenceName, ' ' ) ) = lower ( '$strSequenceName' ) FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName' FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE strSequenceName = '$strSequenceName'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "�������󥹥ơ��֥�", TRUE, "", $objDB );
	}
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	// ���󥯥����
	$lngSequence = $objResult->lngsequence + 1;

	$objDB->freeResult( $lngResultID );

	$strQuery = "UPDATE t_Sequence SET lngSequence = $lngSequence WHERE lower ( ltrim( strSequenceName, ' ' ) ) = lower ( '$strSequenceName' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	//$objDB->transactionCommit();

	return $lngSequence;
}



// -----------------------------------------------------------------
/**
*	���ե������󥹴ؿ�
*
*	���ե������󥹤Υ��󥯥���Ȥ���Ӽ���
*
*	@param  String $year            ǯ
*	@param  String $month           ��
*	@param  String $strSequenceName ����
*	@param  Object $objDB           DB���֥�������
*	@return Long   $lngSequence     �ֹ�(9999Ķ����FALSE)
*	@access public
*/
// -----------------------------------------------------------------
function fncGetDateSequence( $year, $month, $strSequenceName, $objDB )
{
	// ǯ������(1000ǯ�ʾ���ä��鲼2�������)
	if ( $year > 999 )
	{
		$year %= 100;
	}
	// ǯ�������å�
	if ( $year < 0 || $year > 99 || $month > 12 || $month < 1 )
	{
		fncOutputError ( 9051, DEF_ERROR, "���ե������󥹤�ǯ����������꤬����ޤ���", TRUE, "", $objDB );
	}

	// ��������̾������(YYMMXXX)
	$strSequenceName = sprintf( "$strSequenceName.%02d%02d", $year, $month );

	// �ȥ�󥶥�����󳫻�
	//$objDB->transactionBegin();

	// ��å�����
	//$strQuery = "LOCK TABLE t_Sequence IN EXCLUSIVE MODE";
	//list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ���������ֹ����
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = lower('$strSequenceName') FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// �쥳���ɤ��ʤ���л��ꥷ������̾�ˤƥ쥳�����ɲ�
	if ( !$lngResultNum )
	{
		$strQuery = "INSERT INTO t_Sequence VALUES ( lower('$strSequenceName'), 1 )";

		if ( !$objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "�������󥹥ơ��֥�", TRUE, "", $objDB );
		}

		$lngSequence = 1;
	}

	// �쥳���ɤ�����л��ꥷ�����󥹤򥤥󥯥����
	else
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// ���󥯥����
		$lngSequence = $objResult->lngsequence + 1;
		if ( $lngSequence > 9999 )
		{
			return FALSE;
		}

		$objDB->freeResult( $lngResultID );
		$strQuery = "UPDATE t_Sequence SET lngSequence = $lngSequence WHERE ltrim( strSequenceName, ' ' ) = lower('$strSequenceName')";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	}

	//$objDB->transactionCommit();

	return sprintf ( "%02d%02d%04d", $year, $month, $lngSequence );
}



// -----------------------------------------------------------------
/**
*	�ⷿ�ֹ�����ؿ�
*
*	�ⷿ�ֹ�('���ʥ�����'-dd)�Υ��󥯥���Ȥ���Ӽ���
*
*	@param  String $strProductCode      ���ʥ�����
*	@param  String $lngStockSubjectCode �������ܥ�����
*	@param  String $lngStockItemCode    �������ʥ�����
*	@param  Object $objDB               DB���֥�������
*	@return Long   $strNoldNo           �ⷿ�ֹ�('���ʥ�����'-dd)
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMoldNo( $strProductCode, $lngStockSubjectCode, $lngStockItemCode, $objDB )
{
// 2004.05.31 suzukaze update start
	// �������ܥ����� != 433 �ޤ��� �������ʥ����� != 1
	// �������ܥ����� != 431 �ޤ��� �������ʥ����� != 8
	// �ޤ��� ���ʥ����� > 99999 �ξ�硢return FALSE
	$bytFlag = 0;
	if ( $lngStockSubjectCode == 433 and $lngStockItemCode == 1 )
	{
		$bytFlag = 1;
	}
	else if ( $lngStockSubjectCode == 431 and $lngStockItemCode == 8 )
	{
		$bytFlag = 1;
	}

	if ( $strProductCode > 99999 )
	{
		$bytFlag = 0;
	}

	if ( $bytFlag == 0 )
	{
		return FALSE;
	}
// 2004.05.31 suzukaze update end

	// ��������̾������(YYMMXXX)
	$strSequenceName = sprintf( "m_OrderDetail.strMoldNo.%05d", $strProductCode );
	// �ȥ�󥶥�����󳫻�
	//$objDB->transactionBegin();

	// ��å�����
	//$strQuery = "LOCK TABLE t_Sequence IN EXCLUSIVE MODE";
	//list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ���������ֹ����
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName' FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// �쥳���ɤ��ʤ���л��ꥷ������̾�ˤƥ쥳�����ɲ�
	if ( !$lngResultNum )
	{
		$strQuery = "INSERT INTO t_Sequence VALUES ( '$strSequenceName', 1 )";

		if ( !$objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "�������󥹥ơ��֥�", TRUE, "", $objDB );
		}

		$lngSequence = 1;
	}

	// �쥳���ɤ�����л��ꥷ�����󥹤򥤥󥯥����
	else
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// ���󥯥����
		$lngSequence = $objResult->lngsequence + 1;

		// 195(FF)��Ķ�����饨�顼
		if ( $lngSequence > 195 )
		{
			return FALSE;
		}

		$objDB->freeResult( $lngResultID );
		$strQuery = "UPDATE t_Sequence SET lngSequence = $lngSequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	}

	//$objDB->transactionCommit();

	// 100��Ķ���Ƥ�����硢16���Ѵ�(100='a0'�Ȥ���)
	if ( $lngSequence > 99 )
	{
		$lngSequence = sprintf ( "%05d", $strProductCode ) . "-" . dechex ( $lngSequence + 60 );
	}

	// 100̤���ξ�硢0��2��ե����ޥå�
	else
	{
		$lngSequence = sprintf ( "%05d-%02d", $strProductCode, $lngSequence );
	}

	return $lngSequence;
}



////////////////////////////////////////////////////////////////////
// HTML���ϴؿ�
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	�ޥ��������ץ������ꥹ�������ؿ�
*
*	�ޥ������ơ��֥뤫��ץ�������˥塼����
*
*	@param  String $strTable            �ơ��֥�̾
*	@param  String $strValueFieldName   value������ե������̾
*	@param  String $strDisplayFieldName ɽ�������ե������̾
*	@param  Long $lngDefaultValue       �ץ�������˥塼�ν��������
*	@param  String $strQueryWhere       ���(SQL)WHERE����񤭻Ϥ�
*	@param  Object $objDB               DB���֥�������
*	@return $strHtml                    �ץ�������˥塼HTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetPulldown( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
{
	// ���ڡ���ID�Υꥹ�Ȥ����
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";

	return fncGetPulldownQueryExec($strQuery, $lngDefaultValue, $objDB);
}
// -----------------------------------------------------------------
/**
*	�ޥ��������ץ������ꥹ�������ؿ��ʥ����ȥ��������ǡ�
*
*	�ޥ������ơ��֥뤫��ץ�������˥塼����
*
*	@param  String $strTable            �ơ��֥�̾
*	@param  String $strValueFieldName   value������ե������̾
*	@param  String $strDisplayFieldName ɽ�������ե������̾
*	@param  Long $lngDefaultValue       �ץ�������˥塼�ν��������
*	@param  String $strQueryWhere       ���(SQL)WHERE����񤭻Ϥ�
*	@param	Long   $lngSortKey			�����ȥ���
*	@param  Object $objDB               DB���֥�������
*	@return $strHtml                    �ץ�������˥塼HTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetPulldownSort( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $lngSortKey, $objDB )
{
	// ���ڡ���ID�Υꥹ�Ȥ����
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $lngSortKey";

	return fncGetPulldownQueryExec($strQuery, $lngDefaultValue, $objDB);
}


// -----------------------------------------------------------------
/**
*	�ޥ��������ץ������ꥹ�������ؿ��ʽ����
*
*	@lngMaxFieldsCount �����κ���������
*/
// -----------------------------------------------------------------
function fncGetPulldownQueryExec($strQuery, $lngDefaultValue, $objDB, $lngMaxFieldsCount=false)
{
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );

	if($lngMaxFieldsCount)
	{
		if($lngFieldsCount > $lngMaxFieldsCount) $lngFieldsCount = $lngMaxFieldsCount;
	}

	// <OPTION>����
	for ( $count = 0; $count < $lngResultNum; $count++ ) {
		$aryResult = $objDB->fetchArray( $lngResultID, $count );

		$strDisplayValue = "";
		for ( $i = 1; $i < $lngFieldsCount; $i++ )
		{
			$strDisplayValue .= fncHTMLSpecialChars( $aryResult[$i] ) . "\t";
		}

		// HTML����
		if ( $lngDefaultValue == $aryResult[0] )
		{
			$strHtml .= "<OPTION VALUE=\"$aryResult[0]\" SELECTED>$strDisplayValue</OPTION>\n";
		}
		else
		{
			$strHtml .= "<OPTION VALUE=\"$aryResult[0]\">$strDisplayValue</OPTION>\n";
		}
	}

	$objDB->freeResult( $lngResultID );
	return $strHtml;
}

// 2004.04.14 suzukaze update start
// -----------------------------------------------------------------
/**
*	�ޥ��������ޥ���ץ�ꥹ�������ؿ�
*
*	�ޥ������ơ��֥뤫��ޥ���ץ�ꥹ�ȥ�˥塼����
*
*	@param  String $strTable            �ơ��֥�̾
*	@param  String $strValueFieldName   value������ե������̾
*	@param  String $strDisplayFieldName ɽ�������ե������̾
*	@param  Long $lngDefaultValue       �ץ�������˥塼�ν��������
*	@param  String $strQueryWhere       ���(SQL)WHERE����񤭻Ϥ�
*	@param  Object $objDB               DB���֥�������
*	@return $strHtml                    �ץ�������˥塼HTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMultiplePulldown( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
{
	// ���ڡ���ID�Υꥹ�Ȥ����
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	// <OPTION>����
	for ( $count = 0; $count < $lngResultNum; $count++ ) {
		$aryResult = $objDB->fetchArray( $lngResultID, $count );

		$strDisplayValue = "";
		for ( $i = 1; $i < $lngFieldsCount; $i++ )
		{
			$strDisplayValue .= fncHTMLSpecialChars( $aryResult[$i] ) . "\t";
		}

		// HTML����
		$strHtml = "<OPTION VALUE=\"$aryResult[0]\">$strDisplayValue</OPTION>\n";
	}

	$objDB->freeResult( $lngResultID );
	return $strHtml;
}
// 2004.04.14 suzukaze update end


// -----------------------------------------------------------------
/**
*	�ޥ������������å��ܥå��������ؿ�
*
*	�ޥ������ơ��֥뤫��ޥ���ץ�ꥹ�ȥ�˥塼����
*
*	@param  String	$strTable            �ơ��֥�̾
*	@param  String	$strValueFieldName   value������ե������̾
*	@param  String	$strDisplayFieldName ɽ�������ե������̾
*	@param  Long	$strObjectName       �����å��ܥå������֥������Ȥ�̾��
*	@param  String	$strQueryWhere       ���(SQL)WHERE����񤭻Ϥ�
*	@param  Object	$objDB               DB���֥�������
*	@return $strHtml                    �ץ�������˥塼HTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetCheckBoxObject( $strTable, $strValueFieldName, $strDisplayFieldName, $strObjectName, $strQueryWhere, $objDB )
{
	// ���ڡ���ID�Υꥹ�Ȥ����
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	// <OPTION>����
	for ( $count = 0; $count < $lngResultNum; $count++ ) {
		$aryResult = $objDB->fetchArray( $lngResultID, $count );

		$strDisplayValue = "";
		for ( $i = 1; $i < $lngFieldsCount; $i++ )
		{
			$strDisplayValue .= fncHTMLSpecialChars( $aryResult[$i] ) . "��";
		}

		// HTML����
		$strHtml .= '<input class="CheckBox14" type="checkbox" name="'.$strObjectName.'" value="'.$aryResult[0].'">'
		.$strDisplayValue."\n";
	}

	$objDB->freeResult( $lngResultID );
	return $strHtml;
}


// -----------------------------------------------------------------
/**
*	�ޥ������ǡ��������ؿ�
*
*	�ޥ����������ͼ���
*
*	@param  String $strTable            �ơ��֥�̾
*	@param  String $strValueFieldName   value������ե������̾
*	@param  String $strDisplayFieldName ɽ�������ե������̾
*	@param  mixed  $defaultValue        ������('Array'�ξ�硢�����ɤ򥭡��Ȥ���Ϣ��������֤����̻���)
*	@param  String $strQueryWhere       ���(value�������ͤ�Ʊ��)
*	@param  Object $objDB               DB���֥�������
*	@return $aryResult[0]               �ޥ������ǡ���
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMasterValue( $strTable, $strKeyFieldName, $strDisplayFieldName, $defaultValue, $strQueryWhere, $objDB )
{
	// WHERE����Υ���बʸ�������ä����ν���(��:str�פ����ä����''�ǰϤ�)
	list ( $defaultValue, $type ) = explode ( ":", $defaultValue );
	if ( $type == 'str' )
	{
		$defaultValue = "'$defaultValue'";
	}

	// ���ڡ���ID�Υꥹ�Ȥ����
	$strQuery = "SELECT $strKeyFieldName, $strDisplayFieldName FROM $strTable";

	// �����������Ū�Ǥʤ���硢�������ͤ����
	if ( $defaultValue != "Array" )
	{
		$strQuery .= " WHERE $strKeyFieldName = $defaultValue";
	}

	if ( $strQueryWhere )
	{
		$strQuery .= " AND $strQueryWhere";
	}
	//echo "$strQuery<br>";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, $i );
		$aryResultValue[$aryResult[0]] = fncHTMLSpecialChars( $aryResult[1] );
	}

	$objDB->freeResult( $lngResultID );

	// �����������Ū�Ǥʤ���硢�ͤ��֤�
	if ( $defaultValue != "Array" )
	{
		return fncHTMLSpecialChars( $aryResult[1] );
	}

	// �����������Ū�ξ�硢�����ɤ򥭡��Ȥ���Ϣ��������֤�
	else
	{
		return $aryResultValue;
	}

}

// -----------------------------------------------------------------
/**
*	�᡼��ʸ�̼����������ؿ�
*
*	�ޥ�����������������������ˤ���֤�����
*
*	@param  Long $lngFunctionCode ��ǽ������
*	@param  Array  $aryData       �֤�����ʸ����
*	@param  Object $objDB         DB���֥�������
*	@return String $strSubject    �᡼�륿���ȥ�
*	        String $strBody       �᡼����ʸ
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMailMessage( $lngFunctionCode, $aryData, $objDB )
{
	// �᡼���������
	$strQuery = "SELECT strSubject, strBody FROM m_Mailform WHERE lngFunctionCode = $lngFunctionCode AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_WARNING, "����Υ᡼��ƥ�ץ졼�Ȥ�����ޤ���Ǥ�����", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	$objDB->freeResult( $lngResultID );

	// �ƥ�ץ졼�ȥ��֥�����������
	$objTemplate = new clsTemplate();
	$objTemplate->strTemplate = $objResult->strbody;

	// �֤�����
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// ʸ���������Ѵ�(EUC->JIS)
	$objTemplate->strTemplate = mb_convert_encoding( $objTemplate->strTemplate, "JIS", "EUC-JP" );
	$objResult->strsubject    = mb_convert_encoding( $objResult->strsubject, "JIS", "EUC-JP" );
//	$objResult->strsubject    = mb_encode_mimeheader ( $objResult->strsubject , "iso-2022-jp", "B" );

	return array ( $objResult->strsubject, $objTemplate->strTemplate );
}



// -----------------------------------------------------------------
/**
*	�ƥ�ץ졼�ȴؿ�
*
*	�ƥ�ץ졼�Ȥ���HTML����������
*
*	@param  String $strTemplatePath �ƥ�ץ졼�ȥե�����ѥ�
*	@param  Array  $aryPost         POST�ǡ���
*	@param  Object $objAuth         ǧ�ڥ��֥�������
*	@return $strTemplate            ���HTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetReplacedHtml( $strTemplatePath, $aryPost, $objAuth )
{
	global $strBaseTemplate, $aryConfigName;

	// �ѡ��ĥƥ�ץ졼������
	$objContentsTemplate = new clsTemplate();
	$objContentsTemplate->getTemplate( $strTemplatePath );

	// �ü쥿���ǡ�������
	$aryBaseInsert = $objContentsTemplate->getConfig( $aryConfigName );

	// �桼�����ǡ�������
	$aryBaseInsert["strUserID"]             = $objAuth->UserID;
	$aryBaseInsert["strUserFullName"]       = $objAuth->UserFullName;
	$aryBaseInsert["strGroupDisplayCode"]   = $objAuth->GroupDisplayCode;
	$aryBaseInsert["strGroupDisplayName"]   = $objAuth->GroupDisplayName;
	$aryBaseInsert["strAuthorityGroupName"] = $objAuth->AuthorityGroupName;
	$aryBaseInsert["strSessionID"]          = $objAuth->SessionID;


	$aryBaseInsert["strUserID"] = trim( $aryBaseInsert["strUserID"] );


	// �ڡ����ǡ�������
	$aryBaseInsert["lngFunctionCode"]       = $aryPost["lngFunctionCode"];

	if ( $objAuth->UserImageFileName )
	{
		$aryBaseInsert["strUserImageFileName"]  = USER_IMAGE_URL . $objAuth->UserImageFileName;
	}
	else
	{
		$aryBaseInsert["strUserImageFileName"] = USER_IMAGE_URL . "default.gif";
	}

	// �Խ����̤�����(�������̤��ä����Τ��ü����)
	if ( $aryPost["RENEW"] == TRUE )
	{
		$aryPost["strBaseVisibilityName"] = "hidden";
		$aryPost["strBaseDisplayName"] = "none";
		$aryBaseInsert["strBaseVisibilityName"] = "hidden";
		$aryBaseInsert["strBaseDisplayName"] = "none";
		$aryBaseInsert["strCssName"]            = "renewlayout.css";
		$aryBaseInsert["strErrorCssName"]       = "renewerrorlayout.css";
	}
	else
	{
		$aryPost["strBaseVisibilityName"] = "visible";
		$aryPost["strBaseDisplayName"] = "block";
		$aryBaseInsert["strBaseVisibilityName"] = "visible";
		$aryBaseInsert["strBaseDisplayName"] = "block";
		$aryBaseInsert["strCssName"]            = "layout.css";
		$aryBaseInsert["strErrorCssName"]       = "errorlayout.css";
	}
	$aryPost["RENEW"] = "";

	// ���å���������쥳���ɤ���������֤�������
	if ( !$_COOKIE["lngLanguageCode"] )
	{
		$_COOKIE["lngLanguageCode"] = "0";
	}
	$aryBaseInsert["bodyonload"] = preg_replace ( "/_%lngLanguageCode%_/", $_COOKIE["lngLanguageCode"], $aryBaseInsert["bodyonload"] );

// 2004.09.29 suzukaze update start
	$aryBaseInsert["bodyonload"] = preg_replace ( "/_%strHeaderErrorMessage%_/", $aryPost["strHeaderErrorMessage"], $aryBaseInsert["bodyonload"] );
// 2004.09.29 suzukaze update end



	// body.onclick
	$aryBaseInsert["bodyonclick"] = 'fncHideSubMenu();';


	if ( $aryPost )
	{
		$objContentsTemplate->replace( $aryPost );
	}
	
	$aryBaseInsert["BODY"] = $objContentsTemplate->strTemplate;

	// �١����ƥ�ץ졼�ȼ���
	$objBaseTemplate = new clsTemplate();
	$objBaseTemplate->getTemplate( $strBaseTemplate );

	// ���顼��å�����������
	$aryBaseInsert["strErrorMessage"] = $aryPost["strErrorMessage"];
	// �١����ƥ�ץ졼���֤�����
	$objBaseTemplate->replace( $aryBaseInsert );

	$objBaseTemplate->complete();

	// header("Content-type: text/plain; charset=EUC-JP");


//require( LIB_DEBUGFILE );
//fncDebug( 'tmpl.txt', $objBaseTemplate->strTemplate, __FILE__, __LINE__);


	return $objBaseTemplate->strTemplate;
}

// -----------------------------------------------------------------
/**
 *	�ƥ�ץ졼�ȴؿ�
 *
 *	�ƥ�ץ졼�Ȥ���HTML����������
 *
 *	@param  String $strBaseTemplatePath �١����ƥ�ץ졼�ȥե�����ѥ�
 *	@param  String $strTemplatePath �ƥ�ץ졼�ȥե�����ѥ�
 *	@param  Array  $aryPost         POST�ǡ���
 *	@param  Object $objAuth         ǧ�ڥ��֥�������
 *	@return $strTemplate            ���HTML
 *	@access public
 */
// -----------------------------------------------------------------
function fncGetReplacedHtmlWithBase($strBaseTemplate, $strTemplatePath, $aryPost, $objAuth )
{
	global $aryConfigName;

	// �ѡ��ĥƥ�ץ졼������
	$objContentsTemplate = new clsTemplate();
	$objContentsTemplate->getTemplate( $strTemplatePath );

	// �ü쥿���ǡ�������
	$aryBaseInsert = $objContentsTemplate->getConfig( $aryConfigName );

	// �桼�����ǡ�������
	$aryBaseInsert["strUserID"]             = $objAuth->UserID;
	$aryBaseInsert["strUserFullName"]       = $objAuth->UserFullName;
	$aryBaseInsert["strGroupDisplayCode"]   = $objAuth->GroupDisplayCode;
	$aryBaseInsert["strGroupDisplayName"]   = $objAuth->GroupDisplayName;
	$aryBaseInsert["strAuthorityGroupName"] = $objAuth->AuthorityGroupName;
	$aryBaseInsert["strSessionID"]          = $objAuth->SessionID;

	$aryBaseInsert["strUserID"] = trim( $aryBaseInsert["strUserID"] );
	
	$aryBaseInsert["HeaderTitleImage"] = '/img/type01/'.explode('/',$strTemplatePath)[0].'/title_ja.gif';

	// �ڡ����ǡ�������
	$aryBaseInsert["lngFunctionCode"]       = $aryPost["lngFunctionCode"];

	if ( $objAuth->UserImageFileName )
	{
		$aryBaseInsert["strUserImageFileName"]  = USER_IMAGE_URL . $objAuth->UserImageFileName;
	}
	else
	{
		$aryBaseInsert["strUserImageFileName"] = USER_IMAGE_URL . "default.gif";
	}

	// �Խ����̤�����(�������̤��ä����Τ��ü����)
	if ( $aryPost["RENEW"] == TRUE )
	{
		$aryPost["strBaseVisibilityName"] = "hidden";
		$aryPost["strBaseDisplayName"] = "none";
		$aryBaseInsert["strBaseVisibilityName"] = "hidden";
		$aryBaseInsert["strBaseDisplayName"] = "none";
		$aryBaseInsert["strCssName"]            = "renewlayout.css";
		$aryBaseInsert["strErrorCssName"]       = "renewerrorlayout.css";
	}
	else
	{
		$aryPost["strBaseVisibilityName"] = "visible";
		$aryPost["strBaseDisplayName"] = "block";
		$aryBaseInsert["strBaseVisibilityName"] = "visible";
		$aryBaseInsert["strBaseDisplayName"] = "block";
		$aryBaseInsert["strCssName"]            = "layout.css";
		$aryBaseInsert["strErrorCssName"]       = "errorlayout.css";
	}
	$aryPost["RENEW"] = "";

	// ���å���������쥳���ɤ���������֤�������
	if ( !$_COOKIE["lngLanguageCode"] )
	{
		$_COOKIE["lngLanguageCode"] = "0";
	}

	if ( $aryPost )
	{
		$objContentsTemplate->replace( $aryPost );
	}
	$aryBaseInsert["BODY"] = $objContentsTemplate->strTemplate;

	// �١����ƥ�ץ졼�ȼ���
	$objBaseTemplate = new clsTemplate();
	$objBaseTemplate->getTemplate( $strBaseTemplate );

	// ���顼��å�����������
	$aryBaseInsert["strErrorMessage"] = $aryPost["strErrorMessage"];

	// �١����ƥ�ץ졼���֤�����
	$objBaseTemplate->replaceForMold($aryBaseInsert);

	$objBaseTemplate->complete();

	return $objBaseTemplate->strTemplate;
}


// -----------------------------------------------------------------
/**
*	HTMLɽ��������Τʤ�ʸ������֤��ؿ�
*
*	HTMLɽ��������Τʤ�ʸ������֤�
*
*	@param  String $strValue �Ѵ�����ʸ����
*	@return String $strValue �Ѵ����줿ʸ����
*	@access public
*/
// -----------------------------------------------------------------
function fncHTMLSpecialChars( $strValue )
{
//	$strValue = mb_ereg_replace ( "\\\\\"", "\"", $strValue );
//	$strValue = mb_ereg_replace ( "\\\\'", "'", $strValue );
//	$strValue = htmlspecialchars ( $strValue, ENT_QUOTES );

	return $strValue;
}



// -----------------------------------------------------------------
/**
*	HTMLɽ��������Τʤ�ʸ�����������֤��ؿ�
*
*	HTMLɽ��������Τʤ�ʸ�����������֤�
*
*	@param  Array $aryData �Ѵ�����Ϣ������
*	@return Array $aryData �Ѵ����줿Ϣ������
*	@access public
*/
// -----------------------------------------------------------------
function fncToHTMLString( $aryData )
{
	$aryKeys = array_keys ( $aryData );
	foreach ( $aryKeys as $strKey )
	{
		$aryData[$strKey] = fncHTMLSpecialChars( $aryData[$strKey] );
	}
	return $aryData;
}



// -----------------------------------------------------------------
/**
*	������ϴؿ�
*
*	����Υ������ͤΥơ��֥�ޤ��ϥե�����(HIDDEN)����
*
*	@param  Array  $aryData        Ĵ�٤����ǡ�������
*	@param  String $strExpressMode TABLE  : �ơ��֥�Τ�
*                                  HIDDEN : HIDDEN�Τ�
*	                               MIX    : ξ��
*	@return String $strHtmlTable   HTML TABLE
*	@access public
*/
// -----------------------------------------------------------------
function getArrayTable( $aryData, $strExpressMode )
{
	$strTable = "<table border>\n";

	// ����ο������롼��
	$keys = array_keys ( $aryData );
	foreach ( $keys as $key )
	{
		$strTable .= "<tr><th>$key</th><td>$aryData[$key]</td></tr>\n";

		$strHidden .= "<input type=\"hidden\" name=\"$key\" value=\"$aryData[$key]\">\n";
	}

	$strTable .= "</table>\n";

	if ( $strExpressMode == "TABLE" || $strExpressMode == "MIX" )
	{
		$strHtml = $strTable;
	}

	if ( $strExpressMode == "HIDDEN" || $strExpressMode == "MIX" )
	{
		$strHtml .= $strHidden;
	}
	return $strHtml;
}



// -----------------------------------------------------------------
/**
*	���顼����ɽ�������ؿ�
*
*	���顼����ɽ������(visibility:visible; or hidden ���֤�)
*
*	@param  Array   $aryData        Ĵ�٤����ǡ�������
*	@param  Array   $aryCheckResult ���顼����
*	@param  Object  $objDB          DB���֥�������
*	@return Array   $aryData        HTML TABLE
*	        boolean $bytErrorFlag   ���顼��¸�ߥե饰
*	@access public
*/
// -----------------------------------------------------------------
function getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB )
{
	// ����Ƚ��(�������륫��������)
	$value = "strmessagecontentenglish";
	if ( $_COOKIE["lngLanguageCode"] == 1 )
	{
		$value = "strmessagecontent";
	}

	// ���顼��å�������������������
	$strQuery = "SELECT lngMessageCode, $value FROM m_Message";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ���顼�����ɤ򥭡��Ȥ���Ϣ����������
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$objResult->$value = preg_replace ( "/_%.+?%_/", "", $objResult->$value );
		$aryMessage[$objResult->lngmessagecode] = $objResult->$value;
	}
	$objDB->freeResult( $lngResultID );

	// ���顼Ƚ��
	$aryCheckResultKeys = array_keys ( $aryCheckResult );
	foreach ( $aryCheckResultKeys as $key )
	{
		// �����å���̤��ͤ�¸�ߤ�����硢���顼ɽ��ʸ��������
		if ( $aryCheckResult[$key] )
		{
			list ( $lngErrorCode, $strErrorMessage ) = explode ( ":", $aryCheckResult[$key] );
			// ��Ƭ��ʸ�����ORIGINAL�פ����ä���硢������ˤ���ʸ�����
			// ���顼��å������Ȥ���
			if ( preg_match ( "/^ORIGINAL/", $strErrorMessage ) )
			{
				$aryMessage[$lngErrorCode] = preg_replace ( "/^ORIGINAL/", "", $strErrorMessage );
			}

			$aryData[$key] = "visibility:visible;width=16;";
			$aryData[$key . "_Message"] = $aryMessage[$lngErrorCode];
			$bytErrorFlag = TRUE;
		}
		elseif ( !$aryData[$key] )
		{
			$aryData[$key] = "visibility:hidden;width=0;";
		}
	}
	return array ( $aryData, $bytErrorFlag );
}



////////////////////////////////////////////////////////////////////
// ǧ�ڡ����å����ؿ�
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	���å����ؿ�(��ǧ)
*
*	���å����γ�ǧ
*
*	@param  String  $strSessionID   ���å����ID
*	@param  Object  $objAuth        ǧ�ڥ��֥�������
*	@param  Object  $objDB          DB���֥�������
*	@return Object  $objAuth          ǧ�ڥ��֥�������
*	@access public
*/
// -----------------------------------------------------------------
function fncIsSession( $strSessionID, $objAuth, $objDB )
{
	if ( !$strSessionID )
	{
		fncOutputError ( 9052, DEF_ERROR, "���å��������Ϥ���Ƥ��ޤ���", TRUE, "", $objDB );
	}

	// ���å�����ǧ
	if ( !$objAuth->isLogin( $strSessionID, $objDB ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����ॢ���Ȥˤʤ�ޤ������ƥ����󤷤Ʋ�������", TRUE, "/login/login.php", $objDB );
	}
	return $objAuth;
}



// -----------------------------------------------------------------
/**
*	���¥����å��ؿ�
*
*	���¤γ�ǧ
*
*	@param  Long $lngFunctionCode  ��ǽ������
*	@param  Object $objAuth        ǧ�ڥ��֥�������
*	@return Boolean                ���¤�̵ͭ
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckAuthority( $lngFunctionCode, $objAuth )
{
	// ���¥����å�
	if ( $objAuth->FunctionCode[$lngFunctionCode] )
	{
		return TRUE;
	}
	return FALSE;

}



// -----------------------------------------------------------------
/**
*	������ؿ�
*
*	���������
*
*	@param  String $strUserID       ID
*	@param  String $strPasswordHash �ѥ����
*	@param  Object $objAuth        ǧ�ڥ��֥�������
*	@param  Object $objDB           DB���֥�������
*	@return Object  $objDB          ǧ�ڥ��֥�������
*	@access public
*/
// -----------------------------------------------------------------
function fncLogin( $strUserID, $strPasswordHash, $objAuth, $objDB )
{
	// ǧ�ڥ����å�
	if ( !$objAuth->login( $strUserID, $strPasswordHash, $objDB ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "ID�ޤ��ϥѥ���ɤ��ְ�äƤ��ޤ���", TRUE, "", $objDB );
	}
	return $objAuth;
}



// -----------------------------------------------------------------
/**
*	�������ȴؿ�
*
*	�������Ƚ���
*
*	@param  String $SessionID ���å����ID
*	@param  Object $objDB     DB���֥�������
*	@return Boolean           �������Ȥ�����
*	@access public
*/
// -----------------------------------------------------------------
function fncLogout( $strSessionID, $objDB )
{
	$objAuth = new clsAuth();
	if ( !$objAuth->logout( $strSessionID, $objDB ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�������Ȥ˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}
	return TRUE;
}



////////////////////////////////////////////////////////////////////
// ����¾�δؿ�
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	���������ؿ�
*
*	����Υե����ޥåȤ�1�Ԥ���¸���줿ʸ�����Ϣ����������������֤�
*
*	@param  String $strData      ʬ�丵ʸ����
*	@param  String $strAmpersand �ǡ���ʬ��ʸ����
*	@param  String $strEqual     �������ͤ�ʬ��ʸ��
*	@return Array  $aryData      ʬ�������
*	@access public
*/
// -----------------------------------------------------------------
function fncStringToArray ( $strData, $strAmpersand, $strEqual )
{
	$aryString = explode ( $strAmpersand, $strData );

	for ( $i = 0; $i < count ( $aryString ); $i++ )
	{
		list ( $key, $value ) = explode ( $strEqual, $aryString[$i] );
		$aryData[$key] = $value;
	}

	return $aryData;
}



////////////////////////////////////////////////////////////////////
// ���顼���ϴؿ�
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	�����ƥ२�顼�ؿ�
*
*	�����ƥ२�顼����
*
*	@param  Long $errno     ���顼�ֹ�
*	@param  String $errstr  ���顼ʸ
*	@param  String $errfile �ե�����̾
*	@param  Long $errline   ���顼��
*	@access public
*/
// -----------------------------------------------------------------
function fncError ( $errno, $errstr, $errfile, $errline )
{
	if ( $errno == E_ERROR || $errno == E_WARNING || $errno ==  E_PARSE || $errno ==  E_CORE_ERROR || $errno ==  E_CORE_WARNING )
	{
		switch ( $errno )
		{
			case E_ERROR :
				$strMailMessage = "FATAL ERROR! (E_ERROR)\n";
				$strPageMessage = "FATAL ERROR! (E_ERROR)<BR>";
				break;
			case E_WARNING :
				$strMailMessage = "FATAL ERROR! (E_WARNING)\n";
				$strPageMessage = "FATAL ERROR! (E_WARNING)<BR>";
				break;
			case E_PARSE :
				$strMailMessage = "FATAL ERROR! (E_PARSE)\n";
				$strPageMessage = "FATAL ERROR! (E_PARSE)<BR>";
				break;
			case E_CORE_ERROR :
				$strMailMessage = "FATAL ERROR! (E_CORE_ERROR)\n";
				$strPageMessage = "FATAL ERROR! (E_CORE_ERROR)<BR>";
				break;
			case E_CORE_WARNING :
				$strMailMessage = "FATAL ERROR! (E_CORE_WARNING)\n";
				$strPageMessage = "FATAL ERROR! (E_CORE_WARNING)<BR>";
				break;
		}

		// timestamp for the error entry
		$dt = date ( "Y-m-d H:i:s (T)" );

		$strMailMessage .= "DATE $dt\n";
		$strMailMessage .= "NO[$errno] $errstr<br>\n";
		$strMailMessage .= "LINE $errline FILE $errfile\n";

		$strPageMessage .= "<b>DATE $dt</b>\n";
		$strPageMessage .= "NO[$errno]<br>$errstr<br>\n";
		$strPageMessage .= "LINE $errline FILE $errfile<br>\n";
		$strPageMessage .= $_SERVER["QUERY_STRING"];
		//error_log($err, 3, "/usr/local/php4/error.log");
echo $errstr;
		mb_send_mail( ERROR_MAIL_TO,"K.I.D.S. Error Message from " . TOP_URL, $strMailMessage, "From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
		echo $strPageMessage;
	}
}



// -----------------------------------------------------------------
/**
*	���顼���ϴؿ�
*
*	���顼����
*
*	@param  Long  $lngErrorCode     ���顼�����ɡ�m_Message�ޥ����Υ�å����������ɡ�
*	@param  Long  $lngErrorClass    ���顼���ࡡconf.inc�ˤ�����Τ��륨�顼��٥�
*										DEF_ANNOUNCE	�����ƥॢ�ʥ���
*										DEF_WARNING	��ե�٥�����ϥߥ��ʤɡ�
*										DEF_ERROR	���顼��٥�
*										DEF_FATAL	�����ƥ२�顼��٥�
*	@param  Array $aryErrorMessage  ���顼�ִ�ʸ���������
*	@param  Boolean $bytOutputFlag  ���ϥե饰
*							FALSE�������ؿ�������ͤ�String�ˤƥ��顼��å��������֤�
*							TRUE���������顼��å��������̤�ɽ��
*	@param  Long  $strReturnPath    �����ץܥ����URL(���ڡ����ξ�������)
*	@param  Object  $objDB          DB���֥�������
*	@return String $strErrorMessage ���顼��å�����
*	@access public
*/
// -----------------------------------------------------------------
function fncOutputError ( $lngErrorCode, $lngErrorClass, $aryErrorMessage, $bytOutputFlag, $strReturnPath, $objDB )
{
	// DB����³���֤Υ����å�
	if ( !$objDB->isOpen() )
	{
		$strErrorMessage = "DB��³���顼";
		return $strErrorMessage;
	}

	// ��å������μ���
	$strQuery = "SELECT strMessageContent from m_Message WHERE lngMessageCode = " . $lngErrorCode;
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	$strErrorMessage = $objResult->strmessagecontent;

//fncDebug( 'upload_parse_confirm_0a2.txt', $objDB->InputEncoding, __FILE__, __LINE__);
//fncDebug( 'upload_parse_confirm_0a3.txt', $strErrorMessage, __FILE__, __LINE__);


	$objDB->freeResult( $lngResultID );

    

	// ��å�����ʸ������ִ�
	if (!is_array($aryErrorMessage))
	{
		// ʸ�����󥳡��ǥ��󥰤򸡽Ф���
		$encodeType = mb_detect_encoding($aryErrorMessage);
		
		//��DB�Ȥȥ��顼�ִ�ʸ���Υ��󥳡��ǥ��󥰤��ۤʤ���ϥ��󥳡��ǥ��󥰤�Ԥ�
		if ($encodeType != $objDB->InputEncoding) {
			$aryErrorMessage = mb_convert_encoding($aryErrorMessage, $objDB->InputEncoding);
		}		

		$strExchange = "msg1";
		$strErrorMessage = preg_replace ( "/_%" . $strExchange . "%_/i", $aryErrorMessage, $strErrorMessage );
	}
	else
	{

		for ( $i = 0; $i < count($aryErrorMessage); $i++ )
		{
			// ʸ�����󥳡��ǥ��󥰤򸡽Ф���
			$encodeType = mb_detect_encoding($aryErrorMessage[$i]);

			//��DB�Ȥȥ��顼�ִ�ʸ���Υ��󥳡��ǥ��󥰤��ۤʤ���ϥ��󥳡��ǥ��󥰤�Ԥ�
			if ($encodeType != $objDB->InputEncoding) {
			    $aryErrorMessage[$i] = mb_convert_encoding($aryErrorMessage[$i], $objDB->InputEncoding);
			}

			$strExchange = "msg" . (string) ($i + 1);
			$strErrorMessage = preg_replace ( "/_%" . $strExchange . "%_/i", $aryErrorMessage[$i], $strErrorMessage );
		}
	}
	// �ִ�����ʤ��ä��֤�����ʸ�������
	$strErrorMessage = preg_replace ( "/_%.+?%_/", "", $strErrorMessage );

	// ��å�����������å��������ɲ�
	switch ( $lngErrorClass )
	{
		case DEF_WARNING:
//			$strErrorMessage = "WARNING! " . $strErrorMessage;
			break;
		case DEF_ERROR:
			$strErrorMessage = "ERROR! " . $strErrorMessage;
			break;
		case DEF_FATAL:
			$strErrorMessage = "FATAL ERROR! " . $strErrorMessage;
			break;
		case DEF_ANNOUNCE:
			$strErrorMessage = "ANNOUNCE " . $strErrorMessage;
			break;
	}


	// ���顼���̽�������
	if ( $bytOutputFlag )
	{


		// ���顼���̤ν�������ˤĤ��ƤϥǥХå��⡼�ɤˤ���ڤ��ؤ���
		if ( DEF_DEBUG_MODE == 1 )
		{

			//header("Content-Type: text/html;charset=euc-jp");
			mb_http_output($objDB->InputEncoding);

			$strEcho = '<html>';
			$strEcho .= '<head>';
			$strEcho .= '<meta http-equiv="content-type" content="text/html; charset='.$objDB->InputEncoding.'">';
			$strEcho .= '</head><body>';
			$strEcho .= '&nbsp;';
			// ���顼��å���������̤�ɽ������
			$strEcho .= $strErrorMessage . "<BR>";
			$strEcho .= '</body></html>';

//fncDebug( 'upload_parse_confirm_0a4.txt', $strEcho, __FILE__, __LINE__);

			echo $strEcho;
			exit;
		}
		else
		{

		 	// /error/index.php �Ǽ�갷�����󥳡��ǥ��󥰤��Ѵ�����
			$strErrorMessage = mb_convert_encoding($strErrorMessage, 'euc-jp', $objDB->InputEncoding);

			// ���顼���̤ؤΥ�����쥯��
			$strTopUrl = TOP_URL;
			$strRedirectHTML = "
			<script language=javascript>
			if ( opener )
			{
				openerLocation = '$strTopUrl';
			}
			else
			{
				openerLocation = 'nothing';
			}
			window.location='/error/index.php?ref=' + openerLocation + '&path=". rawurlencode($strReturnPath) ."&strMessage=". rawurlencode($strErrorMessage)."';
			</script>
			";

			echo $strRedirectHTML;
			exit;
		}
	}

	return $strErrorMessage;
}





// -----------------------------------------------------------------
/**
*	���ơ����������ɥ����å��ؿ�
*
*	���ơ����������ɤ�����å�����NULL(�ޤ��϶���) �ξ���0�פ��ֵ�
*
*	@param  Long  $status         ���ơ�����������

*	@return Long  $lngStatusCode  ���ơ�����������
*
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckNullStatus( $status )
{
	$lngStatusCode = $status;

	// ���֥����ɤ��� null / "" �פξ�硢��0�פ������
	$lngStatusCode = ( $lngStatusCode == "" || $lngStatusCode == "null" ) ? 0 : $lngStatusCode;

	return $lngStatusCode;
}


// -----------------------------------------------------------------
/**
*	���ơ����������ɥ����å��ؿ�
*
*	���ơ����������ɤ�����å�������0�� �ξ���1�פ��ֵ�
*
*	@param  Long  $status         ���ơ�����������

*	@return Long  $lngStatusCode  ���ơ�����������
*
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckZeroStatus( $status )
{
	$lngStatusCode = $status;

	// ���֥����ɤ���0�פξ�硢��1�פ������
	$lngStatusCode = ( $lngStatusCode == 0 ) ? 1 : $lngStatusCode;

	return $lngStatusCode;
}





// -----------------------------------------------------------------
/**
*	���¥��롼�ץ����ɼ����ؿ�
*
*	�桼�����θ��¥��롼�ץ����ɤ����
*
*	@param  Long     $lngusercode  �桼����������
*	@param  String   $sessionid    ���å����ID
	@param  Object   $objDB        DB���֥�������

*	@return Boolean  $blnRoot
*
*	@access public
*/
// -----------------------------------------------------------------
function fncGetUserAuthorityGroupCode( $lngusercode, $sessionid, $objDB )
{
	$blnRoot  = false;
	$aryQuery = array();
	$strQuery = "";

	$aryQuery[] = "SELECT";
	$aryQuery[] = " lngauthoritygroupcode";
	$aryQuery[] = "FROM";
	$aryQuery[] = " m_user";
	$aryQuery[] = "WHERE";
	$aryQuery[] = " lngusercode = " . $lngusercode;

	$strQuery = implode( "\n", $aryQuery );


	if( isset( $lngResultID ) )
	{
		$objDB->freeResult( $lngResultID );
	}

	// ������¹�
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngauthoritygroupcode = $objResult->lngauthoritygroupcode;
	}
	else
	{
		// ���¥��롼�ץ����ɼ�������
		fncOutputError ( 9052, DEF_WARNING, "���¥��롼�ץ����ɼ�������", TRUE, "menu/menu.php?strSessionID=" . $sessionid, $objDB );
	}


	return $lngauthoritygroupcode;
}





// -----------------------------------------------------------------
/**
*	���¥��롼�ץ����ɥ����å��ؿ�
*
*	�桼�����θ��¥��롼�ץ����ɤ�����å�����
*	�֥桼�����װʲ��ξ���TRUE�ס�
*	����ʳ��ξ���FALSE�פ��ֵ�
*
*	@param  Long     $lngusercode  �桼����������
*	@param  String   $sessionid    ���å����ID
	@param  Object   $objDB        DB���֥�������

*	@return Boolean  $blnRoot
*
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckUserAuthorityGroupCode( $lngusercode, $sessionid, $objDB )
{
	$blnRoot  = false;
	$aryQuery = array();
	$strQuery = "";

	$aryQuery[] = "SELECT";
	$aryQuery[] = " lngauthoritygroupcode";
	$aryQuery[] = "FROM";
	$aryQuery[] = " m_user";
	$aryQuery[] = "WHERE";
	$aryQuery[] = " lngusercode = " . $lngusercode;

	$strQuery = implode( "\n", $aryQuery );


	if( isset( $lngResultID ) )
	{
		$objDB->freeResult( $lngResultID );
	}

	// ������¹�
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngauthoritygroupcode = $objResult->lngauthoritygroupcode;
	}
	else
	{
		// ���¥��롼�ץ����ɼ�������
		fncOutputError ( 9052, DEF_WARNING, "���¥��롼�ץ����ɼ�������", TRUE, "menu/menu.php?strSessionID=" . $sessionid, $objDB );
	}


	// ���¥��롼�פ��֥桼�����װʲ��ξ���TRUE��
	$blnRoot = ( $lngauthoritygroupcode >= 5 ) ? true : false;

	return $blnRoot;
}


// -----------------------------------------------------------------
/**
*	��ǧ�롼�ȥ����å��ؿ�
*
*	�桼�����ξ�ǧ�롼�Ȥ�����å�����
*	¸�ߤ������TRUE�ס�
*	¸�ߤ��ʤ�����FALSE�פ��ֵ�
*
*	@param  Long     $lngusercode  �桼����������
*	@param  String   $sessionid    ���å����ID
	@param  Object   $objDB        DB���֥�������

*	@return Boolean  $blnRoot
*
*	@access public
*/
// -----------------------------------------------------------------
// ��ǧ�롼��¸�ߥ����å�
function fncCheckWorkFlowRoot( $lngusercode, $sessionid, $objDB )
{
	$blnRoot  = false;
	$aryQuery = array();
	$strQuery = "";

	$aryQuery[] = "SELECT DISTINCT ON";
	$aryQuery[] = " ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode";
	$aryQuery[] = "FROM";
	$aryQuery[] = " m_WorkflowOrder w, m_GroupRelation gr";
	$aryQuery[] = "WHERE";
	$aryQuery[] = " gr.lngUserCode = " . $lngusercode;
	$aryQuery[] = "AND w.lngWorkflowOrderGroupCode = gr.lngGroupCode";
	$aryQuery[] = "AND w.bytWorkflowOrderDisplayFlag = true";
	$aryQuery[] = "EXCEPT";
	$aryQuery[] = "SELECT DISTINCT ON";
	$aryQuery[] = " ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode";
	$aryQuery[] = "FROM";
	$aryQuery[] = " m_WorkflowOrder w,";
	$aryQuery[] = " m_User u,";
	$aryQuery[] = " m_AuthorityGroup ag";
	$aryQuery[] = "WHERE";
	$aryQuery[] = " w.lngInChargeCode = " . $lngusercode;
	$aryQuery[] = " OR ag.lngAuthorityLevel >";
	$aryQuery[] = " (";
	$aryQuery[] = "  SELECT";
	$aryQuery[] = "   ag2.lngAuthorityLevel";
	$aryQuery[] = "  FROM";
	$aryQuery[] = "   m_User u2,";
	$aryQuery[] = "   m_AuthorityGroup ag2";
	$aryQuery[] = "  WHERE";
	$aryQuery[] = "   u2.lngUserCode = " . $lngusercode;
	$aryQuery[] = "   AND u2.lngAuthorityGroupCode = ag2.lngAuthorityGroupCode";
	$aryQuery[] = " )";
	$aryQuery[] = " AND w.lngInChargeCode = u.lngUserCode";
	$aryQuery[] = " AND w.bytWorkflowOrderDisplayFlag = true";
	$aryQuery[] = " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode";
	$aryQuery[] = "GROUP BY";
	$aryQuery[] = " w.lngworkflowordercode";

	$strQuery = implode( "\n", $aryQuery );

	// ������¹�
	$lngResultID = $objDB->execute( $strQuery );


	if( !$lngResultID )
	{
		fncOutputError( 3, "DEF_FATAL", "�����꡼�¹ԥ��顼" ,TRUE, "menu/menu.php?strSessionID=" . $sessionid, $objDB );
	}

	// ������ȼ���
	$lngCount = pg_num_rows( $lngResultID );


	// ���������
	$blnRoot = ( $lngCount == 0 ) ? false : true;

	return $blnRoot;
}



// -----------------------------------------------------------------
/**
*	����ô�����������å��ؿ�
*
*	������桼����������ô������������å�����
*	°�������TRUE�ס�
*	°���ʤ�����FALSE�פ��ֵ�
*
*	@param  Long     $lngTargetNo        �о�����������
*	@param  Long     $lngInputUserCode   ������桼����������
*	@param  String   $strFncFlag         ��ǽ�ե饰
	@param  Object   $objDB              DB���֥�������

*	@return Boolean  $blnCheck
*
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckInChargeProduct( $lngTargetNo, $lngInputUserCode, $strFncFlag, $objDB )
{
//require_once( LIB_DEBUGFILE );

	$blnCheck       = true;

	$aryQuery       = array();
	$strQuery       = "";
	$strSelectQuery = "";
	$aryFromQuery   = array();
	$strFromQuery   = "";
	$strWhereQuery  = "";


	// �ƥ����꡼�������
	switch( $strFncFlag )
	{
		case "P":
			$strSelectQuery = "mp.lngproductno";

			$strFromQuery   = "	m_product mp";

			$strWhereQuery  = "	mp.lngproductno = " . $lngTargetNo;
			break;

		case "ES":
			$strSelectQuery = "me.lngestimateno";

			$aryFromQuery[] = "	m_estimate me";
			$aryFromQuery[] = "		left join m_product mp";
			$aryFromQuery[] = "		on mp.strproductcode = me.strproductcode";
			$strFromQuery   = implode( "\n", $aryFromQuery );

			$strWhereQuery  = "	me.lngestimateno = " . $lngTargetNo;
			break;

		case "SO":
			$strSelectQuery = "mr.lngreceiveno";

			$aryFromQuery[] = "	m_receive mr";
			$aryFromQuery[] = "		left join t_receivedetail trd";
			$aryFromQuery[] = "		on trd.lngreceiveno = mr.lngreceiveno";
			$aryFromQuery[] = "			left join m_product mp";
			$aryFromQuery[] = "			on mp.strproductcode = trd.strproductcode";
			$strFromQuery   = implode( "\n", $aryFromQuery );

			$strWhereQuery  = "	mr.lngreceiveno = " . $lngTargetNo;
			break;

		case "PO":
			$strSelectQuery = "mo.lngorderno";

			$aryFromQuery[] = "	m_order mo";
			$aryFromQuery[] = "		left join t_orderdetail tod";
			$aryFromQuery[] = "		on tod.lngorderno = mo.lngorderno";
			$aryFromQuery[] = "			left join m_product mp";
			$aryFromQuery[] = "			on mp.strproductcode = tod.strproductcode";
			$strFromQuery   = implode( "\n", $aryFromQuery );

			$strWhereQuery  = "	mo.lngorderno = " . $lngTargetNo;
			break;

		default:
			break;
	}


	$aryQuery[] = "select distinct";
	$aryQuery[] = $strSelectQuery;
	$aryQuery[] = ",mp.strproductcode";
	$aryQuery[] = "," . $lngInputUserCode . " in (";
	$aryQuery[] = "		select";
	$aryQuery[] = "			mu1.lngusercode";
	$aryQuery[] = "		from";
	$aryQuery[] = "			m_user mu1";
	$aryQuery[] = "		,m_grouprelation mgr1";
	$aryQuery[] = "		,";
	$aryQuery[] = "		(";
	$aryQuery[] = "			select";
	$aryQuery[] = "				mu.lngusercode";
	$aryQuery[] = "				,mg.lnggroupcode";
	$aryQuery[] = "			from";
	$aryQuery[] = "				m_user mu";
	$aryQuery[] = "				left join m_grouprelation mgr";
	$aryQuery[] = "					on mgr.lngusercode = mu.lngusercode";
	$aryQuery[] = "					left join m_group mg";
	$aryQuery[] = "						on mg.lnggroupcode = mgr.lnggroupcode";
	$aryQuery[] = "			where";
	$aryQuery[] = "			mu.lngusercode = mp.lnginchargeusercode";
	$aryQuery[] = "		) as mst1";
	$aryQuery[] = "		where";
	$aryQuery[] = "			mgr1.lnggroupcode = mst1.lnggroupcode";
	$aryQuery[] = "			and mu1.bytinvalidflag = false";	/* ����ô���Ԥ��Ф������ϼ� �� �������ϼԤ�°���륰�롼�פΥޥ͡����㡼�ʾ夬���� */
	$aryQuery[] = "			and mu1.lngusercode = mgr1.lngusercode";
//39����̳3���б��Τ���
	$aryQuery[] = "			and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode or mu1.lngusercode in ('15','29','242','343'))";
	$aryQuery[] = "	) as blnAuthFlag";

	$aryQuery[] = "from";
	$aryQuery[] = $strFromQuery;
	$aryQuery[] = "where";
	$aryQuery[] = $strWhereQuery;


	$strQuery = implode( "\n", $aryQuery );

	// �����꡼�¹�
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum >= 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$blnCheck  = $objResult->blnauthflag;
		$blnCheck  = ( $blnCheck == "f" ) ? false : true;

//fncDebug( 'lib_lib.txt', $objResult, __FILE__, __LINE__);
	}
	else
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}


	unset( $aryFromQuery, $aryQuery );

	return $blnCheck;
}



// -----------------------------------------------------------------
/**
*	�᡼�������ؿ�
*
*
*	@param  Long     $strTo			To���ɥ쥹
*	@param  Long     $strSubject	��̾
*	@param  String   $strMessage	��ʸ
	@param  Object   $strHeader		�إå�������

*	@return Boolean  $blnCheck
*
*	@access public
*/
// -----------------------------------------------------------------
function fncSendMail($strTo, $strSubject, $strMessage, $strHeader="")
{
	// �᡼�������ե饰�� false �Ρʥ᡼�������򤷤ƤϤ����ʤ��˾���ȴ����
	if( SEND_MAIL_FLAG == false )
	{
		return true;
	}
	// ��ȯ�Ķ��ξ��
	if( $_SERVER["HTTP_HOST"] == EXECUTE_HOST_NAME_DEV  || $_SERVER["HTTP_HOST"] == EXECUTE_HOST_NAME_KWG_BACK )
	{
		$strSubject .= " ".$strTo;
		$strTo = ERROR_MAIL_TO;
	}

	// ��������
	mb_language("Japanese");

	// �᡼������
	return mb_send_mail( $strTo, $strSubject, $strMessage, $strHeader );
}





// -----------------------------------------------------------------
/**
*	�ƥ�ݥ��ե�������¸���ե�����̾�ֵ�
*
*
*	@param  String   $strTmpFile	�ƥ�ݥ��ե�����̾
*
*	@return String  $strTmpFileName
*
*	@access public
*/
// -----------------------------------------------------------------
function getTempFileName( $strTmpFile )
{
	// �ƥ�ݥ��ե�����κ���
	$strTmpFileName	= MD5( microtime() ) . ".tmp";

	// �ƥ�ݥ��ե�����ΰ�ư
	if( !move_uploaded_file( $strTmpFile, FILE_UPLOAD_TMPDIR . $strTmpFileName ) )
	{
		fncOutputError( 1106, DEF_FATAL, "", TRUE, "", $objDB );
		return false;
	}

	return $strTmpFileName;
}
// -----------------------------------------------------------------
/**
*	�ƥ�ݥ��ե�������
*
*
*	@param  String   $strTmpFile	�ƥ�ݥ��ե�����̾
*
*
*	@access public
*/
// -----------------------------------------------------------------
function deleteTempFile( $strTmpFile )
{
	// �ƥ�ݥ��ե�����κ��
	if( !unlink( FILE_UPLOAD_TMPDIR . $strTmpFile ) )
	{
		fncOutputError( 1106, DEF_FATAL, "", TRUE, "", $objDB );
		return false;
	}

	return true;
}

///////////////////////////////////////////////
?>
