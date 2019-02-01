SELECT
      mg.strgroupdisplaycode groupdisplaycode
    , mg.strgroupdisplayname groupdisplayname
FROM
    m_group mg
WHERE
    mg.bytgroupdisplayflag = true
AND mg.strgroupdisplaycode = $1
AND mg.strgroupdisplayname LIKE '%' || $2 || '%'
ORDER BY
    mg.strgroupdisplaycode
;
