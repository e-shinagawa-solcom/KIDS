/*
 *    概要：「会社コード」から「表示会社コード」と「表示会社名」を取得
 *    対象：金型移動依頼工場マスタ
 *
 *    プレースホルダ：
 *        $1：会社コード
 *
 */
SELECT
      strfactorydisplaycode companydisplaycode
    , strfactorydisplayname companydisplayname
FROM
    m_moldmoverequestfactory
WHERE
    strfactorydisplaycode = $1
    AND btynondisplayflag = false
    AND bytinvalidflag = false
;
