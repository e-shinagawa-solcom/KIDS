/*
	���ס���ɽ���桼���������ɡס�ɽ���桼����̾�פ����ɽ���桼����̾�פ����
	�оݡ�����ե�����
	������chiba
	��������ƣ
	���͡��֥����ɡס�̾�Ρפ�����פ����̾�Ρפ����
	lngCompanyCode = 1   ���塞���ͤΤ�
*/
SELECT mu.strUserDisplayCode,
	mu.strUserDisplayCode || ' ' || mu.strUserDisplayName AS strUserDisplayCodeName,
	mu.strUserDisplayName
FROM m_user mu
WHERE
	mu.lngCompanyCode = 1
	AND mu.bytInvalidFlag = false
	AND mu.bytUserDisplayFlag = true
	AND mu.strUserDisplayCode LIKE '%_%strFormValue0%_%'
	AND mu.strUserDisplayName LIKE '%_%strFormValue1%_%'
ORDER BY mu.strUserDisplayCode
