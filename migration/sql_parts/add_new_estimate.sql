DO $$
-- 見積原価マスタ、受注マスタ・明細、発注マスタ・明細を基に
-- 見積原価明細を作成する。


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
        and me.lngrevisionno = 0
    group by 
        me.lngestimateno
       ,me.lngrevisionno
       ,me.strproductcode
       ,me.strrevisecode
    order by me.lngestimateno,me.lngrevisionno;

r_check RECORD;
remain integer;
p_total numeric(20,4);
price integer;
per_once integer;

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
    and me.lngrevisionno = 0;

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
           ,me.lngrevisionno as lngestimaterevisionno
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
           ,case tr.lngproductunitcode when 2 then trunc(tr.curproductprice / (case mp.lngcartonquantity when 0 then 1 else mp.lngcartonquantity end) ,4) else tr.curproductprice end as curproductprice
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
            and NOT(salesdivision.lngestimateareaclassno = 1 and salesdivision.lngsalesclasscode = 99 and salesdivision.lngsalesdivisioncode = 2)
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
           ,me.lngrevisionno as lngestimaterevisionno
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
           ,case tr.lngproductunitcode when 2 then trunc(tr.curproductprice / (case mp.lngcartonquantity when 0 then 1 else mp.lngcartonquantity end),4) else tr.curproductprice end as curproductprice
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
            and  me.strrevisecode = tr.strrevisecode
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
    -- 証紙以外のエリア5(cursubtotalpriceが0またはnullのデータを除外したい）
    cur_cost cursor(estimateno integer, revisionno integer) for
    select * from dblink('con111','
select 
    t_estimatedetail.lngestimateno
   ,t_estimatedetail.lngestimatedetailno
   ,0 as lngrevisionno
   ,t_estimatedetail.lngstocksubjectcode
   ,t_estimatedetail.lngstockitemcode
   ,t_estimatedetail.lngcustomercompanycode
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
where t_estimatedetail.lngestimateno = ' || estimateno ||
' and t_estimatedetail.lngrevisionno = (select max(lngrevisionno) from m_estimate where lngestimateno = ' || estimateno || ')
    and t_estimatedetail.lngstocksubjectcode in (1224,1230) 
   ')
    AS T1(
    lngestimateno integer
   ,lngestimatedetailno  integer
   ,lngrevisionno integer
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,lngproductquantity numeric(14,4)
   ,curproductprice numeric(14,4)
   ,curproductrate numeric(15, 6)
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngsortkey integer
   ,lngsalesdivisioncode integer
   ,lngsalesclasscode integer
    );
begin

    delete from t_estimatedetail;
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
--RAISE INFO '% start', me_r.lngestimateno;
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
               ,0
               ,null
               ,null
               ,tr_r.lngcustomercompanycode
               ,coalesce(tr_r.dtmdelivery,(select dtmdeliverylimitdate from m_product where strproductcode = me_r.strproductcode))
               ,false
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
--RAISE INFO '(r)% % % -> % % %', tr_r.lngreceiveno, tr_r.lngreceivedetailno, tr_r.r_lngrevisionno, tr_r.lngestimateno, detailno, tr_r.lngestimaterevisionno;
            update t_receivedetail
            set 
                lngestimateno = tr_r.lngestimateno
               ,lngestimatedetailno = detailno
               ,lngestimaterevisionno = 0
            where 
                lngreceiveno = tr_r.lngreceiveno
                and lngreceivedetailno = tr_r.lngreceivedetailno
                and lngrevisionno = 0;
            detailno = detailno + 1;

        END LOOP;
        close cur_tr;
--RAISE INFO '% receive OK', me_r.lngestimateno;

        open cur_to(me_r.strproductcode);
        LOOP
            FETCH cur_to into tr_r;
            EXIT WHEN NOT FOUND;
            -- 発注明細から見積原価明細（発注分）を作成
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
               ,0
               ,tr_r.lngstocksubjectcode
               ,tr_r.lngstockitemcode
               ,tr_r.lngcustomercompanycode
               ,coalesce(tr_r.dtmdelivery,(select dtmdeliverylimitdate from m_product where strproductcode = me_r.strproductcode))
               ,false    -- 償却はのちに設定
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
--RAISE INFO '(o)% % % -> % % %', tr_r.lngorderno, tr_r.lngorderdetailno, tr_r.r_lngrevisionno, tr_r.lngestimateno, detailno, tr_r.lngestimaterevisionno;
            update t_orderdetail
            set 
                lngestimateno = tr_r.lngestimateno
               ,lngestimatedetailno = detailno
               ,lngestimaterevisionno = 0
            where 
                lngorderno = tr_r.lngorderno
                and lngorderdetailno = tr_r.lngorderdetailno
                and lngrevisionno = 0;
            detailno = detailno + 1;
        END LOOP;
        close cur_to;
--RAISE INFO '% order OK', me_r.lngestimateno;
        
-- エリア5の発注が発生しない経費の明細を移行

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
               ,0
               ,tr_r.lngstocksubjectcode
               ,tr_r.lngstockitemcode
               ,tr_r.lngcustomercompanycode
               ,(select dtmdeliverylimitdate from m_product where strproductcode = me_r.strproductcode)
               ,false    -- 償却はのちに設定
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
            detailno = detailno + 1;
        END LOOP;
        close cur_cost;
--RAISE INFO '% cost OK', me_r.lngestimateno;

RAISE INFO '% completed', me_r.lngestimateno;

    END LOOP;
    close cur_me;

    update m_estimate set lngproductrevisionno = 0;



-- 本荷の明細がない製品に本荷を追加するか、製品マスタの生産数を補正。
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
RAISE INFO 'add honni % % %', r_honni.lngestimateno, r_honni.lngestimatedetailno, r_honni.lngrevisionno;
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
--RAISE INFO 'add honni(m_receive) % % %', r_honni.lngestimateno, r_honni.lngestimatedetailno, r_honni.lngrevisionno;
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
           ,bytinvalidflag
           ,lngcustomercompanycode
           ,lngmonetaryunitcode
           ,lngmonetaryratecode
           ,curconversionrate
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
           ,false
           ,r_honni.lngcustomercompanycode
           ,1
           ,1
           ,1
           );
           update t_sequence set lngsequence = lngsequence+1  where strsequencename = 'm_receive.strreceivecode.' || to_char(r_honni.dtmdelivery,'yymm');
    
        -- 受注明細
RAISE INFO 'add honni(t_receivedetail) % % %', r_honni.lngestimateno, r_honni.lngestimatedetailno, r_honni.lngrevisionno;
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
    END IF;

END LOOP;
close cur_check;

RAISE INFO 'add_new_estimate completed';
END $$