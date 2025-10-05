<?php


class Presupuestos
{

    function getTotalPresupuesto($id_presupuesto)
    {
        $q = mysqli_query($mysqli, "SELECT SUM(cantidad*total) AS x FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
        $result = mysqli_fetch_assoc($q);
        $var = round($result['x'], 2);
        return $var;
    }

    function getSubtotalPresupuesto($id_presupuesto)
    {
        $q = mysqli_query($mysqli, "SELECT SUM(cantidad*subtotal) AS x FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
        $result = mysqli_fetch_assoc($q);
        $var = round($result['x'], 2);
        return $var;
    }

    function getTotalPartida($id_presupuesto_partida)
    {
        $q = mysqli_query($mysqli, "SELECT SUM(cantidad*total) AS x FROM presupuestos_partidas where id=$id_presupuesto_partida");
        $result = mysqli_fetch_assoc($q);
        $var = $result['x'];
        return $var;
    }

    function getSubotalPartida($id_presupuesto_partida)
    {
        $q = mysqli_query($mysqli, "SELECT SUM(cantidad*subtotal) AS x FROM presupuestos_partidas where id=$id_presupuesto_partida");
        $result = mysqli_fetch_assoc($q);
        $var = $result['x'];
        return $var;
    }

    function getCosteByCategory($id_presupuesto_partida, $id_categoria)
    {
        $q = mysqli_query($mysqli, "SELECT SUM(cantidad*precio) AS x FROM presupuestos_subpartidas where id=$id_presupuesto_partida and id_categoria=$id_categoria");
        $result = mysqli_fetch_assoc($q);
        $var = $result['x'];
        return $var;
    }

}