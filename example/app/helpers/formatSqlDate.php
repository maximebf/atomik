<?php

function formatSqlDate($date)
{
    return date('m/d/Y', strtotime($date));
}
