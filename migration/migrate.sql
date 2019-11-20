/*
\i 1_mig_product.sql;
\qecho 'step1 completed'
\i 2_mig_order.sql;
\qecho  'step2 completed'
\i 3_mig_receive.sql;
\qecho 'step3 completed'
\i 4_mig_estimate.sql;
\qecho  'step4 completed'
*/
\i 5_mig_sales.sql;
\qecho  'step5 completed'
--\i 6_update_m_stockitem.sql;
\i 7_mig_stock.sql;
\qecho  'step7 completed'
--\i m_slipkindrelation.sql;\
