drop table if exists public.t_stockdetail;
create table public.t_stockdetail(
    lngstockno integer not null
   ,lngstockdetailno integer not null
   ,lngrevisionno integer not null
   ,lngorderno integer
   ,lngorderdetailno integer
   ,lngorderrevisionno integer
   ,strproductcode text
   ,strrevisecode text
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngdeliverymethodcode integer
   ,lngconversionclasscode integer
   ,curproductprice numeric(14, 4)
   ,lngproductquantity integer
   ,lngproductunitcode integer
   ,lngtaxclasscode integer
   ,lngtaxcode integer
   ,curtaxprice numeric(14, 4)
   ,cursubtotalprice numeric(14, 4)
   ,strnote text
   ,strmoldno text
   ,lngsortkey integer
   ,primary key(lngstockno,lngstockdetailno,lngrevisionno)
);

comment on table public.t_stockdetail is '仕入明細テーブル';
comment on column t_stockdetail.lngstockno is '仕入番号';
comment on column t_stockdetail.lngstockdetailno is '仕入明細番号';
comment on column t_stockdetail.lngrevisionno is 'リビジョン番号';
comment on column t_stockdetail.lngorderno is '発注番号';
comment on column t_stockdetail.lngorderdetailno is '発注明細番号';
comment on column t_stockdetail.lngorderrevisionno is '発注リビジョン番号';
comment on column t_stockdetail.strproductcode is '製品コード';
comment on column t_stockdetail.strrevisecode is '再販コード';
comment on column t_stockdetail.lngstocksubjectcode is '仕入科目コード';
comment on column t_stockdetail.lngstockitemcode is '仕入部品コード';
comment on column t_stockdetail.lngdeliverymethodcode is '運搬方法コード';
comment on column t_stockdetail.lngconversionclasscode is '換算区分コード';
comment on column t_stockdetail.curproductprice is '製品価格';
comment on column t_stockdetail.lngproductquantity is '製品数量';
comment on column t_stockdetail.lngproductunitcode is '製品単位コード';
comment on column t_stockdetail.lngtaxclasscode is '消費税区分コード';
comment on column t_stockdetail.lngtaxcode is '消費税率コード';
comment on column t_stockdetail.curtaxprice is '税額';
comment on column t_stockdetail.cursubtotalprice is '小計金額';
comment on column t_stockdetail.strnote is '備考';
comment on column t_stockdetail.strmoldno is '金型番号';
comment on column t_stockdetail.lngsortkey is '表示用ソートキー';

DROP INDEX IF EXISTS t_stockdetail_pkey;
CREATE UNIQUE INDEX t_stockdetail_pkey on t_stockdetail USING btree(lngstockno ,lngstockdetailno ,lngrevisionno);
