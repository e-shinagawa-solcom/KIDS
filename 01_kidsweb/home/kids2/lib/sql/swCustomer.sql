/*
	���ס��ܵҸ���
	�оݡ����̥��֥�����ɥ�
	��������ƣ�»�
	���͡��ܵҥ����ɡ�̾�Τ�����פ���֥����ɡ�̾�Ρװ���������
*/
SELECT mc.strcompanydisplaycode,(mc.strcompanydisplaycode || ' ' || mc.strcompanydisplayname) AS strcompanydisplaycodename, mc.strcompanydisplayname
FROM m_company mc, m_attribute a, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = a.lngattributecode
	AND mar.lngattributecode = 2
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode LIKE '%_%strFormValue0%_%'
	AND mc.strcompanydisplayname LIKE '%_%strFormValue1%_%'
ORDER BY mc.strcompanydisplaycode
