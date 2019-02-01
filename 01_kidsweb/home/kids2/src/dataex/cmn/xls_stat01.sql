SELECT
	to_char( mp.dtmdeliverylimitdate,'YYYY/MM/DD' ) as dtmdeliverylimitdate,	/* 納期（製品マスタ） */
	to_char( mst1.dtmdeliverydate,'YYYY/MM' ) as dtmdeliverydate2,			/* 納品日2（受注マスタ） */
	mst1.strreceivecode,								/* 受注コード */
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
	to_char( mst1.dtmdeliverydate,'YYYY/MM/DD' ) as dtmdeliverydate,		/* 納品日（受注マスタ） */
	mst1.curproductpriceReceive,							/* 製品価格（受注明細） */
	mst1.lngProductUnitCode,							/* 製品単位コード */
	mst1.lngproductquantity,							/* 数量（受注マスタ） */
	mst1.cursubtotalprice,								/*明細行売上合計金額*/
	mp.strproductcode,								/* 製品コード */
	mp.strproductname,								/* 製品名称 */
	mp.lngproductionunitcode,							/* 生産予定数単位コード*/
	mp.lngProductionQuantity,							/* 生産予定数 */
	/* 製品原価＠ */
	coalesce(mst2.curmembercost, 0) as curmembercost,				/* 部材費合計金額 */
	coalesce(mst2.curmanufacturingcost, 0) as curmanufacturingcost,			/* 総製造費用 */
	mp.lngcartonquantity,								/* カートン入数 */
	mp.curproductprice,								/* 商品価格-納価（商品マスタ） */
	coalesce(mst2.cursalesamount, 0) as cursalesamount,				/* 予定売上高 */
	mp.curretailprice								/* 小売価格-上代 */

FROM
	m_product mp
	LEFT JOIN m_category mc ON mp.lngcategorycode = mc.lngcategorycode
	LEFT JOIN m_group mg ON mp.lnginchargegroupcode = mg.lnggroupcode
	,
	(
		/* 受注マスタ・受注明細テーブルより有効なデータを取得 */
		SELECT
			mr.lngreceiveno,
			mr.strreceivecode,				/* 受注コード */
			mr.strCustomerReceiveCode,			/* 顧客受注番号 */
			mr.lngmonetaryunitcode,				/* 通貨コード */
			mr.lngmonetaryratecode,				/* レートコード */
			mstmm.curConversionRate1,			/* レート（TTM） */
			mstmm.curConversionRate2,			/* レート（社内） */
			trd.lngreceivedetailno,				/* 受注明細番号 */
			trd.strproductcode,				/* 製品コード */
			trd.lngsalesclasscode,				/* 売上区分 */
			trd.dtmdeliverydate,				/* 納期 */
			trd.curproductprice as curproductpriceReceive,	/* 単価 */
			trd.lngProductUnitCode, 			/* 単位 */
			trd.lngproductquantity,				/* 数量 */
			trd.cursubtotalprice				/*明細行売上合計金額*/
			
		FROM
		m_receive mr
		LEFT JOIN t_receivedetail trd ON mr.lngreceiveno = trd.lngreceiveno
		LEFT JOIN m_group mg ON mr.lnggroupcode = mg.lnggroupcode
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
						mstmm.dtmapplystartdate1 <= mr.dtmappropriationdate
						AND mstmm.dtmapplyenddate1 >= mr.dtmappropriationdate
						AND mstmm.dtmapplystartdate2 <= mr.dtmappropriationdate
						AND mstmm.dtmapplyenddate2 >= mr.dtmappropriationdate
						AND mstmm.lngmonetaryunitcode1 = mr.lngmonetaryunitcode
						AND mstmm.lngmonetaryunitcode2 = mr.lngmonetaryunitcode
		,
		(
			SELECT r1.lngreceiveno, r1.strreceivecode, r1.lngrevisionno
			FROM m_Receive r1
			WHERE
			r1.strreceivecode
				NOT IN 
				(
					SELECT r2.strreceivecode  FROM m_Receive r2
					where r2.lngrevisionno < 0
					group by r2.strreceivecode
				)
			AND r1.lngrevisionno = (
						SELECT MAX( rr1.lngRevisionNo ) FROM m_Receive rr1 
							WHERE rr1.strReceiveCode = r1.strReceiveCode 
							AND rr1.bytInvalidFlag = false
							AND rr1.strReviseCode = (
										SELECT MAX( rr2.strReviseCode ) FROM m_Receive rr2 
											WHERE rr2.strReceiveCode = rr1.strReceiveCode 
											AND rr2.bytInvalidFlag = false
										)
						)
		) mrtrue
		WHERE mr.lngreceiveno = mrtrue.lngreceiveno and mr.lngrevisionno = mrtrue.lngrevisionno
		AND mr.bytinvalidflag = FALSE
		AND mr.lngrevisionno >= 0
		/* 受注明細・納品日に対する条件設定 */
		AND to_date( trd.dtmDeliveryDate::text,'YYYY/MM/DD' ) >= to_date( '_%dtmAppropriationDateFrom%_'::text, 'YYYY/MM/DD' ) /* FROM */
		AND to_date( trd.dtmDeliveryDate::text,'YYYY/MM/DD' ) <= to_date( '_%dtmAppropriationDateTo%_'::text, 'YYYY/MM/DD' ) /* TO */
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
	mp.strproductcode, mst1.lngsalesclasscode, dtmdeliverydate