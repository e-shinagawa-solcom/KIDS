/*
	���ס���Ǽ�ʾ�ꥳ���ɡפ����̾�Ρפ����
	�оݡ��ʶ��̡�
	��������ƣ�»�
	���͡����פ���֥����ɡ�̾�Ρװ���������
*/
SELECT mc.strcompanydisplaycode, mc.strcompanydisplayname
FROM m_company mc, m_attribute ma, m_attributerelation mar
WHERE mc.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode = ma.lngattributecode
	AND mar.lngattributecode = 5
	AND mc.bytcompanydisplayflag = true
	AND mc.strcompanydisplaycode = '_%strFormValue0%_'
