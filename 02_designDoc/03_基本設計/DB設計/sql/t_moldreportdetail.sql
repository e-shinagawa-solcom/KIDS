drop table if exists public.t_moldreportdetail;
create table public.t_moldreportdetail(
    moldreportid text not null
   ,revision integer not null default 0
   ,listorder integer not null default 1
   ,moldno text default ''
   ,molddescription text default ''
   ,created timestamp(6) without time zone not null
   ,createby integer not null default 99999
   ,updated timestamp(6) without time zone not null
   ,updateby integer not null default 99999
   ,version integer not null default 0
   ,deleteflag boolean not null default false
   ,primary key(moldreportid,revision,listorder)
);

comment on table public.t_moldreportdetail is '金型帳票詳細';
comment on column t_moldreportdetail.moldreportid is '金型帳票ID';
comment on column t_moldreportdetail.revision is 'リビジョン';
comment on column t_moldreportdetail.listorder is '順序';
comment on column t_moldreportdetail.moldno is '金型NO';
comment on column t_moldreportdetail.molddescription is '金型説明';
comment on column t_moldreportdetail.created is '作成日';
comment on column t_moldreportdetail.createby is '作成者';
comment on column t_moldreportdetail.updated is '更新日';
comment on column t_moldreportdetail.updateby is '更新者';
comment on column t_moldreportdetail.version is 'バージョン';
comment on column t_moldreportdetail.deleteflag is '削除フラグ';

