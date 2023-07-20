<?php declare(strict_types=1);

namespace App\Collections;

use App\Models\Items;
use Illuminate\Database\Eloquent\Collection;

class ItemsCollection extends Collection
{
    public function __construct(mixed $array)
    {
        $newarray = [];
        foreach($array as $row) {
            $newarray[] = $row instanceof Items ? $row : new Items((array) $row);
        }
        parent::__construct($newarray);
    }
}
