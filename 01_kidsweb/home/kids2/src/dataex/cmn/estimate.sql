/*
	概要：見積原価書（エクセルシート形式フォーマット）
	対象：データエクスポート
	作成：斎藤
	備考：

*/
select '01' as NO
	,''		as A
	,''		as B
	,''		as C
	,''		as D
	,''		as E
	,''		as F
	,''		as G
	,''		as H
	,''		as I
union
select '02'
	,'作成日'
	,to_char(now(), 'yyyy/mm/dd')
	,''
	,''
	,''
	,''
	,''
	,''
	,''

union
select '03'
	,'製品コード'
	,(select mp.strproductcode || '_' || mp.strrevisecode from m_product mp where mp.strproductcode = '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_')
	,'製品名'
	,(select mp.strproductname from m_product mp where mp.strproductcode = '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_')
	,''
	,''
	,''
	,''
	,''
union
select '04'
	,'部門コード'
	,(select mg.strgroupdisplaycode from m_product mp, m_group mg where mp.lngInChargeGroupCode = mg.lnggroupcode and mp.strproductcode =  '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_')
	,'部門名称'
	,(select mg.strgroupdisplayname from m_product mp, m_group mg where mp.lngInChargeGroupCode = mg.lnggroupcode and mp.strproductcode =  '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_')
	,'カテゴリ'
	,(select mcg.strcategoryname from m_product mp, m_category mcg where mp.lngcategorycode = mcg.lngcategorycode and mp.strproductcode =  '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_')
	,''
	,''
	,''
union
select '05'
	,'カートン入り数'
	,to_char(mp.lngCartonQuantity, '9999,999,999')
	,''
	,''
	,''
	,''
	,''
	,''
	,''
from
	m_product mp
where
	mp.strproductcode =  '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_'
union
select '06'
	,''
	,''
	,''
	,''
	,''
	,''
	,''
	,''
	,''
union
select '07'
	,'仕入科目'
	,'仕入部品'
	,'仕入先'
	,'償却'
	,'計画個数'
	,'原価'
	,'計画原価'
	,'備考'
	,''
union
select
	trim(to_char(tet.lngestimatedetailno+7, '00'))
	,trim(to_char(tet.lngstocksubjectcode, '9999')) || ':'|| mss.strstocksubjectname
	,ms.strstockitemname
	,mc.strcompanyname
	,case
		when bytPayOffTargetFlag then '○'
		else ''
	end as bytPayOffTargetFlag
	,trim(to_char(tet.lngProductQuantity, '9999,999,999'))
	,substr(mmu.strmonetaryunitsign, length(mmu.strmonetaryunitsign)) || ' ' ||trim(to_char(tet.curProductPrice, '9999,999,999'))
	, '\ ' ||trim(to_char(tet.curSubTotalPrice, '9999,999,999'))
	,tet.strNote
	,''
from
	m_estimate me
	inner join m_estimatehistory meh
	    on meh.lngestimateno = me.lngestimateno
	    and meh.lngrevisionno = me.lngrevisionno
	left join t_estimatedetail tet
		on tet.lngestimateno = meh.lngestimateno
		and tet.lngrevisionno = meh.lngestimatedetailrevisionno
	left join 
		m_stocksubject mss
		on mss.lngstocksubjectcode = tet.lngstocksubjectcode
			left join m_stockitem ms
				on ms.lngstockitemcode = tet.lngstockitemcode
				and mss.lngstocksubjectcode = ms.lngstocksubjectcode
	left join m_monetaryunit mmu
		on mmu.lngmonetaryunitcode = tet.lngmonetaryunitcode
	left join m_company mc
		on mc.lngcompanycode = tet.lngcustomercompanycode
	,m_product mp
where
me.strproductcode = '_%strProductCode%_' and mp.strrevisecode='_%strReviseCode%_'
and me.lngrevisionno = (select max(lngrevisionno) from m_estimate where lngestimateno = me.lngestimateno)
and me.strproductcode = mp.strproductcode
and me.strrevisecode = mp.strrevisecode
