<?
/** 
*	�ǡ����������ݡ����ѥ饤�֥��
*
*	�ǡ����������ݡ����Ѵؿ��饤�֥��
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.04.07	LCͽ��ɽ��ɽ�����ܤ��ɲ�
*	2004.05.13	LCͽ��ɽ��ɽ�����ܤ��ɲ� Ǽ�ʾ����ɲ�
*	2004.05.13	�����ȥ���ɲ�
*/

//////////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////////
// ����ID���
define ( "DEF_QUERY_ROOT", SRC_ROOT . "dataex/cmn/" ); // ������ѥ�

define ( "DEF_EXPORT_SALES",    1 ); // ���쥷��
define ( "DEF_EXPORT_PURCHASE", 2 ); // Purchase Recipe
define ( "DEF_EXPORT_LC",       3 ); // L/Cͽ��ɽ����
define ( "DEF_EXPORT_STOCK",    4 ); // ��������ɽ
define ( "DEF_EXPORT_ESTIMATE", 5 ); // ���Ѹ�����

// lngExportData ��ź���Ȥ���ƥ�ץ졼�ȥե�����ǥ��쥯�ȥ������
$aryDirName = array (
	DEF_EXPORT_SALES    => "sales",
	DEF_EXPORT_PURCHASE => "purchase",
	DEF_EXPORT_LC       => "lc",
	DEF_EXPORT_STOCK    => "stock",
	DEF_EXPORT_ESTIMATE => "estimate"
);

// 2004.05.13 suzukaze update start
// ���쥷��
$aryTitleName[1][1] = "���쥷�ԡ����硦�ܵ���";
$aryTitleName[1][2] = "���쥷�ԡ����硦������";
// Purchase Recipe
$aryTitleName[2][1] = "�У�������塡�ң����塡�ʣ̣á�";
$aryTitleName[2][2] = "�У�������塡�ң����塡�ʣԣԡ�";
$aryTitleName[2][3] = "�У�������塡�ң����塡�ʣϣ�£�����";
// LCͽ��ɽ
$aryTitleName[3][1] = "�̡���ͽ��ɽ�ʿ�����";
$aryTitleName[3][2] = "�̡���ͽ��ɽ�ʥ�Х�����";
// ��������ɽ
$aryTitleName[4][1] = "��������ɽ���������ܡ���������";
$aryTitleName[4][2] = "��������ɽ���������ܡ����硦������";

// ���Ѹ�����
$aryTitleName[5][1] = "���Ѹ�����";
// 2004.05.13 suzukaze update end


// �����̾���
$aryColumnName[1] = Array ( "���׾���", "���No", "����No", "�ܵҥ�����", "�ܵ�̾��", "���祳����", "����̾��", "��ɼNo", "����ʬ������", "���ʥ�����", "����̾��", "�ܵ�����","�̲�̾��", "ñ��", "ñ��", "����", "��ȴ���", "�ǳ�", "��׶��", "��������" );

$aryColumnName[2] = Array ( "�����׾���", "����No", "ȯ��No", "�����襳����", "������̾��", "���祳����", "����̾��", "ô���ԥ�����", "ô����̾��", "��ɼ������", "�̲�̾��", "�졼�ȥ�����", "�̲ߥ졼��", "��ʧ���", "����������", "�������ܥ�����", "��������̾��", "�������ʥ�����", "��������̾��", "���ʥ�����", "����̾��", "ñ��", "ñ��", "����", "��ȴ���", "��������" );

$aryColumnName[3] = Array ( "P.O.No", "���ֹ�", "��Х���", "����", "��ʧ���", "PO�����å�", "Beneeficiary", "LC��", "����CD", "����̾", "����", "ñ��", "ñ��", "���", "���ѳ��� ͽ����", "���ѽ�λ ͽ����", "�׾���", "������", "Ǽ�ʾ��", "���Ѵ���", "ͭ������", "ȯ�Զ��", "��԰�����", "L/C No", "LC��AM Opening date", "�̲�", "����" );

$aryColumnName[4] = Array ( "�����׾���", "����No", "ȯ��No", "�����襳����", "������̾��", "���祳����", "����̾��", "��ɼ������", "�������ܥ�����", "��������̾��", "�������ʥ�����", "��������̾��", "���ʥ�����", "����̾��", "�ܵ�����", "ñ��", "ñ��", "����", "�Ƕ�ʬ", "�̲�̾��" , "��ȴ���", "�ǳ�", "��׶��", "��׶��TTM" );

$aryColumnName[5] = Array ( "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N");


function getFunctionCode( $lngExportData )
{
	if ( $lngExportData == DEF_EXPORT_SALES )
	{
		$lngFunctionCode = DEF_FUNCTION_DE1;
	}
	elseif ( $lngExportData == DEF_EXPORT_PURCHASE )
	{
		$lngFunctionCode = DEF_FUNCTION_DE2;
	}
	elseif ( $lngExportData == DEF_EXPORT_LC )
	{
		$lngFunctionCode = DEF_FUNCTION_DE3;
	}
	elseif ( $lngExportData == DEF_EXPORT_STOCK )
	{
		$lngFunctionCode = DEF_FUNCTION_DE4;
	}

	elseif ( $lngExportData == DEF_EXPORT_ESTIMATE )
	{
		$lngFunctionCode = DEF_FUNCTION_DE5;
	}

	return $lngFunctionCode;
}
?>
