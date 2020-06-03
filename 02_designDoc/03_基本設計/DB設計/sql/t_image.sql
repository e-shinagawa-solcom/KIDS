drop table if exists public.t_image;
create table public.t_image(
    lngimagecode integer not null
   ,objimage oid
   ,strdirectoryname text
   ,strfilename text
   ,strfiletype text
   ,lngfilesize integer
   ,strnote text
   ,blninvalidflag boolean
   ,primary key(lngimagecode)
);

comment on table public.t_image is 'イメージテーブル';
comment on column t_image.lngimagecode is 'イメージキーコード';
comment on column t_image.objimage is 'イメージオブジェクト';
comment on column t_image.strdirectoryname is 'ディレクトリ名';
comment on column t_image.strfilename is 'ファイル名';
comment on column t_image.strfiletype is 'ファイルタイプ';
comment on column t_image.lngfilesize is 'ファイルサイズ';
comment on column t_image.strnote is '備考';
comment on column t_image.blninvalidflag is '無効フラグ';

