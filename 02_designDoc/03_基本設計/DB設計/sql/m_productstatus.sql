drop table if exists public.m_productstatus;
create table public.m_productstatus(
    lngproductstatuscode integer not null
   ,strproductstatusname text
   ,primary key(lngproductstatuscode)
);

comment on table public.m_productstatus is '製品状態マスタ';
comment on column m_productstatus.lngproductstatuscode is '製品状態コード';
comment on column m_productstatus.strproductstatusname is '製品状態名称';

