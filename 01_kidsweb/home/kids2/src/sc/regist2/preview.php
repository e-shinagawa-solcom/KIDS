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
		// プレビュー表示後に登録するため入力データをjsonに変換して退避する
		$aryHeader = $_POST["aryHeader"];
		$aryDetail = $_POST["aryDetail"];
		$aryData["aryHeaderJson"] = EncodeToJson($aryHeader);
		$aryData["aryDetailJson"] = EncodeToJson($aryDetail);

		//TODO:ExcelテンプレートからプレビューHTMLを作成して以下の変数にセット
		$aryData["PREVIEW_STYLE"] = "";
		$aryData["PREVIEW_DATA"] = "";

		// プレビュー画面表示
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		echo $objTemplate->strTemplate;

		return true;
	}

	if ($strMode == "regist-test"){
		// プレビュー表示前に退避した登録データをjsonから復元する
		$aryHeader = DecodeFromJson($_POST["aryHeaderJson"]);
		$aryDetail = DecodeFromJson($_POST["aryDetailJson"]);

		//TODO:登録処理


		// 登録結果画面に表示するパラメータの設定
		$aryData["dtmRegistDate"] = "2019/5/27 12:34:56";
		$aryData["lngSlipNo"] = "KWG19527-01-01";

		// 登録結果画面表示
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/parts.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();
		echo $objTemplate->strTemplate;

		return true;
	}


	// ------------------------
	//   帳票表示
	// ------------------------
	if($strMode == "chouhyou-sample"){
		// 日本語に対応する場合、この1行が必要
		ini_set('default_charset', 'UTF-8');

		// 読み込み
		//use PhpOffice\PhpSpreadsheet\IOFactory;
		//use PhpOffice\PhpSpreadsheet\Writer\Html;

		$file = mb_convert_encoding('template\納品書temple_B社_連絡書付.xlsx', 'UTF-8','EUC-JP' );
		$sheetname = mb_convert_encoding('B社専用', 'UTF-8','EUC-JP' );
		$cellValue = mb_convert_encoding('個別に値をセット', 'UTF-8','EUC-JP' );
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

		// ブックに値を設定する
		$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
		//$outHeader = $writer->generateHTMLHeader();
		$outStyle = $writer->generateStyles(true);
		$outSheetData = $writer->generateSheetData();
		//$outFooter .= $writer->generateHTMLFooter();

		//TODO:明細の数だけ繰り返す
		$outStyle = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
		$outSheetData = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');
		$aryData["PREVIEW_STYLE"] = $outStyle;
		$aryData["PREVIEW_DATA"] = $outSheetData;

		//$out2 = mb_convert_encoding($output, 'EUC-JP', 'UTF-8');


		// プレビュー画面表示
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
		$objTemplate->replace( $aryData );
		$objTemplate->complete();

		//header("Content-Type: text/plain");
		echo $objTemplate->strTemplate;

		return true;
	}



	/*
	define ( "PATH_HOME",	"E:/Source/Repos/solcom-net/KIDS/01_kidsweb/home/kids2" );
	require (PATH_HOME . "/vendor/autoload.php");
	define ( "REPORT_TMPDIR",	PATH_HOME . "/report_tmp/" );
	$filepath = REPORT_TMPDIR . "納品書temple_B社_連絡書付.xls";

	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
	$spreadsheet = $reader->load($filepath);

	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
	$output = $writer->generateHTMLHeader();
	$output .= $writer->generateStyles(true);
	$output .= $writer->generateSheetData();
	$output .= $writer->generateHTMLFooter();
	echo mb_convert_encoding($output, 'EUC-JP', 'UTF-8');
	*/


	// --------------------------------
	//    登録処理
	// --------------------------------
	if($strMode == "regist"){
		// トランザクション開始
		$objDB->transactionBegin();

		// 売上マスタ、売上明細の登録
		/*
		if (!fncRegistSales())
		{
			fncOutputError ( 9051, DEF_FATAL, "売上マスタ登録失敗", TRUE, "", $objDB );
		}

		// 売上明細登録
		if (!fncRegistSalesDetail($lngSlipNo, $objDB))
		{
			fncOutputError ( 9051, DEF_FATAL, "売上明細登録失敗", TRUE, "", $objDB );
		}

		// 納品伝票マスタ登録
		if (!fncRegistSlip($strSlipCode, $objDB, $objAuth))	
		{
			fncOutputError ( 9051, DEF_FATAL, "納品伝票マスタ登録失敗", TRUE, "", $objDB );
		}

		// 納品伝票明細登録
		if (!fncRegistSlipDetail($lngSlipNo, $objDB))
		{
			fncOutputError ( 9051, DEF_FATAL, "納品伝票明細登録失敗", TRUE, "", $objDB );
		}
*/
		// トランザクションコミット
		$objDB->transactionCommit();

		// 登録完了画面の表示
		$aryDeleteData = $aryHeadResult;
		$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
		$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

		// 言語コード：日本語
		$aryDeleteData["lngLanguageCode"] = 1;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

		// テンプレート生成
		$objTemplate->replace( $aryDeleteData );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		$objDB->close();

		return true;
	}

	// --------------------------------
	//    プレビュー表示
	// --------------------------------

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