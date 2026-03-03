<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Operador extends Authenticatable
{
    protected $table = 'operador';
    protected $primaryKey = 'codigo_acesso';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $hidden = ['senha', 'senha_hash', 'remember_token',];

    protected $casts = [
        'admin' => 'boolean',
    ];

    // Laravel vai usar isso como "senha do usuário"
    public function getAuthPassword()
    {
        return $this->senha_hash ?? '';
    }
}
