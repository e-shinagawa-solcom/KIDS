SELECT
      mg.lnggroupcode as groupcode
    , mg.strgroupdisplaycode as groupdisplaycode
FROM
    m_group mg
WHERE
    mg.strgroupdisplaycode = $1
AND mg.bytgroupdisplayflag = true
;
