drop table if exists public.t_goodsplan;
create table public.t_goodsplan(
    lnggoodsplancode integer not null
   ,lngrevisionno integer not null
   ,strrevisecode text not null default '00'
   ,lngproductno integer
   ,dtmcreationdate timestamp without time zone
   ,dtmrevisiondate timestamp without time zone
   ,lnggoodsplanprogresscode integer
   ,lnginputusercode integer
   ,primary key(lnggoodsplancode,lngrevisionno,strrevisecode)
);

comment on table public.t_goodsplan is '商品化企画テーブル';
comment on column t_goodsplan.lnggoodsplancode is '商品化企画コード';
comment on column t_goodsplan.lngrevisionno is 'リビジョン番号';
comment on column t_goodsplan.strrevisecode is '再販コード';
comment on column t_goodsplan.lngproductno is '製品番号';
comment on column t_goodsplan.dtmcreationdate is '作成日時';
comment on column t_goodsplan.dtmrevisiondate is 'リビジョン日時';
comment on column t_goodsplan.lnggoodsplanprogresscode is '企画進行状況コード';
comment on column t_goodsplan.lnginputusercode is '入力者';

DROP INDEX IF EXISTS t_goodsplan_pkey;
CREATE UNIQUE INDEX t_goodsplan_pkey on t_goodsplan USING btree(lnggoodsplancode);
DROP INDEX IF EXISTS t_goodsplan_lngproductno_idx;
CREATE INDEX t_goodsplan_lngproductno_idx on t_goodsplan USING btree(lngproductno);
DROP INDEX IF EXISTS pk2_t_goodsplan;
CREATE UNIQUE INDEX pk2_t_goodsplan on t_goodsplan USING btree(lngproductno ,lngrevisionno);
