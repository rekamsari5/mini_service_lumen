<?php declare(strict_types=1);

namespace App\Collections;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceCollection extends Collection
{
    public function __construct(mixed $array)
    {
        $newarray = [];
        foreach($array as $row) {
            $newarray[] = $row instanceof Invoice ? $row : new Invoice((array) $row);
        }
        parent::__construct($newarray);
    }
}
