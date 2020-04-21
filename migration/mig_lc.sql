drop table t_aclcinfo_old;
alter table  t_aclcinfo rename to t_aclcinfo_old;

create table "public".t_aclcinfo (
  pono character varying(8) not null
  , polineno character varying(2) not null
  , poreviseno character varying(2) not null
  , postate character varying(16)
  , opendate character varying(6)
  , portplace character varying(255)
  , payfcd character varying(8)
  , payfnameomit character varying(255)
  , payfnameformal character varying(255)
  , productcd character varying(8)
  , productrevisecd character varying(2)
  , productname character varying(255)
  , productnamee character varying(255)
  , productnumber integer
  , unitname character varying(8)
  , unitprice numeric(14, 4)
  , moneyprice numeric(14, 4)
  , shipstartdate date
  , shipenddate date
  , sumdate date
  , poupdatedate date
  , deliveryplace character varying(255)
  , currencyclass character varying(8)
  , lcnote text
  , shipterm date
  , validterm date
  , bankcd character varying(8)
  , bankname character varying(255)
  , bankreqdate date
  , lcno character varying(32)
  , lcamopen date
  , validmonth character varying(8)
  , usancesettlement numeric(14, 4)
  , bldetail1date date
  , bldetail1money numeric(14, 4)
  , bldetail2date date
  , bldetail2money numeric(14, 4)
  , bldetail3date date
  , bldetail3money numeric(14, 4)
  , lcstate numeric(2)
  , entryuser text
  , entrydate character(8)
  , entrytime character(8)
  , updateuser text
  , updatedate character(8)
  , updatetime character(8)
  , invalidflag boolean
  , shipym character(6)
  , primary key (pono, polineno, poreviseno)
);


insert into "public".t_aclcinfo (
    pono
  , polineno
  , poreviseno
  , postate
  , opendate
  , portplace
  , payfcd
  , payfnameomit
  , payfnameformal
  , productcd
  , productrevisecd
  , productname
  , productnamee
  , productnumber
  , unitname
  , unitprice
  , moneyprice
  , shipstartdate
  , shipenddate
  , sumdate
  , poupdatedate
  , deliveryplace
  , currencyclass
  , lcnote
  , shipterm
  , validterm
  , bankcd
  , bankname
  , bankreqdate
  , lcno
  , lcamopen
  , validmonth
  , usancesettlement
  , bldetail1date
  , bldetail1money
  , bldetail2date
  , bldetail2money
  , bldetail3date
  , bldetail3money
  , lcstate
  , entryuser
  , entrydate
  , entrytime
  , updateuser
  , updatedate
  , updatetime
  , invalidflag
  , shipym
)
select
    pono
  , polineno
  , poreviseno
  , postate
  , opendate
  , portplace
  , payfcd
  , payfnameomit
  , payfnameformal
  , productcd
  , '00'
  , productname
  , productnamee
  , productnumber
  , unitname
  , unitprice
  , moneyprice
  , shipstartdate
  , shipenddate
  , sumdate
  , poupdatedate
  , deliveryplace
  , currencyclass
  , lcnote
  , shipterm
  , validterm
  , bankcd
  , bankname
  , bankreqdate
  , lcno
  , lcamopen
  , validmonth
  , usancesettlement
  , bldetail1date
  , bldetail1money
  , bldetail2date
  , bldetail2money
  , bldetail3date
  , bldetail3money
  , lcstate
  , entryuser
  , entrydate
  , entrytime
  , updateuser
  , updatedate
  , updatetime
  , invalidflag
  , shipym
 from "public".t_aclcinfo_old;

