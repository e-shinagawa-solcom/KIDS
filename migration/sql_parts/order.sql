
DO $$

--BEGIN TRANSACTION;
--�����}�X�^�J�[�\��
declare
    orderno integer;
    cur_header CURSOR(orderno integer, revisionno integer) FOR
    select * from dblink('con111',
        'select ' ||
        'lngorderno ' || 
        ',lngrevisionno ' || 
        ',strordercode ' || 
        ',strrevisecode ' || 
        ',dtmappropriationdate ' || 
        ',lngcustomercompanycode ' || 
        ',lnggroupcode ' || 
        ',lngusercode ' || 
        ',lngorderstatuscode ' || 
        ',lngmonetaryunitcode ' || 
        ',lngmonetaryratecode ' || 
        ',curconversionrate ' || 
        ',lngpayconditioncode ' || 
        ',lngdeliveryplacecode ' || 
        ',dtmexpirationdate ' || 
        ',lnginputusercode ' || 
        ',bytinvalidflag ' || 
        ',dtminsertdate ' || 
        'from m_order ' || 
        'where lngorderno = ' || orderno ||
        ' and lngrevisionno = ' || revisionno
    ) 
    AS T1
    (
        lngorderno integer
       ,lngrevisionno integer
       ,strordercode text
       ,strrevisecode character(2)
       ,dtmappropriationdate date
       ,lngcustomercompanycode integer
       ,lnggroupcode integer
       ,lngusercode integer
       ,lngorderstatuscode integer
       ,lngmonetaryunitcode integer
       ,lngmonetaryratecode integer
       ,curconversionrate numeric(15, 6)
       ,lngpayconditioncode integer
       ,lngdeliveryplacecode integer
       ,dtmexpirationdate date
       ,lnginputusercode integer
       ,bytinvalidflag boolean
       ,dtminsertdate timestamp without time zone
    );


--�ڍs���������׃e�[�u���J�[�\��
    cur_detail CURSOR FOR
    SELECT * FROM dblink('con111',
        'select ' || 
        'm_order.lngorderno,' || 
        'COALESCE(t_orderdetail.lngorderdetailno, -1) AS lngorderdetailno,' || 
--        't_orderdetail.lngorderdetailno,' || 
        'm_order.lngrevisionno,' || 
        't_orderdetail.strproductcode,' || 
        't_orderdetail.lngstocksubjectcode,' || 
        't_orderdetail.lngstockitemcode,' || 
        't_orderdetail.dtmdeliverydate,' || 
        't_orderdetail.lngdeliverymethodcode,' || 
        't_orderdetail.lngconversionclasscode,' || 
        't_orderdetail.curproductprice,' || 
        't_orderdetail.lngproductquantity,' || 
        't_orderdetail.lngproductunitcode,' || 
--        't_orderdetail.lngtaxclasscode,' || 
--        't_orderdetail.lngtaxcode,' || 
--        't_orderdetail.curtaxprice,' || 
        't_orderdetail.cursubtotalprice,' || 
        't_orderdetail.strnote,' || 
        't_orderdetail.strmoldno ' || 
        'from m_order ' ||
        'left outer join t_orderdetail ' ||
        'on t_orderdetail.lngorderno = m_order.lngorderno ' ||
        'and t_orderdetail.lngrevisionno = m_order.lngrevisionno ' ||
        'where m_order.bytinvalidflag = FALSE ' ||
        'order by m_order.strordercode' ||
        ', t_orderdetail.lngorderdetailno' ||
        ', m_order.lngrevisionno' ||
        ', m_order.lngorderno'
    )
    AS T2
    (
        lngorderno integer
       ,lngorderdetailno integer
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
--       ,lngtaxclasscode integer
--       ,lngtaxcode integer
--       ,curtaxprice numeric(14, 4)
       ,cursubtotalprice numeric(14, 4)
       ,strnote text
       ,strmoldno text
    );
-- �������}�X�^�L�[�����p�J�[�\��
    cur_po_key CURSOR FOR
    select distinct
        strordercode
       ,lngrevisionno
    from m_order
    where lngorderstatuscode >= 4
    order by strordercode, lngrevisionno;
    cur_po_detail CURSOR(ordercode text, revisionno integer) FOR
    select
        t_orderdetail.lngorderdetailno
       ,t_orderdetail.lngrevisionno
       ,t_orderdetail.lngorderno
       ,t_orderdetail.lngstocksubjectcode
       ,t_orderdetail.lngstockitemcode
       ,m_stockitem.strstockitemname
       ,t_orderdetail.lngdeliverymethodcode
       ,m_deliverymethod.strdeliverymethodname
       ,t_orderdetail.curproductprice
       ,t_orderdetail.lngproductquantity
       ,t_orderdetail.lngproductunitcode
       ,m_productunit.strproductunitname
       ,t_orderdetail.cursubtotalprice
       ,t_orderdetail.dtmdeliverydate
       ,t_orderdetail.strnote
       ,t_orderdetail.lngsortkey
       ,t_orderdetail.strproductcode
    from t_orderdetail
    inner join m_order
        on m_order.lngorderno = t_orderdetail.lngorderno
        and m_order.lngrevisionno = t_orderdetail.lngrevisionno
    left outer join m_stockitem
        on m_stockitem.lngstockitemcode = t_orderdetail.lngstockitemcode
        and m_stockitem.lngstocksubjectcode = t_orderdetail.lngstocksubjectcode
    left outer join m_deliverymethod
        on m_deliverymethod.lngdeliverymethodcode = t_orderdetail.lngdeliverymethodcode
    left outer join m_productunit
        on m_productunit.lngproductunitcode = t_orderdetail.lngproductunitcode
    where m_order.strordercode = ordercode
        and m_order.lngrevisionno = revisionno
    order by t_orderdetail.lngorderdetailno;

    detail RECORD;
    header RECORD;
    po_key RECORD;
    write_count integer;
    po_count integer;
    product_code text;
    total_price numeric(14, 4);
    last_order text;
    current_order text;
    last_orderno integer;
    last_detail integer;
    max_detail integer;
    max_revision integer;
    last_revision integer;
BEGIN
    last_order = '';
    current_order = '';
    write_count = 0;
    po_count = 0;
    last_detail = -999;
    max_revision = 0;
    delete from m_order;
    delete from t_orderdetail;
    delete from m_purchaseorder;
    delete from t_purchaseorderdetail;

    drop table if exists order_conversion;
    create table order_conversion(
        old_order integer
       ,detailno  integer
       ,new_order integer
    );
    drop table if exists expire_date;
    create table expire_date
        (
            lngorderno integer
           ,lngrevisionno integer
           ,dtmexpirationdate date
        );

--�������׃J�[�\���I�[�v���i�����F�����ԍ� = �ǂݍ��񂾔����}�X�^�̔����ԍ��j

    open cur_detail;
    LOOP
        FETCH cur_detail INTO detail;
        EXIT WHEN NOT FOUND;
        open cur_header(detail.lngorderno,detail.lngrevisionno);
        FETCH cur_header INTO header;
        close cur_header;
        IF current_order <> header.strordercode OR detail.lngorderdetailno <> last_detail THEN
            write_count = write_count + 1;
            last_detail = detail.lngorderdetailno;
            current_order = header.strordercode;
        END IF;
-- �������׌������A�����}�X�^��o�^
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
--           ,dtmexpirationdate
           ,lnginputusercode
           ,bytinvalidflag
           ,dtminsertdate
        )
        values(
            write_count
           ,header.lngrevisionno
           ,header.strordercode
           ,header.dtmappropriationdate
           ,header.lngcustomercompanycode
           ,header.lnggroupcode
           ,header.lngusercode
           ,header.lngorderstatuscode
           ,header.lngmonetaryunitcode
           ,header.lngmonetaryratecode
           ,header.curconversionrate
           ,header.lngpayconditioncode
           ,header.lngdeliveryplacecode
--           ,header.dtmexpirationdate
           ,header.lnginputusercode
           ,header.bytinvalidflag
           ,header.dtminsertdate
        );
-- �������ׂ�o�^
        IF detail.lngrevisionno >= 0 THEN
--        RAISE INFO '% % % % % ', header.strordercode, header.lngrevisionno, detail.lngorderdetailno, detail.lngorderno, write_count;
            insert into t_orderdetail
            (
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
--               ,lngtaxclasscode
--               ,lngtaxcode
--               ,curtaxprice
               ,cursubtotalprice
               ,strnote
               ,strmoldno
            )
            values
            (
                write_count
               ,detail.lngorderdetailno
               ,detail.lngrevisionno
               ,detail.strproductcode
               ,'00'
               ,detail.lngstocksubjectcode
               ,detail.lngstockitemcode
               ,detail.dtmdeliverydate
               ,detail.lngdeliverymethodcode
               ,detail.lngconversionclasscode
               ,detail.curproductprice
               ,detail.lngproductquantity
               ,detail.lngproductunitcode
--               ,detail.lngtaxclasscode
--               ,detail.lngtaxcode
--               ,detail.curtaxprice
               ,detail.cursubtotalprice
               ,detail.strnote
               ,detail.strmoldno
            );
            insert into order_conversion
            (
                old_order
               ,detailno
               ,new_order
            )
            values
            (
                header.lngorderno
               ,detail.lngorderdetailno
               ,write_count
            );
            insert into expire_date
            (
                lngorderno
               ,lngrevisionno
               ,dtmexpirationdate
            )
            values(
                write_count
               ,detail.lngrevisionno
               ,header.dtmexpirationdate
            );
            total_price = total_price + detail.cursubtotalprice;
            IF detail.lngorderdetailno = 1 THEN
                product_code = detail.strproductcode;
            END IF;
        END IF;
-- �V���ϊ��f�[�^���쐬
    END LOOP;
    close cur_detail;
RAISE INFO 'complete order';
    po_count = 0;
    last_order = '';
    open cur_po_key;
    LOOP
        -- �����}�X�^�̃L�[���ڂŃ��[�v
        FETCH cur_po_key INTO po_key;
        EXIT WHEN NOT FOUND;
        IF last_order <> po_key.strordercode THEN
            last_order = po_key.strordercode;
            po_count = po_count + 1;
            max_revision = 0;
            last_revision = po_key.lngrevisionno;
        END IF;
        IF po_key.lngrevisionno <> last_revision THEN
            last_revision = po_key.lngrevisionno;
            max_revision = max_revision + 1;
        END IF ;
        total_price = 0;
        -- �L�[���ڂŎ擾�����������ׂ̂Ń��[�v
        open cur_po_detail(po_key.strordercode, po_key.lngrevisionno);
        LOOP
            FETCH cur_po_detail into detail;
            EXIT WHEN NOT FOUND;
            select count(*) + 1 into max_detail from t_purchaseorderdetail where lngpurchaseorderno = po_count;
--RAISE INFO '% % % % % ', po_key.strordercode, po_key.lngrevisionno, po_count, max_revision, max_detail;
            -- ���������דo�^
            insert into t_purchaseorderdetail
            (
                lngpurchaseorderno
               ,lngpurchaseorderdetailno
               ,lngrevisionno
               ,lngorderno
               ,lngorderdetailno
               ,lngorderrevisionno
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
               ,detail.lngorderno
               ,detail.lngorderdetailno
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
            last_orderno = detail.lngorderno;
            last_revision = detail.lngrevisionno;
            product_code = detail.strproductcode;
        END LOOP;
        close cur_po_detail;
        
--        RAISE INFO '% % %', last_orderno, last_revision, product_code;
        -- �������}�X�^�o�^
        insert into m_purchaseorder
        (
            lngpurchaseorderno
           ,lngrevisionno
           ,strordercode
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
           ,m_order.strordercode
           ,m_order.lngcustomercompanycode
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
           ,(select dtmexpirationdate from expire_date where lngorderno = last_orderno and lngrevisionno = last_revision)
           ,m_order.lngmonetaryunitcode
           ,m_monetaryunit.strmonetaryunitsign
           ,m_monetaryunit.strmonetaryunitname
           ,m_order.lngmonetaryratecode
           ,m_monetaryrateclass.strmonetaryratename
           ,m_order.lngpayconditioncode
           ,m_paycondition.strpayconditionname
           ,m_order.lnggroupcode
           ,m_group.strgroupname
           ,m_order.lngusercode
           ,m_user.struserdisplayname
           ,m_order.lngdeliveryplacecode
           ,delivery.strcompanyname
           ,total_price
           ,m_order.dtminsertdate
           ,m_order.lnginputusercode
           ,m_user.struserdisplayname
           ,null
           ,0
        from m_order
        left outer join m_company customer
            on customer.lngcompanycode = m_order.lngcustomercompanycode
        left outer join m_monetaryunit
            on m_monetaryunit.lngmonetaryunitcode = m_order.lngmonetaryunitcode
        left outer join m_monetaryrateclass
            on m_monetaryrateclass.lngmonetaryratecode = m_order.lngmonetaryratecode
        left outer join m_paycondition
            on m_paycondition.lngpayconditioncode = m_order.lngpayconditioncode
        left outer join m_group
            on m_group.lnggroupcode = m_order.lnggroupcode
        left outer join m_user
            on m_user.lngusercode = m_order.lnginputusercode
        left outer join m_company delivery
            on delivery.lngcompanycode = m_order.lngdeliveryplacecode
        left outer join m_product
            on  m_product.lngrevisionno = 0
            and m_product.strrevisecode = '00'
         where m_order.lngorderno = last_orderno
             and m_order.lngrevisionno = last_revision
             and m_product.strproductcode = product_code
        ;
        -- �������}�X�^�폜�`�F�b�N
        IF EXISTS( 
            select * 
            from m_order 
            where strordercode = ( select strordercode from m_order where lngorderno = last_orderno and lngrevisionno = last_revision )
                and lngrevisionno = -1 ) THEN
            RAISE INFO 'data deleted % %', product_code, last_revision;
            insert into m_purchaseorder
            (
                lngpurchaseorderno
                ,lngrevisionno
                ,strordercode
            )
            values
            (
                last_orderno
               ,-1
               ,(select strordercode from m_order where lngorderno = lngorderno and lngrevisionno = last_revision)
            );
        END IF;
    END LOOP;
RAISE INFO 'complete po';
END $$
