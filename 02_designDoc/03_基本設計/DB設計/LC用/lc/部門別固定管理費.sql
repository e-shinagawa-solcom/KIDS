drop table if exists t_sectionalFixedManagementCost;
create table t_sectionalFixedManagementCost(
    sectionCode character(2) not null
   ,sectionName character(64)
   ,configurationPrice money

   ,primary key(sectionCode)
);
comment on table t_sectionalFixedManagementCost is '部門別固定管理費';
comment on column t_sectionalFixedManagementCost.sectionCode is '部門コード';
comment on column t_sectionalFixedManagementCost.sectionName is '部門名称';
comment on column t_sectionalFixedManagementCost.configurationPrice is '設定金額';
