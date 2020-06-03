drop table if exists public.m_orderstatus;
create table public.m_orderstatus(
    lngorderstatuscode integer not null
   ,strorderstatusname text
   ,primary key(lngorderstatuscode)
);

comment on table public.m_orderstatus is '発注状態マスタ';
comment on column m_orderstatus.lngorderstatuscode is '発注状態コード';
comment on column m_orderstatus.strorderstatusname is '発注状態名称';

