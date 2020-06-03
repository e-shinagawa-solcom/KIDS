drop table if exists public.t_moldhistory;
create table public.t_moldhistory(
    moldno text not null
   ,historyno integer not null default 0
   ,status character(2) default '00'
   ,actiondate date
   ,sourcefactory integer
   ,destinationfactory integer
   ,remark1 text default ''
   ,remark2 text default ''
   ,remark3 text default ''
   ,remark4 text default ''
   ,created timestamp without time zone not null
   ,createby integer not null default 99999
   ,updated timestamp without time zone not null
   ,updateby integer not null default 99999
   ,version integer not null default 0
   ,deleteflag boolean not null default false
   ,primary key(moldno,historyno)
);

comment on table public.t_moldhistory is '金型履歴テーブル';
comment on column t_moldhistory.moldno is '金型NO';
comment on column t_moldhistory.historyno is '履歴番号';
comment on column t_moldhistory.status is '金型ステータス';
comment on column t_moldhistory.actiondate is '実施日';
comment on column t_moldhistory.sourcefactory is '保管元工場';
comment on column t_moldhistory.destinationfactory is '移動先工場';
comment on column t_moldhistory.remark1 is '備考１';
comment on column t_moldhistory.remark2 is '備考２';
comment on column t_moldhistory.remark3 is '備考３';
comment on column t_moldhistory.remark4 is '備考４';
comment on column t_moldhistory.created is '作成日';
comment on column t_moldhistory.createby is '作成者';
comment on column t_moldhistory.updated is '更新日';
comment on column t_moldhistory.updateby is '更新者';
comment on column t_moldhistory.version is 'バージョン';
comment on column t_moldhistory.deleteflag is '削除フラグ';

