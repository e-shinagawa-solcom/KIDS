<?php
// ----------------------------------------------------------------------------
/**
*       納品書プレビュー
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

	//-------------------------------------------------------------------------
	// ライブラリファイル読込
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
	require PATH_HOME . "/vendor/autoload.php";

	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	// セッションID
	if ($_POST["strSessionID"]){
		$aryData["strSessionID"] = $_POST["strSessionID"];
	}else{
		$aryData["strSessionID"] = $_REQUEST["strSessionID"];   
	}
	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// 処理モード
	$strMode    = $_POST["strMode"];

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;

	// 600 売上管理
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 601 売上管理（売上登録）
	if( fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	// 610 売上管理（行追加・行削除）
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}

	//-------------------------------------------------------------------------
	//  プレビュー画面表示
	//-------------------------------------------------------------------------
	if ($strMode == "display-preview"){
		// --------------------------
		//  登録データ退避
		// --------------------------
		// プレビュー表示後に登録処理を行うため、入力データをjsonに変換して退避する
		$aryHeader = $_POST["aryHeader"];
		$aryDetail = $_POST["aryDetail"];
		$aryData["aryHeaderJson"] = EncodeToJson($aryHeader);
		$aryData["aryDetailJson"] = EncodeToJson($aryDetail);

		// --------------------------------
		//  文字コード変換（UTF-8->EUC-JP）
		// --------------------------------
		//jQueryのajaxでPOSTすると文字コードが UTF-8 になって
		//データ登録時にエラーになるため、DB処理前にEUC-JPに変換する
		$aryHeader = fncConvertArrayHeaderToEucjp($aryHeader);
		$aryDetail = fncConvertArrayDetailToEucjp($aryDetail);

		// --------------------------
		//  プレビュー生成
		// --------------------------
		//登録データとExcelテンプレートとからプレビューHTMLを生成する
		$aryPreview = fncGenerateReportPreview($aryHeader, $aryDetail, $objDB, $objAuth);

		// --------------------------
		//  プレビュー画面表示
		// --------------------------
		// テンプレートから構築したHTMLを出力
		$aryData["PREVIEW_STYLE"] = $aryPreview["PreviewStyle"];
		$aryData["PREVIEW_DATA"] = $aryPreview["PreviewData"];
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		echo $objTemplate->strTemplate;

		// DB切断
		$objDB->close();
		// 処理終了
		return true;
	}

	//-------------------------------------------------------------------------
	//  登録処理
	//-------------------------------------------------------------------------
	if ($strMode == "regist"){
		// --------------------------
		//  登録データ復元
		// --------------------------
		// プレビュー表示前に退避した登録データをjsonから復元する
		$aryHeader = DecodeFromJson($_POST["aryHeaderJson"]);
		$aryDetail = DecodeFromJson($_POST["aryDetailJson"]);

		// --------------------------------
		//  文字コード変換（UTF-8->EUC-JP）
		// --------------------------------
		// json変換時に文字コードが UTF-8 になって
		// データ登録時にエラーになるため、EUC-JPに戻す
		$aryHeader = fncConvertArrayHeaderToEucjp($aryHeader);
		$aryDetail = fncConvertArrayDetailToEucjp($aryDetail);

		// --------------------------
		//  登録前バリデーション
		// --------------------------
		// 受注状態コードが2以外の明細が存在するならエラーとする
		if(fncNotReceivedDetailExists($aryDetail, $objDB))
		{
			MoveToErrorPage("納品書が発行できない状態の明細が選択されています。");
		}

		// --------------------------
		//  データベース登録
		// --------------------------
		// トランザクション開始
		$objDB->transactionBegin();

		// 受注マスタ更新
		$updResult = fncUpdateReceiveMaster($aryDetail, $objDB);
		if (!$updResult){
			MoveToErrorPage("受注データの更新に失敗しました。");
		}

		// 売上マスタ登録、売上詳細登録、納品伝票マスタ登録、納品伝票明細登録
		// TODO:リビジョン番号も受け取るようにする
		$aryRegResult = fncRegisterSalesAndSlip($aryHeader, $aryDetail, $objDB, $objAuth);
		if (!$aryRegResult["result"]){
			MoveToErrorPage("売上・納品伝票データの登録に失敗しました。");
		}

		// コミット
		$objDB->transactionCommit();

		// --------------------------
		//  登録結果画面表示
		// --------------------------
		// 画面に表示するパラメータの設定
		// 納品書NOに紐づく作成日の取得
		// TODO:aryで複数取得に実装変更
		$dtmInsertDate = fncGetSlipInsertDate($aryRegResult["strSlipCode"][0], $objDB);
		// TODO:複数件対応。TABLEのTRを出力するfunctionを追加。納品書NOとリビジョン番号を併せて埋め込み。
		// 作成日の設定
		$aryData["dtmInsertDate"] = $dtmInsertDate;
		// 納品書NOの設定
		$aryData["strSlipCode"] = $aryRegResult["strSlipCode"][0];

		// テンプレートから構築したHTMLを出力
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();
		echo $objTemplate->strTemplate;

		// DB切断
		$objDB->close();
		// 処理終了
		return true;
	}

	if ($strMode == "download"){
		//TODO:帳票ダウンロードの実装。ajax POSTで実装
		//パラメータとして納品書NOとリビジョン番号を受け取る
		$strDownloadSlipCode = $_POST["strdownloadslipcode"];
		$lngDownloadRevisionNo = $_POST["lngdownloadrevisionno"];

		//TODO:帳票印刷データをDBより取得
		//$aryDownloadData = fncGetSlipDownloadData($strDownloadSlipCode, $lngDownloadRevisionNo);

		//TODO:登録データとExcelテンプレートとからダウンロードするExcelオブジェクトを取得する
		//fncDownloadReportExcel($aryHeader, $aryDetail, $objDB, $objAuth);

		// TODO:MIMEタイプをセットしてダウンロード
		//   //MIMEタイプ：https://technet.microsoft.com/ja-jp/ee309278.aspx
		//   header("Content-Description: File Transfer");
		//   header('Content-Disposition: attachment; filename="weather.xlsx"');
		//   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//   header('Content-Transfer-Encoding: binary');
		//   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		//   header('Expires: 0');
		//   ob_end_clean(); //バッファ消去
		   
		//   $writer = new XlsxWriter($spreadsheet);
		//   $writer->save('php://output');
		
		// TODO:メモリ開放

		// 処理終了
		return true;

	}

	// 通常ここに来ることは無い（不明なモードでPOSTした場合ここに来る）
	echo "不明なモードでPOSTされました";
	return true;

	// ヘルパ関数：jsonエンコード後にbase64エンコード
	// base64変換するのは HTMLのhiddenフィールドに安全な形で格納するため。
	function EncodeToJson($object){
		$json = base64_encode(json_encode($object));
		return $json;
	}

	// ヘルパ関数：base64デコード後にjsonデコード
	function DecodeFromJson($json){
		$object = json_decode(base64_decode($json), true);
		return $object;
	}

	// ヘルパ関数：エラー画面への遷移
	function MoveToErrorPage($strMessage){
		
		// 言語コード：日本語
		$aryHtml["lngLanguageCode"] = 1;

		// エラーメッセージの設定
		$aryHtml["strErrorMessage"] = $strMessage;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		exit;
	}


?>