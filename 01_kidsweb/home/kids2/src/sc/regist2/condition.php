<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��Ͽ
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
*         ����Ͽ����
*         �����顼�����å�
*         ����Ͽ������λ�塢��Ͽ��λ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");

	echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "sc/regist2/condition.tmpl", $aryData ,$objAuth );
			
	return true;
?>