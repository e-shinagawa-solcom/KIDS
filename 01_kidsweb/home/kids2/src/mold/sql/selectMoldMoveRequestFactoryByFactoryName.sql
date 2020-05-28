/*
 *    概要：「会社名」から「表示会社コード」と「表示会社名」を取得
 *    対象：金型移動依頼工場マスタ
 *
 *    プレースホルダ：
 *        $1：会社名
 *
 */
SELECT
      strfactorydisplaycode companydisplaycode
    , strfactorydisplayname companydisplayname
FROM
    m_moldmoverequestfactory 
WHERE
    sf_translate_case(strfactorydisplayname) LIKE '%' || sf_translate_case($1) || '%'
    AND btynondisplayflag = false
    AND bytinvalidflag = false
ORDER BY
    strfactorydisplaycode
;
