drop table if exists public.t_exportdata;
create table public.t_exportdata(
    lngexportdatacode integer not null
   ,strexportdatakeycode text
   ,strexportdataname text
   ,strexportdatapathname text
   ,primary key(lngexportdatacode)
);

comment on table public.t_exportdata is 'データエクスポート';
comment on column t_exportdata.lngexportdatacode is 'データエクスポートコード';
comment on column t_exportdata.strexportdatakeycode is 'データエクスポートキーコード';
comment on column t_exportdata.strexportdataname is 'データエクスポート名称';
comment on column t_exportdata.strexportdatapathname is 'データエクスポートパス名';

