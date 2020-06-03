drop table if exists public.m_salesstatus;
create table public.m_salesstatus(
    lngsalesstatuscode integer not null
   ,strsalesstatusname text
   ,primary key(lngsalesstatuscode)
);

comment on table public.m_salesstatus is '売上状態マスタ';
comment on column m_salesstatus.lngsalesstatuscode is '売上状態コード';
comment on column m_salesstatus.strsalesstatusname is '売上状態名称';

