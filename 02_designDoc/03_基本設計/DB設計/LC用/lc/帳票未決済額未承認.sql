drop table if exists t_reportunsettedpriceunapproval;
create table t_reportunsettedpriceunapproval(
    payeeFormalName character varying(255) not null
   ,unsettledPrice numeric(14,4)

   ,primary key(payeeFormalName)
);
comment on table t_reportunsettedpriceunapproval is '帳票未決済額未承認';
comment on column t_reportunsettedpriceunapproval.payeeFormalName is '支払先正式名称';
comment on column t_reportunsettedpriceunapproval.unsettledPrice is '未決済額';
