drop table if exists public.t_sequence;
create table public.t_sequence(
    strsequencename text not null
   ,lngsequence integer
   ,primary key(strsequencename)
);

comment on table public.t_sequence is 'シーケンステーブル';
comment on column t_sequence.strsequencename is 'シーケンス名称';
comment on column t_sequence.lngsequence is 'シーケンス';

