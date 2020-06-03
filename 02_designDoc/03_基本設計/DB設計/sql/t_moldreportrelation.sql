drop table if exists public.t_moldreportrelation;
create table "public".t_moldreportrelation (
  moldreportrelationid integer default nextval('seq_moldreportrelationid'::regclass) not null
  , moldno text not null
  , historyno integer default 0 not null
  , moldreportid text not null
  , revision integer default 0 not null
  , created timestamp(6) without time zone not null
  , createby integer default 99999 not null
  , updated timestamp(6) without time zone not null
  , updateby integer default 99999 not null
  , version integer default 0 not null
  , deleteflag boolean default false not null
  , primary key (moldreportrelationid)
);

comment on table public.t_moldreportrelation is '金型帳票関連テーブル';
comment on column t_moldreportrelation.moldreportrelationid is '金型帳票関連ID';
comment on column t_moldreportrelation.moldno is '金型NO';
comment on column t_moldreportrelation.historyno is '金型履歴NO';
comment on column t_moldreportrelation.moldreportid is '金型帳票ID';
comment on column t_moldreportrelation.revision is 'バージョン';
comment on column t_moldreportrelation.created is '作成日';
comment on column t_moldreportrelation.createby is '作成者';
comment on column t_moldreportrelation.updated is '更新日';
comment on column t_moldreportrelation.updateby is '更新者';
comment on column t_moldreportrelation.version is 'バージョン';
comment on column t_moldreportrelation.deleteflag is '削除フラグ';

