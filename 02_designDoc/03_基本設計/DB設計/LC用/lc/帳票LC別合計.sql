drop table if exists t_reportbylctotal;
drop index if exists t_reportbylctotal_pkey;
create table t_reportbylctotal(
    lcNo character varying(32)
   ,factoryName character varying(255)
   ,price numeric(14,4)
   ,shipterm date
   ,validterm date
   ,bankname character varying(255)
   ,bankreqdate date
   ,lcamopen date
);
comment on table t_reportbylctotal is '帳票LC別合計';
comment on column t_reportbylctotal.lcNo is 'LC番号';
comment on column t_reportbylctotal.factoryName is '工場名';
comment on column t_reportbylctotal.price is '金額';
comment on column t_reportbylctotal.shipterm is '船積期限';
comment on column t_reportbylctotal.validterm is '有効期限';
comment on column t_reportbylctotal.bankname is '発行銀行';
comment on column t_reportbylctotal.bankreqdate is '銀行依頼日';
comment on column t_reportbylctotal.lcamopen is 'LC日付';
