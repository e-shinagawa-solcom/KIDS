drop table if exists public.m_businesscode;
create table "public".m_businesscode (
  businesscodeid integer default nextval('seq_businesscodeid'::regclass) not null
  , businesscodename text not null
  , businesscode text not null
  , description text default ''
  , created timestamp(6) without time zone not null
  , createby integer default 99999 not null
  , updated timestamp(6) without time zone not null
  , updateby integer default 99999 not null
  , version integer default 0 not null
  , deleteflag boolean default false not null
  , primary key (businesscodeid,businesscodename,businesscode)
);

comment on table public.m_businesscode is '業務コードマスタ';
comment on column m_businesscode.businesscodeid is '業務コードID';
comment on column m_businesscode.businesscodename is '業務コード名';
comment on column m_businesscode.businesscode is '業務コード';
comment on column m_businesscode.description is '詳細';
comment on column m_businesscode.created is '作成時間';
comment on column m_businesscode.createby is '作成者';
comment on column m_businesscode.updated is '更新時間';
comment on column m_businesscode.updateby is '更新者';
comment on column m_businesscode.version is 'バージョン';
comment on column m_businesscode.deleteflag is '削除フラグ';

