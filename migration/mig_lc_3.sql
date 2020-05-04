\c ackids
\encoding UTF8;
update t_aclcinfo set productrevisecd = '00' where productrevisecd is null;
update m_acsendinfo set sendfromname = 'From ： KUWAGATA /  ACCOUNTING DIV.';
update m_acbackcolorinfo set bytinvalidflag = true where strclrno = '0012';
update t_aclcinfo set currencyclass = 'USドル' where currencyclass = 'ＵＳドル';

\c kidscore2
\encoding UTF8;
drop table if exists m_sendinfo;
create table m_sendinfo(
    sendno character(4) not null
   ,sendfromname text
   ,sendfromfax text
   ,sendcarenote1 text
   ,sendcarenote2 text

   ,primary key(sendno)
);
comment on table m_sendinfo is '送付元マスタ';
comment on column m_sendinfo.sendno is '送付元番号';
comment on column m_sendinfo.sendfromname is '信用状送付元';
comment on column m_sendinfo.sendfromfax is '送付元ＦＡＸ';
comment on column m_sendinfo.sendcarenote1 is '信用状注意文１';
comment on column m_sendinfo.sendcarenote2 is '信用状注意文２';
ALTER TABLE t_lcinfo ADD COLUMN unreflectedflag boolean;
