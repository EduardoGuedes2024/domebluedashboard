<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\RelatorioEstoqueLojasController;
use App\Http\Controllers\ImageProxyController;
use App\Http\Controllers\MovimentacaoEstoqueController;
use App\Http\Controllers\RelatorioVendasController;
use App\Http\Controllers\RelatorioVendasJKController;
use App\Http\Controllers\RelatorioVendasAlphavilleController;
use App\http\Controllers\RelatorioVendasRioController;
use App\http\Controllers\RelatorioVendasAtacadoController;
use App\http\Controllers\RelatorioVendasEcommerceController;
use App\http\Controllers\RelatorioVendasCuritibaController;
use App\Http\Controllers\EcommerceUfController;
use App\Http\Controllers\ClientesAtivosController;
use Symfony\Component\Routing\Route as RoutingRoute;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return redirect()->route('home');
});

// rota page home protegita com middleware
Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('home'); 

// DASHBOARD Vendas
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'perm:domeblue'])
    ->name('dashboard');

// DashBoard Ecommerce UF
Route::get('/ecommerce_Uf', [EcommerceUfController::class, 'index'])
    ->middleware(['auth', 'perm:ecommerce_uf'])
    ->name('ecommerce_Uf');

// PDF DashBoard Ecommerce UF
Route::get('/ecommerce_Uf/export/pdf', [EcommerceUfController::class, 'exportPdf'])
    ->name('ecommerce_Uf.export.pdf');


/////////------ VENDAS ------- \\\\\\\\\\


//movimento vendas geral
Route::get('/vendas', [RelatorioVendasController::class, 'index'])
    ->middleware(['auth', 'perm:domeblue_vendas'])
    ->name('vendas');

// PDF movimento vendas geral 
Route::get('/relatorios/vendas/pdf', [RelatorioVendasController::class, 'exportPdf'])
    ->middleware(['auth', 'perm:vendas'])
    ->name('relatorios.vendas.export.pdf');    

/// vendas JK    
Route::get('/vendas_JK', [RelatorioVendasJKController::class, 'index'])
    ->middleware(['auth', 'perm:vendas_jk'])
    ->name('vendas_JK');


//// vendas alphaville    
Route::get('/vendas_Alphaville', [RelatorioVendasAlphavilleController::class, 'index'])
    ->middleware(['auth', 'perm:vendas_alphaville'])
    ->name('vendas_Alphaville');    

//// vendas Rio
Route::get('/vendas_Rio', [RelatorioVendasRioController::class, 'index'])
    ->middleware(['auth', 'perm:vendas_rio'])
    ->name('vendas_Rio');

/// vendas Atacado
Route::get('/vendas_Atacado', [RelatorioVendasAtacadoController::class, 'index'])
    ->middleware('auth', 'perm:vendas_atacado')
    ->name('vendas_Atacado');

/// vendas Ecommerce
Route::get('/vendas_Ecommerce', [RelatorioVendasEcommerceController::class, 'index'])
    ->middleware('auth', 'perm:vendas_ecommerce')
    ->name('vendas_Ecommerce');   

//// vendas Curitiba
Route::get('/vendas_Curitiba', [RelatorioVendasCuritibaController::class, 'index'])
    ->middleware('auth', 'perm:vendas_curitiba')
    ->name('vendas_Curitiba'); 
    
    
///////// ------- ESTOQUE -------- \\\\\\\\\\

// consulta estoque
Route::get('/estoque',[EstoqueController::class, 'index'])
    ->middleware(['auth', 'perm:domeblue_estoque'])
    ->name('estoque');

// consulta geral estoque por loja   
Route::get('/estoque_lojas',[RelatorioEstoqueLojasController::class, 'index'])
    ->middleware(['auth', 'perm:relatorios_lojas'])
    ->name('estoque_lojas');

// imagem proxy relatorio   
Route::get('/img/produto/{site}/{ref}', [ImageProxyController::class, 'produto'])
    ->name('img.produto');

// PDF consulta geral estoque por loja
Route::get('/estoque_lojas/export/pdf', [RelatorioEstoqueLojasController::class, 'exportPdf'])
    ->name('estoque_lojas.export.pdf');

// Movimento estoque    
Route::get('/movimentacao_estoque', [MovimentacaoEstoqueController::class, 'index'])
    ->middleware(['auth', 'perm:movimento_estoque'])
    ->name('movimentacao_estoque');

//PDF Movimento estoque
Route::get('/movimentacao_estoque/export/pdf', [MovimentacaoEstoqueController::class, 'exportPdf'])
    ->name('movimentacao.pdf');

///////// ------- CLIENTES -------- \\\\\\\\\\
Route::get('/clientes_Ativos', [ClientesAtivosController::class, 'index'])
    ->middleware(['auth', 'perm:clientes_ativos'])
    ->name('clientes_Ativos');

// Adicione no final do arquivo routes/web.php
Route::get('/api/municipios/{uf}', [ClientesAtivosController::class, 'getMunicipios']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
