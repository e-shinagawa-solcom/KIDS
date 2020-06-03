drop table if exists public.t_loginsession;
create table public.t_loginsession(
    strsessionid text not null
   ,lngusercode integer
   ,strloginuserid character(20)
   ,strloginpassword character(32)
   ,dtmlogintime timestamp without time zone
   ,straccessipaddress text
   ,bytsuccessfulflag boolean
   ,primary key(strsessionid)
);

comment on table public.t_loginsession is 'ログインセッションテーブル';
comment on column t_loginsession.strsessionid is 'セッションID';
comment on column t_loginsession.lngusercode is 'ユーザーコード';
comment on column t_loginsession.strloginuserid is 'ログインユーザーID';
comment on column t_loginsession.strloginpassword is 'ログインパスワード';
comment on column t_loginsession.dtmlogintime is 'ログイン日時';
comment on column t_loginsession.straccessipaddress is 'アクセスIPアドレス';
comment on column t_loginsession.bytsuccessfulflag is 'ログイン成功フラグ';

