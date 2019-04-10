<?php
// ----------------------------------------------------------------------------
/**
*       �׻��ؿ��饤�֥��
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
*		���Ѥ��륽�������ޤ��ϴؿ����������ˤƤ������ٸƤӽФ���conf.inc���ƤӽФ���Ƥ��뤳�ȡ�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------


// -----------------------------------------------------------------
/**
*	������Ф���ü���׻��ؿ�
*
*	���ꤵ�줿������ϰϤ��Ф��ơ����ꤵ�줿������׻�����ؿ�
*	$lngCalcMode ==> 	1	�ڼΤ�
*						2	�ھ夲
*						3	�ͼθ���
*
*	@param	Float		$curValue		�׻��о���		Ex. 1234.5678
*	@param	Integer		$lngCalcMode	�׻��⡼��
*	@param	Integer		$lngDigitNumber	�׻��оݷ��	Ex. �̲ߤ����ܱߤξ��ʤɡ�0
*															�̲ߤ�US��ξ��ʤ�    2
*	@return String      $curValue		�׻�����
*	@access public
*/
// -----------------------------------------------------------------
function fncCalcDigit( $curValue, $lngCalcMode, $lngDigitNumber )
{
	// ������Ƚ��
	if ( !is_int($lngCalcMode) || !is_int($lngDigitNumber) )
	{
		return 0;
	}

	////////////////////////////////////
	// �׻��о��ͤ�Ƚ��
	////////////////////////////////////

	// �׻��оݷ���� 0 ��ˤʤ�褦�ˣ�����ݤ���ʤޤ�����
	if ( $lngDigitNumber > 0 )
	{
		$curCalcValue = floatval( $curValue ) * pow( 10, $lngDigitNumber );
	}
	else if ( $lngDigitNumber < 0 )
	{
		$curCalcValue = floatval( $curValue ) / pow( 10, $lngDigitNumber );
	}
	else
	{
		$curCalcValue = floatval( $curValue );
	}

	// ����Ĵ���ΰٰ���ʸ������Ѵ��微���������Ѵ�����
	$curCalcValue = floatval( strval( $curCalcValue ) );

	////////////////////////////////////
	// ����
	////////////////////////////////////
	// �������Ƥ��ڼΤƤǤ���
	if ( $lngCalcMode == DEF_CALC_KIRISUTE )
	{
		$curReturnBaseValue = floor( $curCalcValue );
	}
	
	// �������Ƥ��ھ夲�Ǥ���
	else if ( $lngCalcMode == DEF_CALC_KIRIAGE )
	{
		$curReturnBaseValue = ceil( $curCalcValue );
	}
	
	// �������Ƥϻͼθ����Ǥ���
	else if ( $lngCalcMode == DEF_CALC_SISHAGONYU )
	{
		$curReturnBaseValue = round( $curCalcValue );
	}

	////////////////////////////////////
	// �׻��о��ͤ��ᤷ
	////////////////////////////////////

	// �׻��оݷ���� �����᤹�褦�����ǳ��ʤޤ��ݤ����
	if ( $lngDigitNumber > 0 )
	{
		$curReturnValue = $curReturnBaseValue / pow( 10, $lngDigitNumber );
	}
	else if ( $lngDigitNumber < 0 )
	{
		$curReturnValue = $curReturnBaseValue * pow( 10, $lngDigitNumber );
	}
	else
	{
		$curReturnValue = $curReturnBaseValue;
	}

	return $curReturnValue;
}






?>
