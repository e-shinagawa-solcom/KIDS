drop table if exists public.m_deliverymethod;
create table public.m_deliverymethod(
    lngdeliverymethodcode integer not null
   ,strdeliverymethodname text
   ,primary key(lngdeliverymethodcode)
);

comment on table public.m_deliverymethod is '運搬方法マスタ';
comment on column m_deliverymethod.lngdeliverymethodcode is '運搬方法コード';
comment on column m_deliverymethod.strdeliverymethodname is '運搬方法名称';

