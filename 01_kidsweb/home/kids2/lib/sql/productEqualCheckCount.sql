/*
	���ס����ʡ����硢Ʊ���ͥ������
	�оݡ����̥��֥�����ɥ�
	��������ƣ�»�
	���͡��Ѹ�����硢�����Ʊ������̾�Υ�����ȿ��פ����
*/
SELECT COUNT(*), COUNT(*) AS datacount
FROM m_product mp, m_group mg
WHERE mp.lnginchargegroupcode = mg.lnggroupcode
	AND mp.bytinvalidflag = false
	AND mg.bytgroupdisplayflag = true
	AND UPPER(mp.strproductenglishname) = UPPER('_%strFormValue0%_')
	AND mg.strgroupdisplaycode = '_%strFormValue1%_'
