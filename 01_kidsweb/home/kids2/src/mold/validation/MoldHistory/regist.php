<?php
// ----------------------------------------------------------------------------
/**
*       金型帳票管理  登録 フォームデータ検証
*/
// ----------------------------------------------------------------------------
require_once('conf.inc');
require_once(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldHistory.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');

// オブジェクト生成
$objDB   = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// セッション確認
$objAuth = fncIsSession( $_REQUEST["strSessionID"], $objAuth, $objDB );

// 1800 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1801 金型管理(登録)
if ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 存在チェックエラーリスト
$errMstList = array();
// 意味チェックエラー
$errSemanticList = array();

// ユーティリティクラスのインスタンス取得
$utilMold = UtilMold::getInstance();
$utilValidation = UtilValidation::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// トランザクション開始
$objDB->transactionBegin();

// 金型関連テーブルのロック
// 処理終了時のロールバックを期待する
pg_query("LOCK m_moldreport");
pg_query("LOCK t_moldreportdetail");
pg_query("LOCK t_moldreportrelation");
pg_query("LOCK t_moldhistory");

// ------------------------------------------------------------------------------
// マスタチェック(日付フォーマットチェックも同時に行う)
// ------------------------------------------------------------------------------
// 製品コード
if (!$utilProduct->existsProductCode($_REQUEST[FormMoldHistory::ProductCode]))
{
	$errMstList[FormMoldHistory::ProductCode] =
		"[製品コード]->製品マスタ上に存在しない値です。:".
		$_REQUEST[FormMoldHistory::ProductCode];
}

// 金型ステータス -> 業務コードマスタ検索
if (!$utilBussinesscode->getDescription("金型ステータス", $_REQUEST[FormMoldHistory::Status], true))
{
	$errMstList[FormMoldHistory::ReportCategory] =
		"[ステータス]->業務コードマスタ上に存在しない値です。:".
		$_REQUEST[FormMoldHistory::ReportCategory];
}
// 実施日 -> 日付フォーマットチェック
if (!$utilValidation::checkDateFormatYMD($_REQUEST[FormMoldHistory::ActionDate]))
{
	$errMstList[FormMoldHistory::ActionDate] =
		"[実施日]->yyyy/mm/dd形式かつ有効な日付を入力してください。:".
		$_REQUEST[FormMoldHistory::ActionDate];
}
// 金型ステータスがエラーでなく、10:移動版又は20:返却版の場合
if (!$errMstList[FormMoldHistory::Status] &&
		($_REQUEST[FormMoldHistory::Status] == "10" || $_REQUEST[FormMoldHistory::Status] == "20"))
{
	// 保管元工場 -> 会社マスタ検索 (表示会社コード)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldHistory::SourceFactory]))
	{
		$errMstList[FormMoldHistory::SourceFactory] =
			"[保管元工場]->会社マスタ上に存在しない値です。:".
			$_REQUEST[FormMoldHistory::SourceFactory];
	}

	// 移動先工場 -> 会社マスタ検索 (表示会社コード)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldHistory::DestinationFactory]))
	{
		$errMstList[FormMoldHistory::DestinationFactory] =
			"[移動先工場]->会社マスタ上に存在しない値です。:".
			$_REQUEST[FormMoldHistory::DestinationFactory];
	}
}

// 金型NO要素の抽出
$molds = $utilMold::extractArray($_REQUEST, FormMoldHistory::MoldNo);

// 金型NO/金型説明の件数が0件の場合
if (!count($molds))
{
	$errMstList["Mold"] =
			"[金型]->金型を選択してください。";
}
// 金型NO/金型説明の個数が正常の場合
else
{
	// 金型NO要素数分走査
	foreach($molds as $index => $moldNo)
	{
		// 金型NOの存在チェック
		if (!$utilMold->existsMoldNo($moldNo))
		{
			$errMstList[$index] =
				"[".$index."]金型マスタ上に存在しない値です。:".$moldNo;
		}
	}
}

// ------------------------------------------------------------------------------
// 意味チェック (マスタチェックが通った場合にのみチェックを行う)
// ------------------------------------------------------------------------------
if (!count($errMstList))
{
	// 登録可能な金型番号リスト
	$moldSelectionList = $utilMold->selectMoldSelectionList($_REQUEST[FormMoldHistory::ProductCode]);
	// ホワイトリスト作成
	foreach ($moldSelectionList as $row => $columns)
	{
		$moldNoList[] = $columns[TableMoldHistory::MoldNo];
	}
	// 選択した金型件数分走査
	foreach ($molds as $index => $moldNo)
	{
		// 「金型NO」 <=> 「製品コード」関連チェック
		if (!$utilMold->existsMoldNoWithProductCode($moldNo,
				$_REQUEST[FormMoldHistory::ProductCode]))
		{
			$message = "[金型NO]製品コード(".$_REQUEST[FormMoldHistory::ProductCode].")と".
					"金型NO(".$moldNo.")組合せが不一致です。";

			array_key_exists("MoldNo<->ProductCode", $errSemanticList) ?
			$errSemanticList["MoldNo<->ProductCode"] = $message :
			$errSemanticList["MoldNo<->ProductCode"] += $message;
		}
		// 登録可能かチェック
		if(!in_array($moldNo, $moldNoList))
		{
			$usedMoldNoList[] = $moldNo;
		}
	}
	// 登録不可な金型番号が検出された場合
	if(count($usedMoldNoList))
	{
		$message = implode("\n ", $usedMoldNoList);

		$errSemanticList[FormMoldHistory::MoldNo] =
			"項目:\n ".
			"金型NO\n".
			"対象:\n ".
			$message."\n".
			"\n".
			"以下に該当する金型は選択できません。\n".
			"・廃棄された金型\n".
			"・未来日の実施日を持つ金型\n".
			"・未完了の金型帳票に紐づく金型\n";
	}

	// 金型ステータスが10:移動版又は20:返却版の場合
	if (($_REQUEST[FormMoldHistory::Status] == "10" ||
		$_REQUEST[FormMoldHistory::Status] == "20"))
	{
		// 対象の「金型NO」の全ての保管工場が入力された保管元工場と同一工場であること
		// 期待する会社コード
		$expectedCompanyCode =
		$utilCompany->selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldHistory::SourceFactory]);

		foreach ($molds as $index => $moldNo)
		{
			// 金型履歴から最新の移動先工場を取得
			$currentFactory = $utilMold->selectCurrentStorageOfMold($moldNo);

			// 金型履歴が存在しない場合(初回)
			if (!$currentFactory)
			{
				// 有効な金型履歴が存在しない金型は仕入れ元を現在の保管工場として扱う
				$currentFactory = $utilMold->selectMoldVender($moldNo);
			}

			// 現在の保管工場と入力された保管工場が一致しない場合
			if ($currentFactory != $utilCompany->
					selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldHistory::SourceFactory]))
			{
				$message = "[保管元工場]指定された保管工場と金型NO:".$moldNo."の現在の保管工場が一致しませんでした。\n";
				array_key_exists(FormMoldHistory::SourceFactory, $errSemanticList) ?
				$errSemanticList[FormMoldHistory::SourceFactory] += $message:
				$errSemanticList[FormMoldHistory::SourceFactory] = $message;
			}
		}

		// 保管元工場と移動先工場が同一工場でないこと
		if ($_REQUEST[FormMoldHistory::SourceFactory] == $_REQUEST[FormMoldHistory::DestinationFactory])
		{
			$errSemanticList[FormMoldHistory::DestinationFactory] =
			"[移動先工場]保管工場と同一の工場は指定できません。";
		}
	}
}

// 検証がOKの場合
if (!count($errMstList) && !count($errSemanticList))
{
	// 帳票区分別に余計なフォームデータを削除する
	switch ($_REQUEST[FormMoldHistory::Status])
	{
		case "10": // 移動版
		case "20": // 返却版
			// 削除要素なし
			break;
		default: // それ以外(廃棄版)
			// 保管元工場を削除
			unset($_REQUEST[FormMoldHistory::SourceFactory]);
			// 移動先工場を削除
			unset($_REQUEST[FormMoldHistory::DestinationFactory]);
			break;
	}

	// 文字列をサニタイズ
	foreach ($_REQUEST as $key => $value)
	{
		if (is_string($value))
		{
			$_REQUEST[$key] = htmlspecialchars($value);
		}
	}

	// 金型履歴のダイジェスト作成
	$summaryHistory = $utilMold->selectSummaryOfMoldHistory($molds);
	$digestHistory = FormCache::hash_arrays($summaryHistory);
	$_REQUEST["digest_history"] = $digestHistory;

	// 金型帳票のダイジェスト作成
	$summaryReport = $utilMold->selectSummaryOfMoldReport($molds);
	$digestReport = FormCache::hash_arrays($summaryReport);
	$_REQUEST["digest_report"] = $digestReport;

	// 金型リストをリクエストに格納
	$_REQUEST["list_moldno"] = $molds;

	// キャッシュインスタンスの取得
	$formCache = FormCache::getInstance();
	// ユーザコード設定
	$formCache->setUserCode($objAuth->UserCode);

	// トランザクション開始
	$objDB->transactionBegin();

	try
	{
		// フォームキャッシュに格納
		$resultHash["resultHash"] = $formCache::hash_arrays($_REQUEST);
		$formCache->add($resultHash["resultHash"], $_REQUEST);
	}
	catch (Exception $e)
	{
		// トランザクション ロールバック
		$objDB->transactionRollback();
		throw $e;
	}

	// トランザクション コミット
	$objDB->transactionCommit();

	// レスポンスヘッダ設定)(json)
	header('Content-Type: application/json');
	$json = json_encode($resultHash, JSON_PRETTY_PRINT);
	echo $json;
}
// 検証がNGの場合
else
{
	// エラー結果配列のマージ
	$errors = array_merge($errMstList, $errSemanticList);

	// json変換の為、一時的にUTF-8へ変換
	mb_convert_variables('UTF-8', 'EUC-JP', $errors);
	// レスポンスヘッダ設定)(json)
	header('Content-Type: application/json');
	$json = json_encode($errors, JSON_PRETTY_PRINT);
	echo $json;
}