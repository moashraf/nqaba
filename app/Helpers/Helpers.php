<?php

namespace App\Helpers;

class Helpers
{
    public function dateParser($date){
        $dateJSON ='{"date": "'.$date.'"}';

        $timestamp = json_decode($dateJSON, true);

        $timestamp = preg_replace( '/[^0-9]/', '', $timestamp['date']);

        $parsedDate = date("Y/m/d", $timestamp / 1000);

        return $parsedDate;
    }
}