/*
	���ס����縡��
	�оݡ����̥��֥�����ɥ�
	��������ƣ�»�
	���͡����祳���ɡ�̾�Τ�����פ���֥����ɡ�̾�Ρװ���������
*/
SELECT DISTINCT mg.strgroupdisplaycode,(mg.strgroupdisplaycode || ' ' || mg.strgroupdisplayname) AS strgroupdisplaycodename, mg.strgroupdisplayname
FROM m_group mg, m_attributerelation mar
WHERE  mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mg.strgroupdisplaycode LIKE '%_%strFormValue0%_%'
	AND mg.strgroupdisplayname LIKE '%_%strFormValue1%_%'
ORDER BY mg.strgroupdisplaycode
