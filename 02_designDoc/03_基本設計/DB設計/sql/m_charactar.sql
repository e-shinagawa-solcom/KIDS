drop table if exists public.m_charactar;
create table public.m_charactar(
    strcharactarcode text not null
   ,lngordercode integer
   ,strkeyword text
   ,blninvalidflag boolean
   ,primary key(strcharactarcode)
);

comment on table public.m_charactar is '特殊文字マスタ';
comment on column m_charactar.strcharactarcode is '文字コード';
comment on column m_charactar.lngordercode is '注文コード';
comment on column m_charactar.strkeyword is 'キーワード';
comment on column m_charactar.blninvalidflag is '無効フラグ';

