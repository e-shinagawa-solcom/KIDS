
DO $$

--BEGIN TRANSACTION;
--発注マスタカーソル
declare
    receiveno integer;
    cur_header CURSOR(receiveno integer, revisionno integer) FOR
    select * from dblink('con111',
        'select ' ||
        'lngreceiveno, ' ||
        'lngrevisionno, ' ||
        'strreceivecode, ' ||
        'strrevisecode, ' ||
        'dtmappropriationdate, ' ||
        'lngcustomercompanycode, ' ||
        'lnggroupcode, ' ||
        'lngusercode, ' ||
        'lngreceivestatuscode, ' ||
        'lngmonetaryunitcode, ' ||
        'lngmonetaryratecode, ' ||
        'curconversionrate, ' ||
        'lnginputusercode, ' ||
        'bytinvalidflag, ' ||
        'dtminsertdate, ' ||
        'strcustomerreceivecode ' ||
        'from m_receive ' || 
        'where lngreceiveno = ' || receiveno ||
        ' and lngrevisionno = ' || revisionno
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
    cur_detail CURSOR FOR
    SELECT * FROM dblink('con111',
        'select ' || 
        'm_receive.lngreceiveno,' || 
        'COALESCE(t_receivedetail.lngreceivedetailno, -1) AS lngreceivedetailno,' || 
--        't_receivedetail.lngreceivedetailno,' || 
        'm_receive.lngrevisionno,' || 
        't_receivedetail.strproductcode,' || 
--        't_receivedetail.strrevisecode,' || 
        't_receivedetail.lngsalesclasscode,' || 
        't_receivedetail.dtmdeliverydate,' || 
        't_receivedetail.lngconversionclasscode,' || 
        't_receivedetail.curproductprice,' || 
        't_receivedetail.lngproductquantity,' || 
        't_receivedetail.lngproductunitcode,' || 
--        't_receivedetail.lngunitquantity,' || 
        't_receivedetail.cursubtotalprice,' || 
        't_receivedetail.strnote,' || 
        't_receivedetail.lngsortkey ' || 
        'from m_receive ' ||
        'left outer join t_receivedetail ' ||
        'on t_receivedetail.lngreceiveno = m_receive.lngreceiveno ' ||
        'and t_receivedetail.lngrevisionno = m_receive.lngrevisionno ' ||
        'where m_receive.bytinvalidflag = FALSE ' ||
        'order by m_receive.strreceivecode' ||
        ', t_receivedetail.lngreceivedetailno' ||
        ', m_receive.lngrevisionno' ||
        ', m_receive.lngreceiveno'
    )
    AS T2
    (
        lngreceiveno integer
       ,lngreceivedetailno integer
       ,lngrevisionno integer
       ,strproductcode	text
--       ,strrevisecode	text
       ,lngsalesclasscode	integer
       ,dtmdeliverydate	date
       ,lngconversionclasscode	integer
       ,curproductprice	numeric(14, 4)
       ,lngproductquantity	integer
       ,lngproductunitcode	integer
--       ,lngunitquantity	integer
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
/*
    delete from m_purchasereceive;
    delete from t_purchasereceivedetail;
*/

    drop table if exists receive_conversion;
    create table receive_conversion(
        old_receive integer
       ,detailno  integer
       ,new_receive integer
    );
--発注明細カーソルオープン（条件：発注番号 = 読み込んだ発注マスタの発注番号）

    open cur_detail;
    LOOP
        FETCH cur_detail INTO detail;
        EXIT WHEN NOT FOUND;
        open cur_header(detail.lngreceiveno,detail.lngrevisionno);
        FETCH cur_header INTO header;
        close cur_header;
        IF current_receive <> header.strreceivecode or 
        ( detail.lngreceivedetailno is not null and detail.lngreceivedetailno >= 0 and last_detail <> detail.lngreceivedetailno) THEN
            write_count = write_count + 1;
            last_detail = detail.lngreceivedetailno;
            current_receive = header.strreceivecode;
        END IF;
--        RAISE INFO '% % % % % ', header.strreceivecode, header.lngrevisionno, detail.lngreceivedetailno, detail.lngreceiveno, write_count;
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
           ,header.lngrevisionno
           ,header.strreceivecode
           ,header.strrevisecode
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
-- 発注明細を登録
        IF detail.lngrevisionno >= 0 THEN
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
               ,detail.lngrevisionno
               ,detail.strproductcode
               ,'00'
               ,detail.lngsalesclasscode
               ,detail.dtmdeliverydate
               ,detail.lngconversionclasscode
               ,detail.curproductprice
               ,detail.lngproductquantity
               ,detail.lngproductunitcode
               ,1
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
        END IF;
    END LOOP;
    close cur_detail;

END $$

