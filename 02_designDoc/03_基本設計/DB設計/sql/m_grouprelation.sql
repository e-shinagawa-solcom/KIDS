drop table if exists public.m_grouprelation;
create table public.m_grouprelation(
    lnggrouprelationcode integer not null
   ,lngusercode integer
   ,lnggroupcode integer
   ,bytdefaultflag boolean
   ,primary key(lnggrouprelationcode)
);

comment on table public.m_grouprelation is 'グループ関連マスタ';
comment on column m_grouprelation.lnggrouprelationcode is 'グループ関連コード';
comment on column m_grouprelation.lngusercode is 'ユーザコード';
comment on column m_grouprelation.lnggroupcode is 'グループコード';
comment on column m_grouprelation.bytdefaultflag is 'デフォルト値フラグ';

