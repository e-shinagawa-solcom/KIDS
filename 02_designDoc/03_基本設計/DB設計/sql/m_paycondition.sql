drop table if exists public.m_paycondition;
create table public.m_paycondition(
    lngpayconditioncode integer not null
   ,strpayconditionname text
   ,primary key(lngpayconditioncode)
);

comment on table public.m_paycondition is '支払条件マスタ';
comment on column m_paycondition.lngpayconditioncode is '支払条件コード';
comment on column m_paycondition.strpayconditionname is '支払条件名称';

