drop table if exists public.m_stocksubject;
create table public.m_stocksubject(
    lngstocksubjectcode integer not null
   ,lngstockclasscode integer not null
   ,strstocksubjectname text
   ,bytdisplayflag boolean
   ,bytinvalidflag boolean
   ,bytdisplayestimateflag boolean
   ,primary key(lngstocksubjectcode,lngstockclasscode)
);

comment on table public.m_stocksubject is '仕入科目マスタ';
comment on column m_stocksubject.lngstocksubjectcode is '仕入科目コード';
comment on column m_stocksubject.lngstockclasscode is '仕入区分コード';
comment on column m_stocksubject.strstocksubjectname is '仕入科目名称';
comment on column m_stocksubject.bytdisplayflag is '表示フラグ';
comment on column m_stocksubject.bytinvalidflag is '無効フラグ';
comment on column m_stocksubject.bytdisplayestimateflag is '見積原価表示フラグ';

