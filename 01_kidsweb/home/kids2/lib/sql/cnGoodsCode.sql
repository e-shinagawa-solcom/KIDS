/*
	���ס������ʥ����ɡפ���ָܵ����֡פ����
	�оݡ�ȯ��������������������������������
	������watanabe
	��������ƣ�»�
	���͡��֥����ɡפ�����פ�����͡פ����
*/
SELECT mp.lngproductno, mp.strGoodsCode FROM m_product mp
WHERE mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
