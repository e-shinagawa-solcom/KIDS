/*
	���ס����硦ô���Ը���
	�оݡ����̥��֥�����ɥ�
	��������ƣ�»�
	���͡����祳���ɡ�ô���ԥ����ɡ�̾�Τ�����פ���֥����ɡ�̾�Ρװ���������
*/
SELECT DISTINCT mu.struserdisplaycode,(mu.struserdisplaycode || ' ' || mu.struserdisplayname) AS struserdisplaycodename, mu.struserdisplayname
FROM m_group mg,  m_grouprelation mgr,  m_user mu,  m_attributerelation mar
WHERE mu.lngcompanycode = mar.lngcompanycode
	AND mu.lngusercode = mgr.lngusercode
	AND mg.lnggroupcode = mgr.lnggroupcode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mu.bytuserdisplayflag = true
	AND mg.strgroupdisplaycode LIKE '%_%strFormValue0%_%'
	AND mu.struserdisplaycode LIKE '%_%strFormValue1%_%'
	AND mu.struserdisplayname LIKE '%_%strFormValue2%_%'
ORDER BY mu.struserdisplaycode
