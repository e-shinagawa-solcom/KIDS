/*
	���ס���lngGroupCode�פ����strGroupDisplayCode�פ����
	�оݡ��ʶ��̡�
	��������ƣ�»�
	���͡��֥����ɡפ�����פ����̾�Ρפ����
*/
SELECT mg.strgroupdisplaycode, mg.strgroupdisplaycode
FROM m_group mg, m_attributerelation mar
WHERE  mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mg.lnggroupcode = '_%strFormValue0%_'
