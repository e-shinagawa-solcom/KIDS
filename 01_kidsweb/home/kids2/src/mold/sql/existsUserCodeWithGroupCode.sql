SELECT
      mu.lngusercode
    , mu.struserdisplaycode
    , mg.lnggroupcode
    , mg.strgroupdisplaycode
FROM
    m_user mu
INNER JOIN
    m_grouprelation mgr
  ON
    mu.lngusercode = mgr.lngusercode
INNER JOIN
    m_group mg
  ON
    mg.lnggroupcode = mgr.lnggroupcode
WHERE
    mu.struserdisplaycode = $1
AND mu.bytuserdisplayflag = true
AND mg.strgroupdisplaycode = $2
AND mg.bytgroupdisplayflag = true
;
