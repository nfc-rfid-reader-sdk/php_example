<?php
//------------------------------------------------------------------------------
function httpHeaderExplode($header)
{
    $headers = [];
    
    $data = explode("\r\n", $header);
    $headers['statuses'] = [];
    $i = 0;
    $headers['statuses'][$i++] = $data[0];
    array_shift($data);
    while (strlen($data[0]) == 0)
    {
        $headers['statuses'][$i++] = $data[1];
        array_shift($data);
        array_shift($data);
    }
    
    foreach($data as $part) {
        //$middle = explode(":", $part);
        if (strlen($part) > 0)
        {
            $middle = explode(":", $part, 2);
            $headers[trim($middle[0])] = trim($middle[1]);
        }
    }
    
    return $headers;
}
//------------------------------------------------------------------------------
