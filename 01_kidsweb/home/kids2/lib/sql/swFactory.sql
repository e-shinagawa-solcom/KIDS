/*
	���ס������������������֥�˸���
	�оݡ����̥��֥�����ɥ�
	��������ƣ�»�
	���͡����פ���֥����ɡ�̾�Ρװ���������
*/
SELECT mc.strcompanydisplaycode,(mc.strcompanydisplaycode || ' ' || mc.strcompanydisplayname) AS strcompanydisplaycodename, mc.strcompanydisplayname
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 4
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode LIKE '%_%strFormValue0%_%'
	AND mc.strcompanydisplayname LIKE '%_%strFormValue1%_%'
ORDER BY mc.strcompanydisplaycode
