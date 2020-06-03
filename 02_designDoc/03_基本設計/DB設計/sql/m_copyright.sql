drop table if exists public.m_copyright;
create table public.m_copyright(
    lngcopyrightcode integer not null
   ,strcopyrightname text
   ,primary key(lngcopyrightcode)
);

comment on table public.m_copyright is '版権元マスタ';
comment on column m_copyright.lngcopyrightcode is '版権元コード';
comment on column m_copyright.strcopyrightname is '版権元名称';

