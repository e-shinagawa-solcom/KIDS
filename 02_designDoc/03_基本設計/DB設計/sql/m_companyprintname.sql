drop table if exists public.m_companyprintname;
create table public.m_companyprintname(
    lngcompanycode integer not null
   ,strprintcompanyname text
   ,primary key(lngcompanycode)
);

comment on table public.m_companyprintname is '印字用会社名マスタ';
comment on column m_companyprintname.lngcompanycode is '会社コード';
comment on column m_companyprintname.strprintcompanyname is '印字用会社名';

