drop table if exists public.m_slipkind;
create table public.m_slipkind(
    lngslipkindcode integer not null
   ,strslipkindname text
   ,lngmaxline integer
   ,primary key(lngslipkindcode)
);

comment on table public.m_slipkind is '納品伝票種別マスタ';
comment on column m_slipkind.lngslipkindcode is '納品伝票種別コード';
comment on column m_slipkind.strslipkindname is '納品伝票種別名';
comment on column m_slipkind.lngmaxline is '行数';

