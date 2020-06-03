drop table if exists public.m_company;
create table public.m_company(
    lngcompanycode integer not null
   ,lngcountrycode integer
   ,lngorganizationcode integer
   ,bytorganizationfront boolean
   ,strcompanyname text
   ,bytcompanydisplayflag boolean
   ,strcompanydisplaycode text
   ,strcompanydisplayname text
   ,strshortname text
   ,strpostalcode character(8)
   ,straddress1 text
   ,straddress2 text
   ,straddress3 text
   ,straddress4 text
   ,strtel1 text
   ,strtel2 text
   ,strfax1 text
   ,strfax2 text
   ,strdistinctcode text
   ,lngcloseddaycode integer
   ,primary key(lngcompanycode)
);

comment on table public.m_company is '会社マスタ';
comment on column m_company.lngcompanycode is '会社コード';
comment on column m_company.lngcountrycode is '国コード';
comment on column m_company.lngorganizationcode is '組織コード';
comment on column m_company.bytorganizationfront is '組織表記';
comment on column m_company.strcompanyname is '会社名称';
comment on column m_company.bytcompanydisplayflag is '会社表示許可フラグ';
comment on column m_company.strcompanydisplaycode is '表示用会社コード';
comment on column m_company.strcompanydisplayname is '表示用会社名称';
comment on column m_company.strshortname is '省略名称';
comment on column m_company.strpostalcode is '郵便番号';
comment on column m_company.straddress1 is '住所1 / 都道府県';
comment on column m_company.straddress2 is '住所2 / 市、区、郡';
comment on column m_company.straddress3 is '住所3 / 町、番地';
comment on column m_company.straddress4 is '住所4 / ビル等、建物名';
comment on column m_company.strtel1 is '電話番号1';
comment on column m_company.strtel2 is '電話番号2';
comment on column m_company.strfax1 is 'ファックス番号1';
comment on column m_company.strfax2 is 'ファックス番号2';
comment on column m_company.strdistinctcode is '識別コード';
comment on column m_company.lngcloseddaycode is '締め日コード';

DROP INDEX IF EXISTS m_company_pkey;
CREATE UNIQUE INDEX m_company_pkey on m_company USING btree(lngcompanycode);
