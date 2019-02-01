SELECT
      mg.strgroupdisplaycode groupdisplaycode
    , mg.strgroupdisplayname groupdisplayname
FROM
    m_group mg
WHERE
    mg.bytgroupdisplayflag = true
AND mg.strgroupdisplayname LIKE '%' || $1 || '%'
ORDER BY
    mg.strgroupdisplaycode
;
