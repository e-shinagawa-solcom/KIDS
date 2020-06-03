drop table if exists public.m_estimate;
create table public.m_estimate(
    lngestimateno integer not null
   ,lngrevisionno integer not null
   ,strproductcode text
   ,strrevisecode text
   ,bytdecisionflag boolean
   ,lngestimatestatuscode integer
   ,curfixedcost numeric(14,4)
   ,curmembercost numeric(14,4)
   ,curtotalprice numeric(14,4)
   ,curmanufacturingcost numeric(14,4)
   ,cursalesamount numeric(14,4)
   ,curprofit numeric(14,4)
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone

   ,lngproductionquantity integer
   ,lngtempno integer
   ,strnote text
   ,lngproductrevisionno integer
   ,lngprintcount integer not null default 0
   ,primary key(lngestimateno,lngrevisionno)
);

comment on table public.m_estimate is '見積原価マスタ';
comment on column m_estimate.lngestimateno is '見積原価番号';
comment on column m_estimate.lngrevisionno is 'リビジョン番号';
comment on column m_estimate.strproductcode is '製品コード';
comment on column m_estimate.strrevisecode is '再販コード';
comment on column m_estimate.bytdecisionflag is '決定フラグ';
comment on column m_estimate.lngestimatestatuscode is '見積原価ステータス';
comment on column m_estimate.curfixedcost is '償却費合計';
comment on column m_estimate.curmembercost is '部材費';
comment on column m_estimate.curtotalprice is '売上総利益';
comment on column m_estimate.curmanufacturingcost is '製造費';
comment on column m_estimate.cursalesamount is '製品売上高';
comment on column m_estimate.curprofit is '営業利益';
comment on column m_estimate.lnginputusercode is '入力者';
comment on column m_estimate.bytinvalidflag is '無効フラグ';
comment on column m_estimate.dtminsertdate is '作成日時';
comment on column m_estimate.lngproductionquantity is '製品数量';
comment on column m_estimate.lngtempno is 'テンポラリNo';
comment on column m_estimate.strnote is '備考';
comment on column m_estimate.lngproductrevisionno is '製品リビジョン番号';
comment on column m_estimate.lngprintcount is '印刷回数';

DROP INDEX IF EXISTS m_estimate_pkey;
CREATE UNIQUE INDEX m_estimate_pkey on m_estimate USING btree(lngestimateno,strrevisecode,lngrevisionno);
DROP INDEX IF EXISTS m_estimate_strproductcode_index;
CREATE INDEX m_estimate_strproductcode_index on m_estimate USING btree(strproductcode);
