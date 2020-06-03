drop table if exists public.m_order;
create table public.m_order(
    lngorderno integer not null
   ,lngrevisionno integer not null
   ,strordercode text
   ,dtmappropriationdate date
   ,lngcustomercompanycode integer
   ,lnggroupcode integer
   ,lngusercode integer
   ,lngorderstatuscode integer
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,lngpayconditioncode integer
   ,lngdeliveryplacecode integer
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,primary key(lngorderno,lngrevisionno)
);

comment on table public.m_order is '発注マスタ';
comment on column m_order.lngorderno is '発注番号';
comment on column m_order.lngrevisionno is 'リビジョン番号';
comment on column m_order.strordercode is '発注コード';
comment on column m_order.dtmappropriationdate is '計上日';
comment on column m_order.lngcustomercompanycode is '会社コード（仕入先）';
comment on column m_order.lnggroupcode is 'グループコード（部門）';
comment on column m_order.lngusercode is 'ユーザコード（担当者）';
comment on column m_order.lngorderstatuscode is '㏄';
comment on column m_order.lngmonetaryunitcode is '通貨単位コード';
comment on column m_order.lngmonetaryratecode is '通貨レートコード';
comment on column m_order.curconversionrate is '換算レート';
comment on column m_order.lngpayconditioncode is '支払条件コード';
comment on column m_order.lngdeliveryplacecode is '納品場所コード';
comment on column m_order.lnginputusercode is '入力者コード';
comment on column m_order.bytinvalidflag is '無効フラグ';
comment on column m_order.dtminsertdate is '作成日時';

DROP INDEX IF EXISTS m_order_pkey;
CREATE UNIQUE INDEX m_order_pkey on m_order USING btree(lngorderno ,lngrevisionno);
DROP INDEX IF EXISTS m_order_lngorderstatuscode_index;
CREATE INDEX m_order_lngorderstatuscode_index on m_order USING btree(lngorderstatuscode);
DROP INDEX IF EXISTS m_order_strordercode_index;
CREATE INDEX m_order_strordercode_index on m_order USING btree(strordercode);
