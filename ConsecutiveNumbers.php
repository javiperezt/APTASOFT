<?php
// Class for generating consecutive numbers on invoices and budgets

class ConsecutiveNumbers{
    //$start = 0002 $count=2 $digits=4 -> 0003
    function generateNumbers ($start, $count, $digits)
    {
        $result = array();
        for ($n = $start; $n < $start + $count; $n++) {
            $result[] = str_pad($n, $digits, "0", STR_PAD_LEFT);
        }
        return $result[1];
    }

    function getCurrentYear()
    {
        $year = substr(date("Y"), -2);
        return $year;
    }

    function getPref ($documentType){
        // Presupuesto
        if($documentType==1){ $pref = "E";}
        // Factura
        if($documentType==2){$pref="F";}

        return $pref;
    }
}

?>