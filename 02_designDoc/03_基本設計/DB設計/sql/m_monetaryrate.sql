drop table if exists public.m_monetaryrate;
create table public.m_monetaryrate(
    lngmonetaryratecode integer not null
   ,lngmonetaryunitcode integer
   ,curconversionrate numeric(16, 6)
   ,dtmapplystartdate date
   ,dtmapplyenddate date
);

comment on table public.m_monetaryrate is '通貨レートマスタ';
comment on column m_monetaryrate.lngmonetaryratecode is '通貨レートコード';
comment on column m_monetaryrate.lngmonetaryunitcode is '通貨単位コード';
comment on column m_monetaryrate.curconversionrate is '換算レート';
comment on column m_monetaryrate.dtmapplystartdate is '適用開始月';
comment on column m_monetaryrate.dtmapplyenddate is '適用終了月';

