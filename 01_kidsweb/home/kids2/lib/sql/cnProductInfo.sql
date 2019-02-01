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
			on mg.lnggroupcode = mgr.lnggroupcode and mgr.bytdefaultflag = true
			left join m_user mu
				on mgr.lngusercode = mu.lngUserCode
				and mu.lngusercode =  mp.lnginchargeusercode
WHERE
	mp.bytinvalidflag = false
	AND mp.strproductcode = '_%strFormValue0%_'
	and ( mp.lngproductstatuscode = 0 or mp.lngproductstatuscode is null) /* WF������ */
