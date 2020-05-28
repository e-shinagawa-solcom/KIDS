/*
 *    概要：「会社コード」と「会社名」から「表示会社コード」と「表示会社名」を取得
 *    対象：金型移動依頼工場マスタ
 *
 *    プレースホルダ：
 *        $1：会社コード
 *        $2：会社名称
 *
 */
SELECT
      strfactorydisplaycode companydisplaycode
    , strfactorydisplayname companydisplayname
FROM
    m_moldmoverequestfactory
WHERE
    strfactorydisplaycode = $1
AND sf_translate_case(strfactorydisplayname) LIKE '%' || sf_translate_case($2) || '%'
AND btynondisplayflag = false
AND bytinvalidflag = false
;
