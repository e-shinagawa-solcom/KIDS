drop table if exists t_reportbybenebktotal;
create table t_reportbybenebktotal(
    beneficiary character varying(255)
   ,bank1 numeric(14,4)
   ,bank2 numeric(14,4)
   ,bank3 numeric(14,4)
   ,bank4 numeric(14,4)
   ,total numeric(14,4)
);
comment on table t_reportbybenebktotal is '帳票BeneBk別合計';
comment on column t_reportbybenebktotal.beneficiary is 'Beneficiary';
comment on column t_reportbybenebktotal.bank1 is '銀行1';
comment on column t_reportbybenebktotal.bank2 is '銀行2';
comment on column t_reportbybenebktotal.bank3 is '銀行3';
comment on column t_reportbybenebktotal.bank4 is '銀行4';
comment on column t_reportbybenebktotal.total is '合計';
