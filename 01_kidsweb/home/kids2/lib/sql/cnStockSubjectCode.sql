/*
	���ס��ֻ�����ʬ�����ɡפ���ֻ������ܡפ����
	�оݡ��ʶ��̡�
	���������շ��
	���͡��֥����ɡפ�����פ���֥����ɡפ����
*/
SELECT lngStockSubjectCode, lngStockSubjectCode || ':' || strStockSubjectName
FROM m_Stocksubject
WHERE lngStockClassCode = '_%strFormValue0%_'
