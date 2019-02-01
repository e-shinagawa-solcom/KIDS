/*
 *    概要：金型NOが金型マスタ上に存在するかチェックを行う
 *         「削除フラグ」が偽のものを対象とする。
 *    プレースホルダ：
 *        $1：金型NO
 */
SELECT
      mm.moldno as moldno
FROM
    m_mold mm
WHERE
    mm.moldno = $1
AND mm.deleteflag = false
;
