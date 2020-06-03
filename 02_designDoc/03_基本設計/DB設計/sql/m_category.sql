drop table if exists public.m_category;
create table public.m_category(
    lngcategorycode integer not null
   ,strcategoryname text
   ,bytdisplayflag boolean
   ,lngsortkey integer
   ,primary key(lngcategorycode)
);

comment on table public.m_category is 'カテゴリーマスタ';
comment on column m_category.lngcategorycode is 'カテゴリーコード';
comment on column m_category.strcategoryname is 'カテゴリー名称';
comment on column m_category.bytdisplayflag is '表示フラグ';
comment on column m_category.lngsortkey is '表示用ソートキー';

