drop table if exists public.m_user;
create table public.m_user(
    lngusercode integer not null
   ,lngcompanycode integer not null
   ,lngauthoritygroupcode integer not null
   ,struserid character(20) not null
   ,strpasswordhash character(32) not null
   ,struserfullname text
   ,bytmailtransmitflag boolean
   ,strmailaddress text
   ,bytuserdisplayflag boolean
   ,struserdisplaycode text
   ,struserdisplayname text
   ,bytinvalidflag boolean
   ,lngaccessipaddresscode integer
   ,struserimagefilename text
   ,strmypageinfo text
   ,strnote text
   ,primary key(lngusercode,lngcompanycode)
);

comment on table public.m_user is 'ユーザマスタ';
comment on column m_user.lngusercode is 'ユーザコード';
comment on column m_user.lngcompanycode is '会社コード';
comment on column m_user.lngauthoritygroupcode is '権限グループコード';
comment on column m_user.struserid is 'ユーザーID';
comment on column m_user.strpasswordhash is 'パスワード';
comment on column m_user.struserfullname is 'ユーザーフルネーム';
comment on column m_user.bytmailtransmitflag is 'メール配信許可フラグ';
comment on column m_user.strmailaddress is 'メールアドレス';
comment on column m_user.bytuserdisplayflag is 'ユーザー表示フラグ';
comment on column m_user.struserdisplaycode is '表示用ユーザーコード';
comment on column m_user.struserdisplayname is '表示用ユーザー名';
comment on column m_user.bytinvalidflag is '無効フラグ';
comment on column m_user.lngaccessipaddresscode is 'アクセスIPアドレスコード';
comment on column m_user.struserimagefilename is 'ユーザーイメージファイル';
comment on column m_user.strmypageinfo is 'マイページ情報';
comment on column m_user.strnote is '備考';

DROP INDEX IF EXISTS m_user_pkey;
CREATE UNIQUE INDEX m_user_pkey on m_user USING btree(lngusercode ,lngcompanycode);
