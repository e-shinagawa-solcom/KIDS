drop table if exists public.m_productunit;
create table public.m_productunit(
    lngproductunitcode integer not null
   ,strproductunitname text
   ,bytproductconversionflag boolean
   ,bytpackingconversionflag boolean
   ,primary key(lngproductunitcode)
);

comment on table public.m_productunit is '製品単位マスタ';
comment on column m_productunit.lngproductunitcode is '製品単位コード';
comment on column m_productunit.strproductunitname is '製品単位名称';
comment on column m_productunit.bytproductconversionflag is '製品換算フラグ';
comment on column m_productunit.bytpackingconversionflag is '荷姿換算フラグ';

