<?php
/*
 * PHP "PDF Signature Verifier - cURL Client Example" version 1.0
 * Testing environment:
 * - HTTP Server: Apache/2.4.37 (Win32)
 * - PHP Version: 7.2.13
 */

require ("http_helper.php");

define("MAX_UPLOAD_FILESIZE", 1048576); // 1MB
header("Access-Control-Allow-Origin: *");

try
{
    $file = file_get_contents('./test.pdf', FALSE, NULL, 0, MAX_UPLOAD_FILESIZE);    
    if (!$file) {
        throw new Exception("Can't open PDF file");
    }
    
    $boundary = bin2hex(random_bytes(10));
        
    $post_fields = "--" . $boundary . "\r\n" .
                   'Content-Disposition: form-data; name="file"; filename="certificate.pem"' . "\r\n" .
                   'Content-Type: application/octet-stream' . "\r\n\r\n" .
                   
                   $file . "\r\n" .
                   "--" . $boundary . "\r\n" .
                   'Content-Disposition: form-data; name="query"' . "\r\n\r\n" .
                   
                   '{"operation":"verify","user_id":0,"security_token":""}' . "\r\n" .
                   "--" . $boundary . "--";
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://signatureverifier.d-logic.com/pdf-sgn-verifier.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $post_fields,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: multipart/form-data; boundary=" . $boundary
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    if ($err) {
        throw new Exception("cURL Error #:" . $err);
    }
    
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    
    curl_close($curl);
    
    $header = substr($response, 0, $header_size);
    $headers = httpHeaderExplode($header);
    if (strlen($headers['Content-Type']) > 0)
    {
        header('Content-Type: ' . $headers['Content-Type']);
    }
      
    $body = substr($response, $header_size);
    
    echo $body;

} catch (Exception $e) {
    echo $e->getMessage();
}

exit();
