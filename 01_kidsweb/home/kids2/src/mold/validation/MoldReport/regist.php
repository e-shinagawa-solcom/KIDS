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
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
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

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1901 金型管理(登録)
if ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 存在チェックエラーリスト
$errMstList = array();
// 意味チェックエラーリスト
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
// 存在チェック(日付フォーマットチェックも同時に行う)
// ------------------------------------------------------------------------------
// 製品コード
if (!$utilProduct->existsProductCode($_REQUEST[FormMoldReport::ProductCode]))
{
	$errMstList[FormMoldReport::ProductCode] =
		"[製品コード]->製品マスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::ProductCode];
}

// 帳票区分 -> 業務コードマスタ検索
if (!$utilBussinesscode->getDescription("帳票区分", $_REQUEST[FormMoldReport::ReportCategory], true))
{
	$errMstList[FormMoldReport::ReportCategory] =
		"[帳票区分]->業務コードマスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::ReportCategory];
}
// 依頼日 -> 日付フォーマットチェック
if (!$utilValidation::checkDateFormatYMD($_REQUEST[FormMoldReport::RequestDate]))
{
	$errMstList[FormMoldReport::RequestDate] =
		"[依頼日]->yyyy/mm/dd形式かつ有効な日付を入力してください。:".
		$_REQUEST[FormMoldReport::RequestDate];
}
// 依頼区分 -> 業務コードマスタ検索
if (!$utilBussinesscode->getDescription("依頼区分", $_REQUEST[FormMoldReport::RequestCategory], true))
{
	$errMstList[FormMoldReport::RequestCategory] =
		"[依頼区分]->業務コードマスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::RequestCategory];
}

// 希望日 -> 日付フォーマットチェック
if (!$utilValidation::checkDateFormatYMD($_REQUEST[FormMoldReport::ActionRequestDate]))
{
	$errMstList[FormMoldReport::ActionRequestDate] =
		"[希望日]->yyyy/mm/dd形式かつ有効な日付を入力してください。:".
		$_REQUEST[FormMoldReport::ActionRequestDate];
}

// 指示区分 -> 業務コードマスタ検索
if (!$utilBussinesscode->getDescription("指示区分", $_REQUEST[FormMoldReport::InstructionCategory], true))
{
	$errMstList[FormMoldReport::InstructionCategory] =
		"[指示区分]->業務コードマスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::InstructionCategory];
}

// 事業部(顧客)コード -> 会社マスタ検索 (表示会社コード)
if (!$utilCompany->existsCustomerCode($_REQUEST[FormMoldReport::CustomerCode]))
{
	$errMstList[FormMoldReport::CustomerCode] =
		"[事業部(顧客)]->会社マスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::CustomerCode];
}

// 担当部署 -> グループマスタ検索(表示グループコード)
if (!$utilGroup->existsGroupCode($_REQUEST[FormMoldReport::KuwagataGroupCode]))
{
	$errMstList[FormMoldReport::KuwagataGroupCode] =
		"[担当部署]->グループマスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::KuwagataGroupCode];
}
// 担当者 -> ユーザマスタ検索(表示ユーザコード)
if (!$utilUser->existsUserCode($_REQUEST[FormMoldReport::KuwagataUserCode]))
{
	$errMstList[FormMoldReport::KuwagataUserCode] =
		"[担当者]->ユーザマスタ上に存在しない値です。:".
		$_REQUEST[FormMoldReport::KuwagataUserCode];
}

// 帳票区分がエラーでなく、10:移動版又は20:返却版の場合
if (!$errMstList[FormMoldReport::ReportCategory] &&
		($_REQUEST[FormMoldReport::ReportCategory] == "10" || $_REQUEST[FormMoldReport::ReportCategory] == "20"))
{
	// 移動方法 -> 業務コードマスタ検索
	if (!$utilBussinesscode->getDescription("移動方法", $_REQUEST[FormMoldReport::TransferMethod], true))
	{
		$errMstList[FormMoldReport::TransferMethod] =
			"[移動方法]->業務コードマスタ上に存在しない値です。:".
			$_REQUEST[FormMoldReport::TransferMethod];
	}

	// 生産後の処理 -> 業務コードマスタ検索
	if (!$utilBussinesscode->getDescription("生産後の処理", $_REQUEST[FormMoldReport::FinalKeep], true))
	{
		$errMstList[FormMoldReport::FinalKeep] =
			"[生産後の処理]->業務コードマスタ上に存在しない値です。:".
			$_REQUEST[FormMoldReport::FinalKeep];
	}
	// 20:保管工場に返却する の場合
	else if ($_REQUEST[FormMoldReport::FinalKeep] == "20")
	{
		// 返却予定日 -> 日付フォーマットチェック
		if (!$utilValidation->checkDateFormatYMD($_REQUEST[FormMoldReport::ReturnSchedule]))
		{
			$errMstList[FormMoldReport::ReturnSchedule] =
				"[返却予定日]->yyyy/mm/dd形式かつ有効な日付を入力してください。:".
				$_REQUEST[FormMoldReport::ReturnSchedule];
		}
	}

	// 保管元工場 -> 会社マスタ検索 (表示会社コード)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldReport::SourceFactory]))
	{
		$errMstList[FormMoldReport::SourceFactory] =
			"[保管元工場]->会社マスタ上に存在しない値です。:".
			$_REQUEST[FormMoldReport::SourceFactory];
	}

	// 移動先工場 -> 会社マスタ検索 (表示会社コード)
	if (!$utilCompany->existsFactoryCode($_REQUEST[FormMoldReport::DestinationFactory]))
	{
		$errMstList[FormMoldReport::DestinationFactory] =
			"[移動先工場]->会社マスタ上に存在しない値です。:".
			$_REQUEST[FormMoldReport::DestinationFactory];
	}
}

// 金型NO要素の抽出
$molds = $utilMold::extractArray($_REQUEST, FormMoldReport::MoldNo);
$descs = $utilMold::extractArray($_REQUEST, FormMoldReport::MoldDescription);

// 金型NO/金型説明の件数が0件の場合
if (!count($molds) || !count($descs))
{
	$errMstList["Mold"] =
			"[金型]->金型を選択してください。";
}
// 金型NOと金型説明の件数が不一致
else if(count($molds) != count($descs))
{
	$errMstList["Mold"] =
			"[金型]->金型と金型説明の件数が不一致です。";
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
// 意味チェック (存在チェックが通った場合にのみチェックを行う)
// ------------------------------------------------------------------------------
if (!count($errMstList))
{
	$usedMoldNoList = array();
	$moldNoList = array();
	// 「顧客品番(商品コード)」が「製品コード」に紐付くものかチェック
	if (!$utilProduct->existsGoodsCodeWithProductCode($_REQUEST[FormMoldReport::GoodsCode],
			$_REQUEST[FormMoldReport::ProductCode]))
	{
		$errSemanticList[FormMoldReport::GoodsCode] =
			"[顧客品番]製品コードとの組合せが不一致です。";
	}

	// 登録可能な金型番号リスト
	$moldSelectionList = $utilMold->selectMoldSelectionList($_REQUEST[FormMoldHistory::ProductCode]);
	// ホワイトリスト作成
	foreach ($moldSelectionList as $row => $columns)
	{
		$moldNoList[] = $columns[TableMoldHistory::MoldNo];
	}
	// 「金型NO」 <=> 「製品コード」関連チェック
	foreach ($molds as $index => $moldNo)
	{
		if (!$utilMold->existsMoldNoWithProductCode($moldNo,
				$_REQUEST[FormMoldReport::ProductCode],
				$_REQUEST[FormMoldReport::ReviseCode]))
		{
			$message = "[金型NO]製品コード(".$_REQUEST[FormMoldReport::ProductCode]."_".$_REQUEST[FormMoldReport::ReviseCode].")と".
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

	// 「担当者」が「担当部署」の子であること
	if (!$utilUser->existsUserCodeWithGroupCode($_REQUEST[FormMoldReport::KuwagataUserCode],
			$_REQUEST[FormMoldReport::KuwagataGroupCode]))
	{
		$errSemanticList[FormMoldReport::KuwagataUserCode] =
			"[担当者]部署との組合せが不一致です。";
	}

	// 「希望日」が現在日よりも未来日であること
	if (!$utilValidation->isFutureDate($_REQUEST[FormMoldReport::ActionRequestDate]))
	{
		$errSemanticList[FormMoldReport::ActionRequestDate] =
			"[希望日]翌日以降の日付を入力してください。";
	}

	// 「生産後の処理」が 20:RETURN TO ORIGINAL(保管工場に返却する)の場合
	if ($_REQUEST[FormMoldReport::FinalKeep] == "20")
	{
		// 「返却予定日」が現在日と「希望日」よりも未来日であること
		if (!$utilValidation->isFutureDate($_REQUEST[FormMoldReport::ReturnSchedule]))
		{
			$errSemanticList[FormMoldReport::ReturnSchedule] =
			"[返却予定日]翌日以降の日付を入力してください。";
		}
		// 「返却予定日」が「希望日」よりも未来日であること
		else if ($utilValidation->compareDate($_REQUEST[FormMoldReport::ReturnSchedule],
				$_REQUEST[FormMoldReport::ActionRequestDate]) != 1)
		{
			$errSemanticList[FormMoldReport::ReturnSchedule] =
			"[返却予定日]「希望日」の翌日以降の日付を入力してください。";
		}
	}

	// 帳票区分が10:移動版又は20:返却版の場合
	if (($_REQUEST[FormMoldReport::ReportCategory] == "10" ||
		 $_REQUEST[FormMoldReport::ReportCategory] == "20"))
	{
		// 対象の「金型NO」の全ての保管工場が入力された保管元工場と同一工場であること
		// 期待する会社コード
		$expectedCompanyCode =
		$utilCompany->selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldReport::SourceFactory]);

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
//echo "currentFactory:" . $currentFactory ."<br>";
			// 現在の保管工場と入力された保管工場が一致しない場合
			if ($currentFactory != $utilCompany->
					selectCompanyCodeByDisplayCompanyCode($_REQUEST[FormMoldReport::SourceFactory]))
			{
				$message = "[保管元工場]指定された保管工場と金型NO:".$moldNo."の現在の保管工場(" . $currentFactory . ")が一致しませんでした。\n";
				array_key_exists(FormMoldReport::SourceFactory, $errSemanticList) ?
				$errSemanticList[FormMoldReport::SourceFactory] += $message:
				$errSemanticList[FormMoldReport::SourceFactory] = $message;
			}
		}

		// 保管元工場と移動先工場が同一工場でないこと
		if ($_REQUEST[FormMoldReport::SourceFactory] == $_REQUEST[FormMoldReport::DestinationFactory])
		{
			$errSemanticList[FormMoldReport::DestinationFactory] =
			"[移動先工場]保管工場と同一の工場は指定できません。";
		}
	}
}

// 検証がOKの場合
if (!count($errMstList) && !count($errSemanticList))
{
	// 帳票区分別に余計なフォームデータを削除する
	switch ($_REQUEST[FormMoldReport::ReportCategory])
	{
		case "10": // 移動版
		case "20": // 返却版
			// 「生産後の処理」が 20:RETURN TO ORIGINAL(保管工場に返却する)以外
			if ($_REQUEST[FormMoldReport::FinalKeep] != "20")
			{
				// 返却予定日を削除
				unset($_REQUEST[FormMoldReport::ReturnSchedule]);
			}
			break;
		default: // それ以外(廃棄版)
			// 保管元工場を削除
			unset($_REQUEST[FormMoldReport::SourceFactory]);
			// 移動先工場を削除
			unset($_REQUEST[FormMoldReport::DestinationFactory]);
			// 移動方法を削除
			unset($_REQUEST[FormMoldReport::TransferMethod]);
			// 生産後の処理を削除
			unset($_REQUEST[FormMoldReport::FinalKeep]);
			// 返却予定日を削除
			unset($_REQUEST[FormMoldReport::ReturnSchedule]);
			break;
	}

	// 文字列をサニタイズ
	// foreach ($_REQUEST as $key => $value)
	// {
	// 	if (is_string($value))
	// 	{
	// 		$_REQUEST[$key] = htmlspecialchars($value);
	// 	}
	// }

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

	// レスポンスヘッダ設定)(json)
	header('Content-Type: application/json');
	$json = json_encode($errors, JSON_PRETTY_PRINT);
	echo $json;
}