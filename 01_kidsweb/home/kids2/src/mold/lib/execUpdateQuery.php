<?php

// 設定読み込み
include ("conf.inc");
require_once (LIB_FILE);
require_once (SRC_ROOT.'/mold/lib/exception/SQLException.class.php');

// sqlファイル置き場
define("QUERY_PATH", SRC_ROOT . "/mold/sql/");
define("QUERY_FILE_SUFFIX", ".sql");

// DBオープン
$objDB   = new clsDB();
$objDB->open("", "", "", "");

$json_string = mb_convert_encoding(file_get_contents('php://input'), "UTF-8") ;
$condition = json_decode($json_string, true);

if (!$condition)
{
	echo "無効な値が指定されました。";
	exit;
}

$_REQUEST = array_merge($_REQUEST, $condition);

// セッションが有効な場合
if ((new clsAuth())->isLogin($_REQUEST["strSessionID"], $objDB))
{
	$queryFileName = $_REQUEST["QueryName"];
	$queryFilePath = QUERY_PATH . $queryFileName . QUERY_FILE_SUFFIX;

	// 有効なクエリファイルの場合
	if($queryFileName && is_readable ($queryFilePath))
	{
		// クエリファイルの読み込み
		$query = file_get_contents($queryFilePath);
		$prepare = pg_prepare($objDB->ConnectID, "", $query);

		// クエリパラメータ
		$params = array();

		// 検索条件を含んでいる場合
		if(array_key_exists("Conditions", $_REQUEST) && count($_REQUEST["Conditions"]))
		{
			// EUC-JPへ変換
			mb_convert_variables('eucjp-win', 'UTF-8', $_REQUEST["Conditions"]);

			// クエリパラメータの作成
			foreach ($_REQUEST["Conditions"] as $key=>$condition)
			{
				$params[] = pg_escape_string($condition);
			}

			// クエリ実行
			$result = pg_execute("", $params);
		}
		// 検索条件を含んでいない場合
		else
		{
			$result = pg_execute("", $params);
		}

		// 結果が得られた場合
		if ($result)
		{
			// 反映された件数を取得
			$recordCount = pg_affected_rows($result);

			// 有効な件数が得られた場合
			if ($recordCount)
			{
				// 検索結果データセット
				$resultDataSet = array();

				// レコード件数分走査
				for ($i = 0; $i < $recordCount; $i++)
				{
					// 検索結果レコード取得
					$resultDataSet[] = pg_fetch_array($result, $i, PGSQL_ASSOC);
				}
				// レスポンスヘッダ設定
				header('Content-Type: application/json');
				// json変換の為、一時的にUTF-8へ変換
				mb_convert_variables('UTF-8', 'eucjp-win', $resultDataSet);
				$json = json_encode($resultDataSet, JSON_PRETTY_PRINT);
				echo $json;
			}
			else
			{
				echo "該当するレコードが見つかりませんでした";
			}
		}
		// 結果が得られなかった(クエリに失敗した)場合
		else
		{
			throw new SQLException(
					"問い合わせに失敗しました",
					$query,
					$params);
		}
	}
	// 無効なクエリファイルの場合
	else
	{
		echo "無効なクエリ名指定";
	}
}
// セッションが無効な場合
else
{
	echo "無効なセッション";
}

//DBクローズ
$objDB->close();