drop table if exists public.m_reportclass;
create table public.m_reportclass(
    lngreportclasscode integer
   ,strreportclassname text
);

comment on table public.m_reportclass is '帳票区分マスタ';
comment on column m_reportclass.lngreportclasscode is '帳票区分コード';
comment on column m_reportclass.strreportclassname is '帳票区分名称';

