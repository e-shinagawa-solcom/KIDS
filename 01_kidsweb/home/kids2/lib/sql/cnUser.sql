/*
	���ס���ô���ԥ����ɡסܡ����祳���ɡפ����̾�Ρפ����
	�оݡ��ʶ��̡�
	��������ƣ�»�
	���͡��֥����ɡפ�����פ����̾�Ρפ����
*/
SELECT mu.struserdisplaycode, mu.struserdisplayname
FROM m_group mg,  m_grouprelation mgr,  m_user mu,  m_attributerelation mar
WHERE  mu.lngcompanycode = mar.lngcompanycode
	AND mu.lngusercode = mgr.lngusercode
	AND mg.lnggroupcode = mgr.lnggroupcode
	AND mar.lngattributecode = 1
	AND mg.bytgroupdisplayflag = true
	AND mu.bytuserdisplayflag = true
	AND mu.struserdisplaycode = '_%strFormValue0%_'
	AND mg.strgroupdisplaycode = '_%strFormValue1%_'
