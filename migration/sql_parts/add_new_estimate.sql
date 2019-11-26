DO $$
-- 見積原価マスタ、受注マスタ・明細、発注マスタ・明細を基に
-- 見積原価マスタ、見積原価明細をリビジョンアップする。


declare 
    me_r RECORD;
    tr_r RECORD;
    detailno integer;
    -- 見積原価マスタカーソル
    cur_me cursor for
        select 
            me.lngestimateno
           ,me.lngrevisionno
           ,me.strproductcode
           ,me.strrevisecode
           ,me.bytdecisionflag
           ,me.lngestimatestatuscode
           ,me.curfixedcost
           ,me.curmembercost
           ,me.curtotalprice
           ,me.curmanufacturingcost
           ,me.cursalesamount
           ,me.curprofit
           ,me.lnginputusercode
           ,me.bytinvalidflag
           ,me.dtminsertdate
           ,me.lngproductionquantity
           ,me.lngtempno
           ,me.strnote
    from m_estimate me
    inner join (
        select
            lngestimateno
           ,max(lngrevisionno) as lngrevisionno
        from  m_estimate
        group by (lngestimateno)
    ) me_max
        on me_max.lngestimateno = me.lngestimateno
        and me_max.lngrevisionno = me.lngrevisionno;
    -- 受注明細カーソル
    cur_tr cursor( productcode text ) for
        select
            me.lngestimateno
           ,999 as lngestimatedetailno
           ,me.lngrevisionno + 1 as lngestimaterevisionno
           ,null as lngstocksubjectcode
           ,null as lngstockitemcode
           ,mr.lngcustomercompanycode
           ,tr.dtmdeliverydate as dtmdelivery
           ,false as bytpayofftargetflag
           ,false as bytpercentinputflag
           ,mr.lngmonetaryunitcode
           ,mr.lngmonetaryratecode
           ,mr.curconversionrate
           ,tr.lngproductquantity
           ,tr.curproductprice
           ,null as curproductrate
           ,tr.cursubtotalprice
           ,tr.strnote
           ,tr.lngsortkey
           ,salesdivision.lngsalesdivisioncode
           ,tr.lngsalesclasscode
           ,tr.lngreceiveno
           ,tr.lngreceivedetailno
           ,tr.lngrevisionno as r_lngrevisionno
        from m_receive mr
        inner join(
            select 
                strreceivecode
               ,MAX(lngrevisionno) as lngrevisionno
            from m_receive
            group by strreceivecode
        ) mr_max
            on mr_max.strreceivecode = mr.strreceivecode
            and  mr_max.lngrevisionno = mr.lngrevisionno
        inner join t_receivedetail tr
            on tr.lngreceiveno = mr.lngreceiveno
            and tr.lngrevisionno = mr.lngrevisionno
        inner join m_estimate me
            on me.strproductcode = tr.strproductcode
        inner join (
            select 
                lngestimateno
               ,MAX(lngrevisionno) as lngrevisionno
            from m_estimate
            group by lngestimateno
        ) me_max
            on me_max.lngestimateno = me.lngestimateno
            and me_max.lngrevisionno = me.lngrevisionno
        inner join m_salesclassdivisonlink salesdivision
            on salesdivision.lngsalesclasscode = tr.lngsalesclasscode
        where tr.strproductcode = productcode
        order by
            mr.strreceivecode
           ,tr.lngreceiveno
           ,tr.lngreceivedetailno;

    -- 発注明細カーソル
    cur_to cursor( productcode text ) for
        select
            me.lngestimateno
           ,999 as lngestimatedetailno
           ,me.lngrevisionno + 1 as lngestimaterevisionno
           ,tr.lngstocksubjectcode
           ,tr.lngstockitemcode
           ,mr.lngcustomercompanycode
           ,tr.dtmdeliverydate as dtmdelivery
           ,false as bytpayofftargetflag
           ,false as bytpercentinputflag
           ,mr.lngmonetaryunitcode
           ,mr.lngmonetaryratecode
           ,mr.curconversionrate
           ,tr.lngproductquantity
           ,tr.curproductprice
           ,null as curproductrate
           ,tr.cursubtotalprice
           ,tr.strnote
           ,tr.lngsortkey
           ,null as lngsalesdivisioncode
           ,null as lngsalesclasscode
           ,tr.lngorderno
           ,tr.lngorderdetailno
           ,tr.lngrevisionno as r_lngrevisionno
        from m_order mr
        inner join(
            select 
                strordercode
               ,MAX(lngrevisionno) as lngrevisionno
            from m_order
            group by strordercode
        ) mr_max
            on mr_max.strordercode = mr.strordercode
            and  mr_max.lngrevisionno = mr.lngrevisionno
        inner join t_orderdetail tr
            on tr.lngorderno = mr.lngorderno
            and tr.lngrevisionno = mr.lngrevisionno
        inner join m_estimate me
            on me.strproductcode = tr.strproductcode
        inner join (
            select 
                lngestimateno
               ,MAX(lngrevisionno) as lngrevisionno
            from m_estimate
            group by lngestimateno
        ) me_max
            on me_max.lngestimateno = me.lngestimateno
            and me_max.lngrevisionno = me.lngrevisionno
        where tr.strproductcode = productcode
        order by
            mr.strordercode
           ,tr.lngorderno
           ,tr.lngorderdetailno;
    -- 
    cur_cost cursor(estimateno integer, revisionno integer) for
        select 
            t_estimatedetail.lngestimateno
           ,t_estimatedetail.lngestimatedetailno
           ,t_estimatedetail.lngrevisionno
           ,t_estimatedetail.lngstocksubjectcode
           ,t_estimatedetail.lngstockitemcode
           ,t_estimatedetail.lngcustomercompanycode
           ,t_estimatedetail.dtmdelivery
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
        inner join m_stockitem
            on m_stockitem.lngstockitemcode = t_estimatedetail.lngstockitemcode
            and m_stockitem.lngstocksubjectcode = t_estimatedetail.lngstocksubjectcode
        where m_stockitem.lngestimateareaclassno = 5
            and lngestimateno = estimateno
            and lngrevisionno = revisionno;


begin
    open cur_me;
    LOOP
        FETCH cur_me into me_r;
        EXIT WHEN NOT FOUND;
        detailno = 1;
        open cur_tr(me_r.strproductcode);
        LOOP
            FETCH cur_tr into tr_r;
            EXIT WHEN NOT FOUND;
            -- 受注明細から見積原価明細（受注分）を作成
            insert into t_estimatedetail
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
            values(
                tr_r.lngestimateno
               ,detailno
               ,tr_r.lngestimaterevisionno
               ,null
               ,null
               ,tr_r.lngcustomercompanycode
               ,tr_r.dtmdelivery
               ,tr_r.bytpayofftargetflag
               ,tr_r.bytpercentinputflag
               ,tr_r.lngmonetaryunitcode
               ,tr_r.lngmonetaryratecode
               ,tr_r.curconversionrate
               ,tr_r.lngproductquantity
               ,tr_r.curproductprice
               ,null
               ,tr_r.cursubtotalprice
               ,tr_r.strnote
               ,tr_r.lngsortkey
               ,tr_r.lngsalesdivisioncode
               ,tr_r.lngsalesclasscode
            );
            -- 作成した見積原価を受注明細と連携
--            RAISE INFO '(r)% % % -> % % %', tr_r.lngreceiveno, tr_r.lngreceivedetailno, tr_r.r_lngrevisionno, tr_r.lngestimateno, detailno, tr_r.lngestimaterevisionno;
            update t_receivedetail
            set 
                lngestimateno = tr_r.lngestimateno
               ,lngestimatedetailno = detailno
               ,lngestimaterevisionno = tr_r.lngestimaterevisionno
            where 
                lngreceiveno = tr_r.lngreceiveno
                and lngreceivedetailno = tr_r.lngreceivedetailno
                and lngrevisionno = tr_r.r_lngrevisionno;
            IF NOT FOUND THEN
                RAISE INFO 'receive not found % % %', tr_r.lngreceiveno, tr_r.lngreceivedetailno, tr_r.r_lngrevisionno;
            END IF;
            detailno = detailno + 1;

        END LOOP;
        close cur_tr;

        open cur_to(me_r.strproductcode);
        LOOP
            FETCH cur_to into tr_r;
            EXIT WHEN NOT FOUND;
            -- 発注明細から見積原価明細（受注分）を作成
            insert into t_estimatedetail
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
            values(
                tr_r.lngestimateno
               ,detailno
               ,tr_r.lngestimaterevisionno
               ,tr_r.lngstocksubjectcode
               ,tr_r.lngstockitemcode
               ,tr_r.lngcustomercompanycode
               ,tr_r.dtmdelivery
               ,tr_r.bytpayofftargetflag
               ,tr_r.bytpercentinputflag
               ,tr_r.lngmonetaryunitcode
               ,tr_r.lngmonetaryratecode
               ,tr_r.curconversionrate
               ,tr_r.lngproductquantity
               ,tr_r.curproductprice
               ,null
               ,tr_r.cursubtotalprice
               ,tr_r.strnote
               ,tr_r.lngsortkey
               ,null
               ,null
            );
            -- 作成した見積原価を発注明細と連携
--            RAISE INFO '(o)% % % -> % % %', tr_r.lngorderno, tr_r.lngorderdetailno, tr_r.r_lngrevisionno, tr_r.lngestimateno, detailno, tr_r.lngestimaterevisionno;
            update t_orderdetail
            set 
                lngestimateno = tr_r.lngestimateno
               ,lngestimatedetailno = detailno
               ,lngestimaterevisionno = tr_r.lngestimaterevisionno
            where 
                lngorderno = tr_r.lngorderno
                and lngorderdetailno = tr_r.lngorderdetailno
                and lngrevisionno = tr_r.r_lngrevisionno;
            IF NOT FOUND THEN
                RAISE INFO 'order not found % % %', tr_r.lngreceiveno, tr_r.lngreceivedetailno, tr_r.r_lngrevisionno;
            END IF;
            detailno = detailno + 1;
        END LOOP;
        close cur_to;
        -- 受発注明細のない見積原価を移行する。
        open cur_cost(me_r.lngestimateno, me_r.lngrevisionno);
        LOOP
            FETCH cur_cost into tr_r;
            EXIT WHEN NOT FOUND;
            insert into t_estimatedetail
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
            values(
                tr_r.lngestimateno
               ,detailno
               ,tr_r.lngrevisionno + 1
               ,tr_r.lngstocksubjectcode
               ,tr_r.lngstockitemcode
               ,tr_r.lngcustomercompanycode
               ,tr_r.dtmdelivery
               ,tr_r.bytpayofftargetflag
               ,tr_r.bytpercentinputflag
               ,tr_r.lngmonetaryunitcode
               ,tr_r.lngmonetaryratecode
               ,tr_r.curconversionrate
               ,tr_r.lngproductquantity
               ,tr_r.curproductprice
               ,tr_r.curproductrate
               ,tr_r.cursubtotalprice
               ,tr_r.strnote
               ,tr_r.lngsortkey
               ,tr_r.lngsalesdivisioncode
               ,tr_r.lngsalesclasscode
            );
            detailno = detailno + 1;
        END LOOP;
        close cur_cost;
        -- 見積原価マスタを追加
        insert into m_estimate(
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
        values(
            me_r.lngestimateno
           ,me_r.lngrevisionno + 1
           ,me_r.strproductcode
           ,me_r.strrevisecode
           ,me_r.bytdecisionflag
           ,me_r.lngestimatestatuscode
           ,me_r.curfixedcost
           ,me_r.curmembercost
           ,me_r.curtotalprice
           ,me_r.curmanufacturingcost
           ,me_r.cursalesamount
           ,me_r.curprofit
           ,me_r.lnginputusercode
           ,me_r.bytinvalidflag
           ,me_r.dtminsertdate
           ,me_r.lngproductionquantity
           ,me_r.lngtempno
           ,me_r.strnote
           ,0
        );
        
        -- 製品マスタ(リビジョン追従分）を追加
        insert into m_product(
            lngproductno
           ,strproductcode
           ,strproductname
           ,strproductenglishname
           ,strgoodscode
           ,strgoodsname
           ,lnginchargegroupcode
           ,lnginchargeusercode
           ,lngdevelopusercode
           ,lnginputusercode
           ,lngcustomercompanycode
           ,lngcustomergroupcode
           ,lngcustomerusercode
           ,strcustomerusername
           ,lngpackingunitcode
           ,lngproductunitcode
           ,lngboxquantity
           ,lngcartonquantity
           ,lngproductionquantity
           ,lngproductionunitcode
           ,lngfirstdeliveryquantity
           ,lngfirstdeliveryunitcode
           ,lngfactorycode
           ,lngassemblyfactorycode
           ,lngdeliveryplacecode
           ,dtmdeliverylimitdate
           ,curproductprice
           ,curretailprice
           ,lngtargetagecode
           ,lngroyalty
           ,lngcertificateclasscode
           ,lngcopyrightcode
           ,strcopyrightdisplaystamp
           ,strcopyrightdisplayprint
           ,lngproductformcode
           ,strproductcomposition
           ,strassemblycontents
           ,strspecificationdetails
           ,strnote
           ,bytinvalidflag
           ,dtminsertdate
           ,dtmupdatedate
           ,strcopyrightnote
           ,lngcategorycode
           ,lngrevisionno
           ,strrevisecode
        ) 
        select 
            m_product.lngproductno
           ,m_product.strproductcode
           ,m_product.strproductname
           ,m_product.strproductenglishname
           ,m_product.strgoodscode
           ,m_product.strgoodsname
           ,m_product.lnginchargegroupcode
           ,m_product.lnginchargeusercode
           ,m_product.lngdevelopusercode
           ,m_product.lnginputusercode
           ,m_product.lngcustomercompanycode
           ,m_product.lngcustomergroupcode
           ,m_product.lngcustomerusercode
           ,m_product.strcustomerusername
           ,m_product.lngpackingunitcode
           ,m_product.lngproductunitcode
           ,m_product.lngboxquantity
           ,m_product.lngcartonquantity
           ,m_product.lngproductionquantity
           ,m_product.lngproductionunitcode
           ,m_product.lngfirstdeliveryquantity
           ,m_product.lngfirstdeliveryunitcode
           ,m_product.lngfactorycode
           ,m_product.lngassemblyfactorycode
           ,m_product.lngdeliveryplacecode
           ,m_product.dtmdeliverylimitdate
           ,m_product.curproductprice
           ,m_product.curretailprice
           ,m_product.lngtargetagecode
           ,m_product.lngroyalty
           ,m_product.lngcertificateclasscode
           ,m_product.lngcopyrightcode
           ,m_product.strcopyrightdisplaystamp
           ,m_product.strcopyrightdisplayprint
           ,m_product.lngproductformcode
           ,m_product.strproductcomposition
           ,m_product.strassemblycontents
           ,m_product.strspecificationdetails
           ,m_product.strnote
           ,m_product.bytinvalidflag
           ,m_product.dtminsertdate
           ,m_product.dtmupdatedate
           ,m_product.strcopyrightnote
           ,m_product.lngcategorycode
           ,m_estimate.lngrevisionno
           ,m_product.strrevisecode 
        from m_product
        inner join m_estimate
        on m_estimate.strproductcode = m_product.strproductcode
        where m_estimate.lngestimateno = me_r.lngestimateno
            and m_product.lngrevisionno = 0
            and m_estimate.lngrevisionno > 0;
    END LOOP;
    close cur_me;
    update m_estimate set lngproductrevisionno = lngrevisionno;
END $$