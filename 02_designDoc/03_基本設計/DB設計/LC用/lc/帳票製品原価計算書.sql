drop table if exists t_reportProductCostAccounts;
create table t_reportProductCostAccounts(
    strproductcd character(4) not null
   ,strproductname text
   ,strgroupcd character(8)
   ,strgroupname character(64)
   ,strgoodscd character(16)
   ,strtakeusercd character(8)
   ,strvendorcd character(8)
   ,strproductlastmonth character(8)
   ,cursalesnumber bigint
   ,cursalesunit character(8)
   ,cursalesmoney money
   ,curcarrystock money
   ,curstockpartsdomestic money
   ,curstockpartsimport money
   ,curstocktooldomestic money
   ,curindirectitoncharge money
   ,curshiponboard money
   ,curplstocktotal money
   ,curoutsideconversion money
   ,curbasemodeldomestic money
   ,curbasemodelimport money
   ,curchangeproductionexpenses money
   ,curcarrysalesprice money
   ,total money
   ,curworkinprocessnumber bigint
   ,curworkinprocessunit character(8)
   ,curworkinprocessprice money
   ,monthManagementCostTotal money
   ,managementProfit money
   ,fixedManagementExpenses money
   ,domesticSalesTotalProfit money
   ,generalManagementCost money
   ,domestictOperatingProfit money
   ,curexchangeprofit money
   ,operatingProfit money

   ,primary key(strproductcd)
);
comment on table t_reportProductCostAccounts is '帳票製品原価計算書';
comment on column t_reportProductCostAccounts.strproductcd is '製品コード';
comment on column t_reportProductCostAccounts.strproductname is '商品名';
comment on column t_reportProductCostAccounts.strgroupcd is '所属部門コード';
comment on column t_reportProductCostAccounts.strgroupname is '所属部門名称';
comment on column t_reportProductCostAccounts.strgoodscd is '商品番号';
comment on column t_reportProductCostAccounts.strtakeusercd is '営業担当コード';
comment on column t_reportProductCostAccounts.strvendorcd is '生産工場ベンダーコード';
comment on column t_reportProductCostAccounts.strproductlastmonth is '製品最終月';
comment on column t_reportProductCostAccounts.cursalesnumber is '製品売上数量';
comment on column t_reportProductCostAccounts.cursalesunit is '製品売上単位';
comment on column t_reportProductCostAccounts.cursalesmoney is '製品売上金額';
comment on column t_reportProductCostAccounts.curcarrystock is '繰越材料在庫';
comment on column t_reportProductCostAccounts.curstockpartsdomestic is '材料費パーツ国内直接';
comment on column t_reportProductCostAccounts.curstockpartsimport is '材料費パーツ輸入直接';
comment on column t_reportProductCostAccounts.curstocktooldomestic is '材料費ツール国内直接';
comment on column t_reportProductCostAccounts.curindirectitoncharge is 'チャージ間接';
comment on column t_reportProductCostAccounts.curshiponboard is '輸送中オンボード';
comment on column t_reportProductCostAccounts.curplstocktotal is 'ＰＬ材料費合計';
comment on column t_reportProductCostAccounts.curoutsideconversion is '外注加工費直接';
comment on column t_reportProductCostAccounts.curbasemodeldomestic is '金型国内直接';
comment on column t_reportProductCostAccounts.curbasemodelimport is '金型輸入直接';
comment on column t_reportProductCostAccounts.curchangeproductionexpenses is '変動製造経費';
comment on column t_reportProductCostAccounts.curcarrysalesprice is '繰越仕掛品';
comment on column t_reportProductCostAccounts.total is '合計';
comment on column t_reportProductCostAccounts.curworkinprocessnumber is '当月仕掛品数量';
comment on column t_reportProductCostAccounts.curworkinprocessunit is '当月仕掛品単位';
comment on column t_reportProductCostAccounts.curworkinprocessprice is '当月仕掛品在庫金額';
comment on column t_reportProductCostAccounts.monthManagementCostTotal is '当月管理原価合計';
comment on column t_reportProductCostAccounts.managementProfit is '管理利益';
comment on column t_reportProductCostAccounts.fixedManagementExpenses is '固定管理経費';
comment on column t_reportProductCostAccounts.domesticSalesTotalProfit is '社内売上総利益';
comment on column t_reportProductCostAccounts.generalManagementCost is '一般管理費';
comment on column t_reportProductCostAccounts.domestictOperatingProfit is '社内営業利益';
comment on column t_reportProductCostAccounts.curexchangeprofit is '為替差損益';
comment on column t_reportProductCostAccounts.operatingProfit is '営業利益';
