drop table if exists public.m_systeminformation;
create table public.m_systeminformation(
    lngsysteminformationcode integer not null
   ,strsysteminformationtitle text
   ,dtminsertdate timestamp(6) without time zone
   ,strsysteminformationbody text
   ,primary key(lngsysteminformationcode)
);

comment on table public.m_systeminformation is 'システムメッセージマスタ';
comment on column m_systeminformation.lngsysteminformationcode is 'システムメッセージコード';
comment on column m_systeminformation.strsysteminformationtitle is 'システムメッセージタイトル';
comment on column m_systeminformation.dtminsertdate is '登録日時';
comment on column m_systeminformation.strsysteminformationbody is 'システムメッセージ本文';

