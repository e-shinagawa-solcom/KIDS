/*
 *    ���ס���ɽ����ҥ����ɡפ���°������5:Ǽ�ʾ�ꡦ�Ҹˡ����ϡ�99:����¾�פΡ�ɽ�����̾�פ����
 *    �оݡ���ҥޥ���
 *
 *    �ץ졼���ۥ����
 *        $1����ҥ�����
 *
 */
SELECT
    mc.strcompanydisplayname locationdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
      mc.strcompanydisplaycode = $1
  AND mar.lngattributecode in (5, 99)
;