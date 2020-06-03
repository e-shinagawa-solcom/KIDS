drop table if exists public.m_conversionclass;
create table public.m_conversionclass(
    lngconversionclasscode integer not null
   ,strconversionclassname text
   ,primary key(lngconversionclasscode)
);

comment on table public.m_conversionclass is '換算区分マスタ';
comment on column m_conversionclass.lngconversionclasscode is '換算区分コード';
comment on column m_conversionclass.strconversionclassname is '換算区分名称';

