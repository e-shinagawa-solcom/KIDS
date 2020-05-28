/*
 *    概要：「表示会社コード」と「表示会社名」を取得
 *    対象：金型移動依頼工場マスタ
 *
 */
SELECT
      strfactorydisplaycode companydisplaycode
    , strfactorydisplayname companydisplayname
FROM
    m_moldmoverequestfactory
WHERE btynondisplayflag = false
    AND bytinvalidflag = false
ORDER BY
    strfactorydisplaycode
;
