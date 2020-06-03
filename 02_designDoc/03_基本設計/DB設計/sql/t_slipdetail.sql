drop table if exists public.t_slipdetail;
create table public.t_slipdetail(
    lngslipno integer not null
   ,lngslipdetailno integer not null
   ,lngrevisionno integer not null
   ,strcustomersalescode text
   ,lngsalesclasscode integer
   ,strsalesclassname text
   ,strgoodscode text
   ,strproductcode text
   ,strrevisecode text
   ,strproductname text
   ,strproductenglishname text
   ,curproductprice numeric(14,4)
   ,lngquantity integer
   ,lngproductquantity integer
   ,lngproductunitcode integer
   ,strproductunitname text
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngreceiveno integer
   ,lngreceivedetailno integer
   ,lngreceiverevisionno integer
   ,lngsortkey integer
   ,primary key(lngslipno,lngslipdetailno,lngrevisionno)
);

comment on table public.t_slipdetail is '納品伝票明細テーブル';
comment on column t_slipdetail.lngslipno is '納品伝票番号';
comment on column t_slipdetail.lngslipdetailno is '納品伝票明細番号';
comment on column t_slipdetail.lngrevisionno is 'リビジョン番号';
comment on column t_slipdetail.strcustomersalescode is '顧客受注番号';
comment on column t_slipdetail.lngsalesclasscode is '売上区分コード';
comment on column t_slipdetail.strsalesclassname is '売上区分名';
comment on column t_slipdetail.strgoodscode is '顧客品番';
comment on column t_slipdetail.strproductcode is '製品コード';
comment on column t_slipdetail.strrevisecode is '再販コード';
comment on column t_slipdetail.strproductname is '製品名';
comment on column t_slipdetail.strproductenglishname is '製品名（英語）';
comment on column t_slipdetail.curproductprice is '単価';
comment on column t_slipdetail.lngquantity is '入数';
comment on column t_slipdetail.lngproductquantity is '数量';
comment on column t_slipdetail.lngproductunitcode is '製品単位コード';
comment on column t_slipdetail.strproductunitname is '製品単位名';
comment on column t_slipdetail.cursubtotalprice is '小計';
comment on column t_slipdetail.strnote is '明細備考';
comment on column t_slipdetail.lngreceiveno is '受注番号';
comment on column t_slipdetail.lngreceivedetailno is '受注明細番号';
comment on column t_slipdetail.lngreceiverevisionno is '受注リビジョン番号';
comment on column t_slipdetail.lngsortkey is '表示用ソートキー';

DROP INDEX IF EXISTS t_slipdetail_pkey;
CREATE UNIQUE INDEX t_slipdetail_pkey on t_slipdetail USING btree(lngslipno ,lngslipdetailno ,lngrevisionno);
