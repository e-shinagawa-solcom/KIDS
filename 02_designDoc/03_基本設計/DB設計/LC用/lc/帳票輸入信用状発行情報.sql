drop table if exists t_reportimportlcorderinfo;
create table t_reportimportlcorderinfo(
    bankreqdate date
   ,pono character varying(10) not null
   ,productcd character varying(8)
   ,productrevisecd character varying(2)
   ,productname character varying(255)
   ,productnumber integer
   ,unitname character varying(8)
   ,unitprice numeric(14,4)
   ,moneyprice numeric(14,4)
   ,shipstartdate date
   ,shipenddate date
   ,shipterm date
   ,validterm date
   ,lcno character varying(32)
   ,reckoningInitialDate date
   ,portplace character varying(255)
   ,bankname character varying(255)
   ,reserve1 text

   ,primary key(pono)
);
comment on table t_reportimportlcorderinfo is '帳票輸入信用状発行情報';
comment on column t_reportimportlcorderinfo.bankreqdate is '銀行依頼日';
comment on column t_reportimportlcorderinfo.pono is 'PO番号';
comment on column t_reportimportlcorderinfo.productcd is '商品ＣＤ';
comment on column t_reportimportlcorderinfo.productrevisecd is '再販コード';
comment on column t_reportimportlcorderinfo.productname is '商品名';
comment on column t_reportimportlcorderinfo.productnumber is '数量';
comment on column t_reportimportlcorderinfo.unitname is '単位';
comment on column t_reportimportlcorderinfo.unitprice is '単価';
comment on column t_reportimportlcorderinfo.moneyprice is '金額';
comment on column t_reportimportlcorderinfo.shipstartdate is '船積予定開始日';
comment on column t_reportimportlcorderinfo.shipenddate is '船積予定終了日';
comment on column t_reportimportlcorderinfo.shipterm is '船積期限';
comment on column t_reportimportlcorderinfo.validterm is '有効期限';
comment on column t_reportimportlcorderinfo.lcno is 'LC番号';
comment on column t_reportimportlcorderinfo.reckoningInitialDate is '起算日';
comment on column t_reportimportlcorderinfo.portplace is '荷揚地';
comment on column t_reportimportlcorderinfo.bankname is '発行銀行名';
comment on column t_reportimportlcorderinfo.reserve1 is '予備１';
