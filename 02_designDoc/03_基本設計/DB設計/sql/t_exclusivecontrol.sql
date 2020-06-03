drop table if exists public.t_exclusivecontrol;
create table public.t_exclusivecontrol(
    strexclusivekey1 text not null
   ,strexclusivekey2 text not null default '0'
   ,strexclusivekey3 text not null default '0'
   ,lngfunctioncode integer
   ,lngusercode integer
   ,strsessionid text
   ,dtminsertdate timestamp without time zone default CURRENT_TIMESTAMP
   ,primary key(strexclusivekey1,strexclusivekey2,strexclusivekey3)
);

comment on table public.t_exclusivecontrol is '排他制御テーブル';
comment on column t_exclusivecontrol.strexclusivekey1 is '排他キー1';
comment on column t_exclusivecontrol.strexclusivekey2 is '排他キー2';
comment on column t_exclusivecontrol.strexclusivekey3 is '排他キー3';
comment on column t_exclusivecontrol.lngfunctioncode is '機能コード';
comment on column t_exclusivecontrol.lngusercode is 'ユーザコード';
comment on column t_exclusivecontrol.strsessionid is 'セッションID';
comment on column t_exclusivecontrol.dtminsertdate is '登録日';

DROP INDEX IF EXISTS t_exclusivecontrol_pkey;
CREATE UNIQUE INDEX t_exclusivecontrol_pkey on t_exclusivecontrol USING btree(strexclusivekey1,
strexclusivekey2,
strexclusivekey3);
