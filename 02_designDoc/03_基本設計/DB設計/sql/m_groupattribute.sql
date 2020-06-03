drop table if exists public.m_groupattribute;
create table public.m_groupattribute(
    lngattributecode integer not null
   ,strattributename text
   ,primary key(lngattributecode)
);

comment on table public.m_groupattribute is 'グループ属性マスタ';
comment on column m_groupattribute.lngattributecode is 'グループ属性コード';
comment on column m_groupattribute.strattributename is 'グループ属性名';

DROP INDEX IF EXISTS m_groupattribute_pkey;
CREATE UNIQUE INDEX m_groupattribute_pkey on m_groupattribute USING btree(lngattributecode);
