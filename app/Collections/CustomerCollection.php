<?php declare(strict_types=1);

namespace App\Collections;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerCollection extends Collection
{
    public function __construct(mixed $array)
    {
        $newarray = [];
        foreach($array as $row) {
            $newarray[] = $row instanceof Customer ? $row : new Customer((array) $row);
        }
        parent::__construct($newarray);
    }
}
