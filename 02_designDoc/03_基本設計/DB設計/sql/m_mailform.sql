drop table if exists public.m_mailform;
create table public.m_mailform(
    lngmailformcode integer not null
   ,strmailformname text
   ,lngfunctioncode integer
   ,strsubject text
   ,strbody text
   ,bytinvalidflag boolean
   ,strnote text
   ,primary key(lngmailformcode)
);

comment on table public.m_mailform is 'メール雛型マスタ';
comment on column m_mailform.lngmailformcode is 'メール雛型コード';
comment on column m_mailform.strmailformname is 'メール雛型名称';
comment on column m_mailform.lngfunctioncode is '機能コード';
comment on column m_mailform.strsubject is 'メールタイトル';
comment on column m_mailform.strbody is 'メール本文';
comment on column m_mailform.bytinvalidflag is '無効フラグ';
comment on column m_mailform.strnote is '備考';

