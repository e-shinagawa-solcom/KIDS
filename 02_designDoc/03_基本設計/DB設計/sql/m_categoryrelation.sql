drop table if exists public.m_categoryrelation;
create table public.m_categoryrelation(
    lngcategoryrelationcode integer not null
   ,lngcategorycode integer not null
   ,lnggroupcode integer
   ,primary key(lngcategoryrelationcode,lngcategorycode)
);

comment on table public.m_categoryrelation is 'カテゴリー関連マスタ';
comment on column m_categoryrelation.lngcategoryrelationcode is 'カテゴリー関連コード';
comment on column m_categoryrelation.lngcategorycode is 'カテゴリーコード';
comment on column m_categoryrelation.lnggroupcode is 'グループコード';

