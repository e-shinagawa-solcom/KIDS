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

		// --------------------------
		//  プレビュー生成
		// --------------------------
		//登録データとExcelテンプレートとからプレビューHTMLを生成する
		$aryPreview = fncGenerateReportPreview($aryHeader, $aryDetail, $objDB, $objAuth);

		// --------------------------
		//  プレビュー画面表示
		// --------------------------
		$aryData["PREVIEW_STYLE"] = $aryPreview["PreviewStyle"];
		$aryData["PREVIEW_DATA"] = $aryPreview["PreviewData"];
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		echo $objTemplate->strTemplate;

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

		// --------------------------
		//  データベース登録
		// --------------------------
		/*
		// トランザクション開始
		$objDB->transactionBegin();

		// 受注マスタ更新
		if (!fncUpdateReceiveMaster($aryDetail, $objDB))
		{
			fncOutputError ( 9051, DEF_FATAL, "受注マスタ更新失敗", TRUE, "", $objDB );
		}

		// 売上マスタ登録、売上詳細登録、納品伝票マスタ登録、納品伝票明細登録
		if (!fncRegisterSalesAndSlip($aryHeader, $aryDetail, $objDB, $objAuth))
		{
			fncOutputError ( 9051, DEF_FATAL, "売上（納品書）登録失敗", TRUE, "", $objDB );
		}

		// コミット
		$objDB->transactionCommit();
		*/

		// --------------------------
		//  登録結果画面表示
		// --------------------------
		// 画面に表示するパラメータの設定
		$aryData["dtmRegistDate"] = "2019/5/27 12:34:56";
		$aryData["lngSlipNo"] = "KWG19527-01-01";

		// テンプレートから構築したHTMLを出力
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();
		echo $objTemplate->strTemplate;

		return true;
	}


	// -----------------------------------
	//   帳票表示サンプル
	// -----------------------------------
	// if($strMode == "chouhyou-sample"){
	// 	// 日本語に対応する場合、この1行が必要
	// 	ini_set('default_charset', 'UTF-8');

	// 	// 読み込み
	// 	$file = mb_convert_encoding('template\納品書temple_B社_連絡書付.xlsx', 'UTF-8','EUC-JP' );
	// 	$sheetname = mb_convert_encoding('B社専用', 'UTF-8','EUC-JP' );
	// 	$cellValue = mb_convert_encoding('個別に値をセット', 'UTF-8','EUC-JP' );
	// 	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
	// 	$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);
	// 	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
	// 	$outStyle = $writer->generateStyles(true);
	// 	$outSheetData = $writer->generateSheetData();
	// 	$outStyle = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
	// 	$outSheetData = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');

	// 	$aryData["PREVIEW_STYLE"] = $outStyle;
	// 	$aryData["PREVIEW_DATA"] = $outSheetData;

	return true;

	function EncodeToJson($object){
		$json = base64_encode(json_encode($object));
		return $json;
	}

	function DecodeFromJson($json){
		$object = json_decode(base64_decode($json), true);
		return $object;
	}

	// エラー画面への遷移
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