drop table if exists public.m_salesdivision;
create table public.m_salesdivision(
    lngsalesdivisioncode integer not null
   ,strsalesdivisionname text
   ,primary key(lngsalesdivisioncode)
);

comment on table public.m_salesdivision is '売上分類マスタ';
comment on column m_salesdivision.lngsalesdivisioncode is '売上分類コード';
comment on column m_salesdivision.strsalesdivisionname is '売上分類名称';

