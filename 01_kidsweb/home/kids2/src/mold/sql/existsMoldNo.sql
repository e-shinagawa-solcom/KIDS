/*
 *    ���ס��ⷿNO���ⷿ�ޥ������¸�ߤ��뤫�����å���Ԥ�
 *         �ֺ���ե饰�פ����Τ�Τ��оݤȤ��롣
 *    �ץ졼���ۥ����
 *        $1���ⷿNO
 */
SELECT
      mm.moldno as moldno
FROM
    m_mold mm
WHERE
    mm.moldno = $1
AND mm.deleteflag = false
;
