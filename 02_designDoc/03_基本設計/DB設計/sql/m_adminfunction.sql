drop table if exists public.m_adminfunction;
create table public.m_adminfunction(
    lngadminfunctioncode integer not null
   ,strclass text
   ,strvalue text
   ,primary key(lngadminfunctioncode)
);

comment on table public.m_adminfunction is '管理者機能マスタ';
comment on column m_adminfunction.lngadminfunctioncode is '管理者機能コード';
comment on column m_adminfunction.strclass is '管理者機能分類';
comment on column m_adminfunction.strvalue is '管理者機能設定値';

