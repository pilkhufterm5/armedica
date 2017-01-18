<?php

    //Crear Factura Electronica
    try{
        include('XMLFacturacionElectronica/index.php');
        if(!DB_query('commit',$db,'','',false,false)){
            throw new Exception('Error al efectuar el commit', 1);
        }
    }
    catch(Exception $exception){
            $msg = $exception->getMessage();
            if($exception->getCode()==1){
                $error = mysql_error();
                $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
            }

            //rollback
            if(!DB_query('rollback',$db,'','',false,false)){
                $msg .= 'Error al efectuar el rollback';
            }
            //termina rollback

            echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $msg . '<p></div>';
            include('includes/footer.inc');
            exit;
        //}
    }
    //Termina Crear Factura Electronica
?>
