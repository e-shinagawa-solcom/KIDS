drop table if exists public.m_accessipaddress;
create table public.m_accessipaddress(
    lngaccessipaddresscode integer
   ,straccessipaddress text
   ,strnote text
   ,primary key(lngaccessipaddresscode)
);

comment on table public.m_accessipaddress is 'アクセスIPアドレスマスタ';
comment on column m_accessipaddress.lngaccessipaddresscode is 'アクセスIPアドレスコード';
comment on column m_accessipaddress.straccessipaddress is 'アクセスIPアドレス';
comment on column m_accessipaddress.strnote is '備考';

