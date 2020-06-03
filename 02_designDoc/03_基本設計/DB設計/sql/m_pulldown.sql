drop table if exists public.m_pulldown;
create table public.m_pulldown(
    lngitemcode integer not null
   ,lngtargetarea integer not null
   ,primary key(lngitemcode,lngtargetarea)
);

comment on table public.m_pulldown is 'プルダウンマスタ';
comment on column m_pulldown.lngitemcode is 'アイテムコード';
comment on column m_pulldown.lngtargetarea is '対象エリアコード';

