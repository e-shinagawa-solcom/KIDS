DO $$
declare
    cur_header cursor for
    select * from dblink('con111',
        'select ' ||
            'm_stock.lngstockno ' ||
           ',m_stock.lngrevisionno ' ||
           ',m_stock.strstockcode ' ||
           ',m_stock.lngorderno ' ||
           ',m_stock.dtmappropriationdate ' ||
           ',m_stock.lngcustomercompanycode ' ||
           ',m_stock.lnggroupcode ' ||
           ',m_stock.lngusercode ' ||
           ',m_stock.lngstockstatuscode ' ||
           ',m_stock.lngmonetaryunitcode ' ||
           ',m_stock.lngmonetaryratecode ' ||
           ',m_stock.curconversionrate ' ||
           ',m_stock.lngpayconditioncode ' ||
           ',m_stock.strslipcode ' ||
           ',m_stock.curtotalprice ' ||
           ',m_stock.lngdeliveryplacecode ' ||
           ',m_stock.dtmexpirationdate ' ||
           ',m_stock.strnote ' ||
           ',m_stock.lnginputusercode ' ||
           ',m_stock.bytinvalidflag ' ||
           ',m_stock.dtminsertdate ' ||
        'from m_stock ' ||
        'inner join ( ' ||
            'select  ' ||
                'm_stock.strstockcode ' ||
            'from m_stock ' ||
            'inner join ( ' ||
                'select * from  ' ||
                '( ' ||
                    'select ' ||
                        'strstockcode ' ||
                       ',MAX(lngrevisionno) as lngrevisionno_max ' ||
                    'from m_stock ' ||
                    'group by strstockcode ' ||
                ') GET_MAX ' ||
            ') rev ' ||
                'on rev.strstockcode = m_stock.strstockcode ' ||
                'and rev.lngrevisionno_max = m_stock.lngrevisionno ' ||
            'where m_stock.lngstockstatuscode >= 4 ' ||
        ') A ' ||
            'on A.strstockcode = m_stock.strstockcode ' ||
        'order by  ' ||
            'm_stock.strstockcode ' ||
           ',m_stock.lngstockno'
    ) AS T1(
        lngstockno integer
       ,lngrevisionno integer
       ,strstockcode text
       ,lngorderno integer   -- ñæç◊Ç÷à⁄ìÆ
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
        T1.*
       ,order_info.lngorderno
       ,order_info.lngorderdetailno
       ,order_info.lngrevisionno as lngorderrevisionno
    from dblink('con111',
        'select '
            't_stockdetail.lngstockno ' ||
           ',t_stockdetail.lngstockdetailno ' ||
           ',t_stockdetail.lngrevisionno ' ||
           ',t_stockdetail.strproductcode ' ||
           ',t_stockdetail.lngstocksubjectcode ' ||
           ',t_stockdetail.lngstockitemcode ' ||
           ',t_stockdetail.dtmdeliverydate ' ||
           ',t_stockdetail.lngdeliverymethodcode ' ||
           ',t_stockdetail.lngconversionclasscode ' ||
           ',t_stockdetail.curproductprice ' ||
           ',t_stockdetail.lngproductquantity ' ||
           ',t_stockdetail.lngproductunitcode ' ||
           ',t_stockdetail.lngtaxclasscode ' ||
           ',t_stockdetail.lngtaxcode ' ||
           ',t_stockdetail.curtaxprice ' ||
           ',t_stockdetail.cursubtotalprice ' ||
           ',t_stockdetail.strnote ' ||
           ',t_stockdetail.strmoldno ' ||
           ',t_stockdetail.lngsortkey ' ||
        'from t_stockdetail ' ||
        'where lngstockno = ' || stockno || ' ' ||
        'and lngrevisionno = ' || revisionno
        
    ) AS T1(
        lngstockno integer
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
    left outer join (
        select 
            order_conversion.old_order, t_orderdetail.* 
        from order_conversion
        inner join t_orderdetail
            on t_orderdetail.lngorderno = order_conversion.new_order
            and t_orderdetail.lngorderdetailno = order_conversion.detailno
        inner join (
            select 
                lngorderno
               ,lngorderdetailno
               ,MAX(lngrevisionno) as lngrevisionno
            from t_orderdetail
            group by 
                lngorderno
               ,lngorderdetailno
        ) max_rev
            on max_rev.lngorderno = t_orderdetail.lngorderno
            and max_rev.lngorderdetailno = t_orderdetail.lngorderdetailno
            and max_rev.lngrevisionno = t_orderdetail.lngrevisionno
        where order_conversion.old_order = orderno
    )order_info
        on  order_info.lngstocksubjectcode = T1.lngstocksubjectcode
        and order_info.lngstockitemcode = T1.lngstockitemcode
        and order_info.curproductprice = T1.curproductprice
        and order_info.lngproductquantity = T1.lngproductquantity
        and order_info.cursubtotalprice = T1.cursubtotalprice
        and order_info.lngorderdetailno = T1.lngstockdetailno
--    where order_info.lngsortkey = T1.lngsortkey

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
           ,header.lngrevisionno
           ,header.strstockcode
           ,header.dtmappropriationdate
           ,header.lngcustomercompanycode
           ,header.lnggroupcode
           ,header.lngusercode
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
        RAISE INFO 'header: % % %', stockno, header.lngrevisionno, header.strstockcode;
        open cur_detail(header.lngstockno, header.lngrevisionno, header.lngorderno);
        LOOP
            FETCH cur_detail into detail;
            EXIT WHEN NOT FOUND;
--            RAISE INFO '% % % % %' , stockno, detail.lngstockdetailno, header.lngorderno, detail.lngorderno, detail.lngorderdetailno;

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
                   ,detail.lngstockdetailno
                   ,detail.lngrevisionno
                   ,(select new_order from order_conversion where old_order = header.lngorderno and detailno = detail.lngorderdetailno)
                   ,detail.lngorderdetailno
                   ,detail.lngorderrevisionno
                   ,detail.strproductcode
                   ,'00'
                   ,detail.lngstocksubjectcode
                   ,detail.lngstockitemcode
 --                  ,detail.dtmdeliverydate
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

/*
            IF detail.lngorderno is null or detail.lngorderdetailno is null THEN
                RAISE INFO '% % % %' , stockno, detail.lngstockdetailno, detail.lngorderno, detail.lngorderdetailno;
            END IF;
            IF NOT EXISTS(
                select 
                    *
                from t_stockdetail
                where lngstockno = detail.lngstockno
                    and lngstockdetailno = detail.lngstockdetailno
                    and lngrevisionno = detail.lngrevisionno
             ) THEN
            ELSE
                RAISE INFO 'detail duplicates % % %' , stockno, detail.lngrevisionno, detail.lngstockdetailno;
            END IF;
*/
        END LOOP;
        close cur_detail;
    END LOOP;
    close cur_header;
END $$