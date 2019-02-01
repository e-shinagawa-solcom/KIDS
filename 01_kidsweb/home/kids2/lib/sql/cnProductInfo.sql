/*
	概要：「製品コード」から「製品情報」を取得
	対象：発注管理、仕入管理、受注管理、売上管理
	作成：斎藤和志
	更新：
	備考：

		_%strFormValue0%_：製品コード
		_%strFormValue1%_：入力者コード（ログインユーザー）

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
	and ( mp.lngproductstatuscode = 0 or mp.lngproductstatuscode is null) /* WF申請中 */
