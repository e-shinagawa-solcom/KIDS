/*
 *    概要：「会社名」から属性が「4:工場」又は「99:その他」の「表示会社コード」と「表示会社名」を取得
 *    対象：会社マスタ
 *
 *    プレースホルダ：
 *        $1：会社名
 *
 */
SELECT
      mc.strcompanydisplaycode companydisplaycode
    , mc.strcompanydisplayname companydisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mc.strcompanydisplayname LIKE '%' || $1 || '%'
AND mar.lngattributecode in (4, 99)
ORDER BY
    mc.strcompanydisplaycode
;
