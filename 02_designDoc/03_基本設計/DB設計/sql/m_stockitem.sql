drop table if exists public.m_stockitem;
create table public.m_stockitem(
    lngstockitemcode integer not null
   ,lngstocksubjectcode integer not null
   ,strstockitemname text
   ,bytdisplayflag boolean
   ,bytinvalidflag boolean
   ,bytdisplayestimateflag boolean
   ,lngestimateareaclassno integer
   ,primary key(lngstockitemcode,lngstocksubjectcode)
);

comment on table public.m_stockitem is '仕入部品マスタ';
comment on column m_stockitem.lngstockitemcode is '仕入部品コード';
comment on column m_stockitem.lngstocksubjectcode is '仕入科目コード';
comment on column m_stockitem.strstockitemname is '仕入部品名称';
comment on column m_stockitem.bytdisplayflag is '表示フラグ';
comment on column m_stockitem.bytinvalidflag is '無効フラグ';
comment on column m_stockitem.bytdisplayestimateflag is '見積原価表示フラグ';
comment on column m_stockitem.lngestimateareaclassno is '見積原価計算書エリア区分番号';

DROP INDEX IF EXISTS m_stockitem_pkey;
CREATE UNIQUE INDEX m_stockitem_pkey on m_stockitem USING btree(lngstockitemcode ,lngstocksubjectcode);
