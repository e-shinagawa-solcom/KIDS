<?php
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');


/**
 * 検索画面でのフォームデータ処理に関する機能を提供する
 *
 */
class UtilSearchForm
{
	/**
	 * 配列から接尾辞が一致する要素群を接尾辞を取り除いた配列を返す。
	 * @param array $formData
	 * @param string $prefix
	 * @return array
	 */
	public static function extractArrayByPrefix(array $formData, $prefix)
	{
		$result = array();

		$pattern = "/^".$prefix."/";

		if (count($formData))
		{
			foreach ($formData as $key => $value)
			{
				// キーの接尾辞と一致する場合
				if (preg_match($pattern, $key))
				{
					// 接尾辞を取り除いたキーの文字列をセット
					$result[preg_replace($pattern, "", $key)] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * FROM,TOの内容を基にSQLのWHERE句文字列を作成する
	 * @param array $from
	 * @param array $to
	 * @param string $column
	 * @return string WHERE句の文字列
	 */
	public static function createQueryCondition(array $from, array $to, $column)
	{
		$result = false;

		if(count($from) && count($to) && is_string($column))
		{
			if($from[$column] && $to[$column])
			{
				$column." between ".$from[$column]." AND ".$to[$column];
			}
			else if ($from[$column] && !$to[$column])
			{
				$column." = ".$from[$column];
			}
			else if (!$from[$column] && $to[$column])
			{
				$column." = ".$to[$column];
			}
		}

		return $result;
	}

	/**
	 * 配列から接尾辞("IsSearch_")が一致する要素群を接尾辞を取り除いた配列を返す。
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByIsSearch(array $formData)
	{
		return static::extractArrayByPrefix($formData, "IsSearch_");
	}

	/**
	 * 配列から接尾辞("IsDisplay_")が一致する要素群を接尾辞を取り除いた配列を返す。
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByIsDisplay(array $formData)
	{
		return static::extractArrayByPrefix($formData, "IsDisplay_");
	}

	/**
	 * 配列から接尾辞("From_")が一致する要素群を接尾辞を取り除いた配列を返す。
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByFrom(array $formData)
	{
		return static::extractArrayByPrefix($formData, "From_");
	}

	/**
	 * 配列から接尾辞("To_")が一致する要素群を接尾辞を取り除いた配列を返す。
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByTo(array $formData)
	{
		return static::extractArrayByPrefix($formData, "To_");
	}

	/**
	 * 配列から接尾辞("Option_")が一致する要素群を接尾辞を取り除いた配列を返す。
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByOption(array $formData)
	{
		return static::extractArrayByPrefix($formData, "Option_");
	}

	/**
	 * 金型履歴検索結果画面のカラムの表示順を示す連想配列を返す
	 *
	 * @return array
	 */
	public static function getColumnOrderForMoldHistory()
	{
		$order = array();

		// 金型帳票ID
		$order[TableMoldReport::MoldReportId] = TableMoldReport::MoldReportId;
		// 仕入マスタ.仕入計上日
		$order["dtmappropriationdate"] = "dtmappropriationdate";
		// 発注マスタ.発注コード-発注マスタ.修正コード
		$order["strordercode"] = "strordercode";
		// [会社マスタ.表示会社コード] 会社マスタ.表示会社名称
		$order["strcompanydisplaycode"] = "strcompanydisplaycode";
		// 製品マスタ.製品コード
		$order["strproductcode"] = "strproductcode";
		// 金型履歴.金型NO
		$order[TableMoldHistory::MoldNo] = TableMoldHistory::MoldNo;
		// 金型履歴.履歴番号
		$order[TableMoldHistory::HistoryNo] = TableMoldHistory::HistoryNo;
		// 製品マスタ.製品名称(日本語)
		$order["strproductname"] = "strproductname";
		// 製品マスタ.製品名称(英語)
		$order["strproductenglishname"] = "strproductenglishname";
		// 製品マスタ.顧客品番
		$order["strgoodscode"] = "strgoodscode";
		// [グループマスタ.表示グループコード] グループマスタ.表示グループ名
		$order["strgroupdisplaycode"] = "strgroupdisplaycode";
		// [ユーザマスタ.表示ユーザコード] ユーザマスタ.表示ユーザ名
		$order["struserdisplaycode"] = "struserdisplaycode";
		// 仕入詳細.製品数量
		$order["lngproductquantity"] = "lngproductquantity";
		// 通貨単位マスタ.通貨単位 || 仕入詳細.税抜金額
		$order["strmonetaryunitsign"] = "strmonetaryunitsign";
		// 金型履歴.金型ステータス
		$order[TableMoldHistory::Status] = TableMoldHistory::Status;
		// 金型履歴.実施日
		$order[TableMoldHistory::ActionDate] = TableMoldHistory::ActionDate;
		// 金型履歴.保管工場
		$order[TableMoldHistory::SourceFactory] = TableMoldHistory::SourceFactory;
		// 金型履歴.移動先工場
		$order[TableMoldHistory::DestinationFactory] = TableMoldHistory::DestinationFactory;
		// 金型履歴.登録日時
		$order[TableMoldHistory::Created] = TableMoldHistory::Created;
		// 金型履歴.登録者
		$order[TableMoldHistory::CreateBy] = TableMoldHistory::CreateBy;
		// 金型履歴.更新日時
		$order[TableMoldHistory::Updated] = TableMoldHistory::Updated;
		// 金型履歴.更新者
		$order[TableMoldHistory::UpdateBy] = TableMoldHistory::UpdateBy;
		// 金型履歴.削除フラグ
		$order[TableMoldHistory::DeleteFlag] = TableMoldHistory::DeleteFlag;

		return $order;
	}

	/**
	 * 金型帳票検索結果画面のカラムの表示順を示す連想配列を返す
	 *
	 * @return array
	 */
	public static function getColumnOrderForMoldReport()
	{
		$order = array();

		// 帳票区分
		$order[TableMoldReport::ReportCategory] = TableMoldReport::ReportCategory;
		// ステータス
		$order[TableMoldReport::Status] = TableMoldReport::Status;
		// 依頼日
		$order[TableMoldReport::RequestDate] = TableMoldReport::RequestDate;
		// 金型帳票ID
		$order[TableMoldReport::MoldReportId] = TableMoldReport::MoldReportId;
		// リビジョン
		$order[TableMoldReport::Revision] = TableMoldReport::Revision;
		// 製品コード
		$order[TableMoldReport::ProductCode] = TableMoldReport::ProductCode;
		// 製品名称
		$order["strproductname"] = strproductname;
		// 製品名称(英語)
		$order["strproductenglishname"] = strproductenglishname;
		// 顧客品番
		$order[TableMoldReport::GoodsCode] = TableMoldReport::GoodsCode;
		// 金型NO
		$order[TableMoldReportDetail::MoldNo] = TableMoldReportDetail::MoldNo;
		// 保管工場
		$order[TableMoldReport::SourceFactory] = TableMoldReport::SourceFactory;
		// 移動先工場
		$order[TableMoldReport::DestinationFactory] = TableMoldReport::DestinationFactory;
		// 依頼区分
		$order[TableMoldReport::RequestCategory] = TableMoldReport::RequestCategory;
		// 希望日
		$order[TableMoldReport::ActionRequestDate] = TableMoldReport::ActionRequestDate;
		// 移動方法
		$order[TableMoldReport::TransferMethod] = TableMoldReport::TransferMethod;
		// 指示区分
		$order[TableMoldReport::InstructionCategory] = TableMoldReport::InstructionCategory;
		// 事業部(顧客)
		$order[TableMoldReport::CustomerCode] = TableMoldReport::CustomerCode;
		// KWG担当部署
		$order[TableMoldReport::KuwagataGroupCode] = TableMoldReport::KuwagataGroupCode;
		// KWG担当者
		$order[TableMoldReport::KuwagataUserCode] = TableMoldReport::KuwagataUserCode;
		// 生産後の処理
		$order[TableMoldReport::FinalKeep] = TableMoldReport::FinalKeep;
		// 返却予定日
		$order[TableMoldReport::ReturnSchedule] = TableMoldReport::ReturnSchedule;
		// 登録日
		$order[TableMoldReport::Created] = TableMoldReport::Created;
		// 登録者
		$order[TableMoldReport::CreateBy] = TableMoldReport::CreateBy;
		// 更新日
		$order[TableMoldReport::Updated] = TableMoldReport::Updated;
		// 更新者
		$order[TableMoldReport::UpdateBy] = TableMoldReport::UpdateBy;
		// 削除フラグ
		$order[TableMoldReport::DeleteFlag] = TableMoldReport::DeleteFlag;

		return $order;
	}
}
