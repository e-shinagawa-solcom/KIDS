/*
	���ס����롼�׸���
	�оݡ��桼��������
	������chiba
	��������ƣ�»�
	���͡���Ҥ�����פ���֥��롼�סװ���������
*/
SELECT DISTINCT ON ( mg.lngGroupCode ) mg.lngGroupCode, (mg.strGroupDisplayCode || ' ' || mg.strGroupDisplayName) AS strgroupdisplaycodename
FROM m_group mg, m_attributerelation mar
WHERE mg.lngcompanycode = mar.lngcompanycode
	AND mar.lngattributecode <> 0
	AND mg.bytGroupDisplayFlag = TRUE
	AND mg.lngCompanyCode = _%strFormValue0%_ 
ORDER BY mg.lnggroupcode
