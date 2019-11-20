
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
/*
-- 納品書マスタキー検索用カーソル
    cur_po_key CURSOR FOR
    select distinct
        strreceivecode
       ,lngrevisionno
    from m_receive
    where lngreceivestatuscode >= 4
    order by strreceivecode, lngrevisionno;
    cur_po_detail CURSOR(receivecode text, revisionno integer) FOR
    select
        t_receivedetail.lngreceivedetailno
       ,t_receivedetail.lngrevisionno
       ,t_receivedetail.lngreceiveno
       ,t_receivedetail.lngstocksubjectcode
       ,t_receivedetail.lngstockitemcode
       ,m_stockitem.strstockitemname
       ,t_receivedetail.lngdeliverymethodcode
       ,m_deliverymethod.strdeliverymethodname
       ,t_receivedetail.curproductprice
       ,t_receivedetail.lngproductquantity
       ,t_receivedetail.lngproductunitcode
       ,m_productunit.strproductunitname
       ,t_receivedetail.cursubtotalprice
       ,t_receivedetail.dtmdeliverydate
       ,t_receivedetail.strnote
       ,t_receivedetail.lngsortkey
       ,t_receivedetail.strproductcode
    from t_receivedetail
    inner join m_receive
        on m_receive.lngreceiveno = t_receivedetail.lngreceiveno
        and m_receive.lngrevisionno = t_receivedetail.lngrevisionno
    left outer join m_stockitem
        on m_stockitem.lngstockitemcode = t_receivedetail.lngstockitemcode
        and m_stockitem.lngstocksubjectcode = t_receivedetail.lngstocksubjectcode
    left outer join m_deliverymethod
        on m_deliverymethod.lngdeliverymethodcode = t_receivedetail.lngdeliverymethodcode
    left outer join m_productunit
        on m_productunit.lngproductunitcode = t_receivedetail.lngproductunitcode
    where m_receive.strreceivecode = receivecode
        and m_receive.lngrevisionno = revisionno
    order by t_receivedetail.lngreceivedetailno;
*/
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
        IF current_receive <> header.strreceivecode OR detail.lngreceivedetailno <> last_detail THEN
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
/*
    po_count = 0;
    last_receive = '';
    open cur_po_key;
    LOOP
        -- 発注マスタのキー項目でループ
        FETCH cur_po_key INTO po_key;
        EXIT WHEN NOT FOUND;
        IF last_receive <> po_key.strreceivecode THEN
            last_receive = po_key.strreceivecode;
            po_count = po_count + 1;
            max_revision = 0;
            last_revision = po_key.lngrevisionno;
        END IF;
        IF po_key.lngrevisionno <> last_revision THEN
            last_revision = po_key.lngrevisionno;
            max_revision = max_revision + 1;
        END IF ;
        total_price = 0;
        -- キー項目で取得した発注明細のでループ
        open cur_po_detail(po_key.strreceivecode, po_key.lngrevisionno);
        LOOP
            FETCH cur_po_detail into detail;
            EXIT WHEN NOT FOUND;
            select count(*) + 1 into max_detail from t_purchasereceivedetail where lngpurchasereceiveno = po_count;
            --RAISE INFO '% % % % % ', po_key.strreceivecode, po_key.lngrevisionno, po_count, max_revision, max_detail;
            -- 発注書明細登録
            insert into t_purchasereceivedetail
            (
                lngpurchasereceiveno
               ,lngpurchasereceivedetailno
               ,lngrevisionno
               ,lngreceiveno
               ,lngreceivedetailno
               ,lngreceiverevisionno
               ,lngstocksubjectcode
               ,lngstockitemcode
               ,strstockitemname
               ,lngdeliverymethodcode
               ,strdeliverymethodname
               ,curproductprice
               ,lngproductquantity
               ,lngproductunitcode
               ,strproductunitname
               ,cursubtotalprice
               ,dtmdeliverydate
               ,strnote
               ,lngsortkey
            )
            values(
                po_count
               ,max_detail
               ,max_revision
               ,detail.lngreceiveno
               ,detail.lngreceivedetailno
               ,detail.lngrevisionno
               ,detail.lngstocksubjectcode
               ,detail.lngstockitemcode
               ,detail.strstockitemname
               ,detail.lngdeliverymethodcode
               ,detail.strdeliverymethodname
               ,detail.curproductprice
               ,detail.lngproductquantity
               ,detail.lngproductunitcode
               ,detail.strproductunitname
               ,detail.cursubtotalprice
               ,detail.dtmdeliverydate
               ,detail.strnote
               ,detail.lngsortkey
            );
            total_price = total_price + detail.cursubtotalprice;
            last_receiveno = detail.lngreceiveno;
            last_revision = detail.lngrevisionno;
            product_code = detail.strproductcode;
        END LOOP;
        close cur_po_detail;
        
--        RAISE INFO '% % %', last_receiveno, last_revision, product_code;
        -- 発注書マスタ登録
        insert into m_purchasereceive
        (
            lngpurchasereceiveno
           ,lngrevisionno
           ,strreceivecode
           ,lngcustomercode
           ,strcustomername
           ,strcustomercompanyaddreess
           ,strcustomercompanytel
           ,strcustomercompanyfax
           ,strproductcode
           ,strrevisecode
           ,strproductname
           ,strproductenglishname
           ,dtmexpirationdate
           ,lngmonetaryunitcode
           ,strmonetaryunitsign
           ,strmonetaryunitname
           ,lngmonetaryratecode
           ,strmonetaryratename
           ,lngpayconditioncode
           ,strpayconditionname
           ,lnggroupcode
           ,strgroupname
           ,lngusercode
           ,strusername
           ,lngdeliveryplacecode
           ,strdeliveryplacename
           ,curtotalprice
           ,dtminsertdate
           ,lnginsertusercode
           ,strinsertusername
           ,strnote
           ,lngprintcount
        )
        select
            po_count
           ,max_revision
           ,m_receive.strreceivecode
           ,m_receive.lngcustomercompanycode
           ,customer.strcompanyname
           ,case customer.lngcountrycode
               when 81 then trim(customer.straddress1 || ' ' || customer.straddress2 || ' ' || customer.straddress3 || ' ' || customer.straddress4)
               else trim(customer.straddress4 || ' ' || customer.straddress3 || ' ' || customer.straddress2 || ' ' || customer.straddress1)
            END
           ,customer.strtel1
           ,customer.strfax1
           ,product_code
           ,m_product.strrevisecode
           ,m_product.strproductname
           ,m_product.strproductenglishname
           ,(select dtmexpirationdate from expire_date where lngreceiveno = last_receiveno and lngrevisionno = last_revision)
           ,m_receive.lngmonetaryunitcode
           ,m_monetaryunit.strmonetaryunitsign
           ,m_monetaryunit.strmonetaryunitname
           ,m_receive.lngmonetaryratecode
           ,m_monetaryrateclass.strmonetaryratename
           ,m_receive.lngpayconditioncode
           ,m_paycondition.strpayconditionname
           ,m_receive.lnggroupcode
           ,m_group.strgroupname
           ,m_receive.lngusercode
           ,m_user.struserdisplayname
           ,m_receive.lngdeliveryplacecode
           ,delivery.strcompanyname
           ,total_price
           ,m_receive.dtminsertdate
           ,m_receive.lnginputusercode
           ,m_user.struserdisplayname
           ,null
           ,0
        from m_receive
        left outer join m_company customer
            on customer.lngcompanycode = m_receive.lngcustomercompanycode
        left outer join m_monetaryunit
            on m_monetaryunit.lngmonetaryunitcode = m_receive.lngmonetaryunitcode
        left outer join m_monetaryrateclass
            on m_monetaryrateclass.lngmonetaryratecode = m_receive.lngmonetaryratecode
        left outer join m_paycondition
            on m_paycondition.lngpayconditioncode = m_receive.lngpayconditioncode
        left outer join m_group
            on m_group.lnggroupcode = m_receive.lnggroupcode
        left outer join m_user
            on m_user.lngusercode = m_receive.lnginputusercode
        left outer join m_company delivery
            on delivery.lngcompanycode = m_receive.lngdeliveryplacecode
        left outer join m_product
            on  m_product.lngrevisionno = 0
            and m_product.strrevisecode = '00'
         where m_receive.lngreceiveno = last_receiveno
             and m_receive.lngrevisionno = last_revision
             and m_product.strproductcode = product_code
        ;
        -- 発注書マスタ削除チェック
        IF EXISTS( 
            select * 
            from m_receive 
            where strreceivecode = ( select strreceivecode from m_receive where lngreceiveno = last_receiveno and lngrevisionno = last_revision )
                and lngrevisionno = -1 ) THEN
            RAISE INFO 'data deleted % %', product_code, last_revision;
            insert into m_purchasereceive
            (
                lngpurchasereceiveno
                ,lngrevisionno
                ,strreceivecode
            )
            values
            (
                last_receiveno
               ,-1
               ,(select strreceivecode from m_receive where lngreceiveno = lngreceiveno and lngrevisionno = last_revision)
            );
        END IF;
    END LOOP;
*/
END $$

