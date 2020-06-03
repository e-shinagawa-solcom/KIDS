drop table if exists public.t_purchaseorderdetail;
create table public.t_purchaseorderdetail(
    lngpurchaseorderno integer not null
   ,lngpurchaseorderdetailno integer not null
   ,lngrevisionno integer not null
   ,lngorderno integer
   ,lngorderdetailno integer
   ,lngorderrevisionno integer
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,strstockitemname text
   ,lngdeliverymethodcode integer
   ,strdeliverymethodname text
   ,curproductprice numeric(14,4)
   ,lngproductquantity integer
   ,lngproductunitcode integer
   ,strproductunitname text
   ,cursubtotalprice numeric(14,4)
   ,dtmdeliverydate date
   ,strnote text
   ,lngsortkey integer
   ,primary key(lngpurchaseorderno,lngpurchaseorderdetailno,lngrevisionno)
);

comment on table public.t_purchaseorderdetail is '発注書詳細テーブル';
comment on column t_purchaseorderdetail.lngpurchaseorderno is '発注書番号';
comment on column t_purchaseorderdetail.lngpurchaseorderdetailno is '発注書明細番号';
comment on column t_purchaseorderdetail.lngrevisionno is 'リビジョン番号';
comment on column t_purchaseorderdetail.lngorderno is '発注番号';
comment on column t_purchaseorderdetail.lngorderdetailno is '発注明細番号';
comment on column t_purchaseorderdetail.lngorderrevisionno is '発注リビジョン番号';
comment on column t_purchaseorderdetail.lngstocksubjectcode is '仕入科目コード';
comment on column t_purchaseorderdetail.lngstockitemcode is '仕入部品コード';
comment on column t_purchaseorderdetail.strstockitemname is '仕入部品名';
comment on column t_purchaseorderdetail.lngdeliverymethodcode is '運搬方法コード';
comment on column t_purchaseorderdetail.strdeliverymethodname is '運搬方法名';
comment on column t_purchaseorderdetail.curproductprice is '単価';
comment on column t_purchaseorderdetail.lngproductquantity is '数量';
comment on column t_purchaseorderdetail.lngproductunitcode is '製品単位コード';
comment on column t_purchaseorderdetail.strproductunitname is '製品単位名';
comment on column t_purchaseorderdetail.cursubtotalprice is '小計';
comment on column t_purchaseorderdetail.dtmdeliverydate is '納品日';
comment on column t_purchaseorderdetail.strnote is '明細備考';
comment on column t_purchaseorderdetail.lngsortkey is '表示用ソートキー';

DROP INDEX IF EXISTS t_purchaseorderdetail_pkey;
CREATE UNIQUE INDEX t_purchaseorderdetail_pkey on t_purchaseorderdetail USING btree(lngpurchaseorderno ,lngpurchaseorderdetailno ,lngrevisionno);
