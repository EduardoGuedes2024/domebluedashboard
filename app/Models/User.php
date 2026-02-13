<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'operador';
    protected $primaryKey = 'codigo_acesso';
    public $incrementing = false;
    protected $keyType = 'string';

    // colunas da sua tabela
    protected $fillable = [
        'codigo_acesso',
        'nome',
        'senha',
        'remember_token',
    ];

    // Laravel espera "password", mas sua coluna é "senha"
    public function getAuthPassword()
    {
        return $this->senha;
    }
}
