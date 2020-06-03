drop table if exists public.t_product;
create table public.t_product(
    lngproductsubno integer not null
   ,lngproductno integer not null
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,strnote text
   ,primary key(lngproductsubno,lngproductno)
);

comment on table public.t_product is '製品テーブル';
comment on column t_product.lngproductsubno is '製品サブ番号';
comment on column t_product.lngproductno is '製品番号';
comment on column t_product.lngstocksubjectcode is '仕入科目コード';
comment on column t_product.lngstockitemcode is '仕入部品コード';
comment on column t_product.strnote is '備考';

