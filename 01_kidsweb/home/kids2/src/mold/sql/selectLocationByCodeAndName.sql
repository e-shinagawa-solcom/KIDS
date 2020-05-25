/*
 *    概要：「会社コード」と「会社名」から属性が「5:納品場所・倉庫」又は「99:その他」の「表示会社コード」と「表示会社名」を取得
 *    対象：会社マスタ
 *
 *    プレースホルダ：
 *        $1：会社コード
 *        $2：会社名称
 *
 */
SELECT
      mc.strcompanydisplaycode locationdisplaycode
    , mc.strcompanydisplayname locationdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mc.strcompanydisplaycode = $1
AND sf_translate_case(mc.strcompanydisplayname) LIKE '%' || sf_translate_case($2) || '%'
AND mar.lngattributecode in (5, 99)
;
