drop table if exists public.m_stock;
create table public.m_stock(
    lngstockno integer not null
   ,lngrevisionno integer not null
   ,strstockcode text
   ,dtmappropriationdate date
   ,lngcustomercompanycode integer
   ,lnggroupcode integer
   ,lngusercode integer
   ,lngstockstatuscode integer
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,lngpayconditioncode integer
   ,strslipcode text
   ,curtotalprice numeric(14, 4)
   ,lngdeliveryplacecode integer
   ,dtmexpirationdate date
   ,strnote text
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,primary key(lngstockno,lngrevisionno)
);

comment on table public.m_stock is '仕入マスタ';
comment on column m_stock.lngstockno is '仕入番号';
comment on column m_stock.lngrevisionno is 'リビジョン番号';
comment on column m_stock.strstockcode is '仕入コード';
comment on column m_stock.dtmappropriationdate is '計上日';
comment on column m_stock.lngcustomercompanycode is '仕入先コード';
comment on column m_stock.lnggroupcode is 'グループコード';
comment on column m_stock.lngusercode is 'ユーザコード';
comment on column m_stock.lngstockstatuscode is '仕入状態コード';
comment on column m_stock.lngmonetaryunitcode is '通貨単位コード';
comment on column m_stock.lngmonetaryratecode is '通貨レートコード';
comment on column m_stock.curconversionrate is '換算レート';
comment on column m_stock.lngpayconditioncode is '支払条件コード';
comment on column m_stock.strslipcode is '伝票コード';
comment on column m_stock.curtotalprice is '合計金額';
comment on column m_stock.lngdeliveryplacecode is '納品場所コード';
comment on column m_stock.dtmexpirationdate is '発注有効期限日';
comment on column m_stock.strnote is '備考';
comment on column m_stock.lnginputusercode is '入力者コード';
comment on column m_stock.bytinvalidflag is '無効フラグ';
comment on column m_stock.dtminsertdate is '登録日';

DROP INDEX IF EXISTS m_stock_pkey;
CREATE UNIQUE INDEX m_stock_pkey on m_stock USING btree(lngstockno ,lngrevisionno);
DROP INDEX IF EXISTS m_stokestrstockcode_index;
CREATE INDEX m_stokestrstockcode_index on m_stock USING btree(strstockcode);
