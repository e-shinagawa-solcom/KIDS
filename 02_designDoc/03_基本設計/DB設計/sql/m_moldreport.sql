drop table if exists public.m_moldreport;
create table public.m_moldreport(
    moldreportid text not null
   ,revision integer not null default 0
   ,reportcategory character(2)
   ,status character(2) default '00'
   ,requestdate date
   ,sendto integer
   ,attention integer
   ,carboncopy integer
   ,productcode text
   ,strrevisecode text default ''
   ,goodscode text
   ,requestcategory character(2)
   ,actionrequestdate date
   ,actiondate date
   ,transfermethod character(2)
   ,sourcefactory integer
   ,destinationfactory integer
   ,instructioncategory text
   ,customercode integer
   ,kuwagatagroupcode integer
   ,kuwagatausercode integer
   ,note text
   ,finalkeep character(2) default ''
   ,returnschedule date
   ,marginalnote text default ''
   ,printed boolean default false
   ,created timestamp without time zone not null
   ,createby integer not null default 99999
   ,updated timestamp without time zone not null
   ,updateby integer not null default 99999
   ,version integer not null default 0
   ,deleteflag boolean not null default false
   ,primary key(moldreportid,revision)
);

comment on table public.m_moldreport is '金型帳票マスタ';
comment on column m_moldreport.moldreportid is '金型帳票ID';
comment on column m_moldreport.revision is 'バージョン';
comment on column m_moldreport.reportcategory is '帳票区分';
comment on column m_moldreport.status is '帳票ステータス';
comment on column m_moldreport.requestdate is '依頼日';
comment on column m_moldreport.sendto is 'TO';
comment on column m_moldreport.attention is 'ATTN';
comment on column m_moldreport.carboncopy is 'CC';
comment on column m_moldreport.productcode is '製品コード';
comment on column m_moldreport.strrevisecode is '再販コード';
comment on column m_moldreport.goodscode is '顧客品番（商品コード）';
comment on column m_moldreport.requestcategory is '依頼区分';
comment on column m_moldreport.actionrequestdate is '希望日';
comment on column m_moldreport.actiondate is '実施日';
comment on column m_moldreport.transfermethod is '移動方法';
comment on column m_moldreport.sourcefactory is '保管工場';
comment on column m_moldreport.destinationfactory is '移動先工場';
comment on column m_moldreport.instructioncategory is '指示区分';
comment on column m_moldreport.customercode is '事業部';
comment on column m_moldreport.kuwagatagroupcode is 'KWG部署';
comment on column m_moldreport.kuwagatausercode is 'KWG部署担当者';
comment on column m_moldreport.note is 'その他';
comment on column m_moldreport.finalkeep is '生産後の処理';
comment on column m_moldreport.returnschedule is '返却予定日';
comment on column m_moldreport.marginalnote is '欄外備考';
comment on column m_moldreport.printed is '印刷済フラグ';
comment on column m_moldreport.created is '作成日';
comment on column m_moldreport.createby is '作成者';
comment on column m_moldreport.updated is '更新日';
comment on column m_moldreport.updateby is '更新者';
comment on column m_moldreport.version is 'バージョン';
comment on column m_moldreport.deleteflag is '削除フラグ';

DROP INDEX IF EXISTS m_moldreport_pkey;
CREATE UNIQUE INDEX m_moldreport_pkey on m_moldreport USING btree(moldreportid ,revision);
