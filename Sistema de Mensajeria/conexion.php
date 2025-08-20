<?php
function conectar (){

 try{
        $ser="localhost";
        $usr="root";
        $ps="";
        $bd="BdSanchezDariotp";

        $c= new Mysqli($ser, $usr, $ps, $bd);
        return $c;
        
    }catch(Throwable $e){
        echo "<br>Problemas de servidor";
        /* <ahref="index.php">Aceptar</a><?php*/
        exit();
    }




}
?>