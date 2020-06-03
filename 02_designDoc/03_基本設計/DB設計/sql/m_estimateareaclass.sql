drop table if exists public.m_estimateareaclass;
create table public.m_estimateareaclass(
    lngestimateareaclassno integer not null
   ,strestimateareaclassname text
   ,primary key(lngestimateareaclassno)
);

comment on table public.m_estimateareaclass is '見積原価計算書エリア区分マスタ';
comment on column m_estimateareaclass.lngestimateareaclassno is '見積原価計算書エリア区分番号';
comment on column m_estimateareaclass.strestimateareaclassname is '見積原価計算書エリア区分コード名';

DROP INDEX IF EXISTS m_estimateareaclass_pkey;
CREATE UNIQUE INDEX m_estimateareaclass_pkey on m_estimateareaclass USING btree(lngestimateareaclassno);
