/*
	概要：製品検索
	対象：共通サブウィンドウ
	作成：斎藤和志
	備考：製品コード＋名称から一致する「コード＋名称」一覧を生成
	
		_%strFormValue0%_：製品コード
		_%strFormValue1%_：製品名称
		_%strFormValue2%_：入力者コード（ログインユーザー）

*/
SELECT   a.strproductcode, 
	(
		CASE WHEN a.strproductname IS NULL THEN (a.strproductcode || ' ' || a.strproductnamenull)
			ELSE (a.strproductcode || ' ' || a.strproductname)
		END
	)
	, a.strproductname, '#FF3300'
FROM
(
	SELECT	mp.strproductcode			AS strproductcode
			,mp.strproductname			AS strproductname
			,NULL						AS strproductnamenull
			,mp.lnginchargeusercode		as lnginchargeusercode
	FROM m_product mp
	WHERE mp.bytinvalidflag = false
		AND (mp.strproductcode LIKE '%_%strFormValue0%_%')
		AND (mp.strproductname LIKE '%_%strFormValue1%_%')
		and ( mp.lngproductstatuscode = 0 or mp.lngproductstatuscode is null) /* WF申請中 */
	GROUP BY mp.strproductcode, mp.strproductname, mp.lnginchargeusercode
	UNION
	SELECT	mp.strproductcode			AS strproductcode
			,NULL						AS strproductname
			,'（製品名称が空です）'		AS strproductnamenull
			,mp.lnginchargeusercode		as lnginchargeusercode
	FROM m_product mp
	WHERE mp.bytinvalidflag = false
		AND (mp.strproductname IS NULL)
	GROUP BY mp.strproductcode, mp.strproductname, mp.lnginchargeusercode

) AS a
WHERE
	CASE
		WHEN
			(SELECT count(*) FROM m_product WHERE strproductcode LIKE '%_%strFormValue0%_%') = (SELECT count(*) FROM m_product WHERE strproductcode LIKE '%%')
			AND
			(SELECT count(*) FROM m_product WHERE strproductname LIKE '%_%strFormValue1%_%') = (SELECT count(*) FROM m_product WHERE strproductname LIKE '%%')
		THEN (a.strproductname IS NOT NULL OR a.strproductnamenull IS NOT NULL)
		ELSE (a.strproductname IS NOT NULL)
	END
ORDER BY a.strproductcode
