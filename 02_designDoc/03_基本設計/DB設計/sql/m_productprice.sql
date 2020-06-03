drop table if exists public.m_productprice;
create table public.m_productprice(
    lngproductpricecode integer not null
   ,lngproductno integer not null
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngsalesclasscode integer
   ,lngmonetaryunitcode integer
   ,curproductprice numeric(14, 4)
   ,primary key(lngproductpricecode,lngproductno)
);

comment on table public.m_productprice is '製品価格マスタ';
comment on column m_productprice.lngproductpricecode is '製品価格コード';
comment on column m_productprice.lngproductno is '製品番号';
comment on column m_productprice.lngstocksubjectcode is '仕入科目コード';
comment on column m_productprice.lngstockitemcode is '仕入部品コード';
comment on column m_productprice.lngsalesclasscode is '売上区分コード';
comment on column m_productprice.lngmonetaryunitcode is '通貨単位コード';
comment on column m_productprice.curproductprice is '製品価格';

