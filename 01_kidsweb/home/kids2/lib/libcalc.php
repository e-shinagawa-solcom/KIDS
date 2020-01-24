<?php
// ----------------------------------------------------------------------------
/**
*       計算関数ライブラリ
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
*		使用するソース、または関数群ソースにてその都度呼び出し（conf.incが呼び出されていること）
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------


// -----------------------------------------------------------------
/**
*	桁数に対する端数計算関数
*
*	指定された桁数の範囲に対して、設定された処理を計算する関数
*	$lngCalcMode ==> 	1	切捨て
*						2	切上げ
*						3	四捨五入
*
*	@param	Float		$curValue		計算対象値		Ex. 1234.5678
*	@param	Integer		$lngCalcMode	計算モード
*	@param	Integer		$lngDigitNumber	計算対象桁数	Ex. 通貨が日本円の場合など　0
*															通貨がUS＄の場合など    2
*	@return String      $curValue		計算後値
*	@access public
*/
// -----------------------------------------------------------------
function fncCalcDigit( $curValue, $lngCalcMode, $lngDigitNumber )
{
	// 引数の判断
	if ( !is_int($lngCalcMode) || !is_int($lngDigitNumber) )
	{
		return 0;
	}

	////////////////////////////////////
	// 計算対象値の判断
	////////////////////////////////////

	// 計算対象桁数を 0 桁になるように１０を掛ける（また割る）
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

	// 内部調整の為一度文字列に変換後小数点型に変換する
	$curCalcValue = floatval( strval( $curCalcValue ) );

	////////////////////////////////////
	// 処理
	////////////////////////////////////
	// 処理内容は切捨てである
	if ( $lngCalcMode == DEF_CALC_KIRISUTE )
	{
		$curReturnBaseValue = floor( $curCalcValue );
	}
	
	// 処理内容は切上げである
	else if ( $lngCalcMode == DEF_CALC_KIRIAGE )
	{
		$curReturnBaseValue = ceil( $curCalcValue );
	}
	
	// 処理内容は四捨五入である
	else if ( $lngCalcMode == DEF_CALC_SISHAGONYU )
	{
		$curReturnBaseValue = round( $curCalcValue );
	}

	////////////////////////////////////
	// 計算対象値の戻し
	////////////////////////////////////

	// 計算対象桁数を 元に戻すよう１０で割る（また掛ける）
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
