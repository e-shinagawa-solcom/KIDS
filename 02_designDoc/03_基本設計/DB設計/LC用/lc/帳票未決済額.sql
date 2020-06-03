drop table if exists t_reportunsettedprice;
create table t_reportunsettedprice(
    managementNo character varying(8) not null default 0
   ,bankname character varying(255)
   ,payeeFormalName character varying(255)
   ,shipstartdate date
   ,lcno character varying(32)
   ,productcode character varying(8)
   ,usancesettlement numeric(14,4)
   ,primary key(managementNo)
);
comment on table t_reportunsettedprice is '帳票未決済額';
comment on column t_reportunsettedprice.managementNo is '管理番号';
comment on column t_reportunsettedprice.bankname is '発行銀行名';
comment on column t_reportunsettedprice.payeeFormalName is '支払先正式名称';
comment on column t_reportunsettedprice.shipstartdate is '船積日';
comment on column t_reportunsettedprice.lcno is 'LC番号';
comment on column t_reportunsettedprice.productcode is '製品コード';
comment on column t_reportunsettedprice.usancesettlement is '未決済額';
