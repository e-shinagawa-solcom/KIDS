<?
/** 
*	商品　企画進捗状況用　月次バッチ処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	現在の日付が月の最初の日　１日　であれば、
*	企画進捗状況テーブルを調査し、先月が納期の商品に対して
*	状態を「納品済」に自動で変更する
*
*/

// 設定の読み込み
include_once ( "/home/kids2/lib/conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

$objDB   = new clsDB();
$objDB->open( "", "", "", "" );

// 実行日時の取得
$run_date = date("d");
if ( $run_date != DEF_BATCH_DAY )
{
	$aryMessage[] = "月次バッチ実行日時が違います。";
}
else
{
	// 先月の日付の取得
	$last_month_01 = date("Y/m/d 00:00:00", strtotime("-1 month"));
	$last_month_lastday = date("Y/m/d 23:59:59", strtotime("-1 day"));

	// echo "<br>last_month_01 = " . $last_month_01;
	// echo "<br>last_month_lastday = " . $last_month_lastday;

	// 先月が納期の商品データを取得する
	$aryQuery[] = "SELECT distinct on (p.lngProductNo) p.lngProductNo as lngProductNo, \n";
	$aryQuery[] = "t_gp.lngRevisionNo as lngRevisionNo, t_gp.dtmCreationDate as dtmCreationDate, \n";
	$aryQuery[] = "t_gp.lngGoodsPlanCode as lngGoodsPlanCode \n";
	$aryQuery[] = "FROM m_Product p, t_GoodsPlan t_gp \n";
//	$aryQuery[] = "WHERE To_Date( p.dtmDeliveryLimitDate, 'YYYY/MM' ) >= To_Date( '" . $last_month_01 . "', 'YYYY/MM' ) \n";	// 〜最終日迄を全て更新対象とした為、コメント
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
		$aryMessage[] = "先月に納期で「納品済」以外の状態の商品はありませんでした。";
	}
	$objDB->freeResult( $lngResultID );

	if ( count($aryResult) )
	{
		// バッチユーザーのユーザーコードを取得する
		$lngBatchUserCode = DEF_BATCH_USERCODE;

		// 取得した一覧から納品済状態を商品企画テーブルにINSERT
		$lngResultCount = count($aryResult);

		for ( $i = 0; $i < $lngResultCount; $i++ )
		{
			// トランザクション開始
			$objDB->transactionBegin();

			// 情報の取得
			$lngProductNo = $aryResult[$i]["lngproductno"];
			$lngRevisionNo = $aryResult[$i]["lngrevisionno"];
			$lngRevisionNo++;
			$dtmCreationDate = $aryResult[$i]["dtmcreationdate"];
			$lngGoodsPlanCode = $aryResult[$i]["lnggoodsplancode"];

			if ( $lngGoodsPlanCode )
			{
				$Flag = FALSE;

				// 更新行の行レベルロック
				$strQuery = "SELECT lngGoodsPlanCode FROM t_GoodsPlan WHERE lngGoodsPlanCode = " . $lngGoodsPlanCode . " FOR UPDATE";

// echo "SELECT FOR UPDATE Query = " . $strQuery . "<BR>";

				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					$aryMessage[] = "システムエラー　製品番号 " . $lngProductNo . " の商品企画テーブルのロック処理に失敗しました。";
					$Flag = TRUE;
				}
				$objDB->freeResult( $lngResultID );

				// 更新行のUPDATE
				$strQuery = "UPDATE t_GoodsPlan SET lngGoodsPlanProgressCode = " . DEF_GOODSPLAN_END . " WHERE lngGoodsPlanCode = " . $lngGoodsPlanCode;

				if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
				{
					$aryMessage[] = "システムエラー　製品番号 " . $lngProductNo . " の商品企画テーブルのUPDATEに失敗しました。";
					$Flag = TRUE;
				}
				$objDB->freeResult( $lngResultID );

				if ( !$Flag )
				{
					$aryMessage[] = "成功　製品番号 " . $lngProductNo . " の情報を製品マスタ、商品企画テーブルへ登録、更新しました。";
				}
			}

		}
		// t_ProcessInformationのシーケンスを取得
		$sequence_t_processinformation = fncGetSequence( 't_processinformation.lngprocessinformationcode', $objDB );

		// 処理情報を格納
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
			$aryMessage[] = "システムエラー　処理情報テーブルのINSERTに失敗しました。";
			$Flag = TRUE;
		}
		$objDB->freeResult( $lngResultID );

		// コミット
		$objDB->transactionCommit();
	}
}

$strMessage = "\n\n".implode( "\n", $aryMessage );

$aryData["strMessage"] = $strMessage;

// メッセージログを管理者宛にメール送信
list ( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_SYS5, $aryData, $objDB );
$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

mb_language("Japanese");
// メールの送信
mb_send_mail( ERROR_MAIL_TO, $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
//mail ( "k-suzukaze@wiseknot.co.jp", $strSubject, $strBody, "From: $strFromMail\nReturn-Path: k-suzukaze@wiseknot.co.jp\n" );

$objDB->close();


return TRUE;
?>
