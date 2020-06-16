update m_estimate set
    --固定費
    curfixedcost = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngstocksubjectcode is not null and bytpayofftargetflag = true and lngestimateno=m_estimate.lngestimateno and lngrevisionno=m_estimate.lngrevisionno),0)
    --部材費
   ,curmembercost = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngstocksubjectcode is not null and bytpayofftargetflag = false and lngestimateno=m_estimate.lngestimateno and lngrevisionno=m_estimate.lngrevisionno),0)
    --製品売上高
   ,cursalesamount = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngsalesdivisioncode = 2 and lngestimateno=m_estimate.lngestimateno and lngrevisionno=m_estimate.lngrevisionno),0);

update m_estimate 
set
  curmembercost = coalesce( 
    ( 
      select
        SUM(coalesce(cursubtotalprice, 0)) 
      from
        t_estimatedetail ted
	  left join  m_stockitem msi
	  on ted.lngstocksubjectcode = msi.lngstocksubjectcode
	  and ted.lngstockitemcode = msi.lngstockitemcode
      where
        lngestimateareaclassno in (4,5) 
        and lngestimateno = m_estimate.lngestimateno 
        and lngrevisionno = m_estimate.lngrevisionno
    ) 
    , 0
  );
  
update m_estimate 
set
    curmanufacturingcost = curfixedcost + curmembercost	--製造費  償却費合計+部材費
   ,curtotalprice = cursalesamount - (curfixedcost + curmembercost) 	--売上総利益  製品売上高-製造費 + 固定費利益
where lngrevisionno = 0;

update m_estimate set
    curprofit = curtotalprice - trunc((curtotalprice*0.060800),0)	--営業利益   売上総利益 - (売上総利益*標準割合）
where lngrevisionno = 0;



select
  me.cursalesamount - (me.curfixedcost + me.curmembercost) + coalesce(curfixedcostsales,0) - coalesce(curnotdepreciationcost,0)
from
  m_estimate me 
  left join ( 
    select
      SUM(coalesce(cursubtotalprice, 0)) curfixedcostsales
      , lngestimateno
      , lngrevisionno 
    from
      t_estimatedetail 
    where
      lngsalesdivisioncode = 1
    group by
      lngestimateno
      , lngrevisionno
  ) curfixedcostsales_ted 
    on me.lngestimateno = curfixedcostsales_ted.lngestimateno 
    and me.lngrevisionno = curfixedcostsales_ted.lngrevisionno 
  left join ( 
    select
      SUM(coalesce(cursubtotalprice, 0)) curnotdepreciationcost
      , lngestimateno
      , lngrevisionno 
    from
      t_estimatedetail ted 
      left join m_stockitem msi 
        on ted.lngstocksubjectcode = msi.lngstocksubjectcode 
        and ted.lngstockitemcode = msi.lngstockitemcode 
    where
      lngestimateareaclassno in (3)
    group by
      lngestimateno
      , lngrevisionno
  ) curnotdepreciationcost_ted 
    on me.lngestimateno = curnotdepreciationcost_ted.lngestimateno 
    and me.lngrevisionno = curnotdepreciationcost_ted.lngrevisionno 
where
  me.strproductcode = '08002'