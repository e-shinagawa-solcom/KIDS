drop table if exists public.m_salesclassdivisonlink;
create table public.m_salesclassdivisonlink(
    lngsalesclasscode integer not null
   ,lngsalesdivisioncode integer not null
   ,lngestimateareaclassno integer
   ,primary key(lngsalesclasscode,lngsalesdivisioncode)
);

comment on table public.m_salesclassdivisonlink is '売上分類区分紐づけマスタ';
comment on column m_salesclassdivisonlink.lngsalesclasscode is '売上区分コード';
comment on column m_salesclassdivisonlink.lngsalesdivisioncode is '売上分類コード';
comment on column m_salesclassdivisonlink.lngestimateareaclassno is '見積原価計算書エリア区分番号';

DROP INDEX IF EXISTS m_salesclassdivisonlink_pkey;
CREATE UNIQUE INDEX m_salesclassdivisonlink_pkey on m_salesclassdivisonlink USING btree(lngsalesclasscode,
lngsalesdivisioncode);
