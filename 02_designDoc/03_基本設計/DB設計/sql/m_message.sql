drop table if exists public.m_message;
create table public.m_message(
    lngmessagecode integer not null
   ,strmessagecontent text
   ,strmessagecontentenglish text
   ,primary key(lngmessagecode)
);

comment on table public.m_message is 'メッセージマスタ';
comment on column m_message.lngmessagecode is 'メッセージコード';
comment on column m_message.strmessagecontent is 'メッセージ内容';
comment on column m_message.strmessagecontentenglish is 'メッセージ内容英語版';

