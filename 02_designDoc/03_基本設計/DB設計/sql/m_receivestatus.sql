drop table if exists public.m_receivestatus;
create table public.m_receivestatus(
    lngreceivestatuscode integer not null
   ,strreceivestatusname text
   ,primary key(lngreceivestatuscode)
);

comment on table public.m_receivestatus is '受注状態マスタ';
comment on column m_receivestatus.lngreceivestatuscode is '受注状態コード';
comment on column m_receivestatus.strreceivestatusname is '受注状態名称';

