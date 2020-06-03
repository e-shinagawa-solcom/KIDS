drop table if exists public.t_cache;
create table public.t_cache(
    chacheid integer default nextval('seq_cacheid'::regclass) not null
   ,hashcode text not null
   ,serializeddata text not null
   ,created timestamp without time zone not null
   ,createby integer not null default 99999
   ,updated timestamp without time zone not null
   ,updateby integer not null default 99999
   ,version integer not null default 0
   ,deleteflag boolean not null default false
   ,primary key(chacheid)
);

comment on table public.t_cache is 'キャッシュテーブル';
comment on column t_cache.chacheid is 'キャッシュID';
comment on column t_cache.hashcode is 'ハッシュコード';
comment on column t_cache.serializeddata is 'データ';
comment on column t_cache.created is '作成日';
comment on column t_cache.createby is '作成者';
comment on column t_cache.updated is '更新日';
comment on column t_cache.updateby is '更新者';
comment on column t_cache.version is 'バージョン';
comment on column t_cache.deleteflag is '削除フラグ';

