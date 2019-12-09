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
,primary key(
pono
,polineno
,poreviseno
)
);


comment on table t_lcinfo is 'LC���e�[�u��';

comment on column t_lcinfo.payfnameomit  is '�x���於��';
comment on column t_lcinfo.openDate  is '�I�[�v���N��';
comment on column t_lcinfo.portplace  is '�חg�n';
comment on column t_lcinfo.pono  is 'PO�ԍ�';
comment on column t_lcinfo.polineno  is 'PO�s�ԍ�';
comment on column t_lcinfo.poreviseno  is 'PO���o�C�Y�ԍ�';
comment on column t_lcinfo.postate  is 'PO�f�[�^���';
comment on column t_lcinfo.payfcd  is '�x����R�[�h';
comment on column t_lcinfo.productcd  is '���i�R�[�h';
comment on column t_lcinfo.productrevisecd  is '�Ĕ̃R�[�h';
comment on column t_lcinfo.productname  is '���i��';
comment on column t_lcinfo.productnumber  is '����';
comment on column t_lcinfo.unitname  is '�P��';
comment on column t_lcinfo.unitprice  is '�P��';
comment on column t_lcinfo.moneyprice  is '���z';
comment on column t_lcinfo.shipstartdate  is '�D�ϊJ�n�\����t';
comment on column t_lcinfo.shipenddate  is '�D�ϏI���\����t';
comment on column t_lcinfo.sumdate  is '�v���';
comment on column t_lcinfo.poupdatedate  is '�X�V��';
comment on column t_lcinfo.deliveryplace  is '�[�i�ꏊ';
comment on column t_lcinfo.currencyclass  is '�ʉ݋敪';
comment on column t_lcinfo.lcnote  is '���l';
comment on column t_lcinfo.shipterm  is '�D�ϊ���';
comment on column t_lcinfo.validterm  is '�L������';
comment on column t_lcinfo.bankname  is '���s��s��';
comment on column t_lcinfo.bankreqdate  is '��s�˗���';
comment on column t_lcinfo.lcno  is 'LC�ԍ�';
comment on column t_lcinfo.lcamopen  is 'LCAM�I�[�v��';
comment on column t_lcinfo.validmonth  is '�L����';
comment on column t_lcinfo.usancesettlement  is '���[�U���X����';
comment on column t_lcinfo.bldetail1date  is 'BL���󖾍ׂP���t';
comment on column t_lcinfo.bldetail1money  is 'BL���󖾍ׂP���z';
comment on column t_lcinfo.bldetail2date  is 'BL���󖾍ׂQ���t';
comment on column t_lcinfo.bldetail2money  is 'BL���󖾍ׂQ���z';
comment on column t_lcinfo.bldetail3date  is 'BL���󖾍ׂR���t';
comment on column t_lcinfo.bldetail3money  is 'BL���󖾍ׂR���z';
comment on column t_lcinfo.payfnameformal  is '�x���搳������';
comment on column t_lcinfo.productnamee  is '���i���p��';
comment on column t_lcinfo.lcstate  is '���';
comment on column t_lcinfo.bankcd  is '��s�R�[�h';
comment on column t_lcinfo.shipym  is '�D�ϔN��';

