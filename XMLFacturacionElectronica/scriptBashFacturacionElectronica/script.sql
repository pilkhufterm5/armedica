-- use prueba_sat;
use cfd;
create table if not exists sat__csd(no_serie varchar(20),fec_inicial_cert timestamp,fec_final_cert timestamp,rfc varchar(20),edo_certificado varchar(1)) ENGINE=innodb;
create table if not exists sat__folios_cfd(rfc varchar(20),no_aprobacion int, ano_aprobacion int,serie varchar(20),folio_inicial int, folio_final int) ENGINE=innodb;

-- investigar como incluir los delete en la transaccion de manera que si el load data infile falla, no se borren los registros anteriores
delete from sat__csd;
delete from sat__folios_cfd;
start transaction;
load data infile '/tmp/CSD.txt' into table sat__csd fields terminated by '|' lines terminated by '\n' ignore 1 lines;
load data infile '/tmp/FoliosCFD.txt' into table sat__folios_cfd fields terminated by '|' lines terminated by '\n' ignore 1 lines;
commit;
