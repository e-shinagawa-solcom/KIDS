/*
	���ס������ֹ桢Ʊ���ͥ������
	�оݡ��������-������Ͽ
	��������ƣ�»�
	���͡����Ϥ��줿�����ֹ椫����פ����Ʊ������ֹ�Υ�����ȿ��פ����
*/
SELECT COUNT(*), COUNT(*) AS datacount
FROM m_Receive mr
WHERE
	mr.bytInvalidFlag = false
	AND mr.strReceiveCode = '_%strFormValue0%_'
--	AND mr.strReviseCode = '_%strFormValue1%_'
