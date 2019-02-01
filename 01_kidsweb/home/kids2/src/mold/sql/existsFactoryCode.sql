/*
 *    概要：表示会社コードが会社マスタ上に存在するかチェックを行う
 *         属性が「4:工場」及び「99:その他」かつ「表示フラグ」が真のものを対象とする。
 *    プレースホルダ：
 *        $1：表示会社コード
 */
SELECT
      mc.lngcompanycode as companycode
    , mc.strcompanydisplaycode as customerdisplaycode
    , mc.strcompanydisplayname as customerdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mc.strcompanydisplaycode = $1
AND mar.lngattributecode in (3, 4, 99)
;
