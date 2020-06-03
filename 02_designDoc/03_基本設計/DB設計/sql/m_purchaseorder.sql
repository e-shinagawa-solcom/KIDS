drop table if exists public.m_purchaseorder;
create table public.m_purchaseorder(
    lngpurchaseorderno integer not null
   ,lngrevisionno integer not null
   ,strordercode text
   ,lngcustomercode integer
   ,strcustomername text
   ,strcustomercompanyaddreess text
   ,strcustomercompanytel text
   ,strcustomercompanyfax text
   ,strproductcode text
   ,strrevisecode text
   ,strproductname text
   ,strproductenglishname text
   ,dtmexpirationdate date
   ,lngmonetaryunitcode integer
   ,strmonetaryunitname text
   ,strmonetaryunitsign text
   ,lngmonetaryratecode integer
   ,strmonetaryratename text
   ,lngpayconditioncode integer
   ,strpayconditionname text
   ,lnggroupcode integer
   ,strgroupname text
   ,txtsignaturefilename text
   ,lngusercode integer
   ,strusername text
   ,lngdeliveryplacecode integer
   ,strdeliveryplacename text
   ,curtotalprice numeric(14,4)
   ,dtminsertdate timestamp without time zone
   ,lnginsertusercode integer
   ,strinsertusername text
   ,strnote text
   ,lngprintcount integer
   ,primary key(lngpurchaseorderno,lngrevisionno)
);

comment on table public.m_purchaseorder is '発注書マスタ';
comment on column m_purchaseorder.lngpurchaseorderno is '発注書番号';
comment on column m_purchaseorder.lngrevisionno is 'リビジョン番号';
comment on column m_purchaseorder.strordercode is '発注コード';
comment on column m_purchaseorder.lngcustomercode is '仕入先コード';
comment on column m_purchaseorder.strcustomername is '仕入先名';
comment on column m_purchaseorder.strcustomercompanyaddreess is '仕入先住所';
comment on column m_purchaseorder.strcustomercompanytel is '仕入先電話番号';
comment on column m_purchaseorder.strcustomercompanyfax is '仕入先FAX番号';
comment on column m_purchaseorder.strproductcode is '製品コード';
comment on column m_purchaseorder.strrevisecode is '再販コード';
comment on column m_purchaseorder.strproductname is '製品名';
comment on column m_purchaseorder.strproductenglishname is '製品名（英語）';
comment on column m_purchaseorder.dtmexpirationdate is '発注有効期限日';
comment on column m_purchaseorder.lngmonetaryunitcode is '通貨コード';
comment on column m_purchaseorder.strmonetaryunitname is '通貨単位名称';
comment on column m_purchaseorder.strmonetaryunitsign is '通貨単位';
comment on column m_purchaseorder.lngmonetaryratecode is '通貨レートコード';
comment on column m_purchaseorder.strmonetaryratename is '通貨レート名';
comment on column m_purchaseorder.lngpayconditioncode is '支払条件コード';
comment on column m_purchaseorder.strpayconditionname is '支払条件名';
comment on column m_purchaseorder.lnggroupcode is '営業部署コード';
comment on column m_purchaseorder.strgroupname is '営業部署名';
comment on column m_purchaseorder.txtsignaturefilename is '署名画像ファイル名';
comment on column m_purchaseorder.lngusercode is '開発担当者コード';
comment on column m_purchaseorder.strusername is '開発担当者名';
comment on column m_purchaseorder.lngdeliveryplacecode is '納品場所コード';
comment on column m_purchaseorder.strdeliveryplacename is '納品場所名';
comment on column m_purchaseorder.curtotalprice is '合計金額';
comment on column m_purchaseorder.dtminsertdate is '作成日';
comment on column m_purchaseorder.lnginsertusercode is '入力者コード';
comment on column m_purchaseorder.strinsertusername is '入力者名';
comment on column m_purchaseorder.strnote is '備考';
comment on column m_purchaseorder.lngprintcount is '印刷回数';

DROP INDEX IF EXISTS m_purchaseorder_pkey;
CREATE UNIQUE INDEX m_purchaseorder_pkey on m_purchaseorder USING btree(lngpurchaseorderno ,lngrevisionno);
DROP INDEX IF EXISTS m_purchaseorder_strordercode_index;
CREATE INDEX m_purchaseorder_strordercode_index on m_purchaseorder USING btree(strordercode);
