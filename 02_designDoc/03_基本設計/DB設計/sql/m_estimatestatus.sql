drop table if exists public.m_estimatestatus;
create table public.m_estimatestatus(
    lngestimatestatuscode integer not null
   ,strestimatestatusname text
   ,primary key(lngestimatestatuscode)
);

comment on table public.m_estimatestatus is '見積原価状態マスタ';
comment on column m_estimatestatus.lngestimatestatuscode is '見積原価状態コード';
comment on column m_estimatestatus.strestimatestatusname is '見積原価状態名称';

