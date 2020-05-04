DO $$

declare
    cur cursor for
    select * from dblink('con111','
select 
    t_estimatedetail.lngestimateno
   ,t_estimatedetail.lngestimatedetailno
   ,t_estimatedetail.lngrevisionno
   ,t_estimatedetail.lngstocksubjectcode
   ,t_estimatedetail.lngstockitemcode
   ,coalesce(t_estimatedetail.lngcustomercompanycode,0) as lngcustomercompanycode
   ,m_product.dtmdeliverylimitdate as dtmdelivery
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
   ,m_product.lnginchargegroupcode as lnggroupcode
   ,m_product.lnginchargeusercode as lngusercode
   ,m_product.strproductcode as strproductcode
from t_estimatedetail
inner join m_estimate
    on m_estimate.lngestimateno = t_estimatedetail.lngestimateno
	and m_estimate.lngrevisionno = t_estimatedetail.lngrevisionno
inner join( 
    select lngestimateno, MAX(lngrevisionno) as lngrevisionno from m_estimate group by lngestimateno
) rev
    on rev.lngestimateno = t_estimatedetail.lngestimateno and rev.lngrevisionno = t_estimatedetail.lngrevisionno
inner join m_product 
    on m_product.strproductcode = m_estimate.strproductcode
where t_estimatedetail.lngproductquantity <> 0
    and t_estimatedetail.lngestimateno not in (select lngestimateno from m_estimate where lngrevisionno < 0 or bytinvalidflag = true)
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
') AS T1(
    lngestimateno integer
   ,lngestimatedetailno integer
   ,lngrevisionno integer
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,dtmdelivery date
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,lngproductquantity integer
   ,curproductprice numeric(14,4)
   ,curproductrate numeric(15, 6)
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngsortkey numeric(14,4)
   ,lngsalesdivisioncode numeric(14,4)
   ,lngsalesclasscode numeric(14,4)
   ,lnggroupcode integer
   ,lngusercode integer
   ,strproductcode text
);
    
    rec RECORD;
    orderno integer;

begin
    open cur;
    LOOP
        FETCH cur INTO rec;
        EXIT WHEN NOT FOUND;
        select MAX(lngorderno) into orderno from  m_order;
RAISE INFO '% % %', rec.lngestimateno, rec.lngestimatedetailno, orderno;
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
        VALUES(
            rec.lngestimateno
           ,coalesce((select max(lngestimatedetailno)+1 from t_estimatedetail where lngestimateno=rec.lngestimateno and lngrevisionno=0),1)
           ,0
           ,rec.lngstocksubjectcode
           ,rec.lngstockitemcode
           ,rec.lngcustomercompanycode
           ,rec.dtmdelivery
           ,rec.bytpayofftargetflag
           ,rec.bytpercentinputflag
           ,rec.lngmonetaryunitcode
           ,rec.lngmonetaryratecode
           ,rec.curconversionrate
           ,rec.lngproductquantity
           ,rec.curproductprice
           ,rec.curproductrate
           ,rec.cursubtotalprice
           ,rec.strnote
           ,rec.lngsortkey
           ,rec.lngsalesdivisioncode
           ,rec.lngsalesclasscode
        );

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
        VALUES(
            orderno+1
           ,0
           ,'2004' || trim(to_char((select lngsequence + 1 from t_sequence where strsequencename = 'm_order.strordercode.2004'),'0000'))
           ,current_timestamp
           ,rec.lngcustomercompanycode
           ,rec.lnggroupcode
           ,rec.lngusercode
           ,1
           ,rec.lngmonetaryunitcode
           ,2
           ,rec.curconversionrate
           ,NULL
           ,NULL
           ,rec.lngusercode
           ,false
           ,current_timestamp
        );
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
        VALUES(
            orderno+1
           ,1
           ,0
           ,rec.strproductcode
           ,'00'
           ,rec.lngstocksubjectcode
           ,rec.lngstockitemcode
           ,coalesce(rec.dtmdelivery,(select max(dtmdelivery) from t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=0))
           ,NULL
           ,1
           ,case rec.bytpercentinputflag when NULL then rec.curproductprice else rec.cursubtotalprice / rec.lngproductquantity end
           ,rec.lngproductquantity
           ,1
           ,rec.cursubtotalprice
           ,rec.strnote
           ,NULL
           ,(select max(lngestimatedetailno) from  t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=0)
           ,rec.lngestimateno
           ,(select max(lngestimatedetailno) from  t_estimatedetail where lngestimateno = rec.lngestimateno and lngrevisionno=0)
           ,0
        );
    END LOOP;
    close cur;

RAISE INFO 'add_estimate_401_1 complete';
END$$
