SELECT DISTINCT
      mu.struserdisplaycode usercode
    , mu.struserdisplayname username
FROM
    m_group mg,  m_grouprelation mgr,  m_user mu
WHERE
    mu.lngusercode = mgr.lngusercode
AND mg.lnggroupcode = mgr.lnggroupcode
AND mg.bytgroupdisplayflag = true
AND mu.bytuserdisplayflag = true
AND mg.strgroupdisplaycode LIKE '%' || $1 || '%'
AND mu.struserdisplaycode = $2
ORDER BY
    mu.struserdisplaycode
;
