/*
	���ס��ֻ����襳���ɡפ����̾�Ρפ����
	�оݡ��ʶ��̡�
	��ܮ��watanabe
	�����������β
	���͡��
*/
SELECT mc.lngcompanycode, mc.strcompanydisplayname , lngcountrycode
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 3
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode = '_%strFormValue0%_'
ORDER BY mc.strcompanydisplaycode
