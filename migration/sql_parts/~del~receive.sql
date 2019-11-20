DO $$

--BEGIN TRANSACTION;
--受注マスタカーソル
declare
    receiveno integer;
    cur_header CURSOR FOR
    select * from dblink('con111',
        'select ' ||
        'lngreceiveno ' ||
        ',lngrevisionno ' ||
        ',strreceivecode ' ||
        ',strrevisecode ' ||
        ',dtmappropriationdate ' ||
        ',lngcustomercompanycode ' ||
        ',lnggroupcode ' ||
        ',lngusercode ' ||
        ',lngreceivestatuscode ' ||
        ',lngmonetaryunitcode ' ||
        ',lngmonetaryratecode ' ||
        ',curconversionrate ' ||
        ',lnginputusercode ' ||
        ',bytinvalidflag ' ||
        ',dtminsertdate ' ||
        ',strcustomerreceivecode ' ||
        'from m_receive ' ||
        'order by strreceivecode, lngreceiveno'
    )
    AS T1
    (
        lngreceiveno integer
       ,lngrevisionno integer
       ,strreceivecode text
       ,strrevisecode character(2)
       ,dtmappropriationdate date
       ,lngcustomercompanycode integer
       ,lnggroupcode integer
       ,lngusercode integer
       ,lngreceivestatuscode integer
       ,lngmonetaryunitcode integer
       ,lngmonetaryratecode integer
       ,curconversionrate numeric(15, 6)
       ,lnginputusercode integer
       ,bytinvalidflag boolean
       ,dtminsertdate timestamp without time zone
       ,strcustomerreceivecode text
    );


--移行元受注明細テーブルカーソル
    cur_detail CURSOR(receiveno integer, revisionno integer) FOR
    SELECT * FROM dblink('con111',
        'select ' || 
        't_receivedetail.lngreceiveno ' || 
        ',t_receivedetail.lngreceivedetailno ' || 
        ',t_receivedetail.lngrevisionno ' || 
        ',t_receivedetail.strproductcode ' || 
        ',''00'' as strrevisecode ' || 
        ',t_receivedetail.lngsalesclasscode ' || 
        ',t_receivedetail.dtmdeliverydate ' || 
        ',t_receivedetail.lngconversionclasscode ' || 
        ',t_receivedetail.curproductprice ' || 
        ',t_receivedetail.lngproductquantity ' || 
        ',t_receivedetail.lngproductunitcode ' || 
        ',1 as lngunitquantity ' || 
        ',t_receivedetail.lngtaxclasscode ' || 
        ',t_receivedetail.lngtaxcode ' || 
        ',t_receivedetail.curtaxprice ' || 
        ',t_receivedetail.cursubtotalprice ' || 
        ',t_receivedetail.strnote ' || 
        ',t_receivedetail.lngsortkey ' || 
        ',null as lngestimateno ' || 
        ',null as lngestimatedetailno ' || 
        'from t_receivedetail ' ||
        'inner join m_product on m_product.strproductcode = t_receivedetail.strproductcode ' ||
        'where lngreceiveno = ' ||  receiveno ||
        'and lngrevisionno = ' ||  revisionno ||
        'order by ' || 
        'lngreceivedetailno,' || 
        'lngrevisionno')
    AS T2
    (
        lngreceiveno integer
       ,lngreceivedetailno integer
       ,lngrevisionno integer
       ,strproductcode text
       ,strrevisecode text
       ,lngsalesclasscode integer
       ,dtmdeliverydate date
       ,lngconversionclasscode integer
       ,curproductprice numeric(14, 4)
       ,lngproductquantity integer
       ,lngproductunitcode integer
       ,lngunitquantity integer
       ,lngtaxclasscode integer
       ,lngtaxcode integer
       ,curtaxprice numeric(14, 4)
       ,cursubtotalprice numeric(14, 4)
       ,strnote text
       ,lngsortkey integer
       ,lngestimateno integer
       ,lngestimatedetailno integer
    );

    detail RECORD;
    header RECORD;
    write_count integer;
    invoice_count integer;
    last_receive text;
BEGIN
    write_count = 0;
    last_receive = '';
    invoice_count = 1;
    delete from m_receive;
    delete from t_receivedetail;
    drop table if exists receive_conversion;
    create table receive_conversion(
        old_receive integer
       ,detailno  integer
       ,new_receive integer
    );
--受注マスタカーソルオープン
    open  cur_header;
    LOOP
        FETCH cur_header INTO header;
        EXIT WHEN NOT FOUND;
--        RAISE INFO '% % %', header.lngreceiveno, header.lngrevisionno, header.strreceivecode;
--受注明細カーソルオープン（条件：受注番号 = 読み込んだ受注マスタの受注番号）
        open cur_detail(header.lngreceiveno, header.lngrevisionno );
        LOOP
            FETCH cur_detail INTO detail;
            EXIT WHEN NOT FOUND;
-- 受注明細件数分、受注マスタを登録
            write_count = write_count + 1;
--            RAISE INFO '% % % %', write_count, header.strreceivecode, header.lngrevisionno, header.lngreceiveno;
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
               ,lngestimateno
               ,lngestimatedetailno
            )
            values
            (
                write_count
               ,detail.lngreceivedetailno
               ,detail.lngrevisionno
               ,detail.strproductcode
               ,header.strrevisecode
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
               ,detail.lngestimateno
               ,detail.lngestimatedetailno
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
            
            last_receive = header.strreceivecode;
--納品書明細、売上明細の登録
/*
            IF header.lngreceivestatuscode >= 4 AND header.lngreceivestatuscode <> 10 THEN
                insert into 
            END IF;
*/
        END LOOP;
        close cur_detail;
        IF header.lngrevisionno = -1 THEN
            RAISE INFO 'delete m_receive %', header.strreceivecode;
            insert into m_receive(
                lngreceiveno
               ,lngrevisionno
               ,strreceivecode
            )
            values(
                write_count
               ,header.lngrevisionno
               ,header.strreceivecode
                );
            write_count = write_count + 1;
        END IF;
--納品書明細マスタ、売上マスタの登録
/*
        IF header.lngreceivestatuscode >= 4 AND header.lngreceivestatuscode <> 10 THEN
            insert into 
            invoice_count = invoice_count + 1;
        END IF;
*/
    END LOOP;
    close cur_header;
END $$



--COMMIT;