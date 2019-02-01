SELECT
      mg.strgroupdisplaycode groupdisplaycode
    , mg.strgroupdisplayname groupdisplayname
FROM
    m_group mg
WHERE
    mg.bytgroupdisplayflag = true
ORDER BY
    mg.strgroupdisplaycode
;
