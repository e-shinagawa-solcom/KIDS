/*
	���ס���ɽ���桼���������ɡפ���֥桼���������ɡפ����
	�оݡ�����ե�����
	������chiba
	��������ƣ
	���͡��֥����ɡפ�����פ���֥����ɡפ����
*/
SELECT mu.lngUserCode, mu.lngUserCode
FROM m_user mu
WHERE
	mu.bytInvalidFlag = false
	AND mu.bytUserDisplayFlag = true
	AND mu.strUserDisplayCode = '_%strFormValue0%_'
