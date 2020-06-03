drop table if exists public.m_imagerelation;
create table public.m_imagerelation(
    lngimagerelationcode integer not null
   ,lngimagecode integer not null
   ,lngfunctioncode integer
   ,strimagekeycode text
   ,primary key(lngimagerelationcode,lngimagecode)
);

comment on table public.m_imagerelation is 'イメージ関連マスタ';
comment on column m_imagerelation.lngimagerelationcode is 'イメージ関連コード';
comment on column m_imagerelation.lngimagecode is 'イメージコード';
comment on column m_imagerelation.lngfunctioncode is '機能コード';
comment on column m_imagerelation.strimagekeycode is 'イメージキーコード';

