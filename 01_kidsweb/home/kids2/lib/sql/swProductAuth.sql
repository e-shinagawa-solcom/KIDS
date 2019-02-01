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
			(SELECT count(*) FROM m_product WHERE strproductcode LIKE '%_%strFormValue0%_%') = (SELECT count(*) FROM m_product WHERE strproductcode LIKE '%_%strFormValue0%_%')
			AND
			(SELECT count(*) FROM m_product WHERE strproductname LIKE '%_%strFormValue1%_%') = (SELECT count(*) FROM m_product WHERE strproductname LIKE '%_%strFormValue1%_%')
		THEN (a.strproductname IS NOT NULL OR a.strproductnamenull IS NOT NULL)
		ELSE (a.strproductname IS NOT NULL)
	END
	/* 製品担当者に対し、入力者 と その入力者が属するグループのマネージャー以上が一致 */
	and _%strFormValue2%_ in
	(
		select
			mu1.lngusercode
		from
			m_user mu1
		,m_grouprelation mgr1
		,
		(
			select
				mu.lngusercode
				,mg.lnggroupcode
			from
				m_user mu
				left join m_grouprelation mgr
					on mgr.lngusercode = mu.lngusercode
					/* and mgr.bytdefaultflag = true */
					left join m_group mg
						on mg.lnggroupcode = mgr.lnggroupcode
			where
			mu.lngusercode = a.lnginchargeusercode /* 製品担当者 */
		) as mst1
		where
			mgr1.lnggroupcode = mst1.lnggroupcode
			/* and mgr1.bytdefaultflag = true */
			and mu1.bytinvalidflag = false
			and mu1.lngusercode = mgr1.lngusercode
/*			and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode )
*/
/*
39期事務3人対応のため
*/			
			and (mu1.lngauthoritygroupcode <= 4 or mu1.lngusercode = mst1.lngUserCode or mu1.lngusercode in ('15','29','242','343'))
	)
ORDER BY a.strproductcode
