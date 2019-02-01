/*
	���ס������ʥ����ɡפ�������ʾ���פ����
	�оݡ�ȯ��������������������������������
	��������ƣ�»�
	������
	���͡�

		_%strFormValue0%_�����ʥ�����
		_%strFormValue1%_�����ϼԥ����ɡʥ�����桼������

*/
SELECT distinct
	mp.lngProductNo
	,mp.strGoodsCode
	,mp.strProductName
	,mp.lngCartonQuantity
	,mg.lnggroupcode as lnginchargegroupcode
	,mg.strgroupdisplaycode
	,mg.strgroupdisplayname
	,(select mu1.lngusercode from m_user mu1 where mu1.lngusercode = mp.lnginchargeusercode) as lngusercode
	,(select mu1.struserdisplaycode from m_user mu1 where mu1.lngusercode = mp.lnginchargeusercode) as struserdisplaycode
	,(select mu1.struserdisplayname from m_user mu1 where mu1.lngusercode = mp.lnginchargeusercode) as struserdisplayname
FROM
	m_Product mp
	full outer join m_group mg
		on mg.lnggroupcode = mp.lnginchargegroupcode
		left join m_grouprelation mgr
			on mg.lnggroupcode = mgr.lnggroupcode
			/* and mgr.bytdefaultflag = true */
			left join m_user mu
				on mgr.lngusercode = mu.lngUserCode
				and mu.lngusercode =  mp.lnginchargeusercode
WHERE
	mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
	/* ����ô���Ԥ��Ф������ϼ� �� �������ϼԤ�°���륰�롼�פΥޥ͡����㡼�ʾ夬���� */
	and _%strFormValue1%_ in
	(
		select
			mu1.lngusercode
		from
			m_user mu1
		,m_grouprelation mgr1
		,
		(
			select
				mu.lngusercode
				,mg.lnggroupcode
			from
				m_user mu
				left join m_grouprelation mgr
					on mgr.lngusercode = mu.lngusercode
					/* and mgr.bytdefaultflag = true */
					left join m_group mg
						on mg.lnggroupcode = mgr.lnggroupcode
			where
			mu.lngusercode = mp.lnginchargeusercode /* ����ô���� */
		) as mst1
		where
			mgr1.lnggroupcode = mst1.lnggroupcode
			/* and mgr1.bytdefaultflag = true */
			and mu1.bytinvalidflag = false
			and mu1.lngusercode = mgr1.lngusercode
/*			and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode)
*/
/*
39����̳3���б��Τ���
*/
			and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode or mu1.lngusercode in ('15','29','242','343'))

	)
	and ( mp.lngproductstatuscode = 0 or mp.lngproductstatuscode is null) /* WF������ */
