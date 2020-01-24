/*
 *    概要：「会社コード」と「会社名」から属性が「3:仕入先」又は「99:その他」の「表示会社コード」と「表示会社名」を取得
 *    対象：会社マスタ
 *
 *    プレースホルダ：
 *        $1：会社コード
 *        $2：会社名称
 *
 */
SELECT
      mc.strcompanydisplaycode supplierdisplaycode
    , mc.strcompanydisplayname supplierdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mc.strcompanydisplaycode = $1
AND mc.strcompanydisplayname LIKE '%' || $2 || '%'
AND mar.lngattributecode in (3, 99)
;
