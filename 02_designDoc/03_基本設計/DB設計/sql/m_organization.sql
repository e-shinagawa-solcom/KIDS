drop table if exists public.m_organization;
create table public.m_organization(
    lngorganizationcode integer not null
   ,strorganizationname text
   ,primary key(lngorganizationcode)
);

comment on table public.m_organization is '組織マスタ';
comment on column m_organization.lngorganizationcode is '組織コード';
comment on column m_organization.strorganizationname is '組織名称';

