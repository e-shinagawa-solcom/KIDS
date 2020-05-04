--商品管理 データ移行

\i ./sql_parts/dblink.sql

-- 既存見積原価マスタを移行（明細は移行しない）
\i ./sql_parts/estimate.sql

-- 受発注あるけど見積原価マスタのないデータから空の見積原価マスタ(Ver0)を作成
\i ./sql_parts/add_empty_estimate.sql

-- 次期の受発注と現行の経費（証紙以外）から見積原価明細(Ver0)を作成
-- あと、本荷の明細のない見積原価データに本荷の明細を追加
\i ./sql_parts/add_new_estimate.sql

-- 見積原価明細のレートには、当時の社内レートを設定

update t_estimatedetail 
set curconversionrate = (select curconversionrate from m_monetaryrate where lngmonetaryratecode = 1 and lngmonetaryunitcode = t_estimatedetail.lngmonetaryunitcode and dtmapplystartdate <= t_estimatedetail.dtmdelivery and dtmapplyenddate >= t_estimatedetail.dtmdelivery)
where lngmonetaryunitcode <> 1;

update t_estimatedetail 
set curconversionrate = 1
where lngmonetaryunitcode = 1;

-- 金額にレートを反映
update t_estimatedetail 
set cursubtotalprice = trunc(lngproductquantity * curproductprice * curconversionrate,2);

-- 現行で発注データのない証紙の見積原価明細とその発注データ（仮発注）を次期に追加
-- （見積原価明細のレートと金額は補正不要）

\i ./sql_parts/add_estimate_401_1.sql


\i ./sql_parts/dblink_disconnect.sql


\i ./add_m_estimatehistory.sql
-- 償却フラグの設定
\i ./sql_parts/update_payoff.sql
-- 見積原価マスタの補正
\i ./sql_parts/update_m_estimate.sql

