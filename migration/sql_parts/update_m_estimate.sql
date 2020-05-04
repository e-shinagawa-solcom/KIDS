DO $$

declare
c_estimate cursor for
    select lngestimateno, lngrevisionno 
    from m_estimate 
    where lngrevisionno = 0 
        --and NOT (lngestimateno=6120 and  lngrevisionno=0) 
    order by lngestimateno, lngrevisionno;

r_estinate RECORD;    

begin

open c_estimate;
LOOP
    FETCH c_estimate INTO r_estinate;
    EXIT WHEN NOT FOUND;
--RAISE INFO '% %', r_estinate.lngestimateno, r_estinate.lngrevisionno;
	--入力者
    update m_estimate set
        --固定費
        curfixedcost = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngstocksubjectcode is not null and bytpayofftargetflag = true and lngestimateno=r_estinate.lngestimateno and lngrevisionno=r_estinate.lngrevisionno),0)
        --部材費
       ,curmembercost = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngstocksubjectcode is not null and bytpayofftargetflag = false and lngestimateno=r_estinate.lngestimateno and lngrevisionno=r_estinate.lngrevisionno),0)
        --売上総利益
       ,cursalesamount = coalesce((select SUM(coalesce(cursubtotalprice,0)) from t_estimatedetail where lngsalesdivisioncode = 2 and lngestimateno=r_estinate.lngestimateno and lngrevisionno=r_estinate.lngrevisionno),0)
    where lngestimateno=r_estinate.lngestimateno and lngrevisionno=r_estinate.lngrevisionno;
END LOOP;
close c_estimate;

update m_estimate set
    curmanufacturingcost = curfixedcost + curmembercost	--製造費  償却費合計+部材費
   ,curtotalprice = cursalesamount - (curfixedcost + curmembercost)	--売上総利益  売上総利益-製造費
where lngrevisionno >= 0;

update m_estimate set
    curprofit = curtotalprice - trunc((curtotalprice*0.060800),0)	--営業利益   売上総利益 - (売上総利益*標準割合）
where lngrevisionno = 0;

END$$
