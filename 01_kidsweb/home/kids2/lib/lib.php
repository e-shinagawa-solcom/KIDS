<?php
// ----------------------------------------------------------------------------
/**
*       関数ライブラリ
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
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// クラスの読み込み
require ( CLS_DB_FILE );
require ( CLS_AUTH_FILE );
require ( CLS_TEMPLATE_FILE );

// 報告エラー種類
error_reporting ( E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING );
// エラー関数の宣言
set_error_handler ( "fncError" );

$strBaseTemplate = "base.tmpl";
$aryConfigName = array ( "bodyonload", "header1", "header2", "header3" );



// -----------------------------------------------------------------
/**
*	変数チェック関数
*
*	機種依存文字、文字列チェック
*	例:日付      Boolean = fncCheckString( "2003/01/01", "null:date" )
*	例:数値0以上 Boolean = fncCheckString( 1920, "null:number(0,)" )
*
*	@param  String  $str          変換対象となるデータ
*	@param  String  $strCheckMode チェックモード[(制限)]
*	                              number(min,max)    : 数値
*	                              english(minlength,maxlength)    : 英字
*	                              numenglish(minlength,maxlength) : 英数字
*	                              ascii(minlength,maxlength)      : 英数字記号
*	                              ID(minlength,maxlength)         : ID
*	                              password(minlength,maxlength)   : パスワード
*	                              email(minlength,maxlength)      : メール
*	                              date(string)                    : YYYY/MM/DD
*	                              file(minlength,maxlength)       : ファイル
*	                              length(minlength,maxlength)     : 文字数
*	                              money(min,max)                  : 金額
*	                              IP(min,max,plural,asterisk)     : IPアドレス
*	                              color                           : 色
*	@return String                エラー情報
*	        Boolean               FALSE エラー無し
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckString( $str, $strCheckMode )
{
	// 半角カナ -> 全角カナ
	//$str = mb_convert_kana ( $str, "K" );

	// 前後の空白を削除
	//$str = mb_ereg_replace ( "^[　\s]+", "", $str );
	//$str = mb_ereg_replace ( "[　\s]+$", "", $str );

	// 不正記号チェック( 共通チェック項目 )
	//if ( mb_ereg ( "[!\"\$&\'()*<>?\[\]\\\\]", $str ) ) {
	//	errorExit ( "不正記号を使用しています。問題のある文字列 \"$str\"" );
	//}

	$aryCheck = explode ( ":", $strCheckMode );
	foreach ( $aryCheck as $strCheckType )
	{
		$lngRange[1] = "";
		$lngRange[2] = "";
		// 必須チェック
//		if ( $strCheckType == "null" && $str == "" && $str != 0 )
		if ( $strCheckType == "null" && ( $str === "" || !isset ( $str ) ) )
		{
			return "9001:$str";
		}

/*
		// 制御文字、機種依存文字チェック( 共通チェック項目 )
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

		// 数字チェック
		if ( ereg ( "^number", $strCheckType ) && $str != "" )
		{
			// スペース除去
			$str = mb_ereg_replace ( "\s", "", $str );

			// 最大最小指定の取得
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*)\)/", $strCheckType , $lngRange );

			// 数値チェック
			if ( !ereg ( "^-?[0-9]*\.?[0-9]+$", $str ) || ereg ( "^(\.|-\.)", $str ) ) {
				return "9003:$str";
			}

			// 最小値チェック
			if ( $lngRange[1] != "" && $str < $lngRange[1] ) {
				return "9004:$str";
			}

			// 最大値チェック
			if ( $lngRange[2] != "" && $str > $lngRange[2] ) {
				return "9005:$str";
			}

		}
*/

		// 数字チェック(エラーメッセージ指定可能チェックテスト運用)
		if ( preg_match ( "/^number/", $strCheckType ) && $str != "" )
		{
			// スペース除去
			$str = mb_ereg_replace ( "[\s,]", "", $str );

			// 最大最小指定、エラーメッセージの取得
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*),?(.*?)?\)/", $strCheckType , $lngRange );

			// 第3引数があった場合、それをエラーメッセージとする
			if ( $lngRange[3] )
			{
				$lngRange[3] = "ORIGINAL" . $lngRange[3];
			}
			else
			{
				$lngRange[3] = $str;
			}

			// 数値チェック
			if ( !preg_match ( "/^-?[0-9]*\.?[0-9]+$/", $str ) || preg_match ( "/^(\.|-\.)/", $str ) ) {
				return "9003:$lngRange[3]";
			}

			// 数値型とする
			settype ( $str, "float" );

			// 最小値チェック
			if ( $lngRange[1] != "" && $str < $lngRange[1] ) {
				return "9004:$lngRange[3]";
			}

			// 最大値チェック
			if ( $lngRange[2] != "" && $str > $lngRange[2] ) {
				return "9005:$lngRange[3]";
			}

		}
		// 英字チェック
		elseif ( preg_match ( "/^english/", $strCheckType ) && $str != "" )
		{
			// スペース除去
			$str = mb_ereg_replace ( "\s", "", $str );

			// 文字列チェック
			if ( !mb_ereg ( "^[a-zA-Z]+$", $str ) )
			{
				return "9008:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// 英数字チェック
		elseif ( preg_match ( "/^numenglish/", $strCheckType ) && $str != "" )
		{
			// スペース除去
			$str = mb_ereg_replace ( "\s", "", $str );

			// 文字列チェック
			if ( !mb_ereg ( "^[a-zA-Z0-9]+$", $str ) )
			{
				return "9009:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// 英数字記号チェック
		elseif ( preg_match ( "/^ascii/", $strCheckType ) && $str != "" )
		{
			// スペース除去
			$str = mb_ereg_replace ( "\s", "", $str );
			if ( !mb_ereg ( "^[0-9a-zA-Z\"#%&\+-\/=^_`\{\}\|~@\.:]+$", $str ) ) {
				return "9010:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// IDチェック
		elseif ( preg_match ( "/^ID/", $strCheckType ) && $str != "" )
		{
			if ( !mb_ereg ( "^[0-9a-zA-Z\"#%\+-\/=^_`\{\}\|~@\.]+$", $str ) || strlen ( $str ) < 3 || strlen ( $str ) > 64 ) {
				return "9011:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// パスワードチェック
		elseif ( preg_match ( "/^password/", $strCheckType ) && $str != "" )
		{
			if ( !mb_ereg ( "^[0-9a-zA-Z]+$", $str ) || strlen ( $str ) > 64 ) {
				return "9012:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// メールアドレスチェック
		elseif ( preg_match ( "/^e?mail/", $strCheckType ) && $str != "" )
		{
			if ( !mb_ereg ( "^[0-9a-zA-Z!\"#\$%&\'\(\)\=\~\|\`\{\+\*\}\<\>\?\_\-\^\@\[\;\:\]\,\.\/\\\\]+$", $str ) || !mb_ereg ( "^[^@.\-][^@]*@[^@.\-][^@]*\..+[a-z]$", $str ) ) {
				return "9013:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// 日付チェック
		if ( preg_match ( "/^date/", $strCheckType ) && $str != "" )
		{
			if ( !preg_match ( "/^[0-9\-\/]+$/", $str ) )
			{
				return "9014:$str";
			}

			list ( $year, $mon, $date ) = explode ( "[-\/]", $str );

			// 日が未記入な場合、1日に強制設定
			if ( !$date )
			{
				$date = 1;
			}

			// 月が未記入な場合、1月に強制設定
			if ( !$mon )
			{
				$mon = 1;
			}

			// 日付チェック
			if ( !checkdate ( $mon, $date, $year) || $year < 1601 ) {
				return "9014:$str";
			}
		}
		// ファイル
		elseif ( preg_match ( "/^file/", $strCheckType ) && $str != "" )
		{
			if ( mb_ereg ( "^\.\.", $str ) || !mb_ereg ( "^[0-9a-zA-Z\"#%\+-\/=^_`\{\}\|~@\.:]+$", $str ) ) {
				return "9016:$str";
			}

			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// 文字数チェック
		elseif ( preg_match ( "/^length/", $strCheckType ) && $str != "" )
		{
			// 文字数チェック
			$strError = fncCheckStringLength( $strCheckType, $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// 金額チェック
		elseif ( preg_match ( "/^money/", $strCheckType ) && $str != "" )
		{
			// スペースとカンマ、\、$ 除去
			$str = mb_ereg_replace ( "[\s,]", "", $str );
			$str = mb_ereg_replace ( "^[\\\\$]", "", $str );

			// 最大最小指定の取得
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*)\)/", $strCheckType , $lngRange );

			// 数値チェック
			if ( !preg_match ( "/^-?[0-9]*\.?[0-9]+$/", $str ) || preg_match ( "/^(\.|-\.)/", $str ) ) {
				return "9017:$str";
			}

			// 最小値チェック
			if ( $lngRange[1] != "" && $str < $lngRange[1] ) {
				return "9018:$str";
			}

			// 最大値チェック
			if ( $lngRange[2] != "" && $str > $lngRange[2] ) {
				return "9019:$str";
			}

		}
		// IPアドレスチェック
		elseif ( preg_match ( "/^IP/", $strCheckType ) && $str != "" )
		{
			// スペース除去
			$str = mb_ereg_replace ( " ", "", $str );

			// 最大最小、複数指定許可、アスタリスク許可指定の取得
			preg_match ( "/\((-?[0-9]*\.?[0-9]*),(-?[0-9]*\.?[0-9]*),(\'.?\')/", $strCheckType , $lngRange );
			$strCheckType = "length($lngRange[1],$lngRange[2])";

			// 複数制定を許可している場合、IPアドレスを分解する
			if ( $lngRange[3] != "''" )
			{
				$lngRange[3] = str_replace ( "'", "", $lngRange[3] );
				$aryStr = explode ( $lngRange[3], $str );
			}
			else
			{
				$aryStr[0] = $str;
			}

			// チェック
			$count = count ( $aryStr );
			for ( $i = 0; $i < $count; $i++ )
			{
				if ( $aryStr[$i] && !preg_match ( "/^[0-9\*\.]+$/", $aryStr[$i] ) || preg_match ( "/(\.\.|\*\*)/", $aryStr[$i] ) )
				{
					return "9017:$str";
				}
			}

			// 文字数チェック
			$strError = fncCheckStringLength( "($lngRange[1],$lngRange[2])", $str );
			if ( $strError )
			{
				return $strError;
			}
		}
		// 色指定チェック
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
*	連想配列で渡されたデータをすべてチェックする関数
*
*	変数のチェックを実行する
*
*	@param  Array $aryData   チェック対象データ(変数名をキーとする連想配列)
*	@param  Array $aryCheck  チェック内容(変数名をキーとする連想配列)
*	@return Array $aryResult チェック真偽(変数名をキーとする連想配列)
*	@access public
*/
// -----------------------------------------------------------------
function fncAllCheck( $aryData, $aryCheck )
{
	// 変数名となるキーを取得
	$aryKey = array_keys( $aryCheck );

	// キーの数だけチェック
	foreach ( $aryKey as $strKey )
	{
		// $aryData[$strKey]  : チェック対象データ
		// $aryCheck[$strKey] : チェック内容(数値、英数字、アスキー等)
		$aryResult[$strKey . "_Error"] = fncCheckString( $aryData[$strKey], $aryCheck[$strKey] );
	}
	return $aryResult;
}



// -----------------------------------------------------------------
/**
*	文字列長チェック関数
*
*	文字列の長さをチェックする
*
*	@param  String $strCheckType チェック内容
*	@param  Atring $str          チェック文字列
*	@return Boolean
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckStringLength( $strCheckType, $str )
{
	// 文字数の取得
	if ( !preg_match ( "/\(([0-9]*),([0-9]*)\)/", $strCheckType , $lngRange ) )
	{
		return FALSE;
	}

	// 文字数チェック
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
*	文字列チェックエラー出力関数
*
*	連想配列で渡されたデータをすべてチェックする関数で取得した結果から
*	文字列チェックエラーを出力する
*
*	@param  Array  $aryResult 文字列チェックエラー結果
*	@param  Object $objDB     DBオブジェクト
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
*	クエリ実行関数
*
*	クエリ実行する
*
*	@param  String $strQuery クエリ
*	@param  Object $objDB    DBオブジェクト
*	@return $lngResultID     結果ID
*	        $lngResultNum    行数
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
*	共通機能マスタデータ取得関数
*
*	共通機能マスタから指定された種類の値を取得する
*
*	@param  String $strClass 種類
*	@param  Object $objDB    DBオブジェクト
*	@return $strVAlue        値
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
		fncOutputError ( 9051, DEF_ERROR, "共通機能マスタ", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strValue  = strtolower ( "strValue" );
	$strValue = $objResult->$strValue;
	$objDB->freeResult( $lngResultID );

	return $strValue;
}



// -----------------------------------------------------------------
/**
*	管理者機能マスタデータ取得関数
*
*	管理者機能マスタから指定された種類の値を取得する
*
*	@param  String $strClass 種類
*	@param  Object $objDB    DBオブジェクト
*	@return $strVAlue        値
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
		fncOutputError ( 9051, DEF_ERROR, "管理者機能マスタ", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strValue  = strtolower ( strValue );
	$strValue = $objResult->$strValue;
	$objDB->freeResult( $lngResultID );

	return $strValue;
}



// -----------------------------------------------------------------
/**
*	シーケンス関数
*
*	現在のシーケンスの取得(インクリメント、UPDATE無し)
*
*	@param  String $strSequenceName 種類
*	@param  Object $objDB           DBオブジェクト
*	@return Long   $lngSequence     番号
*	@access public
*/
// -----------------------------------------------------------------
function fncIsSequence( $strSequenceName, $objDB )
{
	// シーケンス番号取得
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE lower ( ltrim( strSequenceName, ' ' ) ) = lower ( '$strSequenceName' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "シーケンステーブル", TRUE, "", $objDB );
	}
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	$lngSequence = $objResult->lngsequence;

	$objDB->freeResult( $lngResultID );

	return $lngSequence;
}



// -----------------------------------------------------------------
/**
*	シーケンス関数
*
*	シーケンスのインクリメントおよび取得
*
*	@param  String $strSequenceName 種類
*	@param  Object $objDB           DBオブジェクト
*	@return Long   $lngSequence     番号
*	@access public
*/
// -----------------------------------------------------------------
function fncGetSequence( $strSequenceName, $objDB )
{

	// トランザクション開始
	//$objDB->transactionBegin();

	// ロック開始
	//$strQuery = "LOCK TABLE t_Sequence IN EXCLUSIVE MODE";
	//list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// シーケンス番号取得
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE lower ( ltrim( strSequenceName, ' ' ) ) = lower ( '$strSequenceName' ) FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName' FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE strSequenceName = '$strSequenceName'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "シーケンステーブル", TRUE, "", $objDB );
	}
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	// インクリメント
	$lngSequence = $objResult->lngsequence + 1;

	$objDB->freeResult( $lngResultID );

	$strQuery = "UPDATE t_Sequence SET lngSequence = $lngSequence WHERE lower ( ltrim( strSequenceName, ' ' ) ) = lower ( '$strSequenceName' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	//$objDB->transactionCommit();

	return $lngSequence;
}



// -----------------------------------------------------------------
/**
*	日付シーケンス関数
*
*	日付シーケンスのインクリメントおよび取得
*
*	@param  String $year            年
*	@param  String $month           月
*	@param  String $strSequenceName 種類
*	@param  Object $objDB           DBオブジェクト
*	@return Long   $lngSequence     番号(9999超えでFALSE)
*	@access public
*/
// -----------------------------------------------------------------
function fncGetDateSequence( $year, $month, $strSequenceName, $objDB )
{
	// 年数処理(1000年以上だったら下2桁に成形)
	if ( $year > 999 )
	{
		$year %= 100;
	}
	// 年数チェック
	if ( $year < 0 || $year > 99 || $month > 12 || $month < 1 )
	{
		fncOutputError ( 9051, DEF_ERROR, "日付シーケンスの年月設定に問題があります。", TRUE, "", $objDB );
	}

	// シーケンス名の生成(YYMMXXX)
	$strSequenceName = sprintf( "$strSequenceName.%02d%02d", $year, $month );

	// トランザクション開始
	//$objDB->transactionBegin();

	// ロック開始
	//$strQuery = "LOCK TABLE t_Sequence IN EXCLUSIVE MODE";
	//list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// シーケンス番号取得
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = lower('$strSequenceName') FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// レコードがなければ指定シーケンス名にてレコード追加
	if ( !$lngResultNum )
	{
		$strQuery = "INSERT INTO t_Sequence VALUES ( lower('$strSequenceName'), 1 )";

		if ( !$objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "シーケンステーブル", TRUE, "", $objDB );
		}

		$lngSequence = 1;
	}

	// レコードがあれば指定シーケンスをインクリメント
	else
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// インクリメント
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
*	金型番号取得関数
*
*	金型番号('製品コード'-dd)のインクリメントおよび取得
*
*	@param  String $strProductCode      製品コード
*	@param  String $lngStockSubjectCode 仕入科目コード
*	@param  String $lngStockItemCode    仕入部品コード
*	@param  Object $objDB               DBオブジェクト
*	@return Long   $strNoldNo           金型番号('製品コード'-dd)
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMoldNo( $strProductCode, $lngStockSubjectCode, $lngStockItemCode, $objDB )
{
// 2004.05.31 suzukaze update start
	// 仕入科目コード != 433 または 仕入部品コード != 1
	// 仕入科目コード != 431 または 仕入部品コード != 8
	// または 製品コード > 99999 の場合、return FALSE
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

	// シーケンス名の生成(YYMMXXX)
	$strSequenceName = sprintf( "m_OrderDetail.strMoldNo.%05d", $strProductCode );
	// トランザクション開始
	//$objDB->transactionBegin();

	// ロック開始
	//$strQuery = "LOCK TABLE t_Sequence IN EXCLUSIVE MODE";
	//list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// シーケンス番号取得
	$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName' FOR UPDATE";
	//$strQuery = "SELECT lngSequence FROM t_Sequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// レコードがなければ指定シーケンス名にてレコード追加
	if ( !$lngResultNum )
	{
		$strQuery = "INSERT INTO t_Sequence VALUES ( '$strSequenceName', 1 )";

		if ( !$objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "シーケンステーブル", TRUE, "", $objDB );
		}

		$lngSequence = 1;
	}

	// レコードがあれば指定シーケンスをインクリメント
	else
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// インクリメント
		$lngSequence = $objResult->lngsequence + 1;

		// 195(FF)を超えたらエラー
		if ( $lngSequence > 195 )
		{
			return FALSE;
		}

		$objDB->freeResult( $lngResultID );
		$strQuery = "UPDATE t_Sequence SET lngSequence = $lngSequence WHERE ltrim( strSequenceName, ' ' ) = '$strSequenceName'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	}

	//$objDB->transactionCommit();

	// 100を超えていた場合、16進変換(100='a0'とする)
	if ( $lngSequence > 99 )
	{
		$lngSequence = sprintf ( "%05d", $strProductCode ) . "-" . dechex ( $lngSequence + 60 );
	}

	// 100未満の場合、0埋2桁フォーマット
	else
	{
		$lngSequence = sprintf ( "%05d-%02d", $strProductCode, $lngSequence );
	}

	return $lngSequence;
}



////////////////////////////////////////////////////////////////////
// HTML出力関数
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	マスター・プルダウンリスト生成関数
*
*	マスターテーブルからプルダウンメニュー作成
*
*	@param  String $strTable            テーブル名
*	@param  String $strValueFieldName   valueに入るフィールド名
*	@param  String $strDisplayFieldName 表示されるフィールド名
*	@param  Long $lngDefaultValue       プルダウンメニューの初期選択値
*	@param  String $strQueryWhere       条件(SQL)WHEREから書き始め
*	@param  Object $objDB               DBオブジェクト
*	@return $strHtml                    プルダウンメニューHTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetPulldown( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
{
	// 全ページIDのリストを取得
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";

	return fncGetPulldownQueryExec($strQuery, $lngDefaultValue, $objDB);
}
// -----------------------------------------------------------------
/**
*	マスター・プルダウンリスト生成関数（ソートキー指定版）
*
*	マスターテーブルからプルダウンメニュー作成
*
*	@param  String $strTable            テーブル名
*	@param  String $strValueFieldName   valueに入るフィールド名
*	@param  String $strDisplayFieldName 表示されるフィールド名
*	@param  Long $lngDefaultValue       プルダウンメニューの初期選択値
*	@param  String $strQueryWhere       条件(SQL)WHEREから書き始め
*	@param	Long   $lngSortKey			ソートキー
*	@param  Object $objDB               DBオブジェクト
*	@return $strHtml                    プルダウンメニューHTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetPulldownSort( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $lngSortKey, $objDB )
{
	// 全ページIDのリストを取得
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $lngSortKey";

	return fncGetPulldownQueryExec($strQuery, $lngDefaultValue, $objDB);
}


// -----------------------------------------------------------------
/**
*	マスター・プルダウンリスト生成関数（集約）
*
*	@lngMaxFieldsCount カラムの最大数を指定
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

	// <OPTION>生成
	for ( $count = 0; $count < $lngResultNum; $count++ ) {
		$aryResult = $objDB->fetchArray( $lngResultID, $count );

		$strDisplayValue = "";
		for ( $i = 1; $i < $lngFieldsCount; $i++ )
		{
			$strDisplayValue .= fncHTMLSpecialChars( $aryResult[$i] ) . "\t";
		}

		// HTML出力
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
*	マスター・マルチプルリスト生成関数
*
*	マスターテーブルからマルチプルリストメニュー作成
*
*	@param  String $strTable            テーブル名
*	@param  String $strValueFieldName   valueに入るフィールド名
*	@param  String $strDisplayFieldName 表示されるフィールド名
*	@param  Long $lngDefaultValue       プルダウンメニューの初期選択値
*	@param  String $strQueryWhere       条件(SQL)WHEREから書き始め
*	@param  Object $objDB               DBオブジェクト
*	@return $strHtml                    プルダウンメニューHTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMultiplePulldown( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
{
	// 全ページIDのリストを取得
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	// <OPTION>生成
	for ( $count = 0; $count < $lngResultNum; $count++ ) {
		$aryResult = $objDB->fetchArray( $lngResultID, $count );

		$strDisplayValue = "";
		for ( $i = 1; $i < $lngFieldsCount; $i++ )
		{
			$strDisplayValue .= fncHTMLSpecialChars( $aryResult[$i] ) . "\t";
		}

		// HTML出力
		$strHtml = "<OPTION VALUE=\"$aryResult[0]\">$strDisplayValue</OPTION>\n";
	}

	$objDB->freeResult( $lngResultID );
	return $strHtml;
}
// 2004.04.14 suzukaze update end


// -----------------------------------------------------------------
/**
*	マスター・チェックボックス生成関数
*
*	マスターテーブルからマルチプルリストメニュー作成
*
*	@param  String	$strTable            テーブル名
*	@param  String	$strValueFieldName   valueに入るフィールド名
*	@param  String	$strDisplayFieldName 表示されるフィールド名
*	@param  Long	$strObjectName       チェックボックスオブジェクトの名前
*	@param  String	$strQueryWhere       条件(SQL)WHEREから書き始め
*	@param  Object	$objDB               DBオブジェクト
*	@return $strHtml                    プルダウンメニューHTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetCheckBoxObject( $strTable, $strValueFieldName, $strDisplayFieldName, $strObjectName, $strQueryWhere, $objDB )
{
	// 全ページIDのリストを取得
	$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	// <OPTION>生成
	for ( $count = 0; $count < $lngResultNum; $count++ ) {
		$aryResult = $objDB->fetchArray( $lngResultID, $count );

		$strDisplayValue = "";
		for ( $i = 1; $i < $lngFieldsCount; $i++ )
		{
			$strDisplayValue .= fncHTMLSpecialChars( $aryResult[$i] ) . "　";
		}

		// HTML出力
		$strHtml .= '<input class="CheckBox14" type="checkbox" name="'.$strObjectName.'" value="'.$aryResult[0].'">'
		.$strDisplayValue."\n";
	}

	$objDB->freeResult( $lngResultID );
	return $strHtml;
}


// -----------------------------------------------------------------
/**
*	マスターデータ取得関数
*
*	マスターから値取得
*
*	@param  String $strTable            テーブル名
*	@param  String $strValueFieldName   valueに入るフィールド名
*	@param  String $strDisplayFieldName 表示されるフィールド名
*	@param  mixed  $defaultValue        選択値('Array'の場合、コードをキーとする連想配列を返す特別仕様)
*	@param  String $strQueryWhere       条件(valueに入る値と同じ)
*	@param  Object $objDB               DBオブジェクト
*	@return $aryResult[0]               マスターデータ
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMasterValue( $strTable, $strKeyFieldName, $strDisplayFieldName, $defaultValue, $strQueryWhere, $objDB )
{
	// WHERE句内のカラムが文字型だった場合の処理(「:str」があった場合''で囲む)
	list ( $defaultValue, $type ) = explode ( ":", $defaultValue );
	if ( $type == 'str' )
	{
		$defaultValue = "'$defaultValue'";
	}

	// 全ページIDのリストを取得
	$strQuery = "SELECT $strKeyFieldName, $strDisplayFieldName FROM $strTable";

	// 配列取得が目的でない場合、キーと値を指定
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

	// 配列取得が目的でない場合、値を返す
	if ( $defaultValue != "Array" )
	{
		return fncHTMLSpecialChars( $aryResult[1] );
	}

	// 配列取得が目的の場合、コードをキーとする連想配列を返す
	else
	{
		return $aryResultValue;
	}

}

// -----------------------------------------------------------------
/**
*	メール文面取得・生成関数
*
*	マスターから雛型取得、引数による置き換え
*
*	@param  Long $lngFunctionCode 機能コード
*	@param  Array  $aryData       置き換え文字列
*	@param  Object $objDB         DBオブジェクト
*	@return String $strSubject    メールタイトル
*	        String $strBody       メール本文
*	@access public
*/
// -----------------------------------------------------------------
function fncGetMailMessage( $lngFunctionCode, $aryData, $objDB )
{
	// メール雛型取得
	$strQuery = "SELECT strSubject, strBody FROM m_Mailform WHERE lngFunctionCode = $lngFunctionCode AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		fncOutputError ( 9051, DEF_WARNING, "指定のメールテンプレートがありませんでした。", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	$objDB->freeResult( $lngResultID );

	// テンプレートオブジェクト生成
	$objTemplate = new clsTemplate();
	$objTemplate->strTemplate = $objResult->strbody;

	// 置き換え
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// 文字コード変換(EUC->JIS)
	$objTemplate->strTemplate = mb_convert_encoding( $objTemplate->strTemplate, "JIS", "EUC-JP" );
	$objResult->strsubject    = mb_convert_encoding( $objResult->strsubject, "JIS", "EUC-JP" );
//	$objResult->strsubject    = mb_encode_mimeheader ( $objResult->strsubject , "iso-2022-jp", "B" );

	return array ( $objResult->strsubject, $objTemplate->strTemplate );
}



// -----------------------------------------------------------------
/**
*	テンプレート関数
*
*	テンプレートからHTMLを生成する
*
*	@param  String $strTemplatePath テンプレートファイルパス
*	@param  Array  $aryPost         POSTデータ
*	@param  Object $objAuth         認証オブジェクト
*	@return $strTemplate            結果HTML
*	@access public
*/
// -----------------------------------------------------------------
function fncGetReplacedHtml( $strTemplatePath, $aryPost, $objAuth )
{
	global $strBaseTemplate, $aryConfigName;

	// パーツテンプレート生成
	$objContentsTemplate = new clsTemplate();
	$objContentsTemplate->getTemplate( $strTemplatePath );

	// 特殊タグデータ取得
	$aryBaseInsert = $objContentsTemplate->getConfig( $aryConfigName );

	// ユーザーデータ取得
	$aryBaseInsert["strUserID"]             = $objAuth->UserID;
	$aryBaseInsert["strUserFullName"]       = $objAuth->UserFullName;
	$aryBaseInsert["strGroupDisplayCode"]   = $objAuth->GroupDisplayCode;
	$aryBaseInsert["strGroupDisplayName"]   = $objAuth->GroupDisplayName;
	$aryBaseInsert["strAuthorityGroupName"] = $objAuth->AuthorityGroupName;
	$aryBaseInsert["strSessionID"]          = $objAuth->SessionID;


	$aryBaseInsert["strUserID"] = trim( $aryBaseInsert["strUserID"] );


	// ページデータ取得
	$aryBaseInsert["lngFunctionCode"]       = $aryPost["lngFunctionCode"];

	if ( $objAuth->UserImageFileName )
	{
		$aryBaseInsert["strUserImageFileName"]  = USER_IMAGE_URL . $objAuth->UserImageFileName;
	}
	else
	{
		$aryBaseInsert["strUserImageFileName"] = USER_IMAGE_URL . "default.gif";
	}

	// 編集画面の設定(修正画面だった場合のみ特殊処理)
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

	// クッキーから言語コードを取得し、置き換える
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

	// ベーステンプレート取得
	$objBaseTemplate = new clsTemplate();
	$objBaseTemplate->getTemplate( $strBaseTemplate );

	// エラーメッセージ埋め込み
	$aryBaseInsert["strErrorMessage"] = $aryPost["strErrorMessage"];
	// ベーステンプレート置き換え
	$objBaseTemplate->replace( $aryBaseInsert );

	$objBaseTemplate->complete();

	// header("Content-type: text/plain; charset=EUC-JP");


//require( LIB_DEBUGFILE );
//fncDebug( 'tmpl.txt', $objBaseTemplate->strTemplate, __FILE__, __LINE__);


	return $objBaseTemplate->strTemplate;
}

// -----------------------------------------------------------------
/**
 *	テンプレート関数
 *
 *	テンプレートからHTMLを生成する
 *
 *	@param  String $strBaseTemplatePath ベーステンプレートファイルパス
 *	@param  String $strTemplatePath テンプレートファイルパス
 *	@param  Array  $aryPost         POSTデータ
 *	@param  Object $objAuth         認証オブジェクト
 *	@return $strTemplate            結果HTML
 *	@access public
 */
// -----------------------------------------------------------------
function fncGetReplacedHtmlWithBase($strBaseTemplate, $strTemplatePath, $aryPost, $objAuth )
{
	global $aryConfigName;

	// パーツテンプレート生成
	$objContentsTemplate = new clsTemplate();
	$objContentsTemplate->getTemplate( $strTemplatePath );

	// 特殊タグデータ取得
	$aryBaseInsert = $objContentsTemplate->getConfig( $aryConfigName );

	// ユーザーデータ取得
	$aryBaseInsert["strUserID"]             = $objAuth->UserID;
	$aryBaseInsert["strUserFullName"]       = $objAuth->UserFullName;
	$aryBaseInsert["strGroupDisplayCode"]   = $objAuth->GroupDisplayCode;
	$aryBaseInsert["strGroupDisplayName"]   = $objAuth->GroupDisplayName;
	$aryBaseInsert["strAuthorityGroupName"] = $objAuth->AuthorityGroupName;
	$aryBaseInsert["strSessionID"]          = $objAuth->SessionID;

	$aryBaseInsert["strUserID"] = trim( $aryBaseInsert["strUserID"] );
	
	$aryBaseInsert["HeaderTitleImage"] = '/img/type01/'.explode('/',$strTemplatePath)[0].'/title_ja.gif';

	// ページデータ取得
	$aryBaseInsert["lngFunctionCode"]       = $aryPost["lngFunctionCode"];

	if ( $objAuth->UserImageFileName )
	{
		$aryBaseInsert["strUserImageFileName"]  = USER_IMAGE_URL . $objAuth->UserImageFileName;
	}
	else
	{
		$aryBaseInsert["strUserImageFileName"] = USER_IMAGE_URL . "default.gif";
	}

	// 編集画面の設定(修正画面だった場合のみ特殊処理)
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

	// クッキーから言語コードを取得し、置き換える
	if ( !$_COOKIE["lngLanguageCode"] )
	{
		$_COOKIE["lngLanguageCode"] = "0";
	}

	if ( $aryPost )
	{
		$objContentsTemplate->replace( $aryPost );
	}
	$aryBaseInsert["BODY"] = $objContentsTemplate->strTemplate;

	// ベーステンプレート取得
	$objBaseTemplate = new clsTemplate();
	$objBaseTemplate->getTemplate( $strBaseTemplate );

	// エラーメッセージ埋め込み
	$aryBaseInsert["strErrorMessage"] = $aryPost["strErrorMessage"];

	// ベーステンプレート置き換え
	$objBaseTemplate->replaceForMold($aryBaseInsert);

	$objBaseTemplate->complete();

	return $objBaseTemplate->strTemplate;
}


// -----------------------------------------------------------------
/**
*	HTML表示に問題のない文字列を返す関数
*
*	HTML表示に問題のない文字列を返す
*
*	@param  String $strValue 変換する文字列
*	@return String $strValue 変換された文字列
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
*	HTML表示に問題のない文字列を配列で返す関数
*
*	HTML表示に問題のない文字列を配列で返す
*
*	@param  Array $aryData 変換する連想配列
*	@return Array $aryData 変換された連想配列
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
*	配列出力関数
*
*	配列のキーと値のテーブルまたはフォーム(HIDDEN)生成
*
*	@param  Array  $aryData        調べたいデータ配列
*	@param  String $strExpressMode TABLE  : テーブルのみ
*                                  HIDDEN : HIDDENのみ
*	                               MIX    : 両方
*	@return String $strHtmlTable   HTML TABLE
*	@access public
*/
// -----------------------------------------------------------------
function getArrayTable( $aryData, $strExpressMode )
{
	$strTable = "<table border>\n";

	// 配列の数だけループ
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
*	エラー項目表示処理関数
*
*	エラー項目表示処理(visibility:visible; or hidden を返す)
*
*	@param  Array   $aryData        調べたいデータ配列
*	@param  Array   $aryCheckResult エラー配列
*	@param  Object  $objDB          DBオブジェクト
*	@return Array   $aryData        HTML TABLE
*	        boolean $bytErrorFlag   エラーの存在フラグ
*	@access public
*/
// -----------------------------------------------------------------
function getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB )
{
	// 言語判定(取得するカラムの設定)
	$value = "strmessagecontentenglish";
	if ( $_COOKIE["lngLanguageCode"] == 1 )
	{
		$value = "strmessagecontent";
	}

	// エラーメッセージ取得クエリ生成
	$strQuery = "SELECT lngMessageCode, $value FROM m_Message";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// エラーコードをキーとする連想配列生成
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$objResult->$value = preg_replace ( "/_%.+?%_/", "", $objResult->$value );
		$aryMessage[$objResult->lngmessagecode] = $objResult->$value;
	}
	$objDB->freeResult( $lngResultID );

	// エラー判定
	$aryCheckResultKeys = array_keys ( $aryCheckResult );
	foreach ( $aryCheckResultKeys as $key )
	{
		// チェック結果に値が存在した場合、エラー表示文字列設定
		if ( $aryCheckResult[$key] )
		{
			list ( $lngErrorCode, $strErrorMessage ) = explode ( ":", $aryCheckResult[$key] );
			// 先頭に文字列「ORIGINAL」があった場合、その先にある文字列を
			// エラーメッセージとする
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
// 認証・セッション関数
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	セッション関数(確認)
*
*	セッションの確認
*
*	@param  String  $strSessionID   セッションID
*	@param  Object  $objAuth        認証オブジェクト
*	@param  Object  $objDB          DBオブジェクト
*	@return Object  $objAuth          認証オブジェクト
*	@access public
*/
// -----------------------------------------------------------------
function fncIsSession( $strSessionID, $objAuth, $objDB )
{
	if ( !$strSessionID )
	{
		fncOutputError ( 9052, DEF_ERROR, "セッション情報が渡されていません。", TRUE, "", $objDB );
	}

	// セッション確認
	if ( !$objAuth->isLogin( $strSessionID, $objDB ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "タイムアウトになりました。再ログインして下さい。", TRUE, "/login/login.php", $objDB );
	}
	return $objAuth;
}



// -----------------------------------------------------------------
/**
*	権限チェック関数
*
*	権限の確認
*
*	@param  Long $lngFunctionCode  機能コード
*	@param  Object $objAuth        認証オブジェクト
*	@return Boolean                権限の有無
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckAuthority( $lngFunctionCode, $objAuth )
{
	// 権限チェック
	if ( $objAuth->FunctionCode[$lngFunctionCode] )
	{
		return TRUE;
	}
	return FALSE;

}



// -----------------------------------------------------------------
/**
*	ログイン関数
*
*	ログイン処理
*
*	@param  String $strUserID       ID
*	@param  String $strPasswordHash パスワード
*	@param  Object $objAuth        認証オブジェクト
*	@param  Object $objDB           DBオブジェクト
*	@return Object  $objDB          認証オブジェクト
*	@access public
*/
// -----------------------------------------------------------------
function fncLogin( $strUserID, $strPasswordHash, $objAuth, $objDB )
{
	// 認証チェック
	if ( !$objAuth->login( $strUserID, $strPasswordHash, $objDB ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "IDまたはパスワードが間違っています。", TRUE, "", $objDB );
	}
	return $objAuth;
}



// -----------------------------------------------------------------
/**
*	ログアウト関数
*
*	ログアウト処理
*
*	@param  String $SessionID セッションID
*	@param  Object $objDB     DBオブジェクト
*	@return Boolean           ログアウトの成否
*	@access public
*/
// -----------------------------------------------------------------
function fncLogout( $strSessionID, $objDB )
{
	$objAuth = new clsAuth();
	if ( !$objAuth->logout( $strSessionID, $objDB ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "ログアウトに失敗しました。", TRUE, "", $objDB );
	}
	return TRUE;
}



////////////////////////////////////////////////////////////////////
// その他の関数
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	配列生成関数
*
*	特定のフォーマットで1行に保存された文字列を連想配列に成形して返す
*
*	@param  String $strData      分割元文字列
*	@param  String $strAmpersand データ分割文字列
*	@param  String $strEqual     キーと値の分割文字
*	@return Array  $aryData      分割後配列
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
// エラー出力関数
////////////////////////////////////////////////////////////////////
// -----------------------------------------------------------------
/**
*	システムエラー関数
*
*	システムエラー出力
*
*	@param  Long $errno     エラー番号
*	@param  String $errstr  エラー文
*	@param  String $errfile ファイル名
*	@param  Long $errline   エラー行
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
*	エラー出力関数
*
*	エラー出力
*
*	@param  Long  $lngErrorCode     エラーコード（m_Messageマスタのメッセージコード）
*	@param  Long  $lngErrorClass    エラー種類　conf.incにて定義のあるエラーレベル
*										DEF_ANNOUNCE	システムアナウンス
*										DEF_WARNING	注意レベル（入力ミスなど）
*										DEF_ERROR	エラーレベル
*										DEF_FATAL	システムエラーレベル
*	@param  Array $aryErrorMessage  エラー置換文字（配列）
*	@param  Boolean $bytOutputFlag  出力フラグ
*							FALSE・・・関数の戻り値のStringにてエラーメッセージを返す
*							TRUE・・・エラーメッセージ画面の表示
*	@param  Long  $strReturnPath    「戻る」ボタンのURL(前ページの場合は不要)
*	@param  Object  $objDB          DBオブジェクト
*	@return String $strErrorMessage エラーメッセージ
*	@access public
*/
// -----------------------------------------------------------------
function fncOutputError ( $lngErrorCode, $lngErrorClass, $aryErrorMessage, $bytOutputFlag, $strReturnPath, $objDB )
{
	// DBの接続状態のチェック
	if ( !$objDB->isOpen() )
	{
		$strErrorMessage = "DB接続エラー";
		return $strErrorMessage;
	}

	// メッセージの取得
	$strQuery = "SELECT strMessageContent from m_Message WHERE lngMessageCode = " . $lngErrorCode;
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	$strErrorMessage = $objResult->strmessagecontent;

//fncDebug( 'upload_parse_confirm_0a2.txt', $objDB->InputEncoding, __FILE__, __LINE__);
//fncDebug( 'upload_parse_confirm_0a3.txt', $strErrorMessage, __FILE__, __LINE__);


	$objDB->freeResult( $lngResultID );

    

	// メッセージ文字列の置換
	if (!is_array($aryErrorMessage))
	{
		// 文字エンコーディングを検出する
		$encodeType = mb_detect_encoding($aryErrorMessage);
		
		//　DBととエラー置換文字のエンコーディングが異なる場合はエンコーディングを行う
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
			// 文字エンコーディングを検出する
			$encodeType = mb_detect_encoding($aryErrorMessage[$i]);

			//　DBととエラー置換文字のエンコーディングが異なる場合はエンコーディングを行う
			if ($encodeType != $objDB->InputEncoding) {
			    $aryErrorMessage[$i] = mb_convert_encoding($aryErrorMessage[$i], $objDB->InputEncoding);
			}

			$strExchange = "msg" . (string) ($i + 1);
			$strErrorMessage = preg_replace ( "/_%" . $strExchange . "%_/i", $aryErrorMessage[$i], $strErrorMessage );
		}
	}
	// 置換されなかった置き換え文字列を削除
	$strErrorMessage = preg_replace ( "/_%.+?%_/", "", $strErrorMessage );

	// メッセージ種類をメッセージに追加
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


	// エラー画面出力制御
	if ( $bytOutputFlag )
	{


		// エラー画面の出力制御についてはデバッグモードにより切り替える
		if ( DEF_DEBUG_MODE == 1 )
		{

			//header("Content-Type: text/html;charset=euc-jp");
			mb_http_output($objDB->InputEncoding);

			$strEcho = '<html>';
			$strEcho .= '<head>';
			$strEcho .= '<meta http-equiv="content-type" content="text/html; charset='.$objDB->InputEncoding.'">';
			$strEcho .= '</head><body>';
			$strEcho .= '&nbsp;';
			// エラーメッセージを画面に表示する
			$strEcho .= $strErrorMessage . "<BR>";
			$strEcho .= '</body></html>';

//fncDebug( 'upload_parse_confirm_0a4.txt', $strEcho, __FILE__, __LINE__);

			echo $strEcho;
			exit;
		}
		else
		{

		 	// /error/index.php で取り扱うエンコーディングへ変換する
			$strErrorMessage = mb_convert_encoding($strErrorMessage, 'euc-jp', $objDB->InputEncoding);

			// エラー画面へのリダイレクト
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
*	ステータスコードチェック関数
*
*	ステータスコードをチェックし、NULL(または空欄) の場合「0」を返却
*
*	@param  Long  $status         ステータスコード

*	@return Long  $lngStatusCode  ステータスコード
*
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckNullStatus( $status )
{
	$lngStatusCode = $status;

	// 状態コードが「 null / "" 」の場合、「0」を再設定
	$lngStatusCode = ( $lngStatusCode == "" || $lngStatusCode == "null" ) ? 0 : $lngStatusCode;

	return $lngStatusCode;
}


// -----------------------------------------------------------------
/**
*	ステータスコードチェック関数
*
*	ステータスコードをチェックし、「0」 の場合「1」を返却
*
*	@param  Long  $status         ステータスコード

*	@return Long  $lngStatusCode  ステータスコード
*
*	@access public
*/
// -----------------------------------------------------------------
function fncCheckZeroStatus( $status )
{
	$lngStatusCode = $status;

	// 状態コードが「0」の場合、「1」を再設定
	$lngStatusCode = ( $lngStatusCode == 0 ) ? 1 : $lngStatusCode;

	return $lngStatusCode;
}





// -----------------------------------------------------------------
/**
*	権限グループコード取得関数
*
*	ユーザーの権限グループコードを取得
*
*	@param  Long     $lngusercode  ユーザーコード
*	@param  String   $sessionid    セッションID
	@param  Object   $objDB        DBオブジェクト

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

	// クエリ実行
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngauthoritygroupcode = $objResult->lngauthoritygroupcode;
	}
	else
	{
		// 権限グループコード取得失敗
		fncOutputError ( 9052, DEF_WARNING, "権限グループコード取得失敗", TRUE, "menu/menu.php?strSessionID=" . $sessionid, $objDB );
	}


	return $lngauthoritygroupcode;
}





// -----------------------------------------------------------------
/**
*	権限グループコードチェック関数
*
*	ユーザーの権限グループコードをチェックし、
*	「ユーザー」以下の場合「TRUE」、
*	それ以外の場合「FALSE」を返却
*
*	@param  Long     $lngusercode  ユーザーコード
*	@param  String   $sessionid    セッションID
	@param  Object   $objDB        DBオブジェクト

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

	// クエリ実行
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngauthoritygroupcode = $objResult->lngauthoritygroupcode;
	}
	else
	{
		// 権限グループコード取得失敗
		fncOutputError ( 9052, DEF_WARNING, "権限グループコード取得失敗", TRUE, "menu/menu.php?strSessionID=" . $sessionid, $objDB );
	}


	// 権限グループが「ユーザー」以下の場合「TRUE」
	$blnRoot = ( $lngauthoritygroupcode >= 5 ) ? true : false;

	return $blnRoot;
}


// -----------------------------------------------------------------
/**
*	承認ルートチェック関数
*
*	ユーザーの承認ルートをチェックし、
*	存在する場合「TRUE」、
*	存在しない場合「FALSE」を返却
*
*	@param  Long     $lngusercode  ユーザーコード
*	@param  String   $sessionid    セッションID
	@param  Object   $objDB        DBオブジェクト

*	@return Boolean  $blnRoot
*
*	@access public
*/
// -----------------------------------------------------------------
// 承認ルート存在チェック
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

	// クエリ実行
	$lngResultID = $objDB->execute( $strQuery );


	if( !$lngResultID )
	{
		fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "menu/menu.php?strSessionID=" . $sessionid, $objDB );
	}

	// カウント取得
	$lngCount = pg_num_rows( $lngResultID );


	// 戻り値設定
	$blnRoot = ( $lngCount == 0 ) ? false : true;

	return $blnRoot;
}



// -----------------------------------------------------------------
/**
*	製品担当状況チェック関数
*
*	ログインユーザーの製品担当状況をチェックし、
*	属する場合「TRUE」、
*	属さない場合「FALSE」を返却
*
*	@param  Long     $lngTargetNo        対象内部コード
*	@param  Long     $lngInputUserCode   ログインユーザーコード
*	@param  String   $strFncFlag         機能フラグ
	@param  Object   $objDB              DBオブジェクト

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


	// 各クエリー句の生成
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
	$aryQuery[] = "			and mu1.bytinvalidflag = false";	/* 製品担当者に対し、入力者 と その入力者が属するグループのマネージャー以上が一致 */
	$aryQuery[] = "			and mu1.lngusercode = mgr1.lngusercode";
//39期事務3人対応のため
	$aryQuery[] = "			and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode or mu1.lngusercode in ('15','29','242','343'))";
	$aryQuery[] = "	) as blnAuthFlag";

	$aryQuery[] = "from";
	$aryQuery[] = $strFromQuery;
	$aryQuery[] = "where";
	$aryQuery[] = $strWhereQuery;


	$strQuery = implode( "\n", $aryQuery );

	// クエリー実行
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
*	メール送信関数
*
*
*	@param  Long     $strTo			Toアドレス
*	@param  Long     $strSubject	件名
*	@param  String   $strMessage	本文
	@param  Object   $strHeader		ヘッダー情報

*	@return Boolean  $blnCheck
*
*	@access public
*/
// -----------------------------------------------------------------
function fncSendMail($strTo, $strSubject, $strMessage, $strHeader="")
{
	// メール送信フラグが false の（メール送信をしてはいけない）場合は抜ける
	if( SEND_MAIL_FLAG == false )
	{
		return true;
	}
	// 開発環境の場合
	if( $_SERVER["HTTP_HOST"] == EXECUTE_HOST_NAME_DEV  || $_SERVER["HTTP_HOST"] == EXECUTE_HOST_NAME_KWG_BACK )
	{
		$strSubject .= " ".$strTo;
		$strTo = ERROR_MAIL_TO;
	}

	// 言語設定
	mb_language("Japanese");

	// メール送信
	return mb_send_mail( $strTo, $strSubject, $strMessage, $strHeader );
}





// -----------------------------------------------------------------
/**
*	テンポラリファイル保存、ファイル名返却
*
*
*	@param  String   $strTmpFile	テンポラリファイル名
*
*	@return String  $strTmpFileName
*
*	@access public
*/
// -----------------------------------------------------------------
function getTempFileName( $strTmpFile )
{
	// テンポラリファイルの作成
	$strTmpFileName	= MD5( microtime() ) . ".tmp";

	// テンポラリファイルの移動
	if( !move_uploaded_file( $strTmpFile, FILE_UPLOAD_TMPDIR . $strTmpFileName ) )
	{
		fncOutputError( 1106, DEF_FATAL, "", TRUE, "", $objDB );
		return false;
	}

	return $strTmpFileName;
}
// -----------------------------------------------------------------
/**
*	テンポラリファイル削除
*
*
*	@param  String   $strTmpFile	テンポラリファイル名
*
*
*	@access public
*/
// -----------------------------------------------------------------
function deleteTempFile( $strTmpFile )
{
	// テンポラリファイルの削除
	if( !unlink( FILE_UPLOAD_TMPDIR . $strTmpFile ) )
	{
		fncOutputError( 1106, DEF_FATAL, "", TRUE, "", $objDB );
		return false;
	}

	return true;
}

///////////////////////////////////////////////
?>
