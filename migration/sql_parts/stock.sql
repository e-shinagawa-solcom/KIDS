DO $$
declare
    cur_header cursor for
    select * from dblink('con111', '
select 
    m_stock.lngstockno 
   ,m_stock.lngrevisionno 
   ,m_stock.strstockcode 
   ,m_stock.lngorderno 
   ,m_stock.dtmappropriationdate 
   ,m_stock.lngcustomercompanycode 
   ,m_stock.lnggroupcode 
   ,m_stock.lngusercode 
   ,m_stock.lngstockstatuscode 
   ,m_stock.lngmonetaryunitcode 
   ,m_stock.lngmonetaryratecode 
   ,m_stock.curconversionrate 
   ,m_stock.lngpayconditioncode 
   ,m_stock.strslipcode 
   ,m_stock.curtotalprice 
   ,m_stock.lngdeliveryplacecode 
   ,m_stock.dtmexpirationdate 
   ,m_stock.strnote 
   ,m_stock.lnginputusercode 
   ,m_stock.bytinvalidflag 
   ,m_stock.dtminsertdate 
from m_stock 
inner join (
    select strstockcode, MAX(lngrevisionno) as lngrevisionno from m_stock group by strstockcode
) rev
    on rev.strstockcode = m_stock.strstockcode
	and rev.lngrevisionno = m_stock.lngrevisionno
where m_stock.strstockcode not in (select strstockcode from m_stock where lngrevisionno < 0)
order by  
            m_stock.strstockcode 
           ,m_stock.lngstockno
'
    ) AS T1(
        lngstockno integer
       ,lngrevisionno integer
       ,strstockcode text
       ,lngorderno integer   -- 明細へ移動
       ,dtmappropriationdate date
       ,lngcustomercompanycode integer
       ,lnggroupcode integer
       ,lngusercode integer
       ,lngstockstatuscode integer
       ,lngmonetaryunitcode integer
       ,lngmonetaryratecode integer
       ,curconversionrate numeric(15, 6)
       ,lngpayconditioncode integer
       ,strslipcode text
       ,curtotalprice numeric(14, 4)
       ,lngdeliveryplacecode integer
       ,dtmexpirationdate date
       ,strnote text
       ,lnginputusercode integer
       ,bytinvalidflag boolean
       ,dtminsertdate timestamp(6) without time zone
    );
    
    cur_detail cursor(stockno integer, revisionno integer, orderno integer) for
    select 
        t_orderdetail.lngorderno
       ,t_orderdetail.lngorderdetailno
       ,t_orderdetail.lngrevisionno as lngorderrevisionno
       ,T1.*
    from dblink('con111','
select 
ms.lngorderno as old_order,
tod.lngorderdetailno as old_orderdetail ,

tsd.lngstockno,
tsd.lngstockdetailno,
tsd.lngrevisionno,
tsd.strproductcode,
tsd.lngstocksubjectcode,
tsd.lngstockitemcode,
tsd.dtmdeliverydate,
tsd.lngdeliverymethodcode,
tsd.lngconversionclasscode,
tsd.curproductprice,
tsd.lngproductquantity,
tsd.lngproductunitcode,
tsd.lngtaxclasscode,
tsd.lngtaxcode,
tsd.curtaxprice,
tsd.cursubtotalprice,
tsd.strnote,
tsd.strmoldno,
tsd.lngsortkey 

from t_stockdetail tsd 
inner join m_stock ms 
on ms.lngstockno = tsd.lngstockno 
and ms.lngrevisionno = tsd.lngrevisionno 
inner join (
select 
strstockcode,
MAX(lngrevisionno) as lngrevisionno 
from m_stock
group by strstockcode 
) ms_rev 
on ms_rev.strstockcode = ms.strstockcode 
and ms_rev.lngrevisionno = ms.lngrevisionno 
left join t_orderdetail tod 
on tod.lngorderno = ms.lngorderno   
and tod.strproductcode = tsd.strproductcode 
and tod.lngstocksubjectcode = tsd.lngstocksubjectcode 
and tod.lngstockitemcode = tsd.lngstockitemcode 
and tod.lngproductquantity = tsd.lngproductquantity 
and tod.curproductprice = tsd.curproductprice 
and tod.lngproductunitcode = tsd.lngproductunitcode 
and tod.lngorderdetailno = tsd.lngstockdetailno ' ||
        'where ms.lngstockno = ' || stockno || ' ' ||
        'and ms.lngrevisionno = ' || revisionno || ' ' ||
'and ms.strstockcode not in (select strstockcode from m_stock where lngrevisionno < 0)
order by tsd.lngstockno,tsd.lngstockdetailno,tsd.lngrevisionno'
        
    ) AS T1(
        old_order integer
       ,old_orderdetail integer
       ,lngstockno integer
       ,lngstockdetailno integer
       ,lngrevisionno integer
       ,strproductcode text
       ,lngstocksubjectcode integer
       ,lngstockitemcode integer
       ,dtmdeliverydate date
       ,lngdeliverymethodcode integer
       ,lngconversionclasscode integer
       ,curproductprice numeric(14, 4)
       ,lngproductquantity integer
       ,lngproductunitcode integer
       ,lngtaxclasscode integer
       ,lngtaxcode integer
       ,curtaxprice numeric(14, 4)
       ,cursubtotalprice numeric(14, 4)
       ,strnote text
       ,strmoldno text
       ,lngsortkey integer
    )
    left outer join order_conversion
    on order_conversion.old_order = T1.old_order
    and order_conversion.detailno = T1.old_orderdetail
    left outer join t_orderdetail
    on t_orderdetail.lngorderno = order_conversion.new_order
--    and t_orderdetail.lngorderdetailno = T1.lngstockdetailno
    inner join (
        select lngorderno, MAX(lngrevisionno) as lngrevisionno from t_orderdetail group by lngorderno
    )tod_rev
    on tod_rev.lngorderno = t_orderdetail.lngorderno
    and tod_rev.lngrevisionno = t_orderdetail.lngrevisionno
    ;

    header RECORD;
    detail RECORD;
    last_stock text;
    stockno integer;
begin
    delete from m_stock;
    delete from t_stockdetail;
    last_stock = '';
    stockno = 0;
    open cur_header;
    LOOP
        FETCH cur_header into header;
        EXIT WHEN NOT FOUND;
        IF last_stock <> header.strstockcode THEN
            last_stock = header.strstockcode;
            stockno = stockno + 1;
        END IF;
--        RAISE INFO 'header: % % %', stockno, header.lngrevisionno, header.strstockcode;
        insert into m_stock
        (
            lngstockno
           ,lngrevisionno
           ,strstockcode
           ,dtmappropriationdate
           ,lngcustomercompanycode
           ,lnggroupcode
           ,lngusercode
           ,lngstockstatuscode
           ,lngmonetaryunitcode
           ,lngmonetaryratecode
           ,curconversionrate
           ,lngpayconditioncode
           ,strslipcode
           ,curtotalprice
           ,lngdeliveryplacecode
           ,dtmexpirationdate
           ,strnote
           ,lnginputusercode
           ,bytinvalidflag
           ,dtminsertdate
        )
        values
        (
            stockno
           ,0
           ,header.strstockcode
           ,header.dtmappropriationdate
           ,header.lngcustomercompanycode
           ,NULL
           ,NULL
           ,header.lngstockstatuscode
           ,header.lngmonetaryunitcode
           ,header.lngmonetaryratecode
           ,header.curconversionrate
           ,header.lngpayconditioncode
           ,header.strslipcode
           ,header.curtotalprice
           ,header.lngdeliveryplacecode
           ,header.dtmexpirationdate
           ,header.strnote
           ,header.lnginputusercode
           ,header.bytinvalidflag
           ,header.dtminsertdate
        );
        open cur_detail(header.lngstockno, header.lngrevisionno, header.lngorderno);
        LOOP
            FETCH cur_detail into detail;
            EXIT WHEN NOT FOUND;
--            RAISE INFO '% % % % % %' , stockno, detail.lngstockno, detail.lngstockdetailno, header.lngorderno, detail.lngorderno, detail.lngorderdetailno;

                insert into t_stockdetail
                (
                    lngstockno
                   ,lngstockdetailno
                   ,lngrevisionno
                   ,lngorderno
                   ,lngorderdetailno
                   ,lngorderrevisionno
                   ,strproductcode
                   ,strrevisecode
                   ,lngstocksubjectcode
                   ,lngstockitemcode
--                   ,dtmdeliverydate
                   ,lngdeliverymethodcode
                   ,lngconversionclasscode
                   ,curproductprice
                   ,lngproductquantity
                   ,lngproductunitcode
                   ,lngtaxclasscode
                   ,lngtaxcode
                   ,curtaxprice
                   ,cursubtotalprice
                   ,strnote
                   ,strmoldno
                   ,lngsortkey
                )
                values
                (
                    stockno
                   ,detail.lngsortkey
                   ,0
                   ,detail.lngorderno
                   ,detail.lngorderdetailno
                   ,0
                   ,detail.strproductcode
                   ,'00'
                   ,detail.lngstocksubjectcode
                   ,detail.lngstockitemcode
--                   ,detail.dtmdeliverydate
                   ,detail.lngdeliverymethodcode
                   ,detail.lngconversionclasscode
                   ,detail.curproductprice
                   ,detail.lngproductquantity
                   ,detail.lngproductunitcode
                   ,detail.lngtaxclasscode
                   ,detail.lngtaxcode
                   ,detail.curtaxprice
                   ,detail.cursubtotalprice
                   ,detail.strnote
                   ,detail.strmoldno
                   ,detail.lngsortkey
                );

        END LOOP;
        close cur_detail;
    END LOOP;
    close cur_header;
END $$