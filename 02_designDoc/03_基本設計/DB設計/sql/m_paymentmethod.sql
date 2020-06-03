drop table if exists public.m_paymentmethod;
create table public.m_paymentmethod(
    lngpaymentmethodcode integer not null
   ,strpaymentmethodname text
   ,primary key(lngpaymentmethodcode)
);

comment on table public.m_paymentmethod is '支払方法マスタ';
comment on column m_paymentmethod.lngpaymentmethodcode is '支払方法コード';
comment on column m_paymentmethod.strpaymentmethodname is '支払方法名称';

DROP INDEX IF EXISTS m_paycondition_pkey;
CREATE UNIQUE INDEX m_paycondition_pkey on m_paymentmethod USING btree(lngpayconditioncode);
