drop table if exists m_payfinfo;
create table m_payfinfo(
    payfcd character varying(8) not null
   ,payfomitname text
   ,payfformalname text
   ,payfsendname text
   ,payfsendfax text
   ,invalidFlag boolean

   ,primary key(payfcd)
);
comment on table m_payfinfo is '支払先マスタ';
comment on column m_payfinfo.payfcd is '支払先コード';
comment on column m_payfinfo.payfomitname is '支払先省略名称';
comment on column m_payfinfo.payfformalname is '支払先正式名称';
comment on column m_payfinfo.payfsendname is '信用状送付先';
comment on column m_payfinfo.payfsendfax is '送付先ＦＡＸ';
comment on column m_payfinfo.invalidFlag is '無効フラグ';
