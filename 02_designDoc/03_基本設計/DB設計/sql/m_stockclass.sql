drop table if exists public.m_stockclass;
create table public.m_stockclass(
    lngstockclasscode integer not null
   ,strstockclassname text
   ,primary key(lngstockclasscode)
);

comment on table public.m_stockclass is '仕入区分マスタ';
comment on column m_stockclass.lngstockclasscode is '仕入区分コード';
comment on column m_stockclass.strstockclassname is '仕入区分名称';

