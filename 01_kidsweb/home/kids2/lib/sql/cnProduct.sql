/*
	���ס������ʥ����ɡפ��������̾�Ρפ����
	�оݡ�ȯ��������������������������������
	������watanabe
	��������ƣ�»�
	���͡��֥����ɡפ�����פ����̾�Ρפ����
*/
SELECT mp.lngproductno,
	CASE WHEN mp.strproductname IS NULL THEN '������̾�Τ����Ǥ���'
		ELSE mp.strproductname
	END
FROM m_product mp
WHERE mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
	and ( mp.lngproductstatuscode = 0 or mp.lngproductstatuscode is null) /* WF������ */
