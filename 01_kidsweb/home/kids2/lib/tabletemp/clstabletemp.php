<?
// ----------------------------------------------------------------------------
/**
*       �ƥ�ݥ�꡼�ơ��֥����饹
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    1.00
*
*
*       ��������
*   	t_temp �ơ��֥�ؤΥǡ�����Ͽ��������Ԥ���
*
*       ��������
*		2006/05/27	New
*		2006/05/28  strKey ����Ͽ�������� ���ƾ�ʸ�����Ѵ�����褦�ˤ���
*
*/
// ----------------------------------------------------------------------------
//include_once('conf.inc');
//require_once(LIB_FILE);

class clsTableTemp
{
	var $objDB;
	var $aryTemp = array();

	// -------------------------------------------------------
	// constructer
	// -------------------------------------------------------
	function clsTableTemp()
	{
		unset($this->objDB);
	}

	// -------------------------------------------------------
	// Insert
	// Arg	$aryIn	- product info
	// Return	lngTempNo
	// -------------------------------------------------------
	function fncInsert($aryIn)
	{
		if(!isset($aryIn))
		{
			return false;
		}

		$lngFatalCnt=0;

		// �ȥ�󥶥�����󳫻�
		$this->objDB->transactionBegin();

		// t_temp.lngtempno �μ���
		$lngtempno = fncGetSequence("t_temp.lngtempno", $this->objDB);

		// $aryIn ����ʬ����Ͽ����
		while(list($strKey, $strValue) = each($aryIn))
		{
			$arySql = array();
			$arySql[] = 'insert into t_temp';
			$arySql[] = '(lngtempno, strkey, strvalue)';
			$arySql[] = 'values('.$lngtempno.", '".strtolower($strKey)."', '".$strValue."')";
			$strSql = implode($arySql,"\n");

			// ��Ͽ�¹�
			list ($lngResultID, $lngResultNum) = fncQuery($strSql, $this->objDB);
			// ���顼�κݤ�DB���顼��ɽ�������
		}

		// ���ߥå�
		$this->objDB->transactionCommit();

		return $lngtempno;
	}

	// -------------------------------------------------------
	// Select
	// -------------------------------------------------------
	function fncSelect($lngTempNo)
	{

		$arySql = array();
		$arySql[] = 'select strKey, strValue from t_temp';
		$arySql[] = 'where lngtempno='.$lngTempNo;
		$strSql = implode($arySql,"\n");
		
		// strKey, strValue �����
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $this->objDB );

		// strKey, strValue ���Ȥ߹�碌����С��ѿ�������
		for($i = 0; $i < $lngResultNum; $i++)
		{
			$objTemp = $this->objDB->fetchObject( $lngResultID, $i );
			$this->aryTemp[strtolower($objTemp->strkey)] = $objTemp->strvalue;
		}

		return $this->aryTemp;
	}
}

?>
