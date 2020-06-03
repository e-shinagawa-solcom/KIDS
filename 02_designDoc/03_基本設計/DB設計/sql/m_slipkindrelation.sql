drop table if exists public.m_slipkindrelation;
create table public.m_slipkindrelation(
    lngslipkindrelationcode integer not null
   ,lngcompanycode integer not null
   ,lngslipkindcode integer
   ,primary key(lngslipkindrelationcode,lngcompanycode)
);

comment on table public.m_slipkindrelation is '納品伝票種別関連マスタ';
comment on column m_slipkindrelation.lngslipkindrelationcode is '納品伝票種別関連コード';
comment on column m_slipkindrelation.lngcompanycode is '会社コード';
comment on column m_slipkindrelation.lngslipkindcode is '納品伝票種別コード';

