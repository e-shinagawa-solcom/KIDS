drop table if exists public.t_salesdetail;
create table public.t_salesdetail(
    lngsalesno integer not null
   ,lngsalesdetailno integer not null
   ,lngrevisionno integer not null
   ,strproductcode text
   ,strrevisecode text
   ,lngsalesclasscode integer
   ,lngconversionclasscode integer
   ,lngquantity integer
   ,curproductprice numeric(14, 4)
   ,lngproductquantity integer
   ,lngproductunitcode integer
   ,lngtaxclasscode integer
   ,lngtaxcode integer
   ,curtaxprice numeric(14, 4)
   ,cursubtotalprice numeric(14, 4)
   ,strnote text
   ,lngsortkey integer
   ,lngreceiveno integer
   ,lngreceivedetailno integer
   ,lngreceiverevisionno integer
   ,primary key(lngsalesno,lngsalesdetailno,lngrevisionno)
);

comment on table public.t_salesdetail is '売上明細テーブル';
comment on column t_salesdetail.lngsalesno is '売上番号';
comment on column t_salesdetail.lngsalesdetailno is '売上明細番号';
comment on column t_salesdetail.lngrevisionno is 'リビジョン番号';
comment on column t_salesdetail.strproductcode is '製品コード';
comment on column t_salesdetail.strrevisecode is '再販コード';
comment on column t_salesdetail.lngsalesclasscode is '売上区分コード';
comment on column t_salesdetail.lngconversionclasscode is '換算区分コード';
comment on column t_salesdetail.lngquantity is '入数';
comment on column t_salesdetail.curproductprice is '製品価格';
comment on column t_salesdetail.lngproductquantity is '製品数量';
comment on column t_salesdetail.lngproductunitcode is '製品単位コード';
comment on column t_salesdetail.lngtaxclasscode is '消費税区分コード';
comment on column t_salesdetail.lngtaxcode is '消費税率コード';
comment on column t_salesdetail.curtaxprice is '消費税金額';
comment on column t_salesdetail.cursubtotalprice is '小計金額';
comment on column t_salesdetail.strnote is '備考';
comment on column t_salesdetail.lngsortkey is '表示用ソートキー';
comment on column t_salesdetail.lngreceiveno is '受注番号';
comment on column t_salesdetail.lngreceivedetailno is '受注明細番号';
comment on column t_salesdetail.lngreceiverevisionno is '受注リビジョン番号';

DROP INDEX IF EXISTS t_salesdetail_pkey;
CREATE UNIQUE INDEX t_salesdetail_pkey on t_salesdetail USING btree(lngsalesno ,lngsalesdetailno ,lngrevisionno);
