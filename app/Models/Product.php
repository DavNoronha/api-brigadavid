<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'price',
        'amount',
        'image'
    ];

    public function rules()
    {
        return [
            'name' => 'required|unique:products|min:3',
            'desc' => 'required',
            'price' => 'required',
            'amount' => 'required',
            'image' => 'required',
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'name.unique' => 'O nome do produto já existe',
            'name.min' => 'O nome tem que ter no mínimo 3 caractéres'
        ];
    }
}
