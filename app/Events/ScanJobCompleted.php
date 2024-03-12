<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanJobCompleted
{
    use Dispatchable, SerializesModels;

    public $result;

    public function __construct($result)
    {
        $this->result = $result;
    }
}
