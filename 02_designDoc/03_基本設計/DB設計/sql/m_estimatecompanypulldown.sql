drop table if exists public.m_estimatecompanypulldown;
create table public.m_estimatecompanypulldown(
    lngestimateareaclassno integer not null
   ,lngsalesclassstocksubjectcode integer not null
   ,lngcompanycode integer not null
   ,bytinvalidflag boolean default False
   ,primary key(lngestimateareaclassno,lngsalesclassstocksubjectcode,lngcompanycode)
);

comment on table public.m_estimatecompanypulldown is '見積原価会社プルダウンマスタ';
comment on column m_estimatecompanypulldown.lngestimateareaclassno is '見積原価計算書エリア区分番号';
comment on column m_estimatecompanypulldown.lngsalesclassstocksubjectcode is '売上分類・仕入科目コード';
comment on column m_estimatecompanypulldown.lngcompanycode is '会社コード';
comment on column m_estimatecompanypulldown.bytinvalidflag is '無効フラグ';

