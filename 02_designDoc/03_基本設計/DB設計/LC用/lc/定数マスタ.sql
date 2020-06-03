drop table if exists m_constant;
create table m_constant(
    constantCode1 character varying(50) not null
   ,constantCode2 character varying(50)
   ,value character varying(50)
   ,invalidFlag boolean

   ,primary key(constantCode1)
);
comment on table m_constant is '定数マスタ';
comment on column m_constant.constantCode1 is '定数コード1';
comment on column m_constant.constantCode2 is '定数コード2';
comment on column m_constant.value is '値';
comment on column m_constant.invalidFlag is '無効フラグ';
