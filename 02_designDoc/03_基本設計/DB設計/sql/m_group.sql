drop table if exists public.m_group;
create table public.m_group(
    lnggroupcode integer not null
   ,lngcompanycode integer
   ,strgroupname text
   ,bytgroupdisplayflag boolean
   ,strgroupdisplaycode text
   ,strgroupdisplayname text
   ,strgroupdisplaycolor text
   ,primary key(lnggroupcode)
);

comment on table public.m_group is 'グループマスタ';
comment on column m_group.lnggroupcode is 'グループコード';
comment on column m_group.lngcompanycode is '会社コード';
comment on column m_group.strgroupname is 'グループ名称';
comment on column m_group.bytgroupdisplayflag is 'グループ表示許可フラグ';
comment on column m_group.strgroupdisplaycode is '表示用グループコード';
comment on column m_group.strgroupdisplayname is '表示用グループ名称';
comment on column m_group.strgroupdisplaycolor is 'グループ表示カラー';

DROP INDEX IF EXISTS m_group_pkey;
CREATE UNIQUE INDEX m_group_pkey on m_group USING btree(lnggroupcode);
