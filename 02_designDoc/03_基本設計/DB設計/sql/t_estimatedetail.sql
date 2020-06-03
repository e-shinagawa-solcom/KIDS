drop table if exists public.t_estimatedetail;
create table public.t_estimatedetail(
    lngestimateno integer not null
   ,lngestimatedetailno integer not null
   ,lngrevisionno integer not null
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,dtmdelivery timestamp without time zone
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15,6)
   ,lngproductquantity integer
   ,curproductprice numeric(14,4)
   ,curproductrate numeric(15,6)
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngsortkey integer
   ,lngsalesdivisioncode integer
   ,lngsalesclasscode integer
   ,primary key(lngestimateno,lngestimatedetailno,lngrevisionno)
);

comment on table public.t_estimatedetail is '見積原価明細テーブル';
comment on column t_estimatedetail.lngestimateno is '見積原価番号';
comment on column t_estimatedetail.lngestimatedetailno is '見積原価明細番号';
comment on column t_estimatedetail.lngrevisionno is 'リビジョン番号';
comment on column t_estimatedetail.lngstocksubjectcode is '仕入科目コード';
comment on column t_estimatedetail.lngstockitemcode is '仕入部品コード';
comment on column t_estimatedetail.lngcustomercompanycode is '会社コード';
comment on column t_estimatedetail.dtmdelivery is '納期';
comment on column t_estimatedetail.bytpayofftargetflag is '償却対象フラグ';
comment on column t_estimatedetail.bytpercentinputflag is 'パーセント入力フラグ';
comment on column t_estimatedetail.lngmonetaryunitcode is '通貨単位コード';
comment on column t_estimatedetail.lngmonetaryratecode is '通貨レートコード';
comment on column t_estimatedetail.curconversionrate is '為替レート';
comment on column t_estimatedetail.lngproductquantity is '製品数量';
comment on column t_estimatedetail.curproductprice is '製品単価';
comment on column t_estimatedetail.curproductrate is 'パーセント入力値';
comment on column t_estimatedetail.cursubtotalprice is '計画原価';
comment on column t_estimatedetail.strnote is '備考';
comment on column t_estimatedetail.lngsortkey is '表示用ソートキー';
comment on column t_estimatedetail.lngsalesdivisioncode is '売上分類コード';
comment on column t_estimatedetail.lngsalesclasscode is '売上区分コード';

DROP INDEX IF EXISTS t_estimatedetail_pkey;
CREATE UNIQUE INDEX t_estimatedetail_pkey on t_estimatedetail USING btree(lngestimateno ,lngestimatedetailno ,lngrevisionno);
