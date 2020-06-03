drop table if exists public.m_attributerelation;
create table public.m_attributerelation(
    lngattributerelationcode integer not null
   ,lngcompanycode integer not null
   ,lngattributecode integer
   ,primary key(lngattributerelationcode,lngcompanycode)
);

comment on table public.m_attributerelation is '会社属性関連マスタ';
comment on column m_attributerelation.lngattributerelationcode is '会社属性関連コード';
comment on column m_attributerelation.lngcompanycode is '会社コード';
comment on column m_attributerelation.lngattributecode is '会社属性コード';

