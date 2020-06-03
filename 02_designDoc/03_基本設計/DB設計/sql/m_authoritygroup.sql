drop table if exists public.m_authoritygroup;
create table public.m_authoritygroup(
    lngauthoritygroupcode integer not null
   ,lngauthoritylevel integer
   ,strauthoritygroupname text
   ,primary key(lngauthoritygroupcode)
);

comment on table public.m_authoritygroup is '権限グループマスタ';
comment on column m_authoritygroup.lngauthoritygroupcode is '権限グループコード';
comment on column m_authoritygroup.lngauthoritylevel is '権限レベル';
comment on column m_authoritygroup.strauthoritygroupname is '権限グループ名称';

