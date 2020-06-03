drop table if exists public.m_attribute;
create table public.m_attribute(
    lngattributecode integer not null
   ,strattributename text
   ,primary key(lngattributecode)
);

comment on table public.m_attribute is '会社属性マスタ';
comment on column m_attribute.lngattributecode is '会社属性コード';
comment on column m_attribute.strattributename is '会社属性名';

