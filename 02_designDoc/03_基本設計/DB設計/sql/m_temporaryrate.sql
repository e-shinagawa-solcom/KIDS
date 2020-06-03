drop table if exists public.m_temporaryrate;
create table public.m_temporaryrate(
    lngmonetaryunitcode integer
   ,curconversionrate numeric(16, 6)
   ,dtmapplystartdate date
   ,dtmapplyenddate date
);

comment on table public.m_temporaryrate is '想定レートマスタ';
comment on column m_temporaryrate.lngmonetaryunitcode is '通貨単位コード';
comment on column m_temporaryrate.curconversionrate is '換算レート';
comment on column m_temporaryrate.dtmapplystartdate is '適用開始月';
comment on column m_temporaryrate.dtmapplyenddate is '適用終了月';

