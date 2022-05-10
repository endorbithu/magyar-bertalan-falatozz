<?php

namespace App\Models;

use App\Contracts\Services\CrudModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements CrudModelInterface
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'desc',
        'price'
    ];


    public static function getAttributesInfo(): array
    {
        return [
            'id|hidden' => 'ID',
            'name|text|required' => 'Név',
            'desc|textarea' => 'Leírás',
            'price|number|required' => 'Ár',
        ];
    }


}
