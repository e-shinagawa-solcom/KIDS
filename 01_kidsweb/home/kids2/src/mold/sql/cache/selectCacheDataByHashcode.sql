SELECT
      tc.chacheid
    , tc.hashcode
    , tc.serializeddata
    , tc.created
    , tc.createby
    , tc.updated
    , tc.updateby
    , tc.version
    , tc.deleteflag
FROM
    t_cache tc
WHERE
    tc.hashcode = $1
;
