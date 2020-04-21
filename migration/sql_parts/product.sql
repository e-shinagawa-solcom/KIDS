--BEGIN TRANSACTION;

--製品マスタ移行
delete from m_product;

INSERT INTO m_product
(
lngproductno
,strproductcode
,strproductname
,strproductenglishname
,strgoodscode
,strgoodsname
,lnginchargegroupcode
,lnginchargeusercode
,lngdevelopusercode
,lnginputusercode
,lngcustomercompanycode
,lngcustomergroupcode
,lngcustomerusercode
,strcustomerusername
,lngpackingunitcode
,lngproductunitcode
,lngboxquantity
,lngcartonquantity
,lngproductionquantity
,lngproductionunitcode
,lngfirstdeliveryquantity
,lngfirstdeliveryunitcode
,lngfactorycode
,lngassemblyfactorycode
,lngdeliveryplacecode
,dtmdeliverylimitdate
,curproductprice
,curretailprice
,lngtargetagecode
,lngroyalty
,lngcertificateclasscode
,lngcopyrightcode
,strcopyrightdisplaystamp
,strcopyrightdisplayprint
,lngproductformcode
,strproductcomposition
,strassemblycontents
,strspecificationdetails
,strnote
,bytinvalidflag
,dtminsertdate
,dtmupdatedate
,strcopyrightnote
,lngcategorycode
,lngrevisionno
,strrevisecode
)
SELECT * FROM dblink('con111',
    'select ' ||
    'lngproductno,' ||
    'strproductcode,' ||
    'strproductname,' ||
    'strproductenglishname,' ||
    'strgoodscode,' ||
    'strgoodsname,' ||
    'lnginchargegroupcode,' ||
    '204 as lnginchargeusercode,' ||
    'lnginchargeusercode AS lngdevelopusercode,' ||
    'lnginputusercode,' ||
    'lngcustomercompanycode,' ||
    'lngcustomergroupcode,' ||
    'lngcustomerusercode,' ||
    'strcustomerusername,' ||
    'lngpackingunitcode,' ||
    'lngproductunitcode,' ||
    'lngboxquantity,' ||
    'lngcartonquantity,' ||
    'lngproductionquantity,' ||
    'lngproductionunitcode,' ||
    'lngfirstdeliveryquantity,' ||
    'lngfirstdeliveryunitcode,' ||
    'lngfactorycode,' ||
    'lngassemblyfactorycode,' ||
    'lngdeliveryplacecode,' ||
    'dtmdeliverylimitdate,' ||
    'curproductprice,' ||
    'curretailprice,' ||
    'lngtargetagecode,' ||
    'lngroyalty,' ||
    'lngcertificateclasscode,' ||
    'lngcopyrightcode,' ||
    'strcopyrightdisplaystamp,' ||
    'strcopyrightdisplayprint,' ||
    'lngproductformcode,' ||
    'strproductcomposition,' ||
    'strassemblycontents,' ||
    'strspecificationdetails,' ||
    'strnote,' ||
    'bytinvalidflag,' ||
    'dtminsertdate,' ||
    'dtmupdatedate,' ||
    'strcopyrightnote,' ||
    'lngcategorycode,' ||
    '0 AS lngrevisionno,' ||
    '''00'' AS strrevisecode' ||
    ' from m_product'
) AS T1
(
    lngproductno integer 
   ,strproductcode text 
   ,strproductname text 
   ,strproductenglishname text 
   ,strgoodscode text 
   ,strgoodsname text 
   ,lnginchargegroupcode integer 
   ,lnginchargeusercode integer 
   ,lngdevelopusercode integer 
   ,lnginputusercode integer 
   ,lngcustomercompanycode integer 
   ,lngcustomergroupcode integer 
   ,lngcustomerusercode integer 
   ,strcustomerusername text 
   ,lngpackingunitcode integer 
   ,lngproductunitcode integer 
   ,lngboxquantity integer 
   ,lngcartonquantity integer 
   ,lngproductionquantity integer 
   ,lngproductionunitcode integer 
   ,lngfirstdeliveryquantity integer 
   ,lngfirstdeliveryunitcode integer 
   ,lngfactorycode integer 
   ,lngassemblyfactorycode integer 
   ,lngdeliveryplacecode integer 
   ,dtmdeliverylimitdate date 
   ,curproductprice numeric(14, 4) 
   ,curretailprice numeric(14, 4) 
   ,lngtargetagecode integer 
   ,lngroyalty numeric(14, 4) 
   ,lngcertificateclasscode integer 
   ,lngcopyrightcode integer 
   ,strcopyrightdisplaystamp text 
   ,strcopyrightdisplayprint text 
   ,lngproductformcode integer 
   ,strproductcomposition text 
   ,strassemblycontents text 
   ,strspecificationdetails text 
   ,strnote text 
   ,bytinvalidflag boolean 
   ,dtminsertdate timestamp without time zone 
   ,dtmupdatedate timestamp without time zone 
   ,strcopyrightnote text 
   ,lngcategorycode integer 
   ,lngrevisionno integer 
   ,strrevisecode text 

);

--商品化企画テーブル移行
delete from t_goodsplan;

INSERT INTO t_goodsplan
(
    lnggoodsplancode
   ,lngrevisionno
   ,lngproductno
   ,dtmcreationdate
   ,dtmrevisiondate
   ,lnggoodsplanprogresscode
   ,lnginputusercode
--,strrevisecode
)
SELECT * FROM dblink('con111',
    'select ' ||
    'lnggoodsplancode,' ||
    't_goodsplan.lngrevisionno,' ||
    't_goodsplan.lngproductno,' ||
    'dtmcreationdate,' ||
    'dtmrevisiondate,' ||
    'lnggoodsplanprogresscode,' ||
    'lnginputusercode' ||
    ' from t_goodsplan ' ||
    'inner join ( select lngproductno, max(lngrevisionno) as lngrevisionno from t_goodsplan group by lngproductno ) rev_max' ||
    ' on rev_max.lngproductno = t_goodsplan.lngproductno and rev_max.lngrevisionno = t_goodsplan.lngrevisionno'
    ) 
AS T(
    lnggoodsplancode integer
   ,lngrevisionno integer
   ,lngproductno integer
   ,dtmcreationdate timestamp without time zone
   ,dtmrevisiondate timestamp without time zone
   ,lnggoodsplanprogresscode integer
   ,lnginputusercode integer
--,strrevisecode text
);



--COMMIT;