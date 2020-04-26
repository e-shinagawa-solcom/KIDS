--商品管理 データ移行

\i ./sql_parts/dblink.sql
\i ./sql_parts/estimate.sql
-- 既存見積原価データを移行
\i ./sql_parts/dblink_disconnect.sql
-- 受発注あるけど見積のないデータから空の見積原価書(Ver0)を作成
\i ./sql_parts/add_empty_estimate.sql
-- 受発注から見積原価書(Ver1)を作成
\i ./sql_parts/add_new_estimate.sql
-- 現行で発注データのない証紙を次期に追加
\i ./sql_parts/add_estimate_401_1.sql
\i ./add_m_estimatehistory.sql
-- 償却フラグの設定
\i ./sql_parts/update_payoff.sql
-- 見積原価マスタの補正
\i ./sql_parts/update_m_estimate.sql
