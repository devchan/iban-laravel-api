<?php

namespace App\Repositories\IbanNumber;

use App\Repositories\Repository;

class IbanNumberRepository extends Repository
{

    public function get()
    {
        return $this->getModel()
        ->latest()
        ->get();
    }

}
