/*
	���ס��ָܵҥ����ɡפ����̾�Ρפ����
	�оݡ��ʶ��̡�
	��������ƣ�»�
	���͡����פ���֥����ɡ�̾�Ρפ����
*/
SELECT mc.lngcompanycode, mc.strcompanydisplayname
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 2
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode = '_%strFormValue0%_'
ORDER BY mc.strcompanydisplaycode
