<?php

// �����ɤ߹���
include ("conf.inc");
require_once (LIB_FILE);
require_once (SRC_ROOT.'/cmn/exception/SQLException.class.php');

// sql�ե������֤���
define("QUERY_PATH", SRC_ROOT . "cmn/sql/");
define("QUERY_FILE_SUFFIX", ".sql");

// DB�����ץ�
$objDB   = new clsDB();
$objDB->open("", "", "", "");

// ���å����ͭ���ʾ��
if ((new clsAuth())->isLogin($_REQUEST["strSessionID"], $objDB))
{
	$queryFileName = $_POST["QueryName"];
	$queryFilePath = QUERY_PATH . $queryFileName . QUERY_FILE_SUFFIX;
	// ͭ���ʥ�����ե�����ξ��
	if($queryFileName && is_readable ($queryFilePath))
	{
		// ������ե�������ɤ߹���
		$query = file_get_contents($queryFilePath);
		$prepare = pg_prepare($objDB->ConnectID, "", $query);
		// ������ѥ�᡼��
		$params = array();

		// ��������ޤ�Ǥ�����
		if(array_key_exists("Conditions", $_POST) && count($_POST["Conditions"]))
		{
			// EUC-JP���Ѵ�
			mb_convert_variables('eucjp-win', 'UTF-8', $_POST["Conditions"]);

			// ������ѥ�᡼���κ���
			foreach ($_POST["Conditions"] as $key=>$condition)
			{
				$params[] = pg_escape_string($condition);
			}

			// ������¹�
			$result = pg_execute("", $params);
		}
		// ��������ޤ�Ǥ��ʤ����
		else
		{
			$result = pg_execute("", $params);
		}
		// ��̤�����줿���
		if ($result)
		{
			// ��̷�������
			$recordCount = pg_num_rows($result);
			$fieldCount = pg_num_fields($result);
			// ͭ���ʷ��������줿���
			if ($recordCount)
			{
				// ������̥ǡ������å�
				$resultDataSet = array();
                $json = array();

				$args = array();
				// �쥳���ɷ��ʬ����
				for ($i = 0; $i < $recordCount; $i++)
				{
					// ������̥쥳���ɼ���
					$resultDataSet[] = pg_fetch_array($result, $i, PGSQL_ASSOC);
				}
				// �쥹�ݥ󥹥إå�����
				header('Content-Type: application/json');
				// json�Ѵ��ΰ١����Ū��UTF-8���Ѵ�
				mb_convert_variables('UTF-8', 'eucjp-win', $resultDataSet);

                for ($i = 0; $i < $recordCount; $i++)
                {
                    $keys = array_keys($resultDataSet[$i]);
					$values = array_values($resultDataSet[$i]);
					for($j = 0; $j < 4; $j++){
						// $args[$keys[j]] = $values[j];
						$json[$i][$keys[$j]] = $values[$j];
					// echo $keys[0];
					// return;
					}
					// $json[$i] = $args;
					// var_dump($json[$i]);
                    // $json[$i] = array($keys[0]=>$values[0], $keys[1]=>$values[1], $keys[2]=>$values[2]);
				}
                echo json_encode($json);
			}
			else
			{
				echo "��������쥳���ɤ����Ĥ���ޤ���Ǥ���";
			}
		}
		// ��̤������ʤ��ä�(������˼��Ԥ���)���
		else
		{
			throw new SQLException(
					"�䤤��碌�˼��Ԥ��ޤ���",
					$query,
					$params);
		}
	}
	// ̵���ʥ�����ե�����ξ��
	else
	{
		echo $queryFilePath ."̵���ʥ�����̾����";
	}
}
// ���å����̵���ʾ��
else
{
	echo "̵���ʥ��å����";
}

//DB������
$objDB->close();