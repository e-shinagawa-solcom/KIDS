drop table if exists public.t_invoicedetail;
create table public.t_invoicedetail(
    lnginvoiceno integer not null
   ,lnginvoicedetailno integer not null
   ,lngrevisionno integer not null
   ,dtmdeliverydate date
   ,lngdeliveryplacecode integer
   ,strdeliveryplacename text
   ,cursubtotalprice numeric(14,4)
   ,lngtaxclasscode integer
   ,strtaxclassname text
   ,curtax numeric(14,4)
   ,strnote text
   ,strcustomerno varchar(10)
   ,lngslipno integer
   ,lngsliprevisionno integer
   ,primary key(lnginvoiceno,lnginvoicedetailno,lngrevisionno)
);

comment on table public.t_invoicedetail is '請求書明細テーブル';
comment on column t_invoicedetail.lnginvoiceno is '請求書番号';
comment on column t_invoicedetail.lnginvoicedetailno is '請求書明細番号';
comment on column t_invoicedetail.lngrevisionno is 'リビジョン番号';
comment on column t_invoicedetail.dtmdeliverydate is '納品日';
comment on column t_invoicedetail.lngdeliveryplacecode is '納品場所コード';
comment on column t_invoicedetail.strdeliveryplacename is '納品場所名';
comment on column t_invoicedetail.cursubtotalprice is '小計';
comment on column t_invoicedetail.lngtaxclasscode is '課税区分コード';
comment on column t_invoicedetail.strtaxclassname is '課税区分';
comment on column t_invoicedetail.curtax is '消費税率';
comment on column t_invoicedetail.strnote is '備考';
comment on column t_invoicedetail.strcustomerno is '顧客No';
comment on column t_invoicedetail.lngslipno is '納品書番号';
comment on column t_invoicedetail.lngsliprevisionno is '納品書リビジョン番号';

DROP INDEX IF EXISTS t_invoicedetail_pkey;
CREATE UNIQUE INDEX t_invoicedetail_pkey on t_invoicedetail USING btree(lnginvoiceno ,lnginvoicedetailno ,lngrevisionno);
