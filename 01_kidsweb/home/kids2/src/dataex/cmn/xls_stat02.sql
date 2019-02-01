SELECT
	to_char( mp.dtmdeliverylimitdate,'YYYY/MM/DD' ) as dtmdeliverylimitdate,	/* 納期（製品マスタ） */
	to_char( mst1.dtmdeliverydate,'YYYY/MM' ) as dtmdeliverydate2,			/* 納品日2（受注マスタ） */
	mst1.strSalesCode,								/* 売上コード */
	to_char( mst1.dtmAppropriationDate,'YYYY/MM/dd' ) as dtmAppropriationDate,	/* 計上日 */
	mst1.strCustomerReceiveCode,							/* 顧客受注番号 */
	mst1.lngmonetaryratecode,							/* レートコード */
	mst1.lngmonetaryunitcode,							/* 通貨コード */
	mst1.curConversionRate1,							/* レート（TTM）*/
	mst1.curConversionRate2,							/* レート（社内）*/
	mp.lngcategorycode,								/* カテゴリーコード */
	mc.strcategoryname,								/* カテゴリー名称 */
	mp.lngInchargeGroupCode,							/* 担当部門コード */
	mg.strGroupDisplayCode,								/* 部門表示コード */
	mg.strGroupDisplayName,								/* 部門表示名称 */
	mst1.lngsalesclasscode,								/* 売上区分 */
	ms.strsalesclassname,								/* 売上区分名称 */
	to_char( mst1.dtmdeliverydate,'YYYY/MM/DD' ) as dtmdeliverydate,		/* 納品日（売上マスタ） */
	mst1.curproductpriceSales,							/* 製品価格（売上明細） */
	mst1.lngProductUnitCode,							/* 製品単位コード */
	mst1.lngproductquantity,							/* 数量（売上マスタ） */
	mst1.cursubtotalprice,								/*明細行売上合計金額*/
	mp.strproductcode,								/* 製品コード */
	mp.strproductname,								/* 製品名称 */
	mp.lngproductionunitcode,							/* 予定数単位コード*/
	mp.lngProductionQuantity,							/* 生産予定数 */
	/* 製品原価＠ */
/*	mst2.curmembercost,								/* 部材費合計金額 
	mst2.curmanufacturingcost,							/* 総製造費用 
*/	
	coalesce(mst2.curmembercost, 0) as curmembercost,				/* 部材費合計金額 */
	coalesce(mst2.curmanufacturingcost, 0) as curmanufacturingcost,			/* 総製造費用 */

	mp.lngcartonquantity,								/* カートン入数 */
	mp.curproductprice,								/* 商品価格-納価（商品マスタ） */
	mst2.cursalesamount,								/* 予定売上高 */
	mp.curretailprice								/* 小売価格-上代 */

FROM
	m_product mp
	LEFT JOIN m_category mc ON mp.lngcategorycode = mc.lngcategorycode
	LEFT JOIN m_group mg ON mp.lnginchargegroupcode = mg.lnggroupcode
	,
	(
		/* 受注マスタ・受注明細テーブルより有効なデータを取得 */
		SELECT
			ms.lngSalesno,
			ms.strSalesCode,				/* 売上コード */
			ms.lngmonetaryunitcode,				/* 通貨コード */
			ms.dtmAppropriationDate,			/* 計上日 */
			mr.strCustomerReceiveCode,			/* 顧客受注番号 */
			ms.lngmonetaryratecode,				/* レートコード */
			mstmm.curConversionRate1,			/* レート（TTM） */
			mstmm.curConversionRate2,			/* レート（社内） */
			trd.lngSalesDetailno,				/* 売上明細番号 */
			trd.strproductcode,				/* 製品コード */
			trd.lngsalesclasscode,				/* 売上区分 */
			trd.dtmdeliverydate,				/* 納期 */
			trd.curproductprice as curproductpriceSales,	/* 単価 */
			trd.lngProductUnitCode, 			/* 単位 */
			trd.lngproductquantity,				/* 数量 */
			trd.cursubtotalprice				/*明細行売上合計金額*/
			
		FROM
		m_Sales ms
		LEFT JOIN t_SalesDetail trd ON ms.lngSalesNo = trd.lngSalesNo
		LEFT JOIN m_Receive mr on mr.lngreceiveno = trd.lngreceiveno	/* 顧客受注番号取得の為 */
		LEFT JOIN m_group mg ON ms.lnggroupcode = mg.lnggroupcode
		LEFT JOIN
				(	/* 社内レート、TTMレートを同行で取得する */
					SELECT DISTINCT
						mm1.lngmonetaryratecode as lngmonetaryratecode1,
						mm1.lngmonetaryunitcode as lngmonetaryunitcode1,
						mm1.dtmapplystartdate   as dtmapplystartdate1,
						mm1.dtmapplyenddate     as dtmapplyenddate1,
						mm1.curConversionRate   as curConversionRate1,
						mm2.lngmonetaryratecode as lngmonetaryratecode2,
						mm2.lngmonetaryunitcode as lngmonetaryunitcode2,
						mm2.dtmapplystartdate   as dtmapplystartdate2,
						mm2.dtmapplyenddate     as dtmapplyenddate2,
						mm2.curConversionRate as curConversionRate2
					FROM
					(	/* TTMレート */
						SELECT DISTINCT
							mmr.lngmonetaryratecode,
							mmr.lngmonetaryunitcode,
							mmr.dtmapplystartdate,
							mmr.dtmapplyenddate,
							mmr.curConversionRate
						FROM
							m_MonetaryRate mmr
						JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode
						WHERE
							mmr.lngmonetaryratecode = 1
					) mm1
					,
					(	/* 社内レート */
						SELECT distinct
							mmr.lngmonetaryratecode,
							mmr.lngmonetaryunitcode,
							mmr.dtmapplystartdate,
							mmr.dtmapplyenddate,
							mmr.curConversionRate
						FROM
							m_MonetaryRate mmr
						JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode
						WHERE
							mmr.lngmonetaryratecode = 2
					) mm2

				) mstmm on /* 受注マスタの計上日でリンク */
						mstmm.dtmapplystartdate1 <= ms.dtmappropriationdate
						AND mstmm.dtmapplyenddate1 >= ms.dtmappropriationdate
						AND mstmm.dtmapplystartdate2 <= ms.dtmappropriationdate
						AND mstmm.dtmapplyenddate2 >= ms.dtmappropriationdate
						AND mstmm.lngmonetaryunitcode1 = ms.lngmonetaryunitcode
						AND mstmm.lngmonetaryunitcode2 = ms.lngmonetaryunitcode
		,
		(
			SELECT r1.lngSalesNo, r1.strSalescode, r1.lngRevisionNo
			FROM m_Sales r1
			WHERE
			r1.strSalesCode
				NOT IN 
				(
					SELECT r2.strSalesCode  FROM m_Sales r2
					where r2.lngRevisionNo < 0
					group by r2.strSalesCode
				)
			AND r1.lngRevisionNo = (
										SELECT MAX( rr1.lngRevisionNo ) FROM m_Sales rr1 
											WHERE rr1.strSalesCode = r1.strSalesCode 
											AND rr1.bytInvalidFlag = false
									)
		) mstrue
		WHERE ms.lngSalesNo = mstrue.lngSalesNo and ms.lngRevisionNo = mstrue.lngRevisionNo
		AND ms.bytInvalidFlag = FALSE
		AND ms.lngRevisionNo >= 0
		/* 売上マスタ・計上日に対する条件設定 */
		AND to_date( ms.dtmAppropriationDate::text,'YYYY/MM/DD' ) >= to_date( '_%dtmAppropriationDateFrom%_'::text, 'YYYY/MM/DD' ) /* FROM */
		AND to_date( ms.dtmAppropriationDate::text,'YYYY/MM/DD' ) <= to_date( '_%dtmAppropriationDateTo%_'::text, 'YYYY/MM/DD' ) /* TO */
	) mst1
	LEFT JOIN m_salesclass ms ON mst1.lngsalesclasscode = ms.lngsalesclasscode
	LEFT JOIN
		(
			/* 見積原価マスタ・見積原価明細テーブルより有効なデータを取得 */
			SELECT
				me.strproductcode,		/* 製品コード */
		 		me.curfixedcost,		/* 固定費合計金額 */
				me.curmembercost,		/* 部材費合計金額 */
				me.curmanufacturingcost,	/* 総製造費用  */
				me.cursalesamount		/* 予定売上高 */
			FROM
			m_estimate me
			LEFT JOIN t_estimatedetail ted ON me.lngestimateno = ted.lngestimateno
			,
			(
				SELECT r1.lngestimateno, r1.lngrevisionno, r1.strproductcode
				FROM m_Estimate r1
				WHERE
				r1.lngestimateno
					NOT IN 
					(
						SELECT r2.lngestimateno  FROM m_Estimate r2
						where r2.lngrevisionno < 0
						group by r2.lngestimateno
					)
				AND r1.lngrevisionno = (
											SELECT MAX( rr1.lngRevisionNo ) FROM m_Estimate rr1 
												WHERE rr1.lngEstimateNo = r1.lngEstimateNo 
												AND rr1.bytInvalidFlag = false
							)
			) metrue
			WHERE
			me.lngestimateno = metrue.lngestimateno
			AND me.lngrevisionno = metrue.lngrevisionno
			AND me.bytinvalidflag = FALSE
			AND me.bytdecisionflag = TRUE
			GROUP BY
				me.strproductcode,		/* 製品コード */
		 		me.curfixedcost,		/* 固定費合計金額 */
				me.curmembercost,		/* 部材費合計金額 */
				me.curmanufacturingcost,	/* 総製造費用  */
				me.cursalesamount		/* 予定売上高 */
		) mst2
		ON
		mst1.strproductcode = mst2.strproductcode

WHERE
	mst1.strproductcode = mp.strproductcode
AND
	CASE WHEN '0' = '_%lngInchargeGroupCode%_'
	THEN TRUE
	ELSE mp.lnginchargegroupcode = _%lngInchargeGroupCode%_
	END
AND
	CASE WHEN '0' = '_%lngSalesClassCode%_'
	THEN TRUE
	ELSE mst1.lngsalesclasscode in (_%lngSalesClassCode%_)
	END
ORDER BY 
mp.strproductcode, mst1.lngsalesclasscode