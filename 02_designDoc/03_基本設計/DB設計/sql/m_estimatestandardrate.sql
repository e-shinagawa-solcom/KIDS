drop table if exists public.m_estimatestandardrate;
create table public.m_estimatestandardrate(
    lngstandardratecode integer
   ,curstandardrate numeric(16, 6)
   ,dtmapplystartdate date
   ,dtmapplyenddate date
);

comment on table public.m_estimatestandardrate is '見積標準割合マスタ';
comment on column m_estimatestandardrate.lngstandardratecode is '標準割合コード';
comment on column m_estimatestandardrate.curstandardrate is '標準割合';
comment on column m_estimatestandardrate.dtmapplystartdate is '適用開始日';
comment on column m_estimatestandardrate.dtmapplyenddate is '適用終了日';

