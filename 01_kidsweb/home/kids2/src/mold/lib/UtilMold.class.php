<?php

require_once (SRC_ROOT.'/mold/lib/WithQuery.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldHistory.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMetaData.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMold.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldHistory.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReportRelation.class.php');
require_once(SRC_ROOT.'/mold/lib/exception/SQLException.class.php');
require_once(SRC_ROOT.'/mold/lib/exception/KidsLogicException.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');

/**
 * 金型に関する処理を行う
 * clsDBを使用しているが処理が不完全な為注意すること
 * TODO テーブルが混在しているのでクラスを分割する
 *
 * @see clsDB
 */
class UtilMold extends WithQuery
{
	/**
	 * 金型の仕入れ元情報を取得する
	 *
	 * @param string $moldNo 金型NO
	 * @return 下記の連想配列を返す
	 * <pre>
	 * {
	 *     vendercode => value,
	 *     companydisplaycode => value,
	 *     companydisplayname => value
	 * }
	 * </pre>
	 * @throws InvalidArgumentException
	 * @throws SQLException
	 *
	 * @see InvalidArgumentException
	 * @see SQLException
	 */
	public function getVenderInfomation($moldNo)
	{
		$result = array();

		if (is_string($moldNo))
		{
			$queryMoldVender = file_get_contents($this->getQueryFileName("selectMoldVender"));

			// クエリパラメータ作成(SELECT)
			$paramMoldVender = array(
					"moldno" => $moldNo
			);

			// 金型の仕入れ元情報を取得する
			pg_prepare(static::$db->ConnectID, "", $queryMoldVender);
			$pgResultMoldVender = pg_execute("", $paramMoldVender);

			// 検索の問い合わせに成功した場合
			if ($pgResultMoldVender)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResultMoldVender))
				{
					// 検索結果の先頭行からバージョンを取得
					$result = pg_fetch_array($pgResultMoldVender, 0, PGSQL_ASSOC);
				}
				// 一致する行が存在しない場合
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$queryMoldVender,
							$paramMoldVender
					);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$queryMoldVender,
						$paramMoldVender
				);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)."\n"
			);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型帳票データの挿入処理を行う。
	 * </pre>
	 *
	 * @param array フォームデータ
	 * @return INSERT時のRETURNING結果配列
	 */
	public function insertMoldReport(array $formData)
	{
		$result = false;

		// SendTo項目内容の取得(1つ目の金型から仕入れ元を索引する)
		// TO項目(仕入元会社)の索引 暫定的に最初の金型の仕入元を取得する
		$venderInfo = $this->getVenderInfomation($formData[FormMoldReport::MoldNo."1"]);
		$formData["SendTo"] = $venderInfo["vendercode"];

		// 表示コードをキーコード(PK)で置き換える
		// 事業部
		$formData[FormMoldReport::CustomerCode] =
			fncGetMasterValue(
				"m_company",
				"strcompanydisplaycode",
				"lngcompanycode",
				$formData[FormMoldReport::CustomerCode] . ":str",
				'',
				static::$db
			);

		// KWG部署
		$formData[FormMoldReport::KuwagataGroupCode] =
			fncGetMasterValue(
				"m_group",
				"strgroupdisplaycode",
				"lnggroupcode",
				$formData[FormMoldReport::KuwagataGroupCode] . ":str",
				'',
				static::$db
			);

		// KWG担当者
		$formData[FormMoldReport::KuwagataUserCode] =
			fncGetMasterValue(
				"m_user",
				"struserdisplaycode",
				"lngusercode",
				$formData[FormMoldReport::KuwagataUserCode] . ":str",
				'',
				static::$db
			);

		// 帳票区分でSQL及びパラメータを切り替える
		switch ($formData[FormMoldReport::ReportCategory])
		{
			// 移動版/返却版
			case "10":
			case "20":
				// 表示コードをキーコード(PK)で置き換える
				// 保管工場
				$formData[FormMoldReport::SourceFactory] =
					fncGetMasterValue(
						"m_company",
						"strcompanydisplaycode",
						"lngcompanycode",
						$formData[FormMoldReport::SourceFactory]. ":str",
						'',
						static::$db
					);

				// 移動先工場
				$formData[FormMoldReport::DestinationFactory] =
					fncGetMasterValue(
						"m_company",
						"strcompanydisplaycode",
						"lngcompanycode",
						$formData[FormMoldReport::DestinationFactory] . ":str",
						'',
						static::$db
					);

				// SQLファイル読み込み
				$query = file_get_contents($this->getQueryFileName("insertMoldReportForMove"));

				// パラメータ作成
				$params = array(
						TableMoldReport::ReportCategory => $formData[FormMoldReport::ReportCategory],
						TableMoldReport::RequestDate => $formData[FormMoldReport::RequestDate],
						TableMoldReport::SendTo => $formData["SendTo"],
						TableMoldReport::ProductCode => $formData[FormMoldReport::ProductCode],
						TableMoldReport::GoodsCode => $formData[FormMoldReport::GoodsCode],
						TableMoldReport::RequestCategory => $formData[FormMoldReport::RequestCategory],
						TableMoldReport::ActionRequestDate => $formData[FormMoldReport::ActionRequestDate],
						TableMoldReport::TransferMethod => $formData[FormMoldReport::TransferMethod],
						TableMoldReport::SourceFactory => $formData[FormMoldReport::SourceFactory],
						TableMoldReport::DestinationFactory => $formData[FormMoldReport::DestinationFactory],
						TableMoldReport::InstructionCategory => $formData[FormMoldReport::InstructionCategory],
						TableMoldReport::CustomerCode => $formData[FormMoldReport::CustomerCode],
						TableMoldReport::KuwagataGroupCode => $formData[FormMoldReport::KuwagataGroupCode],
						TableMoldReport::KuwagataUserCode => $formData[FormMoldReport::KuwagataUserCode],
						TableMoldReport::FinalKeep => $formData[FormMoldReport::FinalKeep],
						TableMoldReport::ReturnSchedule => $formData[FormMoldReport::ReturnSchedule],
						TableMoldReport::Note => $formData[FormMoldReport::Note],
						TableMoldReport::MarginalNote => $formData[FormMoldReport::MarginalNote],
						TableMoldReport::CreateBy => $this->getUserCode(),
						TableMoldReport::UpdateBy => $this->getUserCode(),
						TableMoldReport::ReviseCode => $formData[FormMoldReport::ReviseCode]
				);
				break;

			// 廃棄版
			case "30":
				// SQLファイル読み込み
				$query = file_get_contents($this->getQueryFileName("insertMoldReportForDispose"));

				// パラメータ作成
				$params = array(
						TableMoldReport::ReportCategory => $formData[FormMoldReport::ReportCategory],
						TableMoldReport::RequestDate => $formData[FormMoldReport::RequestDate],
						TableMoldReport::SendTo => $formData["SendTo"],
						TableMoldReport::ProductCode => $formData[FormMoldReport::ProductCode],
						TableMoldReport::GoodsCode => $formData[FormMoldReport::GoodsCode],
						TableMoldReport::RequestCategory => $formData[FormMoldReport::RequestCategory],
						TableMoldReport::ActionRequestDate => $formData[FormMoldReport::ActionRequestDate],
						TableMoldReport::InstructionCategory => $formData[FormMoldReport::InstructionCategory],
						TableMoldReport::CustomerCode => $formData[FormMoldReport::CustomerCode],
						TableMoldReport::KuwagataGroupCode => $formData[FormMoldReport::KuwagataGroupCode],
						TableMoldReport::KuwagataUserCode => $formData[FormMoldReport::KuwagataUserCode],
						TableMoldReport::Note => $formData[FormMoldReport::Note],
						TableMoldReport::MarginalNote => $formData[FormMoldReport::MarginalNote],
						TableMoldReport::CreateBy => $this->getUserCode(),
						TableMoldReport::UpdateBy => $this->getUserCode(),
						TableMoldReport::ReviseCode => $formData[FormMoldReport::ReviseCode]
				);
				break;

			// それ以外
			default:
				throw new KidsLogicException("帳票区分が既定外の値です。".[FormMoldReport::ReportCategory]);
				break;
		}

		// クエリ構成
		pg_prepare("", $query);

		// クエリ実行結果が得られた場合
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING結果を取得
			$result = pg_fetch_array($pgResult);
			// 結果配列に製品名称を追加
			$result[FormMoldReport::ProductName] = $formData[FormMoldReport::ProductName];
		}
		// クエリ実行結果が得られなかった場合
		else
		{
			throw new SQLException(
					TableMoldReport::TABLE_NAME."へのINSERTに失敗しました。",
					$query,
					$params
			);
		}

		return $result;
	}

	/**
	 * 金型帳票詳細データの挿入処理を行う。
	 *
	 * @param string id
	 * @param string integer
	 * @param array フォームデータ
	 * @return INSERT件数
	 */
	public function insertMoldReportDetail($id, $revision, array $formData)
	{
		$result = false;

		if (!is_string($id) && !is_integer($revision))
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1: 期待する型 string : 渡された型 ".gettype($id)."\n".
					"引数2: 期待する型 integer : 渡された型 ".gettype($revision)."\n"
			);
		}

		// フォームデータから金型NO/金型説明要素を抽出
		$listMoldNo =$this->extractArray($formData, FormMoldReport::MoldNo);
		$listMoldDescription =$this->extractArray($formData, FormMoldReport::MoldDescription);

		// 金型NO/金型説明 抽出件数のチェック
		if (1 <= count($listMoldNo) && 1 <= count($listMoldDescription) &&
				count($listMoldNo) !== count($listMoldDescription))
		{
			throw new KidsLogicException(
					"金型NO又は金型説明の要素が不正です。"."\n".
					"金型NO 件数 :".count($listMoldNo)."\n".
					"金型説明 件数 :".count($listMoldDescription)."\n"
			);
		}

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// 金型NO件数分走査
		for ($i = 1; $i <= count($listMoldNo); $i++)
		{
			// パラメータ作成
			$params = array(
					TableMoldReportDetail::MoldReportId => $id,
					TableMoldReportDetail::Revision => $revision,
					TableMoldReportDetail::ListOrder => $i,
					TableMoldReportDetail::MoldNo => $listMoldNo[FormMoldReport::MoldNo.$i],
					TableMoldReportDetail::MoldDescription => $listMoldDescription[FormMoldReport::MoldDescription.$i],
					TableMoldReport::CreateBy => $this->getUserCode(),
					TableMoldReport::UpdateBy => $this->getUserCode()
			);

			// クエリ実行結果が得られなかった場合
			if (!$pgResult = pg_execute("", $params))
			{
				throw new SQLException(
						TableMoldReportDetail::TABLE_NAME."へのINSERTに失敗しました。",
						$query,
						$params
				);
			}
		}

		// 挿入件数の設定
		$result = $i - 1;

		return $result;
	}

	/**
	 * 金型帳票関連データの挿入処理を行う。
	 *
	 * @param string $moldNo 金型NO
	 * @param integer $historyNo 履歴番号
	 * @param string $id 金型帳票ID
	 * @param integer $revision リビジョン
	 * @return INSERT件数
	 */
	public function insertMoldReportRelation($moldNo, $historyNo, $id, $revision)
	{
		$result = false;

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// パラメータ作成
		$params = array(
			TableMoldReportRelation::MoldNo => $moldNo,
			TableMoldReportRelation::HistoryNo => $historyNo,
			TableMoldReportRelation::MoldReportId => $id,
			TableMoldReportRelation::Revision => $revision,
			TableMoldReportRelation::CreateBy => $this->getUserCode(),
			TableMoldReportRelation::UpdateBy => $this->getUserCode()
		);

		// クエリ実行結果が得られなかった場合
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING結果を取得
			$result = pg_fetch_array($pgResult);
		}
		else
		{
			throw new SQLException(
					TableMoldReportRelation::TABLE_NAME."へのINSERTに失敗しました。",
					$query,
					$params
					);
		}

		return $result;
	}


	/**
	 * フォームデータを基に金型履歴へデータの挿入処理を行う。
	 *
	 * @param array フォームデータ
	 * @return INSERT件数
	 */
	public function insertMoldHistoryByFormData(array $formData)
	{
		$result = false;
		$returning = array();

		// フォームデータから金型NO要素を抽出
		$listMoldNo =$this->extractArray($formData, FormMoldReport::MoldNo);

		// 金型NO抽出件数のチェック
		if (!count($listMoldNo))
		{
			throw new KidsLogicException(
					"金型NO又は金型説明の要素が不正です。"."\n".
					"金型NO 件数 :".count($listMoldNo)."\n"
			);
		}

		// 移動又は返却の場合
		if ($formData[FormMoldHistory::Status] == "10" || $formData[FormMoldHistory::Status] == "20")
		{
			// 表示コードをキーコード(PK)で置き換える
			// 保管工場
			$formData[FormMoldRegistBase::SourceFactory] =
			fncGetMasterValue(
					"m_company",
					"strcompanydisplaycode",
					"lngcompanycode",
					$formData[FormMoldRegistBase::SourceFactory]. ":str",
					'',
					static::$db
			);

			// 移動先工場
			$formData[FormMoldRegistBase::DestinationFactory] =
			fncGetMasterValue(
					"m_company",
					"strcompanydisplaycode",
					"lngcompanycode",
					$formData[FormMoldRegistBase::DestinationFactory] . ":str",
					'',
					static::$db
			);
		}

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName("insertMoldHistory"));
		pg_prepare("", $query);

		// 金型NO件数分走査
		for ($i = 1; $i <= count($listMoldNo); $i++)
		{
			// パラメータ作成
			$params = array(
				TableMoldHistory::MoldNo => $listMoldNo[FormMoldHistory::MoldNo.$i],
				TableMoldHistory::Status => $formData[FormMoldHistory::Status],
				TableMoldHistory::ActionDate => $formData[FormMoldHistory::ActionDate],
				TableMoldHistory::SourceFactory =>
					$formData[FormMoldHistory::SourceFactory] ?
					$formData[FormMoldHistory::SourceFactory] : null,
				TableMoldHistory::DestinationFactory =>
					$formData[FormMoldHistory::DestinationFactory] ?
					$formData[FormMoldHistory::DestinationFactory] : null,
				TableMoldHistory::Remark1 => pg_escape_string($formData[FormMoldHistory::Remark1]),
				TableMoldHistory::Remark2 => pg_escape_string($formData[FormMoldHistory::Remark2]),
				TableMoldHistory::Remark3 => pg_escape_string($formData[FormMoldHistory::Remark3]),
				TableMoldHistory::Remark4 => pg_escape_string($formData[FormMoldHistory::Remark4]),
				TableMoldReport::CreateBy => $this->getUserCode(),
				TableMoldReport::UpdateBy => $this->getUserCode()
			);

			// クエリ実行結果が得られなかった場合
			if ($pgResult = pg_execute("", $params))
			{
				// RETURNING結果を取得
				$returning[] = pg_fetch_array($pgResult);
			}
			else
			{
				throw new SQLException(
						TableMoldHistory::TABLE_NAME."へのINSERTに失敗しました。",
						$query,
						$params
						);
			}
		}

		if (count($returning))
		{
			$result = $returning;
		}

		return $result;
	}

	/**
	 * 金型履歴へデータの挿入処理を行う。
	 *
	 * @param array 金型履歴レコード
	 * @return INSERT件数
	 */
	public function insertMoldHistory(array $record)
	{
		$result = false;

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// パラメータ作成
		$params = array(
			TableMoldHistory::MoldNo => $record[TableMoldHistory::MoldNo],
			TableMoldHistory::Status => $record[TableMoldHistory::Status],
			TableMoldHistory::ActionDate => $record[TableMoldHistory::ActionDate],
			TableMoldHistory::SourceFactory =>
				$record[TableMoldHistory::SourceFactory] ?
				$record[TableMoldHistory::SourceFactory] : null,
			TableMoldHistory::DestinationFactory =>
				$record[TableMoldHistory::DestinationFactory] ?
				$record[TableMoldHistory::DestinationFactory] : null,
			TableMoldHistory::Remark1 => pg_escape_string($record[TableMoldHistory::Remark1]),
			TableMoldHistory::Remark2 => pg_escape_string($record[TableMoldHistory::Remark2]),
			TableMoldHistory::Remark3 => pg_escape_string($record[TableMoldHistory::Remark3]),
			TableMoldHistory::Remark4 => pg_escape_string($record[TableMoldHistory::Remark4]),
			TableMoldReport::CreateBy => $this->getUserCode(),
			TableMoldReport::UpdateBy => $this->getUserCode()
		);

		// クエリ実行結果が得られなかった場合
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING結果を取得
			$result = pg_fetch_array($pgResult);
		}
		else
		{
			throw new SQLException(
					TableMoldHistory::TABLE_NAME."へのINSERTに失敗しました。",
					$query,
					$params
			);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 仕入マスタ及び詳細テーブルから金型マスタをインポートする
	 * 取り込み済みでない金型NOを対象にする
	 * </pre>
	 *
	 * @return 取り込み件数
	 */
	public function importMoldFromStock()
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array();

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			$result = pg_affected_rows($pgResult);
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * 金型履歴のデータの更新処理を行う。
	 *
	 * @param array フォームデータ
	 * @return UPDATE結果のRETURNING連想配列
	 */
	public function modifyMoldHistory(array $formData)
	{
		$result = false;
		$returning = array();

		$status = $formData[FormMoldHistory::Status];

		// 移動又は返却の場合
		if ($status == "10" || $status == "20")
		{
			// 表示コードをキーコード(PK)で置き換える
			// 保管工場
			$formData[FormMoldHistory::SourceFactory] =
			fncGetMasterValue(
					"m_company",
					"strcompanydisplaycode",
					"lngcompanycode",
					$formData[FormMoldRegistBase::SourceFactory]. ":str",
					'',
					static::$db
					);

			// 移動先工場
			$formData[FormMoldHistory::DestinationFactory] =
			fncGetMasterValue(
					"m_company",
					"strcompanydisplaycode",
					"lngcompanycode",
					$formData[FormMoldRegistBase::DestinationFactory] . ":str",
					'',
					static::$db
					);
		}

		// パラメータ作成
		$params = array(
				// WHERE
				TableMoldHistory::MoldNo => $formData[FormMoldHistory::MoldNo],
				TableMoldHistory::HistoryNo => $formData[FormMoldHistory::HistoryNo],
				TableMoldHistory::Version => $formData["Version"],
				// SET
				TableMoldHistory::ActionDate => $formData[FormMoldHistory::ActionDate],
				TableMoldHistory::SourceFactory =>
					$formData[FormMoldHistory::SourceFactory] ?
					$formData[FormMoldHistory::SourceFactory] : null,
				TableMoldHistory::DestinationFactory =>
					$formData[FormMoldHistory::DestinationFactory] ?
					$formData[FormMoldHistory::DestinationFactory] : null,
				TableMoldHistory::Remark1 => pg_escape_string($formData[FormMoldHistory::Remark1]),
				TableMoldHistory::Remark2 => pg_escape_string($formData[FormMoldHistory::Remark2]),
				TableMoldHistory::Remark3 => pg_escape_string($formData[FormMoldHistory::Remark3]),
				TableMoldHistory::Remark4 => pg_escape_string($formData[FormMoldHistory::Remark4]),
				TableMoldReport::UpdateBy => $this->getUserCode()
		);

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);
		
		// クエリ実行結果が得られなかった場合
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING結果を取得
			$returning[] = pg_fetch_array($pgResult);
		}
		else
		{
			throw new SQLException(
					TableMoldHistory::TABLE_NAME."へのUPDATEに失敗しました。",
					$query,
					$params
					);
		}

		if (count($returning))
		{
			$result = $returning;
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型を取得する
	 * </pre>
	 *
	 * @param string $moldno
	 * @return 索引された金型の連想配列
	 */
	public function selectMold($moldno)
	{
		$result = false;

		if(is_string($moldno))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldno)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldno)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型帳票を取得する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param $revision リビジョン
	 * @param $version バージョン
	 * @return 索引された金型帳票の連想配列
	 */
	public function selectMoldReport($moldReportId, $revision, $version)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldreportid" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision),
					"version" => pg_escape_string($version),
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
					);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 未完了の金型帳票を取得する
	 * </pre>
	 *
	 * @return 未完了の金型帳票の連想配列
	 */
	public function selectUnclosedMoldReport()
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array();

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型履歴を取得する
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @param string $historyNo 金型履歴
	 * @param integer $version
	 * @return 索引された金型履歴の連想配列
	 */
	public function selectMoldHistory($moldNo, $historyNo, $version)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo),
					"version" => pg_escape_string($version)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型履歴を取得する
	 * 索引条件にバージョンを含めない
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @param string $historyNo 金型履歴
	 * @return 索引された金型履歴の連想配列
	 */
	public function selectMoldHistoryWithoutVersion($moldNo, $historyNo)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型帳票詳細を取得する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $revision リビジョン
	 * @return 索引された金型帳票詳細の連想配列
	 */
	public function selectMoldReportDetail($moldReportId, $revision)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"moldreportid" => pg_escape_string($moldReportId),
				"revision" => pg_escape_string($revision)
		);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
			else
			{
				throw new SQLException(
						"検索条件に一致するレコードが存在しませんでした。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型履歴情報を基に金型帳票関連を取得する
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @param string $historyNo 金型履歴
	 * @param boolean $required 検索結果必須フラグ
	 * @return 索引された金型帳票関連の連想配列
	 */
	public function selectMoldReportRelationByHistory($moldNo, $historyNo, $required = false)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo),
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_all($pgResult);
				}
				else
				{
					if ($required)
					{
						throw new SQLException(
							"検索条件に一致するレコードが存在しませんでした。",
							$query,
							$param
						);
					}
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型帳票を基に金型帳票関連を取得する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $revision リビジョン
	 * @param boolean $required 検索結果必須フラグ
	 * @return 索引された金型帳票関連の連想配列
	 */
	public function selectMoldReportRelationByReport($moldReportId, $revision, $required = false)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldreportid" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision),
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_all($pgResult);
				}
				else
				{
					if ($required)
					{
						throw new SQLException(
								"検索条件に一致するレコードが存在しませんでした。",
								$query,
								$param
								);
					}
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型の現在の保管工場(会社コード)を取得する
	 *
	 * 「金型NO」から現在金型が保管されている「工場」(会社コード)を取得
	 *
	 * 「廃棄」ステータスの金型履歴を持つ金型は除外する
	 * 「実施日」が未来日の金型履歴を持つ金型は除外する
	 * 「未完了」ステータスの金型帳票を持つ金型は除外する
	 *
	 * # 現在金型が保管されている「工場」 = 最新の移動先工場
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @return 会社コード
	 */
	public function selectCurrentStorageOfMold($moldNo)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"moldno" => pg_escape_string($moldNo)
		);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$record = pg_fetch_array($pgResult, 0);
				// 現在の保管工場(最新の移動先工場)の取得
				$result = $record[TableMoldReport::DestinationFactory];
			}

			// 索引できなかった場合はそのままfalseを返させる
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型の現在の保管工場(会社コード)を取得する
	 *
	 * 「金型NO」から現在金型が保管されている「工場」(会社コード)を取得
	 *
	 * 「廃棄」ステータスの金型履歴を持つ金型は除外する
	 * 「実施日」が未来日の金型履歴を持つ金型は除外する
	 *
	 * # 現在金型が保管されている「工場」 = 最新の移動先工場
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @return 会社コード
	 */
	public function selectMoldVender($moldNo)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// クエリパラメータ作成(SELECT)
		$param = array(
				"moldno" => pg_escape_string($moldNo)
		);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$record = pg_fetch_array($pgResult, 0);
				// 現在の保管工場(最新の移動先工場)の取得
				$result = $record[TableMold::VenderCode];
			}
			else
			{
				throw new SQLException(
						"検索条件に一致するレコードが存在しませんでした。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * ダイジェスト作成用の金型履歴レコードの索引を行う
	 *
	 * @param array $list_moldNo 金型NOリスト
	 * @return 索引結果連想配列
	 */
	public function selectSummaryOfMoldHistory(array $list_moldno)
	{
		$result = false;

		// プレースホルダ群
		$placeholder = array();
		// クエリパラメータ作成(ダミー)
		$param = array();
		// インデックス
		$i = 1;

		// 金型リスト件数分走査
		foreach ($list_moldno as $moldno)
		{
			$placeholder[] = "$".($i++).",";
			$param[] = $moldno;
		}

		// 末尾の余剰カンマの削除
		$placeholder[] = str_replace(",", "", array_pop($placeholder));

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// プレースホルダの置換
		$query = str_replace("_%moldno%_", implode("\n", $placeholder), $query);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
			else
			{
				$result = array();
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * ダイジェスト作成用の金型帳票/詳細レコードの索引を行う
	 *
	 * @param array $list_moldNo 金型NOリスト
	 * @return 索引結果連想配列
	 */
	public function selectSummaryOfMoldReport(array $list_moldno)
	{
		$result = false;

		// プレースホルダ群
		$placeholder = array();
		// クエリパラメータ作成(ダミー)
		$param = array();
		// インデックス
		$i = 1;

		// 金型リスト件数分走査
		foreach ($list_moldno as $moldno)
		{
			$placeholder[] = "$".($i++).",";
			$param[] = $moldno;
		}

		// 末尾の余剰カンマの削除
		$placeholder[] = str_replace(",", "", array_pop($placeholder));

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// プレースホルダの置換
		$query = str_replace("_%moldno%_", implode("\n", $placeholder), $query);

		// 業務コードの説明を取得する
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
			else
			{
				$result = array();
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * 任意の配列要素から指定条件で要素を抽出する
	 *
	 * @param array $anyArray 任意の配列
	 * @param string $condition 抽出条件
	 * @return 金型NOリスト(array)<br>
	 *         金型NOを抽出できなかった場合は空(長さ0)の配列を返す
	 */
	public static function extractArray(array $anyArray, $condition)
	{
		$result = array();

		if (1 <= count($anyArray) && is_string($condition))
		{
			$index = 1;

			foreach ($anyArray as $key => $value)
			{
				if ($key === $condition.$index)
				{
					$result[$key] = $value;
					$index++;
				}
			}
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型NOが金型マスタ上に存在するか確認を行う
	 * </pre>
	 *
	 * @param string $moldNo
	 * @return boolean
	 */
	public function existsMoldNo($moldNo)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型NOが指定された製品コードと紐付くものかチェックを行う
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @param string $productCode 製品コード
	 * @return boolean
	 */
	public function existsMoldNoWithProductCode($moldNo, $productCode, $revisecode)
	{
		$result = false;

		if(is_string($moldNo) && is_string($productCode) && is_string($revisecode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"productcode" => pg_escape_string($productCode),
					"revisecode" => pg_escape_string($revisecode)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo).
					"引数2:".gettype($productCode).
					"引数3:".gettype($revisecode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型帳票IDが金型帳票マスタ上に存在するか確認を行う
	 * </pre>
	 *
	 * @param string $moldReportId
	 * @return boolean
	 */
	public function existsMoldReportId($moldReportId)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldreportid" => pg_escape_string($moldReportId)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 帳票作成可能な金型NOかチェックを行う
	 *
	 * 1.廃棄された金型NOは帳票作成不可
	 * 2.実施日が現在日よりも未来日の金型履歴を持つ金型NOはは帳票作成不可
	 * </pre>
	 *
	 * @param string $moldno
	 * @return boolean
	 */
	public function isCreateReportForMoldNo($moldNo)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					TableMoldHistory::MoldNo => pg_escape_string($moldNo)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"検索の問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 指定した金型帳票レコードを無効化する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $revision リビジョン
	 * @param integer $version バージョン
	 * @return 更新件数
	 */
	public function disableMoldReport($moldReportId, $revision, $version)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision),
					"version" => pg_escape_string($version)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 指定した金型帳票詳細レコードを無効化する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $revision リビジョン
	 * @return 更新件数
	 */
	public function disableMoldReportDetail($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 指定した金型帳票関連レコードを無効化する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $rivision リビジョン
	 * @return 更新件数
	 */
	public function disableMoldReportRelationByReport($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"rivision" => pg_escape_string($revision)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 指定した金型履歴レコードを無効化する
	 * </pre>
	 *
	 * @param string $moldNo 金型NO
	 * @param string $historyNo 金型履歴
	 * @param integer $version
	 * @return 更新件数
	 */
	public function disableMoldHistory($moldNo, $historyNo, $version)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo),
					"version" => pg_escape_string($version)
			);

			// 業務コードの説明を取得する
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 指定した金型帳票レコードの印刷済フラグを印刷済み(true)に更新する
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $revision リビジョン
	 * @return 更新件数
	 */
	public function updateAlredyPrintedReport($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision)
			);

			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 一致する行が存在する場合
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"条件に一致するレコードが存在しませんでした。",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"問い合わせに失敗しました。",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"引数の型が不正です。".
					"引数1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 未完了の金型帳票レコードを完了にする
	 * </pre>
	 *
	 * @param string $moldReportId 金型帳票ID
	 * @param string $revision リビジョン
	 * @return 更新件数
	 */
	public function updateCloseMoldReport($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// クエリパラメータ作成(SELECT)
			$param = array(
				"moldReportId" => pg_escape_string($moldReportId),
				"revision" => pg_escape_string($revision)
			);

			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// 対象行が存在する場合
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
						"条件に一致するレコードが存在しませんでした。",
						$query,
						$param
					);
				}
			}
			else
			{
				throw new SQLException(
					"問い合わせに失敗しました。",
					$query,
					$param
				);
			}
		}
		else
		{
			throw new InvalidArgumentException(
				"引数の型が不正です。".
				"引数1:".gettype($moldReportId)."\n"
			);
		}

		return $result;
	}

	/**
	 * <pre>
	 * 金型帳票データの修正処理を行う。
	 * </pre>
	 *
	 * @param array フォームデータ
	 * @return INSERT時のRETURNING結果配列
	 */
	public function modifyMoldReport(array $formData)
	{
		$result = false;

		// SendTo項目内容の取得(1つ目の金型から仕入れ元を索引する)
		// TO項目(仕入元会社)の索引 暫定的に最初の金型の仕入元を取得する
		$venderInfo = $this->getVenderInfomation($formData[FormMoldReport::MoldNo."1"]);
		$formData["SendTo"] = $venderInfo["vendercode"];

		// 表示コードをキーコード(PK)で置き換える
		// 事業部
		$formData[FormMoldReport::CustomerCode] =
		fncGetMasterValue(
				"m_company",
				"strcompanydisplaycode",
				"lngcompanycode",
				$formData[FormMoldReport::CustomerCode] . ":str",
				'',
				static::$db
				);

		// KWG部署
		$formData[FormMoldReport::KuwagataGroupCode] =
		fncGetMasterValue(
				"m_group",
				"strgroupdisplaycode",
				"lnggroupcode",
				$formData[FormMoldReport::KuwagataGroupCode] . ":str",
				'',
				static::$db
				);

		// KWG担当者
		$formData[FormMoldReport::KuwagataUserCode] =
		fncGetMasterValue(
				"m_user",
				"struserdisplaycode",
				"lngusercode",
				$formData[FormMoldReport::KuwagataUserCode] . ":str",
				'',
				static::$db
				);

		// 帳票区分でSQL及びパラメータを切り替える
		switch ($formData[FormMoldReport::ReportCategory])
		{
			// 移動版/返却版
			case "10":
			case "20":
				// 表示コードをキーコード(PK)で置き換える
				// 保管工場
				$formData[FormMoldReport::SourceFactory] =
				fncGetMasterValue(
				"m_company",
				"strcompanydisplaycode",
				"lngcompanycode",
				$formData[FormMoldReport::SourceFactory]. ":str",
				'',
				static::$db
				);

				// 移動先工場
				$formData[FormMoldReport::DestinationFactory] =
				fncGetMasterValue(
						"m_company",
						"strcompanydisplaycode",
						"lngcompanycode",
						$formData[FormMoldReport::DestinationFactory] . ":str",
						'',
						static::$db
						);

				// SQLファイル読み込み
				$query = file_get_contents($this->getQueryFileName("modifyMoldReportForMove"));

				// パラメータ作成
				$params = array(
						TableMoldReport::MoldReportId => $formData[FormMoldReport::MoldReportId],
						TableMoldReport::ReportCategory => $formData[FormMoldReport::ReportCategory],
						TableMoldReport::RequestDate => $formData[FormMoldReport::RequestDate],
						TableMoldReport::SendTo => $formData["SendTo"],
						TableMoldReport::ProductCode => $formData[FormMoldReport::ProductCode],
						TableMoldReport::GoodsCode => $formData[FormMoldReport::GoodsCode],
						TableMoldReport::RequestCategory => $formData[FormMoldReport::RequestCategory],
						TableMoldReport::ActionRequestDate => $formData[FormMoldReport::ActionRequestDate],
						TableMoldReport::TransferMethod => $formData[FormMoldReport::TransferMethod],
						TableMoldReport::SourceFactory => $formData[FormMoldReport::SourceFactory],
						TableMoldReport::DestinationFactory => $formData[FormMoldReport::DestinationFactory],
						TableMoldReport::InstructionCategory => $formData[FormMoldReport::InstructionCategory],
						TableMoldReport::CustomerCode => $formData[FormMoldReport::CustomerCode],
						TableMoldReport::KuwagataGroupCode => $formData[FormMoldReport::KuwagataGroupCode],
						TableMoldReport::KuwagataUserCode => $formData[FormMoldReport::KuwagataUserCode],
						TableMoldReport::FinalKeep => $formData[FormMoldReport::FinalKeep],
						TableMoldReport::ReturnSchedule => $formData[FormMoldReport::ReturnSchedule],
						TableMoldReport::Note => $formData[FormMoldReport::Note],
						TableMoldReport::MarginalNote => $formData[FormMoldReport::MarginalNote],
						TableMoldReport::CreateBy => $this->getUserCode(),
						TableMoldReport::UpdateBy => $this->getUserCode(),
						TableMoldReport::ReviseCode => $formData[FormMoldReport::ReviseCode]
				);
				break;

				// 廃棄版
			case "30":
				// SQLファイル読み込み
				$query = file_get_contents($this->getQueryFileName("modifyMoldReportForDispose"));

				// パラメータ作成
				$params = array(
						TableMoldReport::MoldReportId => $formData[FormMoldReport::MoldReportId],
						TableMoldReport::ReportCategory => $formData[FormMoldReport::ReportCategory],
						TableMoldReport::RequestDate => $formData[FormMoldReport::RequestDate],
						TableMoldReport::SendTo => $formData["SendTo"],
						TableMoldReport::ProductCode => $formData[FormMoldReport::ProductCode],
						TableMoldReport::GoodsCode => $formData[FormMoldReport::GoodsCode],
						TableMoldReport::RequestCategory => $formData[FormMoldReport::RequestCategory],
						TableMoldReport::ActionRequestDate => $formData[FormMoldReport::ActionRequestDate],
						TableMoldReport::InstructionCategory => $formData[FormMoldReport::InstructionCategory],
						TableMoldReport::CustomerCode => $formData[FormMoldReport::CustomerCode],
						TableMoldReport::KuwagataGroupCode => $formData[FormMoldReport::KuwagataGroupCode],
						TableMoldReport::KuwagataUserCode => $formData[FormMoldReport::KuwagataUserCode],
						TableMoldReport::Note => $formData[FormMoldReport::Note],
						TableMoldReport::MarginalNote => $formData[FormMoldReport::MarginalNote],
						TableMoldReport::CreateBy => $this->getUserCode(),
						TableMoldReport::UpdateBy => $this->getUserCode(),
						TableMoldReport::ReviseCode => $formData[FormMoldReport::ReviseCode]
				);
				break;

				// それ以外
			default:
				throw new KidsLogicException("帳票区分が既定外の値です。".[FormMoldReport::ReportCategory]);
				break;
		}

		// クエリ構成
		pg_prepare("", $query);

		// クエリ実行結果が得られた場合
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING結果を取得
			$result = pg_fetch_array($pgResult);
			// 結果配列に再販コード、製品名称を追加
			$result[FormMoldReport::ReviseCode] = $formData[FormMoldReport::ReviseCode];
			$result[FormMoldReport::ProductName] = $formData[FormMoldReport::ProductName];
		}
		// クエリ実行結果が得られなかった場合
		else
		{
			throw new SQLException(
					TableMoldReport::TABLE_NAME."へのINSERTに失敗しました。",
					$query,
					$params
					);
		}

		return $result;
	}

	/**
	 * 製品コードから金型NOを取得
	 *
	 * @param $productCode 製品コード
	 * @return 索引結果連想配列
	 */
	public function selectMoldSelectionList($productCode)
	{
		$result = false;

		// クエリパラメータ作成(SELECT)
		$param = array(
				"productcode" => pg_escape_string($productCode)
		);

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// クエリ実行
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
			else
			{
				$result = array();
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}
	/**
	 * 製品コードから金型NOを取得
	 *
	 * @param $productCode 製品コード
	 * @return 索引結果連想配列
	 */
	public function selectMoldSelectionListForModify($productCode, $reviseCode, $moldReportId)
	{
		$result = false;

		// クエリパラメータ作成(SELECT)
		$param = array(
				"productcode" => pg_escape_string($productCode),
				"revisecode" => pg_escape_string($reviseCode),
				"moldreportid" => pg_escape_string($moldReportId)
		);

		// SQLファイル読み込み
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// クエリ実行
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// 一致する行が存在する場合
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
			else
			{
				$result = array();
			}
		}
		else
		{
			throw new SQLException(
					"検索の問い合わせに失敗しました。",
					$query,
					$param
					);
		}

		return $result;
	}
}
