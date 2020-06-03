drop table if exists public.m_groupattributerelation;
create table public.m_groupattributerelation(
    lngattributerelationcode integer not null
   ,lnggroupcode integer not null
   ,lngattributecode integer
   ,primary key(lngattributerelationcode,lnggroupcode)
);

comment on table public.m_groupattributerelation is 'グループ属性関連マスタ';
comment on column m_groupattributerelation.lngattributerelationcode is 'グループ属性関連コード';
comment on column m_groupattributerelation.lnggroupcode is 'グループコード';
comment on column m_groupattributerelation.lngattributecode is 'グループ属性コード';

DROP INDEX IF EXISTS m_groupattributerelation_pkey;
CREATE UNIQUE INDEX m_groupattributerelation_pkey on m_groupattributerelation USING btree(lngattributerelationcode ,lnggroupcode);
