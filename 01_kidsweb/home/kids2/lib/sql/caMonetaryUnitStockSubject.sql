/*
	���ס��������ܤΥ����å�
	�оݡ�ȯ������
	��������ƣ�»�
	���͡����򤵤줿�������ܤ�����̲ߡ�2(US$)�סֻ������ܡ�433�װʳ��ξ��ηٹ��å�����
*/
SELECT COUNT(*),
		CASE WHEN COUNT(*) = 1 THEN '���ꤵ�줿��������[433]�Ǥ��̲ߤ�[US$]�ˤ���ɬ�פ�����ޤ�'
			ELSE ''
		END  as alert
WHERE
	2 <> (SELECT lngmonetaryunitcode FROM m_monetaryunit mm WHERE mm.strmonetaryunitsign = '_%strFormValue0%_')
	AND 433 =  '_%strFormValue1%_'
