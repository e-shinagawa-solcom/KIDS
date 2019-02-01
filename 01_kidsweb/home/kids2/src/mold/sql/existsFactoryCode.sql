/*
 *    ���ס�ɽ����ҥ����ɤ���ҥޥ������¸�ߤ��뤫�����å���Ԥ�
 *         °������4:����׵ڤӡ�99:����¾�פ��ġ�ɽ���ե饰�פ����Τ�Τ��оݤȤ��롣
 *    �ץ졼���ۥ����
 *        $1��ɽ����ҥ�����
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
