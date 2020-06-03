drop table if exists public.m_tax;
create table public.m_tax(
    lngtaxcode integer not null
   ,curtax numeric(14, 4)
   ,lngpriority integer
   ,dtmapplystartdate date
   ,dtmapplyenddate date
   ,primary key(lngtaxcode)
);

comment on table public.m_tax is '消費税率マスタ';
comment on column m_tax.lngtaxcode is '消費税率コード';
comment on column m_tax.curtax is '消費税率';
comment on column m_tax.lngpriority is '優先順位';
comment on column m_tax.dtmapplystartdate is '適用開始日';
comment on column m_tax.dtmapplyenddate is '適用終了日';

DROP INDEX IF EXISTS m_tax_pkey;
CREATE UNIQUE INDEX m_tax_pkey on m_tax USING btree(lngtaxcode);
