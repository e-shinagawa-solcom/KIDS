drop table if exists public.m_slip;
create table public.m_slip(
    lngslipno integer not null
   ,lngrevisionno integer not null
   ,strslipcode text
   ,lngsalesno integer
   ,lngcustomercode integer
   ,strcustomercompanyname text
   ,strcustomername text
   ,strcustomeraddress1 text
   ,strcustomeraddress2 text
   ,strcustomeraddress3 text
   ,strcustomeraddress4 text
   ,strcustomerphoneno text
   ,strcustomerfaxno text
   ,strcustomerusername text
   ,strshippercode text
   ,dtmdeliverydate date
   ,lngdeliveryplacecode integer
   ,strdeliveryplacename text
   ,strdeliveryplaceusername text
   ,lngsalesclasscode integer
   ,lngpaymentmethodcode integer
   ,dtmpaymentlimit date
   ,lngtaxclasscode integer
   ,strtaxclassname text
   ,curtax numeric(14,4)
   ,lngusercode integer
   ,strusername text
   ,curtotalprice numeric(14,4)
   ,lngmonetaryunitcode integer
   ,strmonetaryunitsign text
   ,dtminsertdate timestamp without time zone
   ,lnginsertusercode integer
   ,strinsertusername text
   ,strnote text
   ,lngprintcount integer
   ,bytinvalidflag boolean
   ,primary key(lngslipno,lngrevisionno)
);

comment on table public.m_slip is '納品伝票マスタ';
comment on column m_slip.lngslipno is '納品伝票番号';
comment on column m_slip.lngrevisionno is 'リビジョン番号';
comment on column m_slip.strslipcode is '納品伝票コード';
comment on column m_slip.lngsalesno is '売上番号';
comment on column m_slip.lngcustomercode is '顧客コード';
comment on column m_slip.strcustomercompanyname is '顧客社名';
comment on column m_slip.strcustomername is '顧客名';
comment on column m_slip.strcustomeraddress1 is '顧客住所1';
comment on column m_slip.strcustomeraddress2 is '顧客住所2';
comment on column m_slip.strcustomeraddress3 is '顧客住所3';
comment on column m_slip.strcustomeraddress4 is '顧客住所4';
comment on column m_slip.strcustomerphoneno is '顧客電話番号';
comment on column m_slip.strcustomerfaxno is '顧客FAX番号';
comment on column m_slip.strcustomerusername is '顧客担当者名';
comment on column m_slip.strshippercode is '仕入先コード（出荷者）';
comment on column m_slip.dtmdeliverydate is '納品日';
comment on column m_slip.lngdeliveryplacecode is '納品場所コード';
comment on column m_slip.strdeliveryplacename is '納品場所名';
comment on column m_slip.strdeliveryplaceusername is '納品場所担当者名';
comment on column m_slip.lngsalesclasscode is '売上区分コード';
comment on column m_slip.lngpaymentmethodcode is '支払方法コード';
comment on column m_slip.dtmpaymentlimit is '支払期限';
comment on column m_slip.lngtaxclasscode is '課税区分コード';
comment on column m_slip.strtaxclassname is '課税区分';
comment on column m_slip.curtax is '消費税率';
comment on column m_slip.lngusercode is '担当者コード';
comment on column m_slip.strusername is '担当者名';
comment on column m_slip.curtotalprice is '合計金額';
comment on column m_slip.lngmonetaryunitcode is '通貨単位コード';
comment on column m_slip.strmonetaryunitsign is '通貨単位';
comment on column m_slip.dtminsertdate is '作成日';
comment on column m_slip.lnginsertusercode is '入力者コード';
comment on column m_slip.strinsertusername is '入力者名';
comment on column m_slip.strnote is '備考';
comment on column m_slip.lngprintcount is '印刷回数';
comment on column m_slip.bytinvalidflag is '無効フラグ';

DROP INDEX IF EXISTS m_slip_pkey;
CREATE UNIQUE INDEX m_slip_pkey on m_slip USING btree(lngslipno ,lngrevisionno);
