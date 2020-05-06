
DO $$

--BEGIN TRANSACTION;
--発注マスタカーソル
declare
    receiveno integer;
    cur_header CURSOR FOR
    select * from dblink('con111','
select
    lngreceiveno
   ,lngrevisionno
   ,strreceivecode
   ,strrevisecode
   ,dtmappropriationdate
   ,lngcustomercompanycode
   ,lnggroupcode
   ,lngusercode
   ,lngreceivestatuscode
   ,lngmonetaryunitcode
   ,lngmonetaryratecode
   ,curconversionrate
   ,lnginputusercode
   ,bytinvalidflag
   ,dtminsertdate
   ,strcustomerreceivecode
from m_receive
where bytinvalidflag = false
    and lngreceivestatuscode is not null
    and (strreceivecode, lngrevisionno) in
    (
        select
            strreceivecode
           ,MAX(lngrevisionno) as lngrevisionno
        from m_receive
        where strreceivecode not in (select strreceivecode from m_receive where lngrevisionno < 0)
        group by strreceivecode
    )
order by strreceivecode'
    ) 
    AS T1
    (
        lngreceiveno	integer
       ,lngrevisionno	integer
       ,strreceivecode	text
       ,strrevisecode	character(2)
       ,dtmappropriationdate	date
       ,lngcustomercompanycode	integer
       ,lnggroupcode	integer
       ,lngusercode	integer
       ,lngreceivestatuscode	integer
       ,lngmonetaryunitcode	integer
       ,lngmonetaryratecode	integer
       ,curconversionrate	numeric(15, 6)
       ,lnginputusercode	integer
       ,bytinvalidflag	boolean
       ,dtminsertdate	timestamp(6) without time zone
       ,strcustomerreceivecode	text
    );


--移行元発注明細テーブルカーソル
    cur_detail CURSOR(receiveno integer, revisionno integer) FOR
    SELECT * FROM dblink('con111', '
select  
    t_receivedetail.lngreceiveno 
    ,t_receivedetail.lngreceivedetailno 
    ,t_receivedetail.lngrevisionno 
    ,t_receivedetail.strproductcode 
    ,''00'' as strrevisecode 
    ,t_receivedetail.lngsalesclasscode 
    ,t_receivedetail.dtmdeliverydate 
    ,t_receivedetail.lngconversionclasscode 
    ,t_receivedetail.curproductprice 
    ,t_receivedetail.lngproductquantity 
    ,t_receivedetail.lngproductunitcode 
    ,m_product.lngcartonquantity as lngunitquantity 
    ,t_receivedetail.cursubtotalprice 
    ,t_receivedetail.strnote 
    ,t_receivedetail.lngsortkey  
from t_receivedetail 
inner join m_product
    on m_product.strproductcode = t_receivedetail.strproductcode
where t_receivedetail.lngreceiveno = ' || receiveno || 
' and t_receivedetail.lngrevisionno = ' || revisionno || 
' order by t_receivedetail.lngreceivedetailno'
    )
    AS T2
    (
        lngreceiveno integer
       ,lngreceivedetailno integer
       ,lngrevisionno integer
       ,strproductcode	text
       ,strrevisecode	text
       ,lngsalesclasscode	integer
       ,dtmdeliverydate	date
       ,lngconversionclasscode	integer
       ,curproductprice	numeric(14, 4)
       ,lngproductquantity	integer
       ,lngproductunitcode	integer
       ,lngunitquantity	integer
       ,cursubtotalprice	numeric(14, 4)
       ,strnote	text
       ,lngsortkey	integer
    );
    detail RECORD;
    header RECORD;
    po_key RECORD;
    write_count integer;
    po_count integer;
    product_code text;
    total_price numeric(14, 4);
    last_receive text;
    current_receive text;
    last_receiveno integer;
    last_detail integer;
    max_detail integer;
    max_revision integer;
    last_revision integer;
BEGIN

    last_receive = '';
    current_receive = '';
    write_count = 0;
    po_count = 0;
    last_detail = -999;
    max_revision = 0;
    delete from m_receive;
    delete from t_receivedetail;

    drop table if exists receive_conversion;
    create table receive_conversion(
        old_receive integer
       ,detailno  integer
       ,new_receive integer
    );
--受注明細カーソルオープン（条件：受注番号 = 読み込んだ受注マスタの受注番号）

    open cur_header;
    LOOP
        FETCH cur_header INTO header;
        EXIT WHEN NOT FOUND;
        open cur_detail(header.lngreceiveno,header.lngrevisionno);
        LOOP
            FETCH cur_detail INTO detail;
            EXIT WHEN NOT FOUND;
--RAISE INFO '% % % % % ', header.strreceivecode, header.lngrevisionno, detail.lngreceivedetailno, detail.lngreceiveno, write_count;

-- 受注明細件数分、受注マスタを登録
            insert into m_receive(
                lngreceiveno
               ,lngrevisionno
               ,strreceivecode
               ,strrevisecode
               ,dtmappropriationdate
               ,lngcustomercompanycode
               ,lnggroupcode
               ,lngusercode
               ,lngreceivestatuscode
               ,lngmonetaryunitcode
               ,lngmonetaryratecode
               ,curconversionrate
               ,lnginputusercode
               ,bytinvalidflag
               ,dtminsertdate
               ,strcustomerreceivecode
            )
            values(
                write_count
               ,0
               ,header.strreceivecode
               ,'00'
               ,header.dtmappropriationdate
               ,header.lngcustomercompanycode
               ,header.lnggroupcode
               ,header.lngusercode
               ,header.lngreceivestatuscode
               ,header.lngmonetaryunitcode
               ,header.lngmonetaryratecode
               ,header.curconversionrate
               ,header.lnginputusercode
               ,header.bytinvalidflag
               ,header.dtminsertdate
               ,header.strcustomerreceivecode
            );
-- 受注明細を登録
            insert into t_receivedetail
            (
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
            )
            values
            (
                write_count
               ,detail.lngreceivedetailno
               ,0
               ,detail.strproductcode
               ,'00'
               ,detail.lngsalesclasscode
               ,detail.dtmdeliverydate
               ,detail.lngconversionclasscode
               ,detail.curproductprice
               ,detail.lngproductquantity
               ,detail.lngproductunitcode
               ,detail.lngunitquantity
               ,detail.cursubtotalprice
               ,detail.strnote
               ,detail.lngsortkey
            );
-- 新旧変換データを作成
            insert into receive_conversion
            (
                old_receive
               ,detailno
               ,new_receive
            )
            values
            (
                header.lngreceiveno
               ,detail.lngreceivedetailno
               ,write_count
            );
            total_price = total_price + detail.cursubtotalprice;
            write_count = write_count + 1;
        END LOOP;
        close cur_detail;
    END LOOP;
    close cur_header;

END $$

