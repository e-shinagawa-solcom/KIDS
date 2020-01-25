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

// セッションが有効な場合
if ((new clsAuth())->isLogin($_REQUEST["strSessionID"], $objDB))
{
	$queryFileName = $_POST["QueryName"];
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
		if(array_key_exists("Conditions", $_POST) && count($_POST["Conditions"]))
		{
			// クエリパラメータの作成
			foreach ($_POST["Conditions"] as $key=>$condition)
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
			// 結果件数を取得
			$recordCount = pg_num_rows($result);
			// 有効な件数が得られた場合
			if ($recordCount)
			{
				// 検索結果データセット
				$resultDataSet = array();
                $json = array();

				// レコード件数分走査
				for ($i = 0; $i < $recordCount; $i++)
				{
					// 検索結果レコード取得
					$resultDataSet[] = pg_fetch_array($result, $i, PGSQL_ASSOC);
				}
				// レスポンスヘッダ設定
				header('Content-Type: application/json');

                for ($i = 0; $i < $recordCount; $i++)
                {
                    $keys = array_keys($resultDataSet[$i]);
                    $values = array_values($resultDataSet[$i]);
                    $json[$i] = array($keys[0]=>$values[0], $keys[1]=>$values[1], $keys[2]=>$values[2]);
                }
                echo json_encode($json);
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