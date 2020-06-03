drop table if exists m_bank;
create table m_bank(
    bankcd character varying(8) not null
   ,bankomitname text
   ,bankformalname text
   ,bankdivrate numeric(4, 3) default 0
   ,invalidFlag boolean

   ,primary key(bankcd)
);
comment on table m_bank is '銀行マスタ';
comment on column m_bank.bankcd is '銀行コード';
comment on column m_bank.bankomitname is '銀行名省略名称';
comment on column m_bank.bankformalname is '銀行名正式名称';
comment on column m_bank.bankdivrate is '割振率';
comment on column m_bank.invalidFlag is '無効フラグ';
