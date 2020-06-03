drop table if exists public.m_certificateclass;
create table public.m_certificateclass(
    lngcertificateclasscode integer not null
   ,strcertificateclassname text
   ,primary key(lngcertificateclasscode)
);

comment on table public.m_certificateclass is '証紙種類マスタ';
comment on column m_certificateclass.lngcertificateclasscode is '証紙種類コード';
comment on column m_certificateclass.strcertificateclassname is '証紙種類名称';

