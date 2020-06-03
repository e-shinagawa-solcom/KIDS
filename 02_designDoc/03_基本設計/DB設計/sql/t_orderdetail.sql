drop table if exists public.t_orderdetail;
create table public.t_orderdetail(
    lngorderno integer not null
   ,lngorderdetailno integer not null
   ,lngrevisionno integer not null
   ,strproductcode text
   ,strrevisecode text
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,dtmdeliverydate date
   ,lngdeliverymethodcode integer
   ,lngconversionclasscode integer
   ,curproductprice numeric(14, 4)
   ,lngproductquantity integer
   ,lngproductunitcode integer
   ,cursubtotalprice numeric(14, 4)
   ,strnote text
   ,strmoldno text
   ,lngsortkey integer
   ,lngestimateno integer
   ,lngestimatedetailno integer
   ,lngestimaterevisionno integer
   ,primary key(lngorderno,lngorderdetailno,lngrevisionno)
);

comment on table public.t_orderdetail is '発注明細テーブル';
comment on column t_orderdetail.lngorderno is '発注番号';
comment on column t_orderdetail.lngorderdetailno is '発注明細番号';
comment on column t_orderdetail.lngrevisionno is 'リビジョン番号';
comment on column t_orderdetail.strproductcode is '製品コード';
comment on column t_orderdetail.strrevisecode is '再販コード';
comment on column t_orderdetail.lngstocksubjectcode is '仕入科目コード';
comment on column t_orderdetail.lngstockitemcode is '仕入部品コード';
comment on column t_orderdetail.dtmdeliverydate is '納品日';
comment on column t_orderdetail.lngdeliverymethodcode is '運搬方法コード';
comment on column t_orderdetail.lngconversionclasscode is '換算区分コード';
comment on column t_orderdetail.curproductprice is '製品価格';
comment on column t_orderdetail.lngproductquantity is '製品数量';
comment on column t_orderdetail.lngproductunitcode is '製品単位コード';
comment on column t_orderdetail.cursubtotalprice is '小計金額';
comment on column t_orderdetail.strnote is '備考';
comment on column t_orderdetail.strmoldno is '金型番号';
comment on column t_orderdetail.lngsortkey is '表示用ソートキー';
comment on column t_orderdetail.lngestimateno is '見積原価番号';
comment on column t_orderdetail.lngestimatedetailno is '見積原価明細番号';
comment on column t_orderdetail.lngestimaterevisionno is '見積原価リビジョン番号';

DROP INDEX IF EXISTS t_orderdetail_pkey;
CREATE UNIQUE INDEX t_orderdetail_pkey on t_orderdetail USING btree(lngorderno ,lngorderdetailno ,lngrevisionno);
