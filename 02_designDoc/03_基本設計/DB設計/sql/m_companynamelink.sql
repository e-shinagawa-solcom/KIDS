drop table if exists public.m_companynamelink;
create table public.m_companynamelink(
    lngcompanycode integer not null
   ,lngcompanynamecode integer not null
   ,primary key(lngcompanycode)
);

comment on table public.m_companynamelink is '会社名紐づけマスタ';
comment on column m_companynamelink.lngcompanycode is '会社コード';
comment on column m_companynamelink.lngcompanynamecode is '会社名コード';

