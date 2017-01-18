<?php

/**
*
*/
class SqlserverController extends Controller{

    public function actionIndex(){
        $MSCon = SQLServerWS::MSDBConect();
        ini_set('memory_limit', '128M');

        if(!empty($_POST['SQL'])){
            $Query = $_POST['SQL']['Query'];
            $SQLQuery = " {$Query} ";
            $Result = mssql_query($SQLQuery, $MSCon);
            while (($_2Result = mssql_fetch_assoc($Result))) {
                $_2GetResult[] = $_2Result;
            }

            if(!empty($_2GetResult)){
                $THeader = "";
                $THeader .= "<table id='Table_Result' class='table table-striped ' style='width:100%;' > <thead> <tr>";
                $NumCols = 0;
                foreach($_2GetResult[0] as $Name => $Value){
                    $THeader .= "<th>{$Name}</th>";
                    $NumCols ++;
                }
                $THeader .= "</tr></thead>";
                $THeader .= "<tbody>";
                $THeader .= "[[BodyContent]]";
                $THeader .= "</tbody>";
                $THeader .= "</table >";

                $BodyContent = "";
                foreach ($_2GetResult as $_Data) {
                    $BodyContent .= "<tr>";
                    foreach ($_Data as $_2key => $_2value) {
                        $BodyContent .= "<td>{$_2value}</td>";
                    }
                    $BodyContent .= "</tr>";
                }
                $Table = str_replace("[[BodyContent]]", $BodyContent, $THeader);
            }
            echo $Table;
            return;
        }

        $GetTables = mssql_query("SELECT TABLE_SCHEMA + '.' + TABLE_NAME, *
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_TYPE = 'BASE TABLE'
                    ORDER BY TABLE_SCHEMA + '.' + TABLE_NAME", $MSCon);
        while (($_2Tables = mssql_fetch_assoc($GetTables))) {
            $Tables[] = $_2Tables;
        }
        $this->render('index',array('Tables' => $Tables));
    }


}
