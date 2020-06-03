drop table if exists public.m_stockstatus;
create table public.m_stockstatus(
    lngstockstatuscode integer not null
   ,strstockstatusname text
   ,primary key(lngstockstatuscode)
);

comment on table public.m_stockstatus is '仕入状態マスタ';
comment on column m_stockstatus.lngstockstatuscode is '仕入状態コード';
comment on column m_stockstatus.strstockstatusname is '仕入状態名称';

