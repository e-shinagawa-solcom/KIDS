drop table if exists public.m_productform;
create table public.m_productform(
    lngproductformcode integer not null
   ,strproductformname text
   ,primary key(lngproductformcode)
);

comment on table public.m_productform is '商品形態マスタ';
comment on column m_productform.lngproductformcode is '商品形態コード';
comment on column m_productform.strproductformname is '商品形態名称';

