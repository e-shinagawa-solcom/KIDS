drop table if exists t_reportbybenemonthcalculation;
create table t_reportbybenemonthcalculation(
    beneficiary character varying(255)
   ,date1 numeric(14,4)
   ,date2 numeric(14,4)
   ,date3 numeric(14,4)
   ,date4 numeric(14,4)
   ,date5 numeric(14,4)
   ,date6 numeric(14,4)
   ,date7 numeric(14,4)
   ,date8 numeric(14,4)
   ,date9 numeric(14,4)
   ,date10 numeric(14,4)
   ,date11 numeric(14,4)
   ,total numeric(14,4)
);
comment on table t_reportbybenemonthcalculation is '帳票Bene月別集計';
comment on column t_reportbybenemonthcalculation.beneficiary is 'Beneficiary';
comment on column t_reportbybenemonthcalculation.date1 is '年月1';
comment on column t_reportbybenemonthcalculation.date2 is '年月2';
comment on column t_reportbybenemonthcalculation.date3 is '年月3';
comment on column t_reportbybenemonthcalculation.date4 is '年月4';
comment on column t_reportbybenemonthcalculation.date5 is '年月5';
comment on column t_reportbybenemonthcalculation.date6 is '年月6';
comment on column t_reportbybenemonthcalculation.date7 is '年月7';
comment on column t_reportbybenemonthcalculation.date8 is '年月8';
comment on column t_reportbybenemonthcalculation.date9 is '年月9';
comment on column t_reportbybenemonthcalculation.date10 is '年月10';
comment on column t_reportbybenemonthcalculation.date11 is '年月11';
comment on column t_reportbybenemonthcalculation.total is '合計';
