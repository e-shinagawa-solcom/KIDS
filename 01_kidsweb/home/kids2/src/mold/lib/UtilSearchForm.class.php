<?php
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');


/**
 * �������̤ǤΥե�����ǡ��������˴ؤ��뵡ǽ���󶡤���
 *
 */
class UtilSearchForm
{
	/**
	 * ���󤫤������������פ������Ƿ������������������������֤���
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
				// �������������Ȱ��פ�����
				if (preg_match($pattern, $key))
				{
					// ���������������������ʸ����򥻥å�
					$result[preg_replace($pattern, "", $key)] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * FROM,TO�����Ƥ���SQL��WHERE��ʸ������������
	 * @param array $from
	 * @param array $to
	 * @param string $column
	 * @return string WHERE���ʸ����
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
	 * ���󤫤�������("IsSearch_")�����פ������Ƿ������������������������֤���
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByIsSearch(array $formData)
	{
		return static::extractArrayByPrefix($formData, "IsSearch_");
	}

	/**
	 * ���󤫤�������("IsDisplay_")�����פ������Ƿ������������������������֤���
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByIsDisplay(array $formData)
	{
		return static::extractArrayByPrefix($formData, "IsDisplay_");
	}

	/**
	 * ���󤫤�������("From_")�����פ������Ƿ������������������������֤���
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByFrom(array $formData)
	{
		return static::extractArrayByPrefix($formData, "From_");
	}

	/**
	 * ���󤫤�������("To_")�����פ������Ƿ������������������������֤���
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByTo(array $formData)
	{
		return static::extractArrayByPrefix($formData, "To_");
	}

	/**
	 * ���󤫤�������("Option_")�����פ������Ƿ������������������������֤���
	 * @param array $formData
	 * @return array
	 */
	public static function extractArrayByOption(array $formData)
	{
		return static::extractArrayByPrefix($formData, "Option_");
	}

	/**
	 * �ⷿ���򸡺���̲��̤Υ�����ɽ����򼨤�Ϣ��������֤�
	 *
	 * @return array
	 */
	public static function getColumnOrderForMoldHistory()
	{
		$order = array();

		// �ⷿĢɼID
		$order[TableMoldReport::MoldReportId] = TableMoldReport::MoldReportId;
		// �����ޥ���.�����׾���
		$order["dtmappropriationdate"] = "dtmappropriationdate";
		// ȯ��ޥ���.ȯ������-ȯ��ޥ���.����������
		$order["strordercode"] = "strordercode";
		// [��ҥޥ���.ɽ����ҥ�����] ��ҥޥ���.ɽ�����̾��
		$order["strcompanydisplaycode"] = "strcompanydisplaycode";
		// ���ʥޥ���.���ʥ�����
		$order["strproductcode"] = "strproductcode";
		// �ⷿ����.�ⷿNO
		$order[TableMoldHistory::MoldNo] = TableMoldHistory::MoldNo;
		// �ⷿ����.�����ֹ�
		$order[TableMoldHistory::HistoryNo] = TableMoldHistory::HistoryNo;
		// ���ʥޥ���.����̾��(���ܸ�)
		$order["strproductname"] = "strproductname";
		// ���ʥޥ���.����̾��(�Ѹ�)
		$order["strproductenglishname"] = "strproductenglishname";
		// ���ʥޥ���.�ܵ�����
		$order["strgoodscode"] = "strgoodscode";
		// [���롼�ץޥ���.ɽ�����롼�ץ�����] ���롼�ץޥ���.ɽ�����롼��̾
		$order["strgroupdisplaycode"] = "strgroupdisplaycode";
		// [�桼���ޥ���.ɽ���桼��������] �桼���ޥ���.ɽ���桼��̾
		$order["struserdisplaycode"] = "struserdisplaycode";
		// �����ܺ�.���ʿ���
		$order["lngproductquantity"] = "lngproductquantity";
		// �̲�ñ�̥ޥ���.�̲�ñ�� || �����ܺ�.��ȴ���
		$order["strmonetaryunitsign"] = "strmonetaryunitsign";
		// �ⷿ����.�ⷿ���ơ�����
		$order[TableMoldHistory::Status] = TableMoldHistory::Status;
		// �ⷿ����.�»���
		$order[TableMoldHistory::ActionDate] = TableMoldHistory::ActionDate;
		// �ⷿ����.�ݴɹ���
		$order[TableMoldHistory::SourceFactory] = TableMoldHistory::SourceFactory;
		// �ⷿ����.��ư�蹩��
		$order[TableMoldHistory::DestinationFactory] = TableMoldHistory::DestinationFactory;
		// �ⷿ����.��Ͽ����
		$order[TableMoldHistory::Created] = TableMoldHistory::Created;
		// �ⷿ����.��Ͽ��
		$order[TableMoldHistory::CreateBy] = TableMoldHistory::CreateBy;
		// �ⷿ����.��������
		$order[TableMoldHistory::Updated] = TableMoldHistory::Updated;
		// �ⷿ����.������
		$order[TableMoldHistory::UpdateBy] = TableMoldHistory::UpdateBy;
		// �ⷿ����.����ե饰
		$order[TableMoldHistory::DeleteFlag] = TableMoldHistory::DeleteFlag;

		return $order;
	}

	/**
	 * �ⷿĢɼ������̲��̤Υ�����ɽ����򼨤�Ϣ��������֤�
	 *
	 * @return array
	 */
	public static function getColumnOrderForMoldReport()
	{
		$order = array();

		// Ģɼ��ʬ
		$order[TableMoldReport::ReportCategory] = TableMoldReport::ReportCategory;
		// ���ơ�����
		$order[TableMoldReport::Status] = TableMoldReport::Status;
		// ������
		$order[TableMoldReport::RequestDate] = TableMoldReport::RequestDate;
		// �ⷿĢɼID
		$order[TableMoldReport::MoldReportId] = TableMoldReport::MoldReportId;
		// ��ӥ����
		$order[TableMoldReport::Revision] = TableMoldReport::Revision;
		// ���ʥ�����
		$order[TableMoldReport::ProductCode] = TableMoldReport::ProductCode;
		// ����̾��
		$order["strproductname"] = strproductname;
		// ����̾��(�Ѹ�)
		$order["strproductenglishname"] = strproductenglishname;
		// �ܵ�����
		$order[TableMoldReport::GoodsCode] = TableMoldReport::GoodsCode;
		// �ⷿNO
		$order[TableMoldReportDetail::MoldNo] = TableMoldReportDetail::MoldNo;
		// �ݴɹ���
		$order[TableMoldReport::SourceFactory] = TableMoldReport::SourceFactory;
		// ��ư�蹩��
		$order[TableMoldReport::DestinationFactory] = TableMoldReport::DestinationFactory;
		// �����ʬ
		$order[TableMoldReport::RequestCategory] = TableMoldReport::RequestCategory;
		// ��˾��
		$order[TableMoldReport::ActionRequestDate] = TableMoldReport::ActionRequestDate;
		// ��ư��ˡ
		$order[TableMoldReport::TransferMethod] = TableMoldReport::TransferMethod;
		// �ؼ���ʬ
		$order[TableMoldReport::InstructionCategory] = TableMoldReport::InstructionCategory;
		// ������(�ܵ�)
		$order[TableMoldReport::CustomerCode] = TableMoldReport::CustomerCode;
		// KWGô������
		$order[TableMoldReport::KuwagataGroupCode] = TableMoldReport::KuwagataGroupCode;
		// KWGô����
		$order[TableMoldReport::KuwagataUserCode] = TableMoldReport::KuwagataUserCode;
		// ������ν���
		$order[TableMoldReport::FinalKeep] = TableMoldReport::FinalKeep;
		// �ֵ�ͽ����
		$order[TableMoldReport::ReturnSchedule] = TableMoldReport::ReturnSchedule;
		// ��Ͽ��
		$order[TableMoldReport::Created] = TableMoldReport::Created;
		// ��Ͽ��
		$order[TableMoldReport::CreateBy] = TableMoldReport::CreateBy;
		// ������
		$order[TableMoldReport::Updated] = TableMoldReport::Updated;
		// ������
		$order[TableMoldReport::UpdateBy] = TableMoldReport::UpdateBy;
		// ����ե饰
		$order[TableMoldReport::DeleteFlag] = TableMoldReport::DeleteFlag;

		return $order;
	}
}
