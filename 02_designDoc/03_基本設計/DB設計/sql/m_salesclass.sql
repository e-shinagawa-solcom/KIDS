drop table if exists public.m_salesclass;
create table public.m_salesclass(
    lngsalesclasscode integer not null
   ,strsalesclassname text
   ,bytprintslipnoteflg boolean
   ,bytdetailunifiedflg boolean
   ,primary key(lngsalesclasscode)
);

comment on table public.m_salesclass is '売上区分マスタ';
comment on column m_salesclass.lngsalesclasscode is '売上区分コード';
comment on column m_salesclass.strsalesclassname is '売上区分名称';
comment on column m_salesclass.bytprintslipnoteflg is '納品書備考出力フラグ';
comment on column m_salesclass.bytdetailunifiedflg is '明細統一フラグ';

DROP INDEX IF EXISTS m_salesclass_pkey;
CREATE UNIQUE INDEX m_salesclass_pkey on m_salesclass USING btree(lngsalesclasscode);
