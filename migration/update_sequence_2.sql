update  t_sequence set lngsequence=( select max( lngAttributeRelationCode ) from  m_AttributeRelation ) where strsequencename='m_AttributeRelation.lngAttributeRelationCode';
update  t_sequence set lngsequence=( select max( lngcompanycode ) from  m_company ) where strsequencename='m_company.lngcompanycode';
update  t_sequence set lngsequence=( select max( lngEstimateNo ) from  m_Estimate ) where strsequencename='m_Estimate.lngEstimateNo';
update  t_sequence set lngsequence=( select max( lngGroupCode ) from  m_Group ) where strsequencename='m_Group.lngGroupCode';
update  t_sequence set lngsequence=( select max( lngGroupRelationCode ) from  m_GroupRelation ) where strsequencename='m_GroupRelation.lngGroupRelationCode';
update  t_sequence set lngsequence=( select max( lngOrderNo ) from  m_Order ) where strsequencename='m_Order.lngOrderNo';
update  t_sequence set lngsequence=( select max( lngproductno ) from  m_product ) where strsequencename='m_product.lngproductno';
update  t_sequence set lngsequence=( select max( lngpurchaseorderno ) from  m_purchaseorder ) where strsequencename='m_purchaseorder.lngpurchaseorderno';
update  t_sequence set lngsequence=( select max( lngReceiveNo ) from  m_receive ) where strsequencename='m_receive.lngReceiveNo';
update  t_sequence set lngsequence=( select max( lngSalesNo ) from  m_sales ) where strsequencename='m_sales.lngSalesNo';
update  t_sequence set lngsequence=( select max( lngSlipNo ) from  m_Slip ) where strsequencename='m_Slip.lngSlipNo';
update  t_sequence set lngsequence=( select max( lngStockNo ) from  m_stock ) where strsequencename='m_stock.lngStockNo';
update  t_sequence set lngsequence=( select max( lngSystemInformationCode ) from  m_SystemInformation ) where strsequencename='m_SystemInformation.lngSystemInformationCode';
update  t_sequence set lngsequence=( select max( lngUserCode ) from  m_User ) where strsequencename='m_User.lngUserCode';
update  t_sequence set lngsequence=( select max( lnggoodsplancode ) from  t_goodsplan ) where strsequencename='t_goodsplan.lnggoodsplancode';
update  t_sequence set lngsequence=( select max( lngReportCode ) from  t_Report ) where strsequencename='t_Report.lngReportCode';

