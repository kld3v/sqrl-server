<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SslTlsCertificate extends Controller
{
    public function analyzeSSL($url)
    {
        $url = urldecode($url); // Decode the URL parameter
    $scriptPath = base_path("app/Scripts/Ssl_Tls_Check.sh");
  
    $command = "$scriptPath $url";
    $output = shell_exec($command);
    
    return response($output, 200)
        ->header('Content-Type', 'text/plain');
    }
}