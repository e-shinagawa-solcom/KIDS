drop table if exists t_reportbylcdetail;
create table t_reportbylcdetail(
    lcNo character varying(32)
   ,pono character varying(10) not null
   ,factoryName character varying(255)
   ,productcd character varying(8)
   ,productrevisecd character varying(2)
   ,productname character varying(255)
   ,productnumber integer
   ,unitname character varying(8)
   ,unitprice numeric(14,4)
   ,moneyprice numeric(14,4)
   ,shipstartdate date
   ,shipenddate date
   ,portplace character varying(255)
   ,shipterm date
   ,validterm date
   ,bankname character varying(255)
   ,bankreqdate date
   ,lcamopen date

   ,primary key(pono)
);
comment on table t_reportbylcdetail is '帳票LC別明細';
comment on column t_reportbylcdetail.lcNo is 'LC番号';
comment on column t_reportbylcdetail.pono is 'PO番号';
comment on column t_reportbylcdetail.factoryName is '工場名';
comment on column t_reportbylcdetail.productcd is '商品CD';
comment on column t_reportbylcdetail.productrevisecd is '再販コード';
comment on column t_reportbylcdetail.productname is '商品名';
comment on column t_reportbylcdetail.productnumber is '数量';
comment on column t_reportbylcdetail.unitname is '単位';
comment on column t_reportbylcdetail.unitprice is '単価';
comment on column t_reportbylcdetail.moneyprice is '金額';
comment on column t_reportbylcdetail.shipstartdate is '船積開始予定日';
comment on column t_reportbylcdetail.shipenddate is '船積終了予定日';
comment on column t_reportbylcdetail.portplace is '荷揚地';
comment on column t_reportbylcdetail.shipterm is '船積期限';
comment on column t_reportbylcdetail.validterm is '有効期限';
comment on column t_reportbylcdetail.bankname is '発行銀行';
comment on column t_reportbylcdetail.bankreqdate is '銀行依頼日';
comment on column t_reportbylcdetail.lcamopen is 'LC日付';
