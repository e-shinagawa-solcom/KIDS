/*
	���ס������祳���ɡפ����̾�Ρפ����
	�оݡ��ʶ��̡�
	��������ƣ�»�
	���͡��֥����ɡפ�����פ����̾�Ρפ����
*/
SELECT mg.strgroupdisplaycode, mg.strgroupdisplayname
FROM m_group mg, m_attributerelation mar
WHERE  mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mg.strgroupdisplaycode = '_%strFormValue0%_'
