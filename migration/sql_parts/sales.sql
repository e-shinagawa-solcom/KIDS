DO $$
declare
    cur_header cursor for
    select T1.* from dblink('con111',
        'select ' ||
            'm_sales.lngsalesno ' ||
           ',m_sales.lngrevisionno ' ||
           ',m_sales.strsalescode ' ||
           ',m_sales.lngreceiveno ' ||
           ',m_sales.dtmappropriationdate ' ||
           ',m_sales.lngcustomercompanycode ' ||
           ',m_sales.lnggroupcode ' ||
           ',m_sales.lngusercode ' ||
           ',m_sales.lngsalesstatuscode ' ||
           ',m_sales.lngmonetaryunitcode ' ||
           ',m_sales.lngmonetaryratecode ' ||
           ',m_sales.curconversionrate ' ||
           ',m_sales.strslipcode ' ||
           ',m_sales.curtotalprice ' ||
           ',m_sales.strnote ' ||
           ',m_sales.lnginputusercode ' ||
           ',m_sales.bytinvalidflag ' ||
           ',m_sales.dtminsertdate ' ||
        'from m_sales ' ||
        'where strsalescode in  (' ||
            'select strsalescode from m_sales '
            'where lngsalesstatuscode >= 4 '
        ')'
        'order by ' ||
            'm_sales.strsalescode ' ||
           ',m_sales.lngsalesno '
    ) AS T1
    (
        lngsalesno integer
       ,lngrevisionno integer
       ,strsalescode text
       ,lngreceiveno integer
       ,dtmappropriationdate date
       ,lngcustomercompanycode integer
       ,lnggroupcode integer
       ,lngusercode integer
       ,lngsalesstatuscode integer
       ,lngmonetaryunitcode integer
       ,lngmonetaryratecode integer
       ,curconversionrate numeric(15, 6)
       ,strslipcode text
       ,curtotalprice numeric(14, 4)
       ,strnote text
       ,lnginputusercode integer
       ,bytinvalidflag boolean
       ,dtminsertdate timestamp(6) without time zone
    );
    cur_detail cursor(salesno integer, revisionno integer, receiveno integer) FOR
        select OLD2NEW.new_receive, OLD2NEW_SUB.new_receive as new_receive_sub, T1.* from dblink('con111',
            'select ' ||
            'lngsalesno ' ||
           ',lngsalesdetailno ' ||
           ',lngrevisionno ' ||
           ',strproductcode ' ||
           ',lngsalesclasscode ' ||
           ',dtmdeliverydate ' ||
           ',lngconversionclasscode ' ||
           ',curproductprice ' ||
           ',lngproductquantity ' ||
           ',lngproductunitcode ' ||
           ',lngtaxclasscode ' ||
           ',lngtaxcode ' ||
           ',curtaxprice ' ||
           ',cursubtotalprice ' ||
           ',strnote ' ||
           ',lngsortkey ' ||
           ',lngreceiveno ' ||
           ',lngreceivedetailno  ' ||
        'from t_salesdetail  ' ||
        'where lngsalesno =' || salesno || ' ' ||
            'and lngrevisionno = ' || revisionno
        ) AS T1(
            lngsalesno integer
           ,lngsalesdetailno integer
           ,lngrevisionno integer
           ,strproductcode text
           ,lngsalesclasscode integer
           ,dtmdeliverydate date
           ,lngconversionclasscode integer
           ,curproductprice numeric(14, 4)
           ,lngproductquantity integer
           ,lngproductunitcode integer
           ,lngtaxclasscode integer
           ,lngtaxcode integer
           ,curtaxprice numeric(14, 4)
           ,cursubtotalprice numeric(14, 4)
           ,strnote text
           ,lngsortkey integer
           ,lngreceiveno integer
           ,lngreceivedetailno integer
        )
        left outer join (
            select
                old_receive
               ,detailno
               ,new_receive
            from receive_conversion
            where old_receive = receiveno
        ) OLD2NEW
            on OLD2NEW.detailno = lngsalesdetailno
        left outer join (
            select
                old_receive
               ,detailno
               ,new_receive
            from receive_conversion
        ) OLD2NEW_SUB
            on OLD2NEW_SUB.detailno = lngsalesdetailno
            and OLD2NEW_SUB.old_receive = T1.lngreceiveno

        ;
    
    cur_slip_head cursor for
        select
            m_sales.lngrevisionno    -- ���r�W�����ԍ�
           ,m_sales.strslipcode    -- �[�i�`�[�R�[�h
           ,m_sales.lngsalesno    -- ����ԍ�
           ,m_sales.lngcustomercompanycode as lngcustomercode     -- �ڋq�R�[�h
           ,m_company.strcompanyname as strcustomername    -- �ڋq��
           ,null as strcustomerusername    -- �ڋq�S���Җ�
           ,m_sales.dtmappropriationdate as dtmdeliverydate    -- �[�i��
           ,null as strdeliveryplacename    -- �[�i�ꏊ��
           ,null as strdeliveryplaceusername    -- �[�i�ꏊ�S���Җ�
           ,t_salesdetail.lngtaxclasscode as lngtaxclasscode    -- �ېŋ敪�R�[�h
           ,m_taxclass.strtaxclassname as strtaxclassname    -- �ېŋ敪
           ,m_tax.curtax as curtax    -- ����ŗ�
           ,seller.lngusercode as lngusercode    -- �S���҃R�[�h
           ,seller.struserdisplayname as strusername    -- �S���Җ�
           ,m_sales.curtotalprice    -- ���v���z
           ,m_sales.lngmonetaryunitcode    -- �ʉݒP�ʃR�[�h
           ,m_monetaryunit.strmonetaryunitsign as strmonetaryunitsign    -- �ʉݒP��
           ,m_sales.dtminsertdate    -- �쐬��
           ,register.lngusercode as lnginsertusercode    -- ���͎҃R�[�h
           ,register.struserdisplayname as strinsertusername    -- ���͎Җ�
           ,m_sales.strnote    -- ���l
           ,0 as lngprintcount    -- �����
           ,m_sales.bytinvalidflag    -- �����t���O
        from m_sales
        left outer join m_company
            on m_company.lngcompanycode = m_sales.lngcustomercompanycode
        left outer join m_user seller
            on seller.lngusercode = m_sales.lngusercode
        left outer join m_user register
            on register.lngusercode = m_sales.lnginputusercode
        left outer join m_monetaryunit
            on m_monetaryunit.lngmonetaryunitcode = m_sales.lngmonetaryunitcode
        left outer join (
            select 
                lngsalesno
               ,lngrevisionno
               ,MIN(lngsalesdetailno) as lngsalesdetailno
            from t_salesdetail
            group by lngsalesno,lngrevisionno
        ) min_detail
            on min_detail.lngsalesno = m_sales.lngsalesno
            and min_detail.lngrevisionno = m_sales.lngrevisionno
        left outer join t_salesdetail
            on t_salesdetail.lngsalesno = min_detail.lngsalesno
            and t_salesdetail.lngsalesdetailno = min_detail.lngsalesdetailno
            and t_salesdetail.lngrevisionno = min_detail.lngrevisionno
        left outer join m_taxclass
            on m_taxclass.lngtaxclasscode = t_salesdetail.lngtaxclasscode
        left outer join m_tax
            on m_tax.dtmapplystartdate <= m_sales.dtmappropriationdate
            and m_tax.dtmapplyenddate >= m_sales.dtmappropriationdate
        order by m_sales.lngsalesno;
        
    header RECORD;
    detail RECORD;
    slip_header RECORD;
    slip_detail RECORD;
    slip_count integer;
    new_receiveno integer;
    sales_count integer;
    last_sales  text;
    last_slip text;
    last_no integer;
begin
    delete from m_sales;
    delete from t_salesdetail;
    delete from m_slip;
    delete from t_slipdetail;
    sales_count = 0;
    last_sales = '';

    open cur_header;
    LOOP
        FETCH cur_header INTO header;
        EXIT WHEN NOT FOUND;
        IF header.strsalescode is not null AND last_sales <> header.strsalescode THEN
            last_sales = header.strsalescode;
            sales_count = sales_count + 1;
        END IF;
        insert into m_sales
        (
            lngsalesno
           ,lngrevisionno
           ,strsalescode
           ,dtmappropriationdate
           ,lngcustomercompanycode
           ,lnggroupcode
           ,lngusercode
           ,lngsalesstatuscode
           ,lngmonetaryunitcode
           ,lngmonetaryratecode
           ,curconversionrate
           ,strslipcode
           ,lnginvoiceno
           ,curtotalprice
           ,strnote
           ,lnginputusercode
           ,bytinvalidflag
           ,dtminsertdate
        )
        values
        (
            sales_count
           ,header.lngrevisionno
           ,header.strsalescode
           ,header.dtmappropriationdate
           ,header.lngcustomercompanycode
           ,header.lnggroupcode
           ,header.lngusercode
           ,header.lngsalesstatuscode
           ,header.lngmonetaryunitcode
           ,header.lngmonetaryratecode
           ,header.curconversionrate
           ,header.strslipcode
           ,null
           ,header.curtotalprice
           ,header.strnote
           ,header.lnginputusercode
           ,header.bytinvalidflag
           ,header.dtminsertdate
        );
        open cur_detail(header.lngsalesno, header.lngrevisionno, header.lngreceiveno);
        LOOP
            FETCH cur_detail INTO detail;
            EXIT WHEN NOT FOUND;
            -- �󒍔ԍ��̑��i���ׂ��w�b�_���j
            IF header.lngreceiveno is null THEN
                IF detail.lngreceiveno is not null THEN
                    --RAISE INFO '% % use receive no on detail % -> %', detail.lngsalesno, detail.lngsalesdetailno, detail.lngreceiveno, detail.new_receive_sub;
                    new_receiveno = detail.new_receive_sub;
                    IF new_receiveno is null THEN
                        RAISE INFO '% % no receiveno matched from detail', detail.lngsalesno, detail.lngsalesdetailno;
                    END IF;
                ELSE
                    RAISE INFO '% % receive no not found', detail.lngsalesno, detail.lngsalesdetailno;
                    new_receiveno = null;
                END IF;
            ELSE
                --RAISE INFO '% % use receive no on header % -> %', detail.lngsalesno, detail.lngsalesdetailno, header.lngreceiveno, detail.new_receive;
                new_receiveno = detail.new_receive;
                IF new_receiveno is null THEN
                    RAISE INFO '% % no receiveno matched from header', detail.lngsalesno, detail.lngsalesdetailno;
                END IF;
            END IF;
            IF header.lngreceiveno is not null and  detail.lngreceiveno is not null AND header.lngreceiveno <> detail.lngreceiveno THEN
                -- �w�b�_�Ɩ��ׂŎw���󒍔ԍ����قȂ�����G���[
                RAISE INFO '% % different receive no  betweeen header(%) and detail(%)', detail.lngsalesno, detail.lngsalesdetailno, header.lngreceiveno, detail.lngreceiveno;
            ELSE
                -- ���㖾�׈ڍs
                insert into t_salesdetail
                (
                    lngsalesno
                   ,lngsalesdetailno
                   ,lngrevisionno
                   ,strproductcode
                   ,strrevisecode
                   ,lngsalesclasscode
--                   ,dtmdeliverydate
                   ,lngconversionclasscode
                   ,lngquantity
                   ,curproductprice
                   ,lngproductquantity
                   ,lngproductunitcode
                   ,lngtaxclasscode
                   ,lngtaxcode
                   ,curtaxprice
                   ,cursubtotalprice
                   ,strnote
                   ,lngsortkey
                   ,lngreceiveno
                   ,lngreceivedetailno
                   ,lngreceiverevisionno
                )
                values
                (
                    sales_count
                   ,detail.lngsalesdetailno
                   ,detail.lngrevisionno
                   ,detail.strproductcode
                   ,'00'   -- �Ĕ̃R�[�h
                   ,detail.lngsalesclasscode
--                   ,detail.dtmdeliverydate
                   ,detail.lngconversionclasscode
                   ,1      -- �����̋t�Z�s��
                   ,detail.curproductprice
                   ,detail.lngproductquantity
                   ,detail.lngproductunitcode
                   ,detail.lngtaxclasscode
                   ,detail.lngtaxcode
                   ,detail.curtaxprice
                   ,detail.cursubtotalprice
                   ,detail.strnote
                   ,detail.lngsortkey
                   ,new_receiveno    --�ڍs��̎󒍔ԍ�
                   ,detail.lngreceivedetailno
                   ,(select lngrevisionno from t_receivedetail where lngreceiveno = new_receiveno and lngreceivedetailno = detail.lngreceivedetailno and lngrevisionno = detail.lngrevisionno)
                );
            END IF;
        END LOOP;
        close cur_detail;
    END LOOP;
    close cur_header;
    last_no = -1;
    -- �[�i���f�[�^�쐬
    slip_count = 0;
    open cur_slip_head;
    LOOP
        FETCH cur_slip_head INTO slip_header;
        EXIT WHEN NOT FOUND;
        IF slip_header.lngrevisionno < 0 THEN
            RAISE INFO '% deleted', slip_header.lngsalesno;
        END IF;
        IF last_no <> slip_header.lngsalesno THEN
            slip_count = slip_count + 1;
            last_no = slip_header.lngsalesno;
        END IF;
        insert into m_slip
        (
            lngslipno
           ,lngrevisionno
           ,strslipcode
           ,lngsalesno
           ,lngcustomercode
           ,strcustomername
           ,strcustomerusername
           ,dtmdeliverydate
           ,strdeliveryplacename
           ,strdeliveryplaceusername
           ,lngtaxclasscode
           ,strtaxclassname
           ,curtax
           ,lngusercode
           ,strusername
           ,curtotalprice
           ,lngmonetaryunitcode
           ,strmonetaryunitsign
           ,dtminsertdate
           ,lnginsertusercode
           ,strinsertusername
           ,strnote
           ,lngprintcount
           ,bytinvalidflag
,strcustomercompanyname
,strcustomeraddress1
,strcustomeraddress2
,strcustomeraddress3
,strcustomeraddress4
,strcustomerphoneno
,strcustomerfaxno
,strshippercode
,lngpaymentmethodcode
,dtmpaymentlimit
        )
        select
            slip_count
           ,slip_header.lngrevisionno
           ,slip_header.strslipcode
           ,slip_header.lngsalesno
           ,slip_header.lngcustomercode
           ,slip_header.strcustomername
           ,slip_header.strcustomerusername
           ,slip_header.dtmdeliverydate
           ,slip_header.strdeliveryplacename
           ,slip_header.strdeliveryplaceusername
           ,slip_header.lngtaxclasscode
           ,slip_header.strtaxclassname
           ,slip_header.curtax
           ,slip_header.lngusercode
           ,slip_header.strusername
           ,slip_header.curtotalprice
           ,slip_header.lngmonetaryunitcode
           ,slip_header.strmonetaryunitsign
           ,slip_header.dtminsertdate
           ,slip_header.lnginsertusercode
           ,slip_header.strinsertusername
           ,slip_header.strnote
           ,slip_header.lngprintcount
           ,slip_header.bytinvalidflag
           ,m_companyprintname.strprintcompanyname
           ,m_company.straddress1
           ,m_company.straddress2
           ,m_company.straddress3
           ,m_company.straddress4
           ,m_company.strtel1
           ,m_company.strfax1
           ,m_stockcompanycode.strstockcompanycode
           ,1
           ,slip_header.dtmdeliverydate + interval '1 months'
        from (SELECT slip_header.lngcustomercode AS lngcustomercode ) A
        left outer join m_company
            on m_company.lngcompanycode = A.lngcustomercode
        left outer join m_companyprintname
            on m_companyprintname.lngcompanycode = A.lngcustomercode
        left outer join m_stockcompanycode
            on m_stockcompanycode.lngcompanyno = A.lngcustomercode;
        insert into t_slipdetail(
            lngslipno
           ,lngslipdetailno
           ,lngrevisionno
           ,strcustomersalescode
           ,lngsalesclasscode
           ,strsalesclassname
           ,strgoodscode
           ,strproductcode
           ,strrevisecode
           ,strproductname
           ,strproductenglishname
           ,curproductprice
           ,lngquantity
           ,lngproductquantity
           ,lngproductunitcode
           ,strproductunitname
           ,cursubtotalprice
           ,strnote
           ,lngreceiveno
           ,lngreceivedetailno
           ,lngreceiverevisionno
           ,lngsortkey
        )
        select
            slip_count    --  �[�i�`�[�ԍ�
           ,t_salesdetail.lngsalesdetailno    --  �[�i�`�[���הԍ�
           ,t_salesdetail.lngrevisionno    --  ���r�W�����ԍ�
           ,m_receive.strcustomerreceivecode    --  �ڋq�󒍔ԍ�
           ,t_salesdetail.lngsalesclasscode    --  ����敪�R�[�h
           ,m_salesclass.strsalesclassname    --  ����敪��
           ,m_product.strgoodscode    --  �ڋq�i��
           ,t_salesdetail.strproductcode    --  ���i�R�[�h
           ,t_salesdetail.strrevisecode    --  �Ĕ̃R�[�h
           ,m_product.strproductname    --  ���i��
           ,m_product.strproductenglishname    --  ���i���i�p��j
           ,t_salesdetail.curproductprice    --  �P��
           ,t_salesdetail.lngquantity    --  ����
           ,t_salesdetail.lngproductquantity    --  ����
           ,t_salesdetail.lngproductunitcode    --  �P�ʃR�[�h
           ,m_productunit.strproductunitname    --  �P��
           ,t_salesdetail.cursubtotalprice    --  ���v
           ,t_salesdetail.strnote    --  ���ה��l
           ,t_salesdetail.lngreceiveno    --  �󒍔ԍ�
           ,t_salesdetail.lngreceivedetailno    --  �󒍖��הԍ�
           ,t_receivedetail.lngrevisionno    --  �󒍃��r�W�����ԍ�
           ,t_salesdetail.lngsortkey    --  �\���p�\�[�g�L�[
        from t_salesdetail
        left outer join m_product
            on m_product.strproductcode = t_salesdetail.strproductcode
            and m_product.strrevisecode = t_salesdetail.strrevisecode
        left outer join m_salesclass
            on m_salesclass.lngsalesclasscode = t_salesdetail.lngsalesclasscode
        left outer join m_productunit
            on  m_productunit.lngproductunitcode = t_salesdetail.lngproductunitcode
        left outer join t_receivedetail
            on t_receivedetail.lngreceiveno = t_salesdetail.lngreceiveno
            and t_receivedetail.lngreceivedetailno = t_salesdetail.lngreceivedetailno
            and t_receivedetail.lngrevisionno = t_salesdetail.lngreceiverevisionno
        left outer join m_receive
            on m_receive.lngreceiveno = t_salesdetail.lngreceiveno
            and m_receive.lngrevisionno = t_salesdetail.lngrevisionno
        where t_salesdetail.lngsalesno = slip_header.lngsalesno
            and t_salesdetail.lngrevisionno = slip_header.lngrevisionno;
        --slip_count = slip_count + 1;
    END LOOP;
    close cur_slip_head;

END $$
