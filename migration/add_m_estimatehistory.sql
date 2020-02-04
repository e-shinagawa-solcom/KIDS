delete from m_estimatehistory;
insert into m_estimatehistory(
lngestimateno,
lngrevisionno,
lngestimaterowno,
lngestimatedetailno,
lngestimatedetailrevisionno
)
SELECT
lngestimateno,
lngrevisionno,
lngestimatedetailno,
lngestimatedetailno,
lngrevisionno
FROM t_estimatedetail;
