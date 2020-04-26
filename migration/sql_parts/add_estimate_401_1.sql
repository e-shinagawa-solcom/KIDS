DO $$

declare
    cur cursor for
    select 
        lngestimateno
       ,strproductcode
    from(
        select
            me.lngestimateno
           ,me.strproductcode
           ,count(te.*) as m_cnt
           ,count(tod.*) as o_cnt
        from t_estimatedetail te
        inner join m_estimate me
            on me.lngestimateno = te.lngestimateno
            and me.lngrevisionno = te.lngrevisionno
        left outer join t_orderdetail tod
            on tod.strproductcode = me.strproductcode
            and tod.strrevisecode = me.strrevisecode
            and tod.lngstocksubjectcode = 401
            and tod.lngstockitemcode = 1
        where te.lngrevisionno = 0
            and me.lngestimateno not in (select lngestimateno from m_estimate where lngrevisionno < 0)
            and te.lngstocksubjectcode = 401
            and te.lngstockitemcode = 1
        group by me.lngestimateno,me.strproductcode
    ) T
    where T.o_cnt = 0;
    
    rec RECORD;
    orderno integer;

begin
    open cur;
    LOOP
        FETCH cur INTO rec;
        EXIT WHEN NOT FOUND;
        select MAX(lngorderno) into orderno from  m_order;
RAISE INFO '% % ', rec.lngestimateno, orderno;
        insert into t_estimatedetail(
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
        select
            lngestimateno
           ,coalesce((select max(lngestimatedetailno)+1 from  t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=1),1)
           ,1
           ,lngstocksubjectcode
           ,lngstockitemcode
           ,coalesce(lngcustomercompanycode,0)
           ,coalesce(dtmdelivery,(select max(dtmdelivery) from t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=1))
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
        from t_estimatedetail
        where lngestimateno = rec.lngestimateno and lngstocksubjectcode = 401 and lngstockitemcode = 1;

        insert into m_order(
            lngorderno
           ,lngrevisionno
           ,strordercode
           ,dtmappropriationdate
           ,lngcustomercompanycode
           ,lnggroupcode
           ,lngusercode
           ,lngorderstatuscode
           ,lngmonetaryunitcode
           ,lngmonetaryratecode
           ,curconversionrate
           ,lngpayconditioncode
           ,lngdeliveryplacecode
           ,lnginputusercode
           ,bytinvalidflag
           ,dtminsertdate
        )
        select
            orderno+1
           ,0
           ,'2004' || trim(to_char((select lngsequence + 1 from t_sequence where strsequencename = 'm_order.strordercode.2004'),'0000'))
           ,current_timestamp
           ,coalesce(te.lngcustomercompanycode,0)
           ,mp.lnginchargegroupcode
           ,mp.lnginchargeusercode
           ,1
           ,1
           ,1
           ,1
           ,0
           ,NULL
           ,mp.lnginchargeusercode
           ,false
           ,current_timestamp
        from t_estimatedetail te
        inner join m_estimate me
            on me.lngestimateno = te.lngestimateno
            and me.lngrevisionno = te.lngrevisionno
            and me.lngrevisionno = 1
        inner join m_product mp
            on mp.strproductcode = me.strproductcode
            and mp.strrevisecode = me.strrevisecode
            and mp.lngrevisionno = me.lngrevisionno
        where te.lngestimateno = rec.lngestimateno and te.lngstocksubjectcode = 401 and te.lngstockitemcode = 1;
        update t_sequence set lngsequence = lngsequence + 1 where strsequencename = 'm_order.strordercode.2004';

        insert into t_orderdetail(
            lngorderno
           ,lngorderdetailno
           ,lngrevisionno
           ,strproductcode
           ,strrevisecode
           ,lngstocksubjectcode
           ,lngstockitemcode
           ,dtmdeliverydate
           ,lngdeliverymethodcode
           ,lngconversionclasscode
           ,curproductprice
           ,lngproductquantity
           ,lngproductunitcode
           ,cursubtotalprice
           ,strnote
           ,strmoldno
           ,lngsortkey
           ,lngestimateno
           ,lngestimatedetailno
           ,lngestimaterevisionno
        )
        select 
            orderno+1
           ,1
           ,0
           ,me.strproductcode
           ,me.strrevisecode
           ,te.lngstocksubjectcode
           ,te.lngstockitemcode
           ,coalesce(te.dtmdelivery,(select max(dtmdelivery) from t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=1))
           ,NULL
           ,1
           ,te.curproductprice
           ,te.lngproductquantity
           ,1
           ,te.cursubtotalprice
           ,te.strnote
           ,NULL
           ,(select max(lngestimatedetailno) from  t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=1)
           ,te.lngestimateno
           ,(select max(lngestimatedetailno) from  t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=1)
           ,1
        from t_estimatedetail te
        inner join m_estimate me
            on me.lngestimateno = te.lngestimateno
            and me.lngrevisionno = te.lngrevisionno
            and me.lngrevisionno = 1
        where te.lngestimateno = rec.lngestimateno and te.lngstocksubjectcode = 401 and te.lngstockitemcode = 1;
    END LOOP;
    close cur;


END$$
