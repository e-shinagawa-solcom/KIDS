drop table if exists public.m_monetaryrateclass;
create table public.m_monetaryrateclass(
    lngmonetaryratecode integer not null
   ,strmonetaryratename text
   ,primary key(lngmonetaryratecode)
);

comment on table public.m_monetaryrateclass is '通貨レート区分マスタ';
comment on column m_monetaryrateclass.lngmonetaryratecode is '通貨レートコード';
comment on column m_monetaryrateclass.strmonetaryratename is '通貨レート名称';

