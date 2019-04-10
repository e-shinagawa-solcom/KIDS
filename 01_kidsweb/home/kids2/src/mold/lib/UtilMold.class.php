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
 * �ⷿ�˴ؤ��������Ԥ�
 * clsDB����Ѥ��Ƥ��뤬�������Դ����ʰ���դ��뤳��
 * TODO �ơ��֥뤬���ߤ��Ƥ���Τǥ��饹��ʬ�䤹��
 *
 * @see clsDB
 */
class UtilMold extends WithQuery
{
	/**
	 * �ⷿ�λ����층������������
	 *
	 * @param string $moldNo �ⷿNO
	 * @return ������Ϣ��������֤�
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

			// ������ѥ�᡼������(SELECT)
			$paramMoldVender = array(
					"moldno" => $moldNo
			);

			// �ⷿ�λ����층������������
			pg_prepare(static::$db->ConnectID, "", $queryMoldVender);
			$pgResultMoldVender = pg_execute("", $paramMoldVender);

			// �������䤤��碌�������������
			if ($pgResultMoldVender)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResultMoldVender))
				{
					// ������̤���Ƭ�Ԥ���С����������
					$result = pg_fetch_array($pgResultMoldVender, 0, PGSQL_ASSOC);
				}
				// ���פ���Ԥ�¸�ߤ��ʤ����
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$queryMoldVender,
							$paramMoldVender
					);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$queryMoldVender,
						$paramMoldVender
				);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)."\n"
			);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿĢɼ�ǡ���������������Ԥ���
	 * </pre>
	 *
	 * @param array �ե�����ǡ���
	 * @return INSERT����RETURNING�������
	 */
	public function insertMoldReport(array $formData)
	{
		$result = false;

		// SendTo�������Ƥμ���(1���ܤζⷿ��������층���������)
		// TO����(���������)�κ��� ����Ū�˺ǽ�ζⷿ�λ��������������
		$venderInfo = $this->getVenderInfomation($formData[FormMoldReport::MoldNo."1"]);
		$formData["SendTo"] = $venderInfo["vendercode"];

		// ɽ�������ɤ򥭡�������(PK)���֤�������
		// ������
		$formData[FormMoldReport::CustomerCode] =
			fncGetMasterValue(
				"m_company",
				"strcompanydisplaycode",
				"lngcompanycode",
				$formData[FormMoldReport::CustomerCode] . ":str",
				'',
				static::$db
			);

		// KWG����
		$formData[FormMoldReport::KuwagataGroupCode] =
			fncGetMasterValue(
				"m_group",
				"strgroupdisplaycode",
				"lnggroupcode",
				$formData[FormMoldReport::KuwagataGroupCode] . ":str",
				'',
				static::$db
			);

		// KWGô����
		$formData[FormMoldReport::KuwagataUserCode] =
			fncGetMasterValue(
				"m_user",
				"struserdisplaycode",
				"lngusercode",
				$formData[FormMoldReport::KuwagataUserCode] . ":str",
				'',
				static::$db
			);

		// Ģɼ��ʬ��SQL�ڤӥѥ�᡼�����ڤ��ؤ���
		switch ($formData[FormMoldReport::ReportCategory])
		{
			// ��ư��/�ֵ���
			case "10":
			case "20":
				// ɽ�������ɤ򥭡�������(PK)���֤�������
				// �ݴɹ���
				$formData[FormMoldReport::SourceFactory] =
					fncGetMasterValue(
						"m_company",
						"strcompanydisplaycode",
						"lngcompanycode",
						$formData[FormMoldReport::SourceFactory]. ":str",
						'',
						static::$db
					);

				// ��ư�蹩��
				$formData[FormMoldReport::DestinationFactory] =
					fncGetMasterValue(
						"m_company",
						"strcompanydisplaycode",
						"lngcompanycode",
						$formData[FormMoldReport::DestinationFactory] . ":str",
						'',
						static::$db
					);

				// SQL�ե������ɤ߹���
				$query = file_get_contents($this->getQueryFileName("insertMoldReportForMove"));

				// �ѥ�᡼������
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
						TableMoldReport::UpdateBy => $this->getUserCode()
				);
				break;

			// �Ѵ���
			case "30":
				// SQL�ե������ɤ߹���
				$query = file_get_contents($this->getQueryFileName("insertMoldReportForDispose"));

				// �ѥ�᡼������
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
						TableMoldReport::UpdateBy => $this->getUserCode()
				);
				break;

			// ����ʳ�
			default:
				throw new KidsLogicException("Ģɼ��ʬ�����곰���ͤǤ���".[FormMoldReport::ReportCategory]);
				break;
		}

		// �����깽��
		pg_prepare("", $query);

		// ������¹Է�̤�����줿���
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING��̤����
			$result = pg_fetch_array($pgResult);
			// ������������̾�Τ��ɲ�
			$result[FormMoldReport::ProductName] = $formData[FormMoldReport::ProductName];
		}
		// ������¹Է�̤������ʤ��ä����
		else
		{
			throw new SQLException(
					TableMoldReport::TABLE_NAME."�ؤ�INSERT�˼��Ԥ��ޤ�����",
					$query,
					$params
			);
		}

		return $result;
	}

	/**
	 * �ⷿĢɼ�ܺ٥ǡ���������������Ԥ���
	 *
	 * @param string id
	 * @param string integer
	 * @param array �ե�����ǡ���
	 * @return INSERT���
	 */
	public function insertMoldReportDetail($id, $revision, array $formData)
	{
		$result = false;

		if (!is_string($id) && !is_integer($revision))
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1: ���Ԥ��뷿 string : �Ϥ��줿�� ".gettype($id)."\n".
					"����2: ���Ԥ��뷿 integer : �Ϥ��줿�� ".gettype($revision)."\n"
			);
		}

		// �ե�����ǡ�������ⷿNO/�ⷿ�������Ǥ����
		$listMoldNo =$this->extractArray($formData, FormMoldReport::MoldNo);
		$listMoldDescription =$this->extractArray($formData, FormMoldReport::MoldDescription);

		// �ⷿNO/�ⷿ���� ��з���Υ����å�
		if (1 <= count($listMoldNo) && 1 <= count($listMoldDescription) &&
				count($listMoldNo) !== count($listMoldDescription))
		{
			throw new KidsLogicException(
					"�ⷿNO���϶ⷿ���������Ǥ������Ǥ���"."\n".
					"�ⷿNO ��� :".count($listMoldNo)."\n".
					"�ⷿ���� ��� :".count($listMoldDescription)."\n"
			);
		}

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// �ⷿNO���ʬ����
		for ($i = 1; $i <= count($listMoldNo); $i++)
		{
			// �ѥ�᡼������
			$params = array(
					TableMoldReportDetail::MoldReportId => $id,
					TableMoldReportDetail::Revision => $revision,
					TableMoldReportDetail::ListOrder => $i,
					TableMoldReportDetail::MoldNo => $listMoldNo[FormMoldReport::MoldNo.$i],
					TableMoldReportDetail::MoldDescription => $listMoldDescription[FormMoldReport::MoldDescription.$i],
					TableMoldReport::CreateBy => $this->getUserCode(),
					TableMoldReport::UpdateBy => $this->getUserCode()
			);

			// ������¹Է�̤������ʤ��ä����
			if (!$pgResult = pg_execute("", $params))
			{
				throw new SQLException(
						TableMoldReportDetail::TABLE_NAME."�ؤ�INSERT�˼��Ԥ��ޤ�����",
						$query,
						$params
				);
			}
		}

		// �������������
		$result = $i - 1;

		return $result;
	}

	/**
	 * �ⷿĢɼ��Ϣ�ǡ���������������Ԥ���
	 *
	 * @param string $moldNo �ⷿNO
	 * @param integer $historyNo �����ֹ�
	 * @param string $id �ⷿĢɼID
	 * @param integer $revision ��ӥ����
	 * @return INSERT���
	 */
	public function insertMoldReportRelation($moldNo, $historyNo, $id, $revision)
	{
		$result = false;

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// �ѥ�᡼������
		$params = array(
			TableMoldReportRelation::MoldNo => $moldNo,
			TableMoldReportRelation::HistoryNo => $historyNo,
			TableMoldReportRelation::MoldReportId => $id,
			TableMoldReportRelation::Revision => $revision,
			TableMoldReportRelation::CreateBy => $this->getUserCode(),
			TableMoldReportRelation::UpdateBy => $this->getUserCode()
		);

		// ������¹Է�̤������ʤ��ä����
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING��̤����
			$result = pg_fetch_array($pgResult);
		}
		else
		{
			throw new SQLException(
					TableMoldReportRelation::TABLE_NAME."�ؤ�INSERT�˼��Ԥ��ޤ�����",
					$query,
					$params
					);
		}

		return $result;
	}


	/**
	 * �ե�����ǡ������˶ⷿ����إǡ���������������Ԥ���
	 *
	 * @param array �ե�����ǡ���
	 * @return INSERT���
	 */
	public function insertMoldHistoryByFormData(array $formData)
	{
		$result = false;
		$returning = array();

		// �ե�����ǡ�������ⷿNO���Ǥ����
		$listMoldNo =$this->extractArray($formData, FormMoldReport::MoldNo);

		// �ⷿNO��з���Υ����å�
		if (!count($listMoldNo))
		{
			throw new KidsLogicException(
					"�ⷿNO���϶ⷿ���������Ǥ������Ǥ���"."\n".
					"�ⷿNO ��� :".count($listMoldNo)."\n"
			);
		}

		// ��ư�����ֵѤξ��
		if ($formData[FormMoldHistory::Status] == "10" || $formData[FormMoldHistory::Status] == "20")
		{
			// ɽ�������ɤ򥭡�������(PK)���֤�������
			// �ݴɹ���
			$formData[FormMoldRegistBase::SourceFactory] =
			fncGetMasterValue(
					"m_company",
					"strcompanydisplaycode",
					"lngcompanycode",
					$formData[FormMoldRegistBase::SourceFactory]. ":str",
					'',
					static::$db
			);

			// ��ư�蹩��
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

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName("insertMoldHistory"));
		pg_prepare("", $query);

		// �ⷿNO���ʬ����
		for ($i = 1; $i <= count($listMoldNo); $i++)
		{
			// �ѥ�᡼������
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

			// ������¹Է�̤������ʤ��ä����
			if ($pgResult = pg_execute("", $params))
			{
				// RETURNING��̤����
				$returning[] = pg_fetch_array($pgResult);
			}
			else
			{
				throw new SQLException(
						TableMoldHistory::TABLE_NAME."�ؤ�INSERT�˼��Ԥ��ޤ�����",
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
	 * �ⷿ����إǡ���������������Ԥ���
	 *
	 * @param array �ⷿ����쥳����
	 * @return INSERT���
	 */
	public function insertMoldHistory(array $record)
	{
		$result = false;

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// �ѥ�᡼������
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

		// ������¹Է�̤������ʤ��ä����
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING��̤����
			$result = pg_fetch_array($pgResult);
		}
		else
		{
			throw new SQLException(
					TableMoldHistory::TABLE_NAME."�ؤ�INSERT�˼��Ԥ��ޤ�����",
					$query,
					$params
			);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �����ޥ����ڤӾܺ٥ơ��֥뤫��ⷿ�ޥ����򥤥�ݡ��Ȥ���
	 * �����ߺѤߤǤʤ��ⷿNO���оݤˤ���
	 * </pre>
	 *
	 * @return �����߷��
	 */
	public function importMoldFromStock()
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array();

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			$result = pg_affected_rows($pgResult);
		}
		else
		{
			throw new SQLException(
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * �ⷿ����Υǡ����ι���������Ԥ���
	 *
	 * @param array �ե�����ǡ���
	 * @return UPDATE��̤�RETURNINGϢ������
	 */
	public function modifyMoldHistory(array $formData)
	{
		$result = false;
		$returning = array();

		$status = $formData[FormMoldHistory::Status];

		// ��ư�����ֵѤξ��
		if ($status == "10" || $status == "20")
		{
			// ɽ�������ɤ򥭡�������(PK)���֤�������
			// �ݴɹ���
			$formData[FormMoldHistory::SourceFactory] =
			fncGetMasterValue(
					"m_company",
					"strcompanydisplaycode",
					"lngcompanycode",
					$formData[FormMoldRegistBase::SourceFactory]. ":str",
					'',
					static::$db
					);

			// ��ư�蹩��
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

		// �ѥ�᡼������
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

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		pg_prepare("", $query);

		// ������¹Է�̤������ʤ��ä����
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING��̤����
			$returning[] = pg_fetch_array($pgResult);
		}
		else
		{
			throw new SQLException(
					TableMoldHistory::TABLE_NAME."�ؤ�UPDATE�˼��Ԥ��ޤ�����",
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
	 * �ⷿ���������
	 * </pre>
	 *
	 * @param string $moldno
	 * @return �������줿�ⷿ��Ϣ������
	 */
	public function selectMold($moldno)
	{
		$result = false;

		if(is_string($moldno))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldno)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldno)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿĢɼ���������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param $revision ��ӥ����
	 * @param $version �С������
	 * @return �������줿�ⷿĢɼ��Ϣ������
	 */
	public function selectMoldReport($moldReportId, $revision, $version)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldreportid" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision),
					"version" => pg_escape_string($version),
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
					);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ̤��λ�ζⷿĢɼ���������
	 * </pre>
	 *
	 * @return ̤��λ�ζⷿĢɼ��Ϣ������
	 */
	public function selectUnclosedMoldReport()
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array();

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
		}
		else
		{
			throw new SQLException(
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿ������������
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @param string $historyNo �ⷿ����
	 * @param integer $version
	 * @return �������줿�ⷿ�����Ϣ������
	 */
	public function selectMoldHistory($moldNo, $historyNo, $version)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo),
					"version" => pg_escape_string($version)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿ������������
	 * �������˥С�������ޤ�ʤ�
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @param string $historyNo �ⷿ����
	 * @return �������줿�ⷿ�����Ϣ������
	 */
	public function selectMoldHistoryWithoutVersion($moldNo, $historyNo)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_array($pgResult, 0);
				}
				else
				{
					throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿĢɼ�ܺ٤��������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $revision ��ӥ����
	 * @return �������줿�ⷿĢɼ�ܺ٤�Ϣ������
	 */
	public function selectMoldReportDetail($moldReportId, $revision)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"moldreportid" => pg_escape_string($moldReportId),
				"revision" => pg_escape_string($revision)
		);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
			if (1 <= pg_num_rows($pgResult))
			{
				$result = pg_fetch_all($pgResult);
			}
			else
			{
				throw new SQLException(
						"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new SQLException(
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿ���������˶ⷿĢɼ��Ϣ���������
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @param string $historyNo �ⷿ����
	 * @param boolean $required �������ɬ�ܥե饰
	 * @return �������줿�ⷿĢɼ��Ϣ��Ϣ������
	 */
	public function selectMoldReportRelationByHistory($moldNo, $historyNo, $required = false)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo),
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_all($pgResult);
				}
				else
				{
					if ($required)
					{
						throw new SQLException(
							"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
						);
					}
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿĢɼ���˶ⷿĢɼ��Ϣ���������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $revision ��ӥ����
	 * @param boolean $required �������ɬ�ܥե饰
	 * @return �������줿�ⷿĢɼ��Ϣ��Ϣ������
	 */
	public function selectMoldReportRelationByReport($moldReportId, $revision, $required = false)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldreportid" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision),
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = pg_fetch_all($pgResult);
				}
				else
				{
					if ($required)
					{
						throw new SQLException(
								"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
								$query,
								$param
								);
					}
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿ�θ��ߤ��ݴɹ���(��ҥ�����)���������
	 *
	 * �ֶⷿNO�פ��鸽�߶ⷿ���ݴɤ���Ƥ���ֹ����(��ҥ�����)�����
	 *
	 * ���Ѵ��ץ��ơ������ζⷿ�������Ķⷿ�Ͻ�������
	 * �ּ»����פ�̤�����ζⷿ�������Ķⷿ�Ͻ�������
	 * ��̤��λ�ץ��ơ������ζⷿĢɼ����Ķⷿ�Ͻ�������
	 *
	 * # ���߶ⷿ���ݴɤ���Ƥ���ֹ���� = �ǿ��ΰ�ư�蹩��
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @return ��ҥ�����
	 */
	public function selectCurrentStorageOfMold($moldNo)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"moldno" => pg_escape_string($moldNo)
		);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
			if (1 <= pg_num_rows($pgResult))
			{
				$record = pg_fetch_array($pgResult, 0);
				// ���ߤ��ݴɹ���(�ǿ��ΰ�ư�蹩��)�μ���
				$result = $record[TableMoldReport::DestinationFactory];
			}

			// �����Ǥ��ʤ��ä����Ϥ��Τޤ�false���֤�����
		}
		else
		{
			throw new SQLException(
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿ�θ��ߤ��ݴɹ���(��ҥ�����)���������
	 *
	 * �ֶⷿNO�פ��鸽�߶ⷿ���ݴɤ���Ƥ���ֹ����(��ҥ�����)�����
	 *
	 * ���Ѵ��ץ��ơ������ζⷿ�������Ķⷿ�Ͻ�������
	 * �ּ»����פ�̤�����ζⷿ�������Ķⷿ�Ͻ�������
	 *
	 * # ���߶ⷿ���ݴɤ���Ƥ���ֹ���� = �ǿ��ΰ�ư�蹩��
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @return ��ҥ�����
	 */
	public function selectMoldVender($moldNo)
	{
		$result = false;

		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
		// ������ѥ�᡼������(SELECT)
		$param = array(
				"moldno" => pg_escape_string($moldNo)
		);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
			if (1 <= pg_num_rows($pgResult))
			{
				$record = pg_fetch_array($pgResult, 0);
				// ���ߤ��ݴɹ���(�ǿ��ΰ�ư�蹩��)�μ���
				$result = $record[TableMold::VenderCode];
			}
			else
			{
				throw new SQLException(
						"�������˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new SQLException(
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * �����������Ⱥ����Ѥζⷿ����쥳���ɤκ�����Ԥ�
	 *
	 * @param array $list_moldNo �ⷿNO�ꥹ��
	 * @return �������Ϣ������
	 */
	public function selectSummaryOfMoldHistory(array $list_moldno)
	{
		$result = false;

		// �ץ졼���ۥ����
		$placeholder = array();
		// ������ѥ�᡼������(���ߡ�)
		$param = array();
		// ����ǥå���
		$i = 1;

		// �ⷿ�ꥹ�ȷ��ʬ����
		foreach ($list_moldno as $moldno)
		{
			$placeholder[] = "$".($i++).",";
			$param[] = $moldno;
		}

		// ������;�ꥫ��ޤκ��
		$placeholder[] = str_replace(",", "", array_pop($placeholder));

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// �ץ졼���ۥ�����ִ�
		$query = str_replace("_%moldno%_", implode("\n", $placeholder), $query);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
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
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * �����������Ⱥ����ѤζⷿĢɼ/�ܺ٥쥳���ɤκ�����Ԥ�
	 *
	 * @param array $list_moldNo �ⷿNO�ꥹ��
	 * @return �������Ϣ������
	 */
	public function selectSummaryOfMoldReport(array $list_moldno)
	{
		$result = false;

		// �ץ졼���ۥ����
		$placeholder = array();
		// ������ѥ�᡼������(���ߡ�)
		$param = array();
		// ����ǥå���
		$i = 1;

		// �ⷿ�ꥹ�ȷ��ʬ����
		foreach ($list_moldno as $moldno)
		{
			$placeholder[] = "$".($i++).",";
			$param[] = $moldno;
		}

		// ������;�ꥫ��ޤκ��
		$placeholder[] = str_replace(",", "", array_pop($placeholder));

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// �ץ졼���ۥ�����ִ�
		$query = str_replace("_%moldno%_", implode("\n", $placeholder), $query);

		// ��̳�����ɤ��������������
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
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
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}

	/**
	 * Ǥ�դ��������Ǥ������������Ǥ���Ф���
	 *
	 * @param array $anyArray Ǥ�դ�����
	 * @param string $condition ��о��
	 * @return �ⷿNO�ꥹ��(array)<br>
	 *         �ⷿNO����ФǤ��ʤ��ä����϶�(Ĺ��0)��������֤�
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
	 * �ⷿNO���ⷿ�ޥ������¸�ߤ��뤫��ǧ��Ԥ�
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
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿNO�����ꤵ�줿���ʥ����ɤ�ɳ�դ���Τ������å���Ԥ�
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @param string $productCode ���ʥ�����
	 * @return boolean
	 */
	public function existsMoldNoWithProductCode($moldNo, $productCode)
	{
		$result = false;

		if(is_string($moldNo) && is_string($productCode))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"productcode" => pg_escape_string($productCode)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo).
					"����2:".gettype($productCode)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿĢɼID���ⷿĢɼ�ޥ������¸�ߤ��뤫��ǧ��Ԥ�
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
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldreportid" => pg_escape_string($moldReportId)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * Ģɼ������ǽ�ʶⷿNO�������å���Ԥ�
	 *
	 * 1.�Ѵ����줿�ⷿNO��Ģɼ�����Բ�
	 * 2.�»���������������̤�����ζⷿ�������ĶⷿNO�Ϥ�Ģɼ�����Բ�
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
			// ������ѥ�᡼������(SELECT)
			$param = array(
					TableMoldHistory::MoldNo => pg_escape_string($moldNo)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_num_rows($pgResult))
				{
					$result = true;
				}
			}
			else
			{
				throw new SQLException(
						"�������䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ���ꤷ���ⷿĢɼ�쥳���ɤ�̵��������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $revision ��ӥ����
	 * @param integer $version �С������
	 * @return �������
	 */
	public function disableMoldReport($moldReportId, $revision, $version)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision),
					"version" => pg_escape_string($version)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"���˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ���ꤷ���ⷿĢɼ�ܺ٥쥳���ɤ�̵��������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $revision ��ӥ����
	 * @return �������
	 */
	public function disableMoldReportDetail($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"���˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ���ꤷ���ⷿĢɼ��Ϣ�쥳���ɤ�̵��������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $rivision ��ӥ����
	 * @return �������
	 */
	public function disableMoldReportRelationByReport($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"rivision" => pg_escape_string($revision)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"���˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ���ꤷ���ⷿ����쥳���ɤ�̵��������
	 * </pre>
	 *
	 * @param string $moldNo �ⷿNO
	 * @param string $historyNo �ⷿ����
	 * @param integer $version
	 * @return �������
	 */
	public function disableMoldHistory($moldNo, $historyNo, $version)
	{
		$result = false;

		if(is_string($moldNo))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldno" => pg_escape_string($moldNo),
					"historyno" => pg_escape_string($historyNo),
					"version" => pg_escape_string($version)
			);

			// ��̳�����ɤ��������������
			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"���˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldNo)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ���ꤷ���ⷿĢɼ�쥳���ɤΰ����ѥե饰������Ѥ�(true)�˹�������
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $revision ��ӥ����
	 * @return �������
	 */
	public function updateAlredyPrintedReport($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
					"moldReportId" => pg_escape_string($moldReportId),
					"revision" => pg_escape_string($revision)
			);

			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// ���פ���Ԥ�¸�ߤ�����
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
							"���˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
							$query,
							$param
							);
				}
			}
			else
			{
				throw new SQLException(
						"�䤤��碌�˼��Ԥ��ޤ�����",
						$query,
						$param
						);
			}
		}
		else
		{
			throw new InvalidArgumentException(
					"�����η��������Ǥ���".
					"����1:".gettype($moldReportId)."\n"
					);
		}

		return $result;
	}

	/**
	 * <pre>
	 * ̤��λ�ζⷿĢɼ�쥳���ɤ�λ�ˤ���
	 * </pre>
	 *
	 * @param string $moldReportId �ⷿĢɼID
	 * @param string $revision ��ӥ����
	 * @return �������
	 */
	public function updateCloseMoldReport($moldReportId, $revision)
	{
		$result = false;

		if(is_string($moldReportId))
		{
			$query = file_get_contents($this->getQueryFileName(__FUNCTION__));
			// ������ѥ�᡼������(SELECT)
			$param = array(
				"moldReportId" => pg_escape_string($moldReportId),
				"revision" => pg_escape_string($revision)
			);

			pg_prepare(static::$db->ConnectID, "", $query);
			$pgResult = pg_execute("", $param);

			if ($pgResult)
			{
				// �оݹԤ�¸�ߤ�����
				if (1 <= pg_affected_rows($pgResult))
				{
					$result = pg_affected_rows($pgResult);
				}
				else
				{
					throw new SQLException(
						"���˰��פ���쥳���ɤ�¸�ߤ��ޤ���Ǥ�����",
						$query,
						$param
					);
				}
			}
			else
			{
				throw new SQLException(
					"�䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
				);
			}
		}
		else
		{
			throw new InvalidArgumentException(
				"�����η��������Ǥ���".
				"����1:".gettype($moldReportId)."\n"
			);
		}

		return $result;
	}

	/**
	 * <pre>
	 * �ⷿĢɼ�ǡ����ν���������Ԥ���
	 * </pre>
	 *
	 * @param array �ե�����ǡ���
	 * @return INSERT����RETURNING�������
	 */
	public function modifyMoldReport(array $formData)
	{
		$result = false;

		// SendTo�������Ƥμ���(1���ܤζⷿ��������층���������)
		// TO����(���������)�κ��� ����Ū�˺ǽ�ζⷿ�λ��������������
		$venderInfo = $this->getVenderInfomation($formData[FormMoldReport::MoldNo."1"]);
		$formData["SendTo"] = $venderInfo["vendercode"];

		// ɽ�������ɤ򥭡�������(PK)���֤�������
		// ������
		$formData[FormMoldReport::CustomerCode] =
		fncGetMasterValue(
				"m_company",
				"strcompanydisplaycode",
				"lngcompanycode",
				$formData[FormMoldReport::CustomerCode] . ":str",
				'',
				static::$db
				);

		// KWG����
		$formData[FormMoldReport::KuwagataGroupCode] =
		fncGetMasterValue(
				"m_group",
				"strgroupdisplaycode",
				"lnggroupcode",
				$formData[FormMoldReport::KuwagataGroupCode] . ":str",
				'',
				static::$db
				);

		// KWGô����
		$formData[FormMoldReport::KuwagataUserCode] =
		fncGetMasterValue(
				"m_user",
				"struserdisplaycode",
				"lngusercode",
				$formData[FormMoldReport::KuwagataUserCode] . ":str",
				'',
				static::$db
				);

		// Ģɼ��ʬ��SQL�ڤӥѥ�᡼�����ڤ��ؤ���
		switch ($formData[FormMoldReport::ReportCategory])
		{
			// ��ư��/�ֵ���
			case "10":
			case "20":
				// ɽ�������ɤ򥭡�������(PK)���֤�������
				// �ݴɹ���
				$formData[FormMoldReport::SourceFactory] =
				fncGetMasterValue(
				"m_company",
				"strcompanydisplaycode",
				"lngcompanycode",
				$formData[FormMoldReport::SourceFactory]. ":str",
				'',
				static::$db
				);

				// ��ư�蹩��
				$formData[FormMoldReport::DestinationFactory] =
				fncGetMasterValue(
						"m_company",
						"strcompanydisplaycode",
						"lngcompanycode",
						$formData[FormMoldReport::DestinationFactory] . ":str",
						'',
						static::$db
						);

				// SQL�ե������ɤ߹���
				$query = file_get_contents($this->getQueryFileName("modifyMoldReportForMove"));

				// �ѥ�᡼������
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
						TableMoldReport::UpdateBy => $this->getUserCode()
				);
				break;

				// �Ѵ���
			case "30":
				// SQL�ե������ɤ߹���
				$query = file_get_contents($this->getQueryFileName("modifyMoldReportForDispose"));

				// �ѥ�᡼������
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
						TableMoldReport::UpdateBy => $this->getUserCode()
				);
				break;

				// ����ʳ�
			default:
				throw new KidsLogicException("Ģɼ��ʬ�����곰���ͤǤ���".[FormMoldReport::ReportCategory]);
				break;
		}

		// �����깽��
		pg_prepare("", $query);

		// ������¹Է�̤�����줿���
		if ($pgResult = pg_execute("", $params))
		{
			// RETURNING��̤����
			$result = pg_fetch_array($pgResult);
			// ������������̾�Τ��ɲ�
			$result[FormMoldReport::ProductName] = $formData[FormMoldReport::ProductName];
		}
		// ������¹Է�̤������ʤ��ä����
		else
		{
			throw new SQLException(
					TableMoldReport::TABLE_NAME."�ؤ�INSERT�˼��Ԥ��ޤ�����",
					$query,
					$params
					);
		}

		return $result;
	}

	/**
	 * ���ʥ����ɤ���ⷿNO�����
	 *
	 * @param $productCode ���ʥ�����
	 * @return �������Ϣ������
	 */
	public function selectMoldSelectionList($productCode)
	{
		$result = false;

		// ������ѥ�᡼������(SELECT)
		$param = array(
				"productcode" => pg_escape_string($productCode)
		);

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// ������¹�
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
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
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}
	/**
	 * ���ʥ����ɤ���ⷿNO�����
	 *
	 * @param $productCode ���ʥ�����
	 * @return �������Ϣ������
	 */
	public function selectMoldSelectionListForModify($productCode, $moldReportId)
	{
		$result = false;

		// ������ѥ�᡼������(SELECT)
		$param = array(
				"productcode" => pg_escape_string($productCode),
				"moldreportid" => pg_escape_string($moldReportId)
		);

		// SQL�ե������ɤ߹���
		$query = file_get_contents($this->getQueryFileName(__FUNCTION__));

		// ������¹�
		pg_prepare(static::$db->ConnectID, "", $query);
		$pgResult = pg_execute("", $param);

		if ($pgResult)
		{
			// ���פ���Ԥ�¸�ߤ�����
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
					"�������䤤��碌�˼��Ԥ��ޤ�����",
					$query,
					$param
					);
		}

		return $result;
	}
}
