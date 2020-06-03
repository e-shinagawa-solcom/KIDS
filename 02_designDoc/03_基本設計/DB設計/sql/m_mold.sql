drop table if exists public.m_mold;
create table public.m_mold(
    moldno text not null
   ,vendercode integer
   ,productcode text default ''
   ,strrevisecode text default ''
   ,created timestamp without time zone not null
   ,createby integer not null default 99999
   ,updated timestamp without time zone not null
   ,updateby integer not null default 99999
   ,version integer not null default 0
   ,deleteflag boolean not null default false
   ,primary key(moldno)
);

comment on table public.m_mold is '金型マスタ';
comment on column m_mold.moldno is '金型番号';
comment on column m_mold.vendercode is '仕入元コード';
comment on column m_mold.productcode is '製品コード';
comment on column m_mold.strrevisecode is '再販コード';
comment on column m_mold.created is '作成日';
comment on column m_mold.createby is '作成者';
comment on column m_mold.updated is '更新日';
comment on column m_mold.updateby is '更新者';
comment on column m_mold.version is 'バージョン';
comment on column m_mold.deleteflag is '削除フラグ';

DROP INDEX IF EXISTS m_mold_pkey;
CREATE UNIQUE INDEX m_mold_pkey on m_mold USING btree(moldno);
