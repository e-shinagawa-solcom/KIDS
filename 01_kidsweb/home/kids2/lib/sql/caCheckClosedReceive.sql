/*
	���ס��׾��������������������å�
	�оݡ�����
	��������ƣ�»�
	���͡��׾����˻��ꤵ�줿����˰��ʾ���������ǡ�����¸�ߤ�����硢�ٹ�
*/

SELECT COUNT(*),
		CASE WHEN COUNT(*) >= 1 THEN '���Ϥ��줿�׾���'|| to_char( date_trunc( 'month', to_date('_%strFormValue0%_', 'YYYY/MM') ), 'YYYY/MM') ||'�פϴ�����������Ѥߤΰ����Ͻ���ޤ��󡣷�����������̤��Ʋ�������'
			ELSE ''
		END  as alert
FROM
	m_Receive
WHERE
	lngReceiveStatusCode =99
	AND bytInvalidFlag = FALSE
	AND  to_char( date_trunc( 'month', dtmAppropriationDate ), 'YYYY/MM') = to_char( date_trunc( 'month', to_date('_%strFormValue0%_', 'YYYY/MM') ), 'YYYY/MM')
