/*
	���ס��������ܤΥ����å�
	�оݡ�ȯ������
	��������ƣ�»�
	���͡����򤵤줿�������ܤ���401�פ����ͤ����ξ��ηٹ��å�����
*/
SELECT COUNT(*),
		CASE WHEN COUNT(*) = 1 THEN '�������ʤ����Ƥ�����͡פ˵������Ʋ�������'
			ELSE ''
		END  as alert
WHERE
	 401 =  '_%strFormValue0%_'
	 AND '' = '_%strFormValue1%_'
