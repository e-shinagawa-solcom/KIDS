
drop table if exists public.m_moldmoverequestfactory;
create table public.m_moldmoverequestfactory( 
  lngfactorycode integer not null
  , strfactorydisplaycode text
  , strfactorydisplayname text
  , strfactoryshortname text
  , strforeignfactoryname text
  , strforeignfactoryaddress text
  , strinchargeattention text
  , strinchargecc text
  , strmoveactionuser text
  , stractionusertel text
  , btynondisplayflag boolean default false
  , bytinvalidflag boolean default false
  , primary key (lngfactorycode)
); 

comment on table public.m_moldmoverequestfactory is '金型移動依頼工場マスタ'; 
comment on column m_moldmoverequestfactory.lngfactorycode is '工場コード';
comment on column m_moldmoverequestfactory.strfactorydisplaycode is '工場表示コード'; 
comment on column m_moldmoverequestfactory.strfactorydisplayname is '工場表示名称'; 
comment on column m_moldmoverequestfactory.strfactoryshortname is '工場省略名称'; 
comment on column m_moldmoverequestfactory.strforeignfactoryname is '外国語工場名称'; 
comment on column m_moldmoverequestfactory.strforeignfactoryaddress is '外国語工場住所'; 
comment on column m_moldmoverequestfactory.strinchargeattention is '担当者(attn)'; 
comment on column m_moldmoverequestfactory.strinchargecc is 'cc者'; 
comment on column m_moldmoverequestfactory.strmoveactionuser is '移動実施者'; 
comment on column m_moldmoverequestfactory.stractionusertel is '実施者電話番号'; 
comment on column m_moldmoverequestfactory.btynondisplayflag is '非表示フラグ'; 
comment on column m_moldmoverequestfactory.bytinvalidflag is '無効フラグ'; 

INSERT 
INTO m_moldmoverequestfactory( 
  lngfactorycode
  , strfactorydisplaycode
  , strfactorydisplayname
  , strfactoryshortname
) 
SELECT
  mc.lngcompanycode
  , mc.strcompanydisplaycode
  , mc.strcompanydisplayname
  , mc.strshortname 
FROM
  m_attributerelation mar 
  INNER JOIN m_company mc 
    ON mar.lngcompanycode = mc.lngcompanycode 
WHERE
  mar.lngattributecode in (4) 
ORDER BY
  mc.lngcompanycode;
  
ALTER TABLE m_moldreport ALTER COLUMN finalkeep DROP DEFAULT;
ALTER TABLE m_moldreport ALTER COLUMN note set default '';
update m_moldreport
set finalkeep = null
where trim(finalkeep) = '';


alter table m_invoice add strinvoicemode character(1);
alter table m_invoice add lnginvoicemonth integer;

comment on column m_invoice.strinvoicemode is '請求モード';
comment on column m_invoice.lnginvoicemonth is '請求月'; 