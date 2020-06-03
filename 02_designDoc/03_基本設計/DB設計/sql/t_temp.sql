drop table if exists public.t_temp;
create table public.t_temp(
    lngtempno integer not null
   ,strkey text not null
   ,strvalue text
   ,primary key(lngtempno,strkey)
);

comment on table public.t_temp is 'テンポラリテーブル';
comment on column t_temp.lngtempno is 'テンポラリ番号';
comment on column t_temp.strkey is 'キー';
comment on column t_temp.strvalue is '値';

