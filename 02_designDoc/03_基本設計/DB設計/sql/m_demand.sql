drop table if exists public.m_demand;
create table public.m_demand(
    lngdemandcode integer not null
   ,strdemandname text
   ,primary key(lngdemandcode)
);

comment on table public.m_demand is '請求区分マスタ';
comment on column m_demand.lngdemandcode is '請求区分コード';
comment on column m_demand.strdemandname is '請求区分名';

