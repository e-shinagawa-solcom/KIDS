<?
// ----------------------------------------------------------------------------
/**
*       �ǥХå��Ѵؿ�
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*		DEF_DEBUG_DIR �ʲ��˥ǥХå��ѥ��ե��������Ϥ��ޤ���
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

include_once("conf.inc");

//
// ���ס�����ץǡ�����������
// ������
// @param	$strOutFile	������ե�����̾
// @param	$objValue		������о��ѿ�
// @param	$strFile	__FILE__
// @param	$strLine	__LINE__
//
// ����͡������� True�����Ի� False
//
function fncDebug($strOutFile, $objValue, $strFile="", $strLine="", $mode="w")
{
	$strOutFile = DEF_DEBUG_DIR . $strOutFile;
	
	// �ǥ��쥯�ȥ��¸�߳�ǧ
	if( !file_exists( dirname($strOutFile) ) )
	{
		return false;
	}
	
	// ���ֲ�ư�⡼�ɤξ���̵�뤹��
	if( DEF_DEBUG_MODE == 0 )
	{
		return false;
	}

	
	// �Хåե���󥰳���
	ob_start();
	// ����׳���
	echo "===== ".date("Y-m-d H:i:s", time()) . " ===== >> " . $strFile . " [" . $strLine . "]\n";

	var_dump($objValue);
	// �ե�����񤭹���
	$fp = fopen($strOutFile, $mode);
	fwrite($fp, ob_get_contents() );
	fclose($fp);
	
	// �Хåե���󥰽�λ
	ob_end_clean();
	
	return true;

}

?>
