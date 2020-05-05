update m_estimate set
    --固定費
    curfixedcost = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngstocksubjectcode is not null and bytpayofftargetflag = true and lngestimateno=m_estimate.lngestimateno and lngrevisionno=m_estimate.lngrevisionno),0)
    --部材費
   ,curmembercost = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngstocksubjectcode is not null and bytpayofftargetflag = false and lngestimateno=m_estimate.lngestimateno and lngrevisionno=m_estimate.lngrevisionno),0)
    --売上総利益
   ,cursalesamount = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngsalesdivisioncode = 2 and lngestimateno=m_estimate.lngestimateno and lngrevisionno=m_estimate.lngrevisionno),0);

update m_estimate 
set
    curmanufacturingcost = curfixedcost + curmembercost	--製造費  償却費合計+部材費
   ,curtotalprice = cursalesamount - (curfixedcost + curmembercost)	--売上総利益  売上総利益-製造費
where lngrevisionno = 0;

update m_estimate set
    curprofit = curtotalprice - trunc((curtotalprice*0.060800),0)	--営業利益   売上総利益 - (売上総利益*標準割合）
where lngrevisionno = 0;
