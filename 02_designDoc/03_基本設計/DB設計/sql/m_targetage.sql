drop table if exists public.m_targetage;
create table public.m_targetage(
    lngtargetagecode integer not null
   ,strtargetagename text
   ,primary key(lngtargetagecode)
);

comment on table public.m_targetage is '対象年齢マスタ';
comment on column m_targetage.lngtargetagecode is '対象年齢コード';
comment on column m_targetage.strtargetagename is '対象年齢名称';

