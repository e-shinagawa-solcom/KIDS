drop table if exists public.m_goodsplanprogress;
create table public.m_goodsplanprogress(
    lnggoodsplanprogresscode integer not null
   ,strgoodsplanprogressname text
   ,primary key(lnggoodsplanprogresscode)
);

comment on table public.m_goodsplanprogress is '企画進行状況マスタ';
comment on column m_goodsplanprogress.lnggoodsplanprogresscode is '企画進行状況コード';
comment on column m_goodsplanprogress.strgoodsplanprogressname is '企画進行状況名称';

