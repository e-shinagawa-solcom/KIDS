drop table if exists public.m_monetaryunit;
create table public.m_monetaryunit(
    lngmonetaryunitcode integer not null
   ,strmonetaryunitname text
   ,strmonetaryunitsign text
   ,primary key(lngmonetaryunitcode)
);

comment on table public.m_monetaryunit is '通貨単位マスタ';
comment on column m_monetaryunit.lngmonetaryunitcode is '通貨単位コード';
comment on column m_monetaryunit.strmonetaryunitname is '通貨単位名称';
comment on column m_monetaryunit.strmonetaryunitsign is '通貨単位記号';

