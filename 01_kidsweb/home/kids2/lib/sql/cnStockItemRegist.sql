/*
	���ס��ֻ������ܥ����ɡפ����̾�Ρפ����
	�оݡ�ȯ���������������
	������watanabe
	��������ƣ�»�
	��������������
	���͡��֥����ɡפ�����פ����̾�Ρפ������ȯ��������Ͽ��Ϣ�����ѡ�ɽ���ե饰������ե饰�����ꤵ��Ƥ��ʤ������μ�����
*/
SELECT ms.lngstockitemcode, ms.lngstockitemcode || ' ' || ms.strstockitemname AS lngstockitemcodename 
FROM m_stockitem ms 
WHERE ms.lngstocksubjectcode = _%strFormValue0%_ AND ms.bytdisplayflag = TRUE AND ms.bytinvalidflag = FALSE 
ORDER BY ms.lngstockitemcode
