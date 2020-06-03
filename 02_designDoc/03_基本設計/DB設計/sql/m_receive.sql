drop table if exists public.m_receive;
create table public.m_receive(
    lngreceiveno integer not null
   ,lngrevisionno integer not null
   ,strreceivecode text
   ,strrevisecode character(2)
   ,dtmappropriationdate date
   ,lngcustomercompanycode integer
   ,lnggroupcode integer
   ,lngusercode integer
   ,lngreceivestatuscode integer
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15, 6)
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,strcustomerreceivecode text
   ,primary key(lngreceiveno,lngrevisionno)
);

comment on table public.m_receive is '受注マスタ';
comment on column m_receive.lngreceiveno is '受注番号';
comment on column m_receive.lngrevisionno is 'リビジョン番号';
comment on column m_receive.strreceivecode is '受注コード';
comment on column m_receive.strrevisecode is 'リバイズコード';
comment on column m_receive.dtmappropriationdate is '計上日';
comment on column m_receive.lngcustomercompanycode is '顧客コード';
comment on column m_receive.lnggroupcode is 'グープルコード';
comment on column m_receive.lngusercode is 'ユーザコード';
comment on column m_receive.lngreceivestatuscode is '受注状態コード';
comment on column m_receive.lngmonetaryunitcode is '通貨単位コード';
comment on column m_receive.lngmonetaryratecode is '通貨レートコード';
comment on column m_receive.curconversionrate is '換算レート';
comment on column m_receive.lnginputusercode is '入力者コード';
comment on column m_receive.bytinvalidflag is '無効フラグ';
comment on column m_receive.dtminsertdate is '登録日';
comment on column m_receive.strcustomerreceivecode is '顧客受注番号';

DROP INDEX IF EXISTS m_receive_pkey;
CREATE UNIQUE INDEX m_receive_pkey on m_receive USING btree(lngreceiveno ,lngrevisionno);
DROP INDEX IF EXISTS m_receive_strreceivecode_index;
CREATE INDEX m_receive_strreceivecode_index on m_receive USING btree(strreceivecode);
