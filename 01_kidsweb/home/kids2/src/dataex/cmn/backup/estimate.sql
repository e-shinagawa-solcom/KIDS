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
	,'_%strProductCode%_'
	,'製品名'
	,(select mp.strproductname from m_product mp where mp.strproductcode = '_%strProductCode%_')
	,''
	,''
	,''
	,''
	,''
union
select '04'
	,'部門コード'
	,'20'
	,''
	,''
	,''
	,''
	,''
	,''
	,''
union
select '05'
	,'カートン入り数'
	,'11,000'
	,''
	,''
	,''
	,''
	,''
	,''
	,''
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
	, '\\ ' ||trim(to_char(tet.curSubTotalPrice, '9999,999,999'))
	,tet.strNote
	,''
from
	m_estimate me
	left join t_estimatedetail tet
		on tet.lngestimateno = me.lngestimateno
		and tet.lngrevisionno = me.lngrevisionno
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
me.strproductcode = '_%strProductCode%_'
and me.lngrevisionno = (select max(lngrevisionno) from m_estimate where lngestimateno = me.lngestimateno)
and me.strproductcode = mp.strproductcode
