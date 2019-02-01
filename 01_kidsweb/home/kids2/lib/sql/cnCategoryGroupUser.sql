/*
	���ס��֥��롼�ץ����ɡפ���ִ�Ϣ���륫�ƥ���פ����
	�оݡ����ʸ���
	������
	��������ƣ�»�
	���͡��֥����ɡפ�����פ�����͡פ����
*/
SELECT mc.lngcategorycode, mc.strcategoryname, mc.lngsortkey
FROM m_Category mc
	LEFT JOIN m_CategoryRelation mcr
	ON mc.lngcategorycode = mcr.lngcategorycode
WHERE mc.bytDisplayFlag=true
AND mcr.lnggroupcode in
	(
	select
		mg.lnggroupcode
	from
		m_group as mg 
		left join m_grouprelation mgr
			on mg.lnggroupcode = mgr.lnggroupcode
		,m_user mu
	where
		mg.bytgroupdisplayflag = true
		and mu.bytinvalidflag = false
		and mgr.lngusercode = mu.lngusercode
/*
		and
	 	case when '_%strFormValue1%_' != '' then
	 		mu.struserdisplaycode = '_%strFormValue1%_'
	 	else
	 		true
	 	end
*/
		and
	 	case when ('_%strFormValue0%_' != '' and '_%strFormValue0%_' != '0')then
	 		mg.lnggroupcode = '_%strFormValue0%_'
		else
			true
	 	end
	order by 
		mg.lnggroupcode
	)
GROUP BY mc.lngcategorycode, mc.strcategoryname, mc.lngsortkey
union
SELECT
0, mc.strcategoryname, mc.lngsortkey
FROM m_Category mc where mc.lngcategorycode = 0
ORDER BY lngSortKey
