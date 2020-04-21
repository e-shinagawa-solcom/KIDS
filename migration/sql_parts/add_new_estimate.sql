DO $$
-- 見積原価マスタ、受注マスタ・明細、発注マスタ・明細を基に
-- 見積原価マスタ、見積原価明細をリビジョンアップする。


declare 

cur_check cursor for
    select
        me.lngestimateno
       ,me.lngrevisionno
       ,me.strproductcode
       ,me.strrevisecode
       ,count(ted.lngproductquantity) as detail_cnt
       ,sum(coalesce(ted.lngproductquantity,0)) as detail_q
    from m_estimate me

    left outer join t_estimatedetail ted
       on ted.lngestimateno = me.lngestimateno
       and ted.lngrevisionno = me.lngrevisionno
       and ted.lngsalesdivisioncode = 2
       and ted.lngsalesclasscode = 1
    where me.lngestimateno not in ( select lngestimateno from m_estimate where lngrevisionno < 0)
        and me.lngrevisionno = 1
    group by 
        me.lngestimateno
       ,me.lngrevisionno
       ,me.strproductcode
       ,me.strrevisecode
    order by me.lngestimateno,me.lngrevisionno;

r_check RECORD;


r_honni RECORD;
cur_honni cursor(e_no integer) for
select 
    me.lngestimateno as lngestimateno
	,detailno.lngestimatedetailno as lngestimatedetailno
	,me.lngrevisionno as lngrevisionno
	,mp.lngcustomercompanycode as lngcustomercompanycode
	,mp.dtmdeliverylimitdate as dtmdelivery
	, false as bytpayofftargetflag
	, false as bytpercentinputflag
	,1 as lngmonetaryunitcode
    ,2 as lngmonetaryratecode
	,1 as curconversionrate
	,mp.lngproductionquantity as lngproductquantity
	,mp.curproductprice as curproductprice
	,mp.curproductprice * mp.lngproductionquantity as cursubtotalprice
	,detailno.lngestimatedetailno as lngsortkey
	,2 as lngsalesdivisioncode
	,1 as lngsalesclasscode
-- 4 m_receive
	,(select MAX(lngreceiveno)+1 from m_receive) as lngreceiveno
	,0 as lngreceiverevisionno
	,'d' || to_char(mp.dtmdeliverylimitdate,'yymm') || trim(to_char(coalesce((select MAX(lngsequence) from t_sequence where strsequencename = 'm_receive.strreceivecode.' || to_char(mp.dtmdeliverylimitdate,'yymm')),0) + 1,'0009'))  as strreceivecode
	,me.strrevisecode as strreceiverevisecode
	,me.dtminsertdate as dtmappropriationdate
	,mp.lnginchargegroupcode as lnggroupcode
	,mp.lnginchargeusercode as lngusercode
	,1 as lngreceivestatuscode
	,mp.lnginputusercode as lnginputusercode
    ,me.dtminsertdate as dtminsertdate
-- 4 t_receivedetail
    ,coalesce((select MAX(lngreceivedetailno) from t_receivedetail where strproductcode=me.strproductcode and strrevisecode=me.strrevisecode),0)+1 as lngreceivedetailno
	,1 as lngconversionclasscode
	,1 as lngproductunitcode
	,1 as lngunitquantity
	,me.strproductcode as strproductcode
	,me.strrevisecode as strrevisecode

from m_estimate me
inner join m_product mp
on mp.strproductcode = me.strproductcode
and mp.strrevisecode = me.strrevisecode
and mp.lngrevisionno = me.lngrevisionno
inner join (
    select lngestimateno, lngrevisionno, MAX(lngestimatedetailno) + 1 as lngestimatedetailno from t_estimatedetail group by lngestimateno, lngrevisionno
) detailno
on detailno.lngestimateno = me.lngestimateno
and detailno.lngrevisionno = me.lngrevisionno
    where me.lngestimateno = e_no 
    and me.lngrevisionno = 1;

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
        select substr(strproductcode,1,5) as strproductcode, strrevisecode, max(lngestimateno) as lngestimateno
        from m_estimate
        group by substr(strproductcode,1,5), strrevisecode
    ) me_act
        on me_act.lngestimateno = me.lngestimateno
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
           ,case tr.lngproductunitcode when 2 then tr.lngproductquantity * mp.lngcartonquantity else tr.lngproductquantity end as lngproductquantity
           ,case tr.lngproductunitcode when 2 then trunc(tr.curproductprice / mp.lngcartonquantity,4) else tr.curproductprice end as curproductprice
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
               ,MIN(lngrevisionno) as min_rev
            from m_receive
            group by strreceivecode
        ) mr_max
            on mr_max.strreceivecode = mr.strreceivecode
            and  mr_max.lngrevisionno = mr.lngrevisionno
            and mr_max.min_rev >= 0
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
        inner join m_product mp
            on mp.strproductcode = me.strproductcode
            and mp.strrevisecode = me.strrevisecode
            and mp.lngrevisionno = me.lngrevisionno
        inner join m_salesclassdivisonlink salesdivision
            on salesdivision.lngsalesclasscode = tr.lngsalesclasscode
            and NOT(salesdivision.lngestimateareaclassno = 2 and salesdivision.lngsalesclasscode = 9 and salesdivision.lngsalesdivisioncode = 1)
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
           ,case tr.lngproductunitcode when 2 then tr.lngproductquantity * mp.lngcartonquantity else tr.lngproductquantity end as lngproductquantity
           ,case tr.lngproductunitcode when 2 then trunc(tr.curproductprice / mp.lngcartonquantity,4) else tr.curproductprice end as curproductprice
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
               ,MIN(lngrevisionno) as min_rev
            from m_order
            group by strordercode
        ) mr_max
            on mr_max.strordercode = mr.strordercode
            and  mr_max.lngrevisionno = mr.lngrevisionno
            and  mr_max.min_rev >= 0
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
        inner join m_product mp
            on mp.strproductcode = me.strproductcode
            and mp.strrevisecode = me.strrevisecode
            and mp.lngrevisionno = me.lngrevisionno
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

    update t_receivedetail
    set
        lngestimateno = null,
        lngestimatedetailno = null,
        lngestimaterevisionno = null;
    update t_orderdetail
    set
        lngestimateno = null,
        lngestimatedetailno = null,
        lngestimaterevisionno = null;
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
--RAISE INFO '% % % % ', me_r.lngestimateno, me_r.strproductcode, me_r.lngrevisionno, me_r.strrevisecode; 
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
        on substr(m_estimate.strproductcode,1,5) = m_product.strproductcode
        and m_estimate.strrevisecode = m_product.strrevisecode
        inner join (
            select lngestimateno,MAX(lngrevisionno) as lngrevisionno from m_estimate group by lngestimateno
        ) me_max on me_max.lngestimateno = m_estimate.lngestimateno
            and me_max.lngrevisionno = m_estimate.lngrevisionno
        where m_estimate.lngestimateno = me_r.lngestimateno
            and m_product.lngrevisionno = 0
            and m_estimate.lngrevisionno > 0;
    END LOOP;
    close cur_me;
    update m_estimate set lngproductrevisionno = lngrevisionno;


open cur_check;
LOOP
    FETCH cur_check into r_check;
    EXIT WHEN NOT FOUND;

RAISE INFO '% % % % % %', r_check.lngestimateno, r_check.lngrevisionno, r_check.strproductcode, r_check.strrevisecode, r_check.detail_cnt, r_check.detail_q;
    IF r_check.detail_cnt = 0 THEN
        -- 製品マスタから見積原価明細、受注マスタ、受注明細を作成

        open cur_honni(r_check.lngestimateno);
        LOOP
            FETCH cur_honni into r_honni;
            EXIT WHEN NOT FOUND;
RAISE INFO '% % %', r_honni.lngestimateno, r_honni.lngestimatedetailno, r_honni.lngrevisionno;
        -- 見積原価明細
            insert into t_estimatedetail(
            lngestimateno
	       ,lngestimatedetailno
	       ,lngrevisionno
	       ,lngcustomercompanycode
	       ,dtmdelivery
	       ,bytpayofftargetflag
	       ,bytpercentinputflag
	       ,lngmonetaryunitcode
           ,lngmonetaryratecode
	       ,curconversionrate
	       ,lngproductquantity
	       ,curproductprice
	       ,cursubtotalprice
	       ,lngsortkey
	       ,lngsalesdivisioncode
	       ,lngsalesclasscode
           )
           VALUES(
           r_honni.lngestimateno
           ,r_honni.lngestimatedetailno
	       ,r_honni.lngrevisionno
	       ,r_honni.lngcustomercompanycode
	       ,r_honni.dtmdelivery
	       ,r_honni.bytpayofftargetflag
	       ,r_honni.bytpercentinputflag
	       ,r_honni.lngmonetaryunitcode
           ,r_honni.lngmonetaryratecode
	       ,r_honni.curconversionrate
	       ,r_honni.lngproductquantity
	       ,r_honni.curproductprice
	       ,r_honni.cursubtotalprice
           ,r_honni.lngestimatedetailno
	       ,r_honni.lngsalesdivisioncode
	       ,r_honni.lngsalesclasscode
           );
        -- 受注マスタ
           insert into m_receive(
	       lngreceiveno
	       ,lngrevisionno
	       ,strreceivecode
	       ,strrevisecode
	       ,dtmappropriationdate
	       ,lnggroupcode
	       ,lngusercode
	       ,lngreceivestatuscode
	       ,lnginputusercode
           ,dtminsertdate
           )
           values(
	       (select MAX(lngreceiveno)+1 from m_receive)
	       ,r_honni.lngreceiverevisionno
	       ,'d' || to_char(r_honni.dtmdelivery,'yymm') || trim(to_char(coalesce((select MAX(lngsequence) from t_sequence where strsequencename = 'm_receive.strreceivecode.' || to_char(r_honni.dtmdelivery,'yymm')),0) + 1,'0009'))
	       ,r_honni.strreceiverevisecode
	       ,r_honni.dtmappropriationdate
	       ,r_honni.lnggroupcode
	       ,r_honni.lngusercode
	       ,r_honni.lngreceivestatuscode
	       ,r_honni.lnginputusercode
           ,r_honni.dtminsertdate
           );
           update t_sequence set lngsequence = lngsequence+1  where strsequencename = 'm_receive.strreceivecode.' || to_char(r_honni.dtmdelivery,'yymm');
    
        -- 受注明細
           insert into t_receivedetail(
              lngreceiveno
              ,lngreceivedetailno
              ,lngrevisionno
              ,strproductcode
              ,strrevisecode
              ,lngsalesclasscode
              ,dtmdeliverydate
              ,lngconversionclasscode
              ,curproductprice
              ,lngproductquantity
              ,lngproductunitcode
              ,lngunitquantity
              ,cursubtotalprice
              ,strnote
              ,lngsortkey
              ,lngestimateno
              ,lngestimatedetailno
              ,lngestimaterevisionno
           )
           values(
	       (select MAX(lngreceiveno) from m_receive) 
           ,coalesce((select MAX(lngreceivedetailno) from t_receivedetail where strproductcode=r_honni.strproductcode and strrevisecode=r_honni.strrevisecode),0)+1
	       ,r_honni.lngreceiverevisionno
           ,r_honni.strproductcode
           ,r_honni.strrevisecode
           ,r_honni.lngsalesclasscode
           ,r_honni.dtmdelivery
           ,r_honni.lngconversionclasscode
           ,r_honni.curproductprice
           ,r_honni.lngproductquantity
           ,r_honni.lngproductunitcode
           ,r_honni.lngunitquantity
           ,r_honni.cursubtotalprice
           ,NULL
           ,coalesce((select MAX(lngreceivedetailno) from t_receivedetail where strproductcode=r_honni.strproductcode and strrevisecode=r_honni.strrevisecode),0)+1
           ,r_honni.lngestimateno
           ,r_honni.lngestimatedetailno
           ,r_honni.lngrevisionno
	       );
        END LOOP; 
        close cur_honni;

    ELSE
        -- 製品マスタの生産数を書き換え
        update m_product set lngproductionquantity = r_check.detail_q where strproductcode = r_check.strproductcode and strrevisecode = r_check.strrevisecode and lngrevisionno = 1;
    END IF;

    -- リビジョン0の見積原価明細作成
    insert into t_estimatedetail(
        lngestimateno
	   ,lngestimatedetailno
	   ,lngrevisionno
	   ,lngcustomercompanycode
	   ,dtmdelivery
	   ,bytpayofftargetflag
	   ,bytpercentinputflag
	   ,lngmonetaryunitcode
       ,lngmonetaryratecode
	   ,curconversionrate
	   ,lngproductquantity
	   ,curproductprice
	   ,cursubtotalprice
	   ,lngsortkey
	   ,lngsalesdivisioncode
	   ,lngsalesclasscode
    )
    select 
        r_check.lngestimateno
       ,(select coalesce(max(lngestimatedetailno),0) + 1 from t_estimatedetail where lngestimateno = r_check.lngestimateno and lngrevisionno=0)
       ,0
       ,mp.lngcustomercompanycode
       ,mp.dtmdeliverylimitdate
       ,false
       ,false
       ,1
       ,1
       ,1
       ,mp.lngproductionquantity
       ,mp.curproductprice
       ,mp.curproductprice * mp.lngproductionquantity
       ,(select coalesce(max(lngestimatedetailno),0) + 1 from t_estimatedetail where lngestimateno = r_check.lngestimateno and lngrevisionno=0)
       ,2
       ,1
   from m_product mp
   where mp.strproductcode = r_check.strproductcode 
       and mp.strrevisecode = r_check.strrevisecode
       and mp.lngrevisionno = 0;

END LOOP;
close cur_check;
END $$