drop table if exists public.m_functionauthority;
create table public.m_functionauthority(
    lngfunctionauthoritycode integer not null
   ,lngfunctioncode integer
   ,lngfunctiongroupcode integer
   ,lngusercode integer
   ,bytauthorityflag boolean
   ,primary key(lngfunctionauthoritycode)
);

comment on table public.m_functionauthority is '機能権限マスタ';
comment on column m_functionauthority.lngfunctionauthoritycode is '機能権限コード';
comment on column m_functionauthority.lngfunctioncode is '機能コード';
comment on column m_functionauthority.lngfunctiongroupcode is '機能グループコード';
comment on column m_functionauthority.lngusercode is 'ユーザーコード';
comment on column m_functionauthority.bytauthorityflag is '権限有無フラグ';

