drop table if exists public.t_receivedetail;
create table public.t_receivedetail(
    lngreceiveno integer not null
   ,lngreceivedetailno integer not null
   ,lngrevisionno integer not null
   ,strproductcode text
   ,strrevisecode text
   ,lngsalesclasscode integer
   ,dtmdeliverydate date
   ,lngconversionclasscode integer
   ,curproductprice numeric(14, 4)
   ,lngproductquantity integer
   ,lngproductunitcode integer
   ,lngunitquantity integer
   ,cursubtotalprice numeric(14, 4)
   ,strnote text
   ,lngsortkey integer
   ,lngestimateno integer
   ,lngestimatedetailno integer
   ,lngestimaterevisionno integer
   ,primary key(lngreceiveno,lngreceivedetailno,lngrevisionno)
);

comment on table public.t_receivedetail is '受注明細テーブル';
comment on column t_receivedetail.lngreceiveno is '受注番号';
comment on column t_receivedetail.lngreceivedetailno is '受注明細番号';
comment on column t_receivedetail.lngrevisionno is 'リビジョン番号';
comment on column t_receivedetail.strproductcode is '製品コード';
comment on column t_receivedetail.strrevisecode is '再販コード';
comment on column t_receivedetail.lngsalesclasscode is '売上区分コード';
comment on column t_receivedetail.dtmdeliverydate is '納品日';
comment on column t_receivedetail.lngconversionclasscode is '換算区分コード';
comment on column t_receivedetail.curproductprice is '製品価格';
comment on column t_receivedetail.lngproductquantity is '製品数量';
comment on column t_receivedetail.lngproductunitcode is '製品単位コード';
comment on column t_receivedetail.lngunitquantity is '入数';
comment on column t_receivedetail.cursubtotalprice is '小計金額';
comment on column t_receivedetail.strnote is '備考';
comment on column t_receivedetail.lngsortkey is '表示用ソートキー';
comment on column t_receivedetail.lngestimateno is '見積原価番号';
comment on column t_receivedetail.lngestimatedetailno is '見積原価明細番号';
comment on column t_receivedetail.lngestimaterevisionno is '見積原価リビジョン番号';

DROP INDEX IF EXISTS t_receivedetail_pkey;
CREATE UNIQUE INDEX t_receivedetail_pkey on t_receivedetail USING btree(lngreceivedetailno ,lngreceiveno ,lngrevisionno);
