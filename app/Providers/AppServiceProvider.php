<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\SqlServerConnection;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        DB::extend('odbcsqlsrv', function (array $config, string $name) {
            $dsn = $config['dsn'] ?? null;
            if (!$dsn) {
                throw new \InvalidArgumentException("DB_DSN não definido no .env");
            }

            $username = $config['username'] ?? null;
            $password = $config['password'] ?? null;

            $options = $config['options'] ?? [];
            $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

            // Aqui conecta somente quando realmente usar DB::connection()
            $pdo = new \PDO($dsn, $username, $password, $options);

            return new SqlServerConnection($pdo, $config['database'] ?? '', $config['prefix'] ?? '', $config);
        });
    }
}
