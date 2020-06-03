drop table if exists public.m_closedday;
create table public.m_closedday(
    lngcloseddaycode integer not null
   ,strcloseddaycode text
   ,lngclosedday integer
   ,primary key(lngcloseddaycode)
);

comment on table public.m_closedday is '締め日マスタ';
comment on column m_closedday.lngcloseddaycode is '締め日コード';
comment on column m_closedday.strcloseddaycode is '締め日名称';
comment on column m_closedday.lngclosedday is '締め日数';

DROP INDEX IF EXISTS m_closedday_pkey;
CREATE UNIQUE INDEX m_closedday_pkey on m_closedday USING btree(lngcloseddaycode);
