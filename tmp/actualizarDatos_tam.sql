use tam_erp_001;
BEGIN;

select "/////////////////RH_TITULAR" d;

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address3` = '';

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address3` = `rh_titular`.`address4`;

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address4` = '';

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address4` = `rh_titular`.`address5`;

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address5` = '';

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address5` = `rh_titular`.`address3`;

update `tam_erp_001`.`rh_titular`
set `rh_titular`.`address3` = '';

select "/////////////////CUSTBRANCH" d;
/**/
select * from custbranch
where braddress3 <> '';

update `tam_erp_001`.`custbranch`
set `custbranch`.`braddress3` = `custbranch`.`braddress4`;

update `tam_erp_001`.`custbranch`
set `custbranch`.`braddress4` = '';

update `tam_erp_001`.`custbranch`
set `custbranch`.`braddress4` = `custbranch`.`braddress5`;

update `tam_erp_001`.`custbranch`
set `custbranch`.`braddress5` = '';

update `tam_erp_001`.`custbranch`
set `custbranch`.`braddress5` = `custbranch`.`braddress3`;

update `tam_erp_001`.`custbranch`
set `custbranch`.`braddress3` = '';

select "/////////////////DEBTORSMASTER" d;

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address3` = '';

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address3` = `debtorsmaster`.`address4`;

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address4` = '';

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address4` = `debtorsmaster`.`address5`;

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address5` = '';

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address5` = `debtorsmaster`.`address3`;

update `tam_erp_001`.`debtorsmaster`
set `debtorsmaster`.`address3` = '';

commit;
