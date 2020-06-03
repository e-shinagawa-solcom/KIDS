drop table if exists public.m_product;
create table public.m_product(
    lngproductno integer not null
   ,strproductcode text
   ,strproductname text
   ,strproductenglishname text
   ,strgoodscode text
   ,strgoodsname text
   ,lnginchargegroupcode integer
   ,lnginchargeusercode integer
   ,lngdevelopusercode integer
   ,lnginputusercode integer
   ,lngcustomercompanycode integer
   ,lngcustomergroupcode integer
   ,lngcustomerusercode integer
   ,strcustomerusername text
   ,lngpackingunitcode integer
   ,lngproductunitcode integer
   ,lngboxquantity integer
   ,lngcartonquantity integer
   ,lngproductionquantity integer
   ,lngproductionunitcode integer
   ,lngfirstdeliveryquantity integer
   ,lngfirstdeliveryunitcode integer
   ,lngfactorycode integer
   ,lngassemblyfactorycode integer
   ,lngdeliveryplacecode integer
   ,dtmdeliverylimitdate date
   ,curproductprice numeric(14, 4)
   ,curretailprice numeric(14, 4)
   ,lngtargetagecode integer
   ,lngroyalty numeric(14, 4)
   ,lngcertificateclasscode integer
   ,lngcopyrightcode integer
   ,strcopyrightdisplaystamp text
   ,strcopyrightdisplayprint text
   ,lngproductformcode integer
   ,strproductcomposition text
   ,strassemblycontents text
   ,strspecificationdetails text
   ,strnote text
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,dtmupdatedate timestamp without time zone
   ,strcopyrightnote text
   ,lngcategorycode integer
   ,lngrevisionno integer not null default 0
   ,strrevisecode text not null default '00'
   ,lngprintcount integer not null default 0
   ,primary key(lngproductno,lngrevisionno,strrevisecode)
);

comment on table public.m_product is '製品マスタ';
comment on column m_product.lngproductno is '製品番号';
comment on column m_product.strproductcode is '製品コード';
comment on column m_product.strproductname is '製品名称';
comment on column m_product.strproductenglishname is '製品名称（英語）';
comment on column m_product.strgoodscode is '顧客商品コード';
comment on column m_product.strgoodsname is '顧客商品名';
comment on column m_product.lnginchargegroupcode is '部門コード';
comment on column m_product.lnginchargeusercode is '担当者コード';
comment on column m_product.lngdevelopusercode is '開発担当者コード';
comment on column m_product.lnginputusercode is '入力者コード';
comment on column m_product.lngcustomercompanycode is '顧客コード';
comment on column m_product.lngcustomergroupcode is '顧客部門コード';
comment on column m_product.lngcustomerusercode is '顧客担当者コード';
comment on column m_product.strcustomerusername is '顧客担当者';
comment on column m_product.lngpackingunitcode is '荷姿単位';
comment on column m_product.lngproductunitcode is '製品単位';
comment on column m_product.lngboxquantity is '内箱（袋）入数';
comment on column m_product.lngcartonquantity is 'カートン入数';
comment on column m_product.lngproductionquantity is '生産予定数';
comment on column m_product.lngproductionunitcode is '生産予定数の単位';
comment on column m_product.lngfirstdeliveryquantity is '初回納品数';
comment on column m_product.lngfirstdeliveryunitcode is '初回納品数の単位';
comment on column m_product.lngfactorycode is '生産工場';
comment on column m_product.lngassemblyfactorycode is 'アッセンブリ工場';
comment on column m_product.lngdeliveryplacecode is '納品場所';
comment on column m_product.dtmdeliverylimitdate is '納品期限日';
comment on column m_product.curproductprice is '納価(pcs)';
comment on column m_product.curretailprice is '上代(pcs)';
comment on column m_product.lngtargetagecode is '対象年齢';
comment on column m_product.lngroyalty is 'ロイヤルティ';
comment on column m_product.lngcertificateclasscode is '証紙';
comment on column m_product.lngcopyrightcode is '版権元';
comment on column m_product.strcopyrightdisplaystamp is '版権表示（刻印)';
comment on column m_product.strcopyrightdisplayprint is '版権表示(印刷物)';
comment on column m_product.lngproductformcode is '商品形態';
comment on column m_product.strproductcomposition is '製品構成';
comment on column m_product.strassemblycontents is 'アッセンブリ内容';
comment on column m_product.strspecificationdetails is '仕様詳細';
comment on column m_product.strnote is '備考';
comment on column m_product.bytinvalidflag is '無効フラグ';
comment on column m_product.dtminsertdate is '作成日時';
comment on column m_product.dtmupdatedate is '改訂日時';
comment on column m_product.strcopyrightnote is '版権元備考';
comment on column m_product.lngcategorycode is 'カテゴリーコード';
comment on column m_product.lngrevisionno is 'リビジョン番号';
comment on column m_product.strrevisecode is '再販コード';
comment on column m_product.lngprintcount is '印刷回数';

DROP INDEX IF EXISTS m_product_pkey;
CREATE UNIQUE INDEX m_product_pkey on m_product USING btree(lngproductno,strrevisecode,lngrevisionno);
DROP INDEX IF EXISTS m_product_strproductcode_index;
CREATE INDEX m_product_strproductcode_index on m_product USING btree(strproductcode);
