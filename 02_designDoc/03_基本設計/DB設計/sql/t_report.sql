drop table if exists public.t_report;
create table public.t_report(
    lngreportcode integer not null
   ,lngreportclasscode integer
   ,strreportkeycode text
   ,strreportname text
   ,strreportpathname text
   ,primary key(lngreportcode)
);

comment on table public.t_report is '帳票テーブル';
comment on column t_report.lngreportcode is '帳票コード';
comment on column t_report.lngreportclasscode is '帳票区分コード';
comment on column t_report.strreportkeycode is '帳票キーコード';
comment on column t_report.strreportname is '帳票名称';
comment on column t_report.strreportpathname is '帳票パス名';

