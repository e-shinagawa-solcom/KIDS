drop table if exists public.m_signature;
create table public.m_signature(
    lnggroupcode integer not null
   ,txtsignaturefilename text
   ,dtmapplystartdate date
   ,dtmapplyenddate date
   ,primary key(lnggroupcode)
);

comment on table public.m_signature is '署名マスタ';
comment on column m_signature.lnggroupcode is 'グループコード';
comment on column m_signature.txtsignaturefilename is '署名画像ファイル名';
comment on column m_signature.dtmapplystartdate is '適用開始日';
comment on column m_signature.dtmapplyenddate is '適用終了日';

