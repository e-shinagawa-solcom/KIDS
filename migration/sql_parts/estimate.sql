--BEGIN TRANSACTION;

--製品マスタ移行
delete from m_estimate;

INSERT INTO m_estimate
(
    lngestimateno
   ,lngrevisionno
   ,strproductcode
   ,strrevisecode
   ,bytdecisionflag
   ,lngestimatestatuscode
   ,curfixedcost
   ,curmembercost
   ,curtotalprice
   ,curmanufacturingcost
   ,cursalesamount
   ,curprofit
   ,lnginputusercode
   ,bytinvalidflag
   ,dtminsertdate
   ,lngproductionquantity
   ,lngtempno
   ,strnote
   ,lngproductrevisionno
)
SELECT * FROM dblink('con111',
    'select ' ||
    'lngestimateno' ||
    ',lngrevisionno' ||
    ',strproductcode' ||
    ',''00''' ||
    ',bytdecisionflag' ||
    ',lngestimatestatuscode' ||
    ',curfixedcost' ||
    ',curmembercost' ||
    ',curtotalprice' ||
    ',curmanufacturingcost' ||
    ',cursalesamount' ||
    ',curprofit' ||
    ',lnginputusercode' ||
    ',bytinvalidflag' ||
    ',dtminsertdate' ||
    ',lngproductionquantity' ||
    ',lngtempno' ||
    ',strnote' ||
    ',0 as lngproductrevisionno' ||
    ' from m_estimate'
) AS T1
(
    lngestimateno integer
   ,lngrevisionno integer
   ,strproductcode text
   ,strrevisecode text
   ,bytdecisionflag boolean
   ,lngestimatestatuscode integer
   ,curfixedcost numeric(14,4)
   ,curmembercost numeric(14,4)
   ,curtotalprice numeric(14,4)
   ,curmanufacturingcost numeric(14,4)
   ,cursalesamount numeric(14,4)
   ,curprofit numeric(14,4)
   ,lnginputusercode integer
   ,bytinvalidflag boolean
   ,dtminsertdate timestamp without time zone
   ,lngproductionquantity integer
   ,lngtempno integer
   ,strnote text
   ,lngproductrevisionno integer
);

--見積原価明細テーブル移行
delete from t_estimatedetail;

INSERT INTO t_estimatedetail
(
    lngestimateno
   ,lngestimatedetailno
   ,lngrevisionno
   ,lngstocksubjectcode
   ,lngstockitemcode
   ,lngcustomercompanycode
   ,dtmdelivery
   ,bytpayofftargetflag
   ,bytpercentinputflag
   ,lngmonetaryunitcode
   ,lngmonetaryratecode
   ,curconversionrate
   ,lngproductquantity
   ,curproductprice
   ,curproductrate
   ,cursubtotalprice
   ,strnote
   ,lngsortkey
   ,lngsalesdivisioncode
   ,lngsalesclasscode
)
SELECT * FROM dblink('con111',
    'select ' ||
    'lngestimateno' ||
    ',lngestimatedetailno' ||
    ',lngrevisionno' ||
    ',lngstocksubjectcode' ||
    ',lngstockitemcode' ||
    ',lngcustomercompanycode' ||
    ',null as dtmdelivery' ||
    ',bytpayofftargetflag' ||
    ',bytpercentinputflag' ||
    ',lngmonetaryunitcode' ||
    ',lngmonetaryratecode' ||
    ',curconversionrate' ||
    ',lngproductquantity' ||
    ',curproductprice' ||
    ',curproductrate' ||
    ',cursubtotalprice' ||
    ',strnote' ||
    ',lngsortkey' ||
    ',lngsalesdivisioncode' ||
    ',lngsalesclasscode' ||
    ' from t_estimatedetail') 
AS T(
    lngestimateno integer
   ,lngestimatedetailno integer
   ,lngrevisionno integer
   ,lngstocksubjectcode integer
   ,lngstockitemcode integer
   ,lngcustomercompanycode integer
   ,dtmdelivery timestamp without time zone
   ,bytpayofftargetflag boolean
   ,bytpercentinputflag boolean
   ,lngmonetaryunitcode integer
   ,lngmonetaryratecode integer
   ,curconversionrate numeric(15,6)
   ,lngproductquantity integer
   ,curproductprice numeric(14,4)
   ,curproductrate numeric(15,6)
   ,cursubtotalprice numeric(14,4)
   ,strnote text
   ,lngsortkey integer
   ,lngsalesdivisioncode integer
   ,lngsalesclasscode integer
);

--COMMIT;