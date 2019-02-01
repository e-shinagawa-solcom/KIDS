SELECT
      mu.lngusercode as usercode
    , mu.struserdisplaycode as userdisplaycode
FROM
    m_user mu
WHERE
    mu.struserdisplaycode = $1
AND mu.bytuserdisplayflag = true
;
