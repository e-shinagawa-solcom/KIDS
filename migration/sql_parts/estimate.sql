delete from m_estimate;

INSERT INTO m_estimate
(
    lngestimateno
   ,lngrevisionno
   ,strproductcode
   ,strrevisecode
   ,bytdecisionflag
   ,lngestimatestatuscode
   ,curfixedcost
   ,curmembercost
   ,curtotalprice
   ,curmanufacturingcost
   ,cursalesamount
   ,curprofit
   ,lnginputusercode
   ,bytinvalidflag
   ,dtminsertdate
   ,lngproductionquantity
   ,lngtempno
   ,strnote
   ,lngproductrevisionno
)
SELECT * FROM dblink('con111',
    'select ' ||
    'm_estimate.lngestimateno' ||
    ',0 as lngrevisionno' ||
    ',m_estimate.strproductcode' ||
    ',''00'' as strrevisecode' ||
    ',m_estimate.bytdecisionflag' ||
    ',m_estimate.lngestimatestatuscode' ||
    ',m_estimate.curfixedcost' ||
    ',m_estimate.curmembercost' ||
    ',m_estimate.curtotalprice' ||
    ',m_estimate.curmanufacturingcost' ||
    ',m_estimate.cursalesamount' ||
    ',m_estimate.curprofit' ||
    ',m_estimate.lnginputusercode' ||
    ',m_estimate.bytinvalidflag' ||
    ',m_estimate.dtminsertdate' ||
    ',m_estimate.lngproductionquantity' ||
    ',m_estimate.lngtempno' ||
    ',m_estimate.strnote' ||
    ',0 as lngproductrevisionno' ||
    ' from m_estimate ' ||
    'inner join( select lngestimateno, MAX(lngrevisionno) as lngrevisionno from m_estimate group by lngestimateno) rev' ||
    ' on rev.lngestimateno = m_estimate.lngestimateno and rev.lngrevisionno = m_estimate.lngrevisionno ' ||
    'where m_estimate.lngestimateno not in (select lngestimateno from m_estimate where lngrevisionno < 0)'
) AS T1
(
    lngestimateno integer
   ,lngrevisionno integer
   ,strproductcode text
   ,strrevisecode text
   ,bytdecisionflag boolean
   ,lngestimatestatuscode integer
   ,curfixedcost numeric(14,4)
   ,curmembercost numeric(14,4)
   ,curtotalprice numeric(14,4)
   ,curmanufacturingcost numeric(14,4)
   ,cursalesamount numeric(14,4)
   ,curprofit numeric(14,4)
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,lngproductionquantity integer
   ,lngtempno integer
   ,strnote text
   ,lngproductrevisionno integer
);
/*
delete from t_estimatedetail;

--見積原価明細テーブルの内、エリア5の発注データのない分のみ移行
INSERT INTO t_estimatedetail
(
    lngestimateno
   ,lngestimatedetailno
   ,lngrevisionno
   ,lngstocksubjectcode
   ,lngstockitemcode
   ,lngcustomercompanycode
   ,dtmdelivery
   ,bytpayofftargetflag
   ,bytpercentinputflag
   ,lngmonetaryunitcode
   ,lngmonetaryratecode
   ,curconversionrate
   ,lngproductquantity
   ,curproductprice
   ,curproductrate
   ,cursubtotalprice
   ,strnote
   ,lngsortkey
   ,lngsalesdivisioncode
   ,lngsalesclasscode
)
SELECT * FROM dblink('con111','
select 
    t_estimatedetail.lngestimateno
   ,t_estimatedetail.lngestimatedetailno
   ,0 as lngrevisionno
   ,t_estimatedetail.lngstocksubjectcode
   ,t_estimatedetail.lngstockitemcode
   ,t_estimatedetail.lngcustomercompanycode
   ,null as dtmdelivery
   ,t_estimatedetail.bytpayofftargetflag
   ,t_estimatedetail.bytpercentinputflag
   ,t_estimatedetail.lngmonetaryunitcode
   ,t_estimatedetail.lngmonetaryratecode
   ,t_estimatedetail.curconversionrate
   ,t_estimatedetail.lngproductquantity
   ,t_estimatedetail.curproductprice
   ,t_estimatedetail.curproductrate
   ,t_estimatedetail.cursubtotalprice
   ,t_estimatedetail.strnote
   ,t_estimatedetail.lngsortkey
   ,t_estimatedetail.lngsalesdivisioncode
   ,t_estimatedetail.lngsalesclasscode
from t_estimatedetail
inner join m_estimate
    on m_estimate.lngestimateno = t_estimatedetail.lngestimateno
	and m_estimate.lngrevisionno = t_estimatedetail.lngrevisionno
inner join( 
    select lngestimateno, MAX(lngrevisionno) as lngrevisionno from m_estimate group by lngestimateno
) rev
    on rev.lngestimateno = t_estimatedetail.lngestimateno and rev.lngrevisionno = t_estimatedetail.lngrevisionno
where t_estimatedetail.lngestimateno not in (select lngestimateno from m_estimate where lngrevisionno < 0 or bytinvalidflag = true)
    and t_estimatedetail.lngstocksubjectcode in (1224,1230) 
order by t_estimatedetail.lngestimateno,t_estimatedetail.lngestimatedetailno
'
    ) 
AS T(
    lngestimateno integer
   ,lngestimatedetailno integer
   ,lngrevisionno integer
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,dtmdelivery timestamp without time zone
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15,6)
   ,lngproductquantity integer
   ,curproductprice numeric(14,4)
   ,curproductrate numeric(15,6)
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngsortkey integer
   ,lngsalesdivisioncode integer
   ,lngsalesclasscode integer
);

--見積原価明細テーブルの内、エリア5の発注データのない証紙を移行
INSERT INTO t_estimatedetail
(
    lngestimateno
   ,lngestimatedetailno
   ,lngrevisionno
   ,lngstocksubjectcode
   ,lngstockitemcode
   ,lngcustomercompanycode
   ,dtmdelivery
   ,bytpayofftargetflag
   ,bytpercentinputflag
   ,lngmonetaryunitcode
   ,lngmonetaryratecode
   ,curconversionrate
   ,lngproductquantity
   ,curproductprice
   ,curproductrate
   ,cursubtotalprice
   ,strnote
   ,lngsortkey
   ,lngsalesdivisioncode
   ,lngsalesclasscode
)
SELECT * FROM dblink('con111','
select 
    t_estimatedetail.lngestimateno
   ,t_estimatedetail.lngestimatedetailno
   ,0 as lngrevisionno
   ,t_estimatedetail.lngstocksubjectcode
   ,t_estimatedetail.lngstockitemcode
   ,t_estimatedetail.lngcustomercompanycode
   ,null as dtmdelivery
   ,t_estimatedetail.bytpayofftargetflag
   ,t_estimatedetail.bytpercentinputflag
   ,t_estimatedetail.lngmonetaryunitcode
   ,t_estimatedetail.lngmonetaryratecode
   ,t_estimatedetail.curconversionrate
   ,t_estimatedetail.lngproductquantity
   ,t_estimatedetail.curproductprice
   ,t_estimatedetail.curproductrate
   ,t_estimatedetail.cursubtotalprice
   ,t_estimatedetail.strnote
   ,t_estimatedetail.lngsortkey
   ,t_estimatedetail.lngsalesdivisioncode
   ,t_estimatedetail.lngsalesclasscode
from t_estimatedetail
inner join m_estimate
    on m_estimate.lngestimateno = t_estimatedetail.lngestimateno
	and m_estimate.lngrevisionno = t_estimatedetail.lngrevisionno
inner join( 
    select lngestimateno, MAX(lngrevisionno) as lngrevisionno from m_estimate group by lngestimateno
) rev
    on rev.lngestimateno = t_estimatedetail.lngestimateno and rev.lngrevisionno = t_estimatedetail.lngrevisionno
where t_estimatedetail.lngestimateno not in (select lngestimateno from m_estimate where lngrevisionno < 0 or bytinvalidflag = true)
    and t_estimatedetail.lngstocksubjectcode = 401 and t_estimatedetail.lngstockitemcode=1
    and m_estimate.strproductcode not in (
        select distinct
            t_orderdetail.strproductcode
        from m_order
        inner join t_orderdetail
            on t_orderdetail.lngorderno = m_order.lngorderno
            and t_orderdetail.lngrevisionno = m_order.lngrevisionno
        where m_order.bytinvalidflag = false
            and (m_order.strordercode, m_order.lngrevisionno) in (
                select 
                    strordercode
                   ,MAX(lngrevisionno) as lngrevisionno
                from m_order
                where strordercode not in (
                    select strordercode from m_order where lngrevisionno < 0
                )
                group by strordercode
            )
	        and t_orderdetail.lngstocksubjectcode = 401 and t_orderdetail.lngstockitemcode=1
    )
    order by t_estimatedetail.lngestimateno,t_estimatedetail.lngestimatedetailno
'
    ) 
AS T(
    lngestimateno integer
   ,lngestimatedetailno integer
   ,lngrevisionno integer
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,dtmdelivery timestamp without time zone
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15,6)
   ,lngproductquantity integer
   ,curproductprice numeric(14,4)
   ,curproductrate numeric(15,6)
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngsortkey integer
   ,lngsalesdivisioncode integer
   ,lngsalesclasscode integer
);
*/

--COMMIT;