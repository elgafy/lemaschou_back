<?php
use Illuminate\Support\Facades\Config;

function success()
{
    return true;
}

function error()
{
    return false;
}

function expired()
{
    return false;
}


function failed()
{
    return false;
}

function getLang()
{
    $lang = (Request()->hasHeader('lang')) ? Request()->header('lang') : 'en';
    return $lang;
}
