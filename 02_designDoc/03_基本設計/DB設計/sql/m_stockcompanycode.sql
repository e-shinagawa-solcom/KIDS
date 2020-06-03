drop table if exists public.m_stockcompanycode;
create table public.m_stockcompanycode(
    lngcompanyno integer not null
   ,strstockcompanycode text
   ,primary key(lngcompanyno)
);

comment on table public.m_stockcompanycode is '仕入先コードマスタ';
comment on column m_stockcompanycode.lngcompanyno is '会社番号';
comment on column m_stockcompanycode.strstockcompanycode is '仕入先コード';

DROP INDEX IF EXISTS m_stockcompanycode_pkey;
CREATE UNIQUE INDEX m_stockcompanycode_pkey on m_stockcompanycode USING btree(lngcompanyno);
