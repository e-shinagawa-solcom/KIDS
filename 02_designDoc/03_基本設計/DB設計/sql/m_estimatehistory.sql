drop table if exists public.m_estimatehistory;
create table public.m_estimatehistory(
    lngestimateno integer not null
   ,lngrevisionno integer not null
   ,lngestimaterowno integer not null
   ,lngestimatedetailno integer not null
   ,lngestimatedetailrevisionno integer not null default 0
   ,primary key(lngestimateno,lngrevisionno,lngestimaterowno)
);

comment on table public.m_estimatehistory is '見積原価履歴マスタ';
comment on column m_estimatehistory.lngestimateno is '見積原価番号';
comment on column m_estimatehistory.lngrevisionno is 'リビジョン番号';
comment on column m_estimatehistory.lngestimaterowno is '見積原価行番号';
comment on column m_estimatehistory.lngestimatedetailno is '見積原価明細番号';
comment on column m_estimatehistory.lngestimatedetailrevisionno is '見積原価明細リビジョン番号';

