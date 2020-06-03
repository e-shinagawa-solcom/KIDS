drop table if exists public.m_taxclass;
create table public.m_taxclass(
    lngtaxclasscode integer not null
   ,strtaxclassname text
   ,primary key(lngtaxclasscode)
);

comment on table public.m_taxclass is '消費税区分マスタ';
comment on column m_taxclass.lngtaxclasscode is '消費税区分コード';
comment on column m_taxclass.strtaxclassname is '消費税区分名称';

