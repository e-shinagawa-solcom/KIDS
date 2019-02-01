/*
	���ס������ݡ���塢��Ϣ�����å�
	�оݡ�����
	��������ƣ�»�
	���͡��������ٹԤ��Ф������˰������ƺѤߤ����Ƥ��������������
*/

select
	sum(tsd.lngproductquantity) as lngproductquantity
	,	sum(tsd.lngproductquantity) as lngproductquantity
from
	m_sales ms
		left join t_salesdetail tsd on tsd.lngsalesno = ms.lngsalesno
where
tsd.lngreceiveno in 
(
	select ms1.lngreceiveno
	from
		m_receive ms1
	where
		ms1.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = _%strFormValue0%_)
)
and tsd.lngreceivedetailno = _%strFormValue1%_
and ms.bytinvalidflag = false
AND ms.lngRevisionNo = (
	SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.bytInvalidFlag = false and s1.strSalesCode = ms.strSalesCode)
	AND 0 <= (
		SELECT MIN( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.bytInvalidFlag = false and s2.strSalesCode = ms.strSalesCode )
AND
	tsd.lngconversionclasscode = _%strFormValue2%_

