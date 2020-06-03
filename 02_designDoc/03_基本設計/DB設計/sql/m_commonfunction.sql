drop table if exists public.m_commonfunction;
create table public.m_commonfunction(
    lngcommonfunctioncode integer not null
   ,strclass text
   ,strvalue text
   ,primary key(lngcommonfunctioncode)
);

comment on table public.m_commonfunction is '共通機能マスタ';
comment on column m_commonfunction.lngcommonfunctioncode is '共通機能コード';
comment on column m_commonfunction.strclass is '分類';
comment on column m_commonfunction.strvalue is '設定値';

