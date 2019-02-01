/*
	���ס��ܵҡ�ô���Ը���
	�оݡ����̥��֥�����ɥ�
	��������ƣ�»�
	���͡��ܵҥ����ɡ�ô���ԥ����ɡ�̾�Τ�����פ���֥����ɡ�̾�Ρװ���������
*/
SELECT DISTINCT mu.struserdisplaycode,(mu.struserdisplaycode || ' ' || mu.struserdisplayname) AS struserdisplaycodename, mu.struserdisplayname
FROM m_user mu, m_company mc, m_attributerelation mal
WHERE mc.lngcompanycode = mal.lngcompanycode 
	AND mal.lngattributecode = 2
	AND mu.lngcompanycode = mc.lngcompanycode
	AND mc.bytcompanydisplayflag = true
	AND mu.bytuserdisplayflag = true
	AND mc.strcompanydisplaycode LIKE '%_%strFormValue0%_%'
	AND mu.struserdisplaycode LIKE '%_%strFormValue1%_%'
	AND mu.struserdisplayname LIKE '%_%strFormValue2%_%'
ORDER BY mu.struserdisplaycode
