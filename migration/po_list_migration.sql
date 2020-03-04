\i ./sql_parts/dblink.sql

--select dblink_connect('con111','hostaddr=192.168.1.111 port=5432 dbname=kidscore2 user=kids password=kids');

update m_purchaseorder
set lngprintcount = 1
where strordercode in (
SELECT * FROM dblink('con111',
'select mo.strordercode as strordercode from t_report tr inner join m_order mo on mo.lngorderno = cast(tr.strreportkeycode as integer) where tr.lngreportclasscode=2'
) AS T1(
strordercode text
)
);

\i ./sql_parts/dblink_disconnect.sql
