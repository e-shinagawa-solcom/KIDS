drop table if exists public.m_function;
create table public.m_function(
    lngfunctioncode integer not null
   ,strfunctionname text
   ,strfunctionoutline text
   ,primary key(lngfunctioncode)
);

comment on table public.m_function is '機能マスタ';
comment on column m_function.lngfunctioncode is '機能コード';
comment on column m_function.strfunctionname is '機能名称';
comment on column m_function.strfunctionoutline is '機能概要';

