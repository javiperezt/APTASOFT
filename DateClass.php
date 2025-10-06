<?php

class DateClass
{
    public function getUnixFromDate($formatedDate)
    {
        $var = new DateTime("$formatedDate", new DateTimeZone('Europe/Madrid'));
        $unixTimeDate = $var->getTimestamp();
        return $unixTimeDate;
    }

    public function getCurrentUnix()
    {
        $var1 = new DateTime('now', new DateTimeZone('Europe/Madrid'));
        $unixTime = $var1->getTimestamp();
        return $unixTime;
    }

    public function getFormatedDate($dateUnix, $format)
    {
        $var2 = new DateTime("@$dateUnix", new DateTimeZone('Europe/Madrid'));
        //Fecha formateada ($format="Y-m-d")
        $formatedTime = $var2->format("$format");
        return $formatedTime;
    }

    public function getSecondsFromFormatedHour($formatedHour)
    {
        sscanf($formatedHour, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
        return $time_seconds;
    }

    public function getFormatedHourFromSeconds($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
        $formated = array($hours, $minutes, $seconds);
        return $formated;
    }


}