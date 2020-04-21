drop table t_lcinfo;

create table t_lcinfo(
payfnameomit character varying(255)
,openDate character varying(6)
,portplace character varying(255)
,pono character varying(8) not null
,polineno character varying(2) not null
,poreviseno character varying(2) not null
,postate character varying(16)
,payfcd character varying(8)
,productcd character varying(8)
,productrevisecd character varying(2)
,productname character varying(255)
,productnumber integer default 0
,unitname character varying(8)
,unitprice numeric(14, 4)
,moneyprice numeric(14, 4)
,shipstartdate date
,shipenddate date
,sumdate date
,poupdatedate date
,deliveryplace character varying(255)
,currencyclass character varying(8)
,lcnote text
,shipterm date
,validterm date
,bankname character varying(255)
,bankreqdate date
,lcno character varying(32)
,lcamopen date
,validmonth character varying(8)
,usancesettlement numeric(14, 4)
,bldetail1date date
,bldetail1money numeric(14, 4)
,bldetail2date date
,bldetail2money numeric(14, 4)
,bldetail3date date
,bldetail3money numeric(14, 4)
,payfnameformal character varying(255)
,productnamee character varying(255)
,lcstate numeric(2)
,bankcd character varying(8)
,shipym character(6)
,unreflectedflag boolean
,primary key(
pono
,polineno
,poreviseno
)
);


comment on table t_lcinfo is 'LC情報テーブル';

comment on column t_lcinfo.payfnameomit  is '支払先名称';
comment on column t_lcinfo.openDate  is 'オープン年月';
comment on column t_lcinfo.portplace  is '荷揚地';
comment on column t_lcinfo.pono  is 'PO番号';
comment on column t_lcinfo.polineno  is 'PO行番号';
comment on column t_lcinfo.poreviseno  is 'POリバイズ番号';
comment on column t_lcinfo.postate  is 'POデータ状態';
comment on column t_lcinfo.payfcd  is '支払先コード';
comment on column t_lcinfo.productcd  is '商品コード';
comment on column t_lcinfo.productrevisecd  is '再販コード';
comment on column t_lcinfo.productname  is '商品名';
comment on column t_lcinfo.productnumber  is '数量';
comment on column t_lcinfo.unitname  is '単位';
comment on column t_lcinfo.unitprice  is '単価';
comment on column t_lcinfo.moneyprice  is '金額';
comment on column t_lcinfo.shipstartdate  is '船積開始予定日付';
comment on column t_lcinfo.shipenddate  is '船積終了予定日付';
comment on column t_lcinfo.sumdate  is '計上日';
comment on column t_lcinfo.poupdatedate  is '更新日';
comment on column t_lcinfo.deliveryplace  is '納品場所';
comment on column t_lcinfo.currencyclass  is '通貨区分';
comment on column t_lcinfo.lcnote  is '備考';
comment on column t_lcinfo.shipterm  is '船積期限';
comment on column t_lcinfo.validterm  is '有効期限';
comment on column t_lcinfo.bankname  is '発行銀行名';
comment on column t_lcinfo.bankreqdate  is '銀行依頼日';
comment on column t_lcinfo.lcno  is 'LC番号';
comment on column t_lcinfo.lcamopen  is 'LCAMオープン';
comment on column t_lcinfo.validmonth  is '有効日';
comment on column t_lcinfo.usancesettlement  is 'ユーザンス決済';
comment on column t_lcinfo.bldetail1date  is 'BL引受明細１日付';
comment on column t_lcinfo.bldetail1money  is 'BL引受明細１金額';
comment on column t_lcinfo.bldetail2date  is 'BL引受明細２日付';
comment on column t_lcinfo.bldetail2money  is 'BL引受明細２金額';
comment on column t_lcinfo.bldetail3date  is 'BL引受明細３日付';
comment on column t_lcinfo.bldetail3money  is 'BL引受明細３金額';
comment on column t_lcinfo.payfnameformal  is '支払先正式名称';
comment on column t_lcinfo.productnamee  is '商品名英名';
comment on column t_lcinfo.lcstate  is '状態';
comment on column t_lcinfo.bankcd  is '銀行コード';
comment on column t_lcinfo.shipym  is '船積年月';

