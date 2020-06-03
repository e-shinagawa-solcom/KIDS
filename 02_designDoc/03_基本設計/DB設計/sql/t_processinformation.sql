drop table if exists public.t_processinformation;
create table public.t_processinformation(
    lngprocessinformationcode integer not null
   ,lngfunctioncode integer
   ,dtminsertdate timestamp without time zone
   ,lnginputusercode integer
   ,dtmstartdate date
   ,dtmenddate date
   ,primary key(lngprocessinformationcode)
);

comment on table public.t_processinformation is '処理情報テーブル';
comment on column t_processinformation.lngprocessinformationcode is '処理情報コード';
comment on column t_processinformation.lngfunctioncode is '機能コード';
comment on column t_processinformation.dtminsertdate is '登録日';
comment on column t_processinformation.lnginputusercode is '入力者';
comment on column t_processinformation.dtmstartdate is '申請日';
comment on column t_processinformation.dtmenddate is '完了日';

