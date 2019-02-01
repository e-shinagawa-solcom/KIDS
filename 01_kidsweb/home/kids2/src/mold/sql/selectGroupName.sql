SELECT
    mg.strgroupdisplayname groupdisplayname
FROM
    m_group mg
WHERE
      mg.strgroupdisplaycode = $1
;
