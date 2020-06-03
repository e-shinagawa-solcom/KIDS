drop table if exists public.m_estimatedefault;
create table public.m_estimatedefault(
    lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,lngproductquantity integer
   ,curproductprice numeric(14, 4)
   ,curproductrate numeric(15, 6)
   ,cursubtotalprice numeric(14, 4)
   ,dtmapplystartdate date
   ,dtmapplyenddate date
);

comment on table public.m_estimatedefault is 'デフォルト見積原価マスタ';
comment on column m_estimatedefault.lngstocksubjectcode is '仕入科目コード';
comment on column m_estimatedefault.lngstockitemcode is '仕入部品コード';
comment on column m_estimatedefault.lngcustomercompanycode is '仕入先コード';
comment on column m_estimatedefault.bytpayofftargetflag is '償却対象フラグ';
comment on column m_estimatedefault.bytpercentinputflag is 'パーセント入力フラグ';
comment on column m_estimatedefault.lngmonetaryunitcode is '通貨単位コード';
comment on column m_estimatedefault.lngmonetaryratecode is '通貨レートコード';
comment on column m_estimatedefault.curconversionrate is '換算レート';
comment on column m_estimatedefault.lngproductquantity is '計画個数';
comment on column m_estimatedefault.curproductprice is '単価';
comment on column m_estimatedefault.curproductrate is '計画率';
comment on column m_estimatedefault.cursubtotalprice is '計画原価';
comment on column m_estimatedefault.dtmapplystartdate is '適用開始日';
comment on column m_estimatedefault.dtmapplyenddate is '適用終了日';

