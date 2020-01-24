/*
 *    概要：「会社コード」と「会社名」から属性が「2:」又は「99:その他」の「締め日数」を取得する
 *    対象：会社マスタ 締め日マスタ
 *
 *    プレースホルダ：
 *        $1：会社コード
 *        $2：会社名称
 *
 */
SELECT
      mc.strcompanydisplaycode customerdisplaycode
    , mc.strcompanydisplayname customerdisplayname
    , mcd.lngclosedday
FROM
    m_company mc
INNER JOIN
    m_closedday mcd
  ON
    mcd.lngcloseddaycode = mc.lngcloseddaycode
WHERE
    mc.strcompanydisplaycode = $1
AND mc.bytcompanydisplayflag = true
;