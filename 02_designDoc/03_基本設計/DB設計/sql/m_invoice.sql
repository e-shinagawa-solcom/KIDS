drop table if exists public.m_invoice;
create table public.m_invoice(
    lnginvoiceno integer not null
   ,lngrevisionno integer not null
   ,strinvoicecode text
   ,dtminvoicedate date
   ,lngcustomercode integer
   ,strcustomername text
   ,strcustomercompanyname text
   ,dtmchargeternstart date
   ,dtmchargeternend date
   ,curlastmonthbalance numeric(14, 4)
   ,curthismonthamount numeric(14, 4)
   ,lngmonetaryunitcode integer
   ,strmonetaryunitsign text
   ,lngtaxclasscode integer
   ,strtaxclassname text
   ,cursubtotal1 numeric(14, 4)
   ,curtax1 numeric(14, 4)
   ,curtaxprice1 numeric(14, 4)
   ,cursubtotal2 numeric(14, 4)
   ,curtax2 numeric(14, 4)
   ,curtaxprice2 numeric(14, 4)
   ,dtminsertdate timestamp(6) without time zone
   ,lngusercode integer
   ,strusername text
   ,lnginsertusercode integer
   ,strinsertusername text
   ,description text
   ,strnote text
   ,lngprintcount integer
   ,bytinvalidflag boolean
   ,primary key(lnginvoiceno,lngrevisionno)
);

comment on table public.m_invoice is '請求書マスタ';
comment on column m_invoice.lnginvoiceno is '請求書番号';
comment on column m_invoice.lngrevisionno is 'リビジョン番号';
comment on column m_invoice.strinvoicecode is '請求書コード';
comment on column m_invoice.dtminvoicedate is '請求日';
comment on column m_invoice.lngcustomercode is '顧客コード';
comment on column m_invoice.strcustomername is '顧客名';
comment on column m_invoice.strcustomercompanyname is '顧客社名';
comment on column m_invoice.dtmchargeternstart is '請求期間(FROM)';
comment on column m_invoice.dtmchargeternend is '請求期間(TO)';
comment on column m_invoice.curlastmonthbalance is '前月請求残額';
comment on column m_invoice.curthismonthamount is '御請求金額';
comment on column m_invoice.lngmonetaryunitcode is '通貨単位コード';
comment on column m_invoice.strmonetaryunitsign is '通貨単位';
comment on column m_invoice.lngtaxclasscode is '課税区分コード';
comment on column m_invoice.strtaxclassname is '課税区分名';
comment on column m_invoice.cursubtotal1 is '税抜金額1';
comment on column m_invoice.curtax1 is '消費税率1';
comment on column m_invoice.curtaxprice1 is '消費税額1';
comment on column m_invoice.cursubtotal2 is '税抜金額2';
comment on column m_invoice.curtax2 is '消費税率2';
comment on column m_invoice.curtaxprice2 is '消費税額2';
comment on column m_invoice.dtminsertdate is '作成日';
comment on column m_invoice.lngusercode is '担当者コード';
comment on column m_invoice.strusername is '担当者名';
comment on column m_invoice.lnginsertusercode is '作成者コード';
comment on column m_invoice.strinsertusername is '作成者名';
comment on column m_invoice.description is '但し書き';
comment on column m_invoice.strnote is '備考';
comment on column m_invoice.lngprintcount is '印刷回数';
comment on column m_invoice.bytinvalidflag is '無効フラグ';

DROP INDEX IF EXISTS m_invoice_pkey;
CREATE UNIQUE INDEX m_invoice_pkey on m_invoice USING btree(lnginvoiceno ,lngrevisionno);
