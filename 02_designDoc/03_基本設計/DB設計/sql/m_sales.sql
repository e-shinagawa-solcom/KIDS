drop table if exists public.m_sales;
create table public.m_sales(
    lngsalesno integer not null
   ,lngrevisionno integer not null
   ,strsalescode text
   ,dtmappropriationdate date
   ,lngcustomercompanycode integer
   ,lnggroupcode integer
   ,lngusercode integer
   ,lngsalesstatuscode integer
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,strslipcode text
   ,lnginvoiceno integer
   ,curtotalprice numeric(14, 4)
   ,strnote text
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,primary key(lngsalesno,lngrevisionno)
);

comment on table public.m_sales is '売上マスタ';
comment on column m_sales.lngsalesno is '売上番号';
comment on column m_sales.lngrevisionno is 'リビジョン番号';
comment on column m_sales.strsalescode is '売上コード';
comment on column m_sales.dtmappropriationdate is '計上日';
comment on column m_sales.lngcustomercompanycode is '顧客コード';
comment on column m_sales.lnggroupcode is 'グループコード';
comment on column m_sales.lngusercode is 'ユーザコード';
comment on column m_sales.lngsalesstatuscode is '売上状態コード';
comment on column m_sales.lngmonetaryunitcode is '通貨単位コード';
comment on column m_sales.lngmonetaryratecode is '通貨レートコード';
comment on column m_sales.curconversionrate is '換算レート';
comment on column m_sales.strslipcode is '納品書NO';
comment on column m_sales.lnginvoiceno is '請求書番号';
comment on column m_sales.curtotalprice is '合計金額';
comment on column m_sales.strnote is '備考';
comment on column m_sales.lnginputusercode is '入力者コード';
comment on column m_sales.bytinvalidflag is '無効フラグ';
comment on column m_sales.dtminsertdate is '登録日';

DROP INDEX IF EXISTS m_sales_pkey;
CREATE UNIQUE INDEX m_sales_pkey on m_sales USING btree(lngsalesno ,lngrevisionno);
DROP INDEX IF EXISTS m_sales_strsalescode_index;
CREATE INDEX m_sales_strsalescode_index on m_sales USING btree(strsalescode);
