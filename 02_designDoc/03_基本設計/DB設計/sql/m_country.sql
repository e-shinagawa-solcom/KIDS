drop table if exists public.m_country;
create table public.m_country(
    lngcountrycode integer not null
   ,strcountryname text
   ,strcountryenglishname text
   ,primary key(lngcountrycode)
);

comment on table public.m_country is '国マスタ';
comment on column m_country.lngcountrycode is '国コード';
comment on column m_country.strcountryname is '国名称（日本語）';
comment on column m_country.strcountryenglishname is '国名称（英語）';

