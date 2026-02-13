<div class="flex flex-col h-screen px-1 py-2">

    
    <div class="flex-none">
        <div class="flex items-center justify-between mb-8 px-2 pt-2">
            <div class="flex items-center">
                <img src="<?php echo e(asset('imagens/logodomeblueazul.png')); ?>" alt="Logo DomeBlue" class="h-10 w-auto mr-3">
                <span class="text-blue-600 text-[25px] font-bold tracking-wider">DomeBlue</span>
            </div>

            <button class="md:hidden pl-2 pb-10 rounded-lg hover:bg-slate-700 transition"
                    onclick="document.getElementById('menuOverlay').click()"
                    type="button" aria-label="Fechar menu">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="px-3 mb-6 text-sm text-gray-300">
            <p>Logado como:</p>
            <p class="font-bold text-white uppercase text-[12px]">
                <?php echo e(auth()->user()->nome ?? auth()->user()->email); ?>

            </p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-1 custom-scrollbar">
        <ul class="space-y-2">
            <?php
                $user = auth()->user();

                $isAdmin = (int) ($user->admin ?? 0) === 1;

                /// --- DASHBOARD ----\\\
                $podeVendasDash = $isAdmin || (int) ($user->domeblue ?? 0) === 1;
                $podeEcomm_Uf = $isAdmin || (int) ($user->ecommerce_uf ?? 0) === 1;

                ///--- GRUPOS VENDAS ----\\\
                $podeVenda_JK = $isAdmin || (int) ($user->vendas_jk ?? 0) === 1;
                $podeVendas_Aplha = $isAdmin || (int) ($user->vendas_alphaville ?? 0) === 1;
                $podeVendas_Rio = $isAdmin || (int) ($user->vendas_rio ?? 0) === 1;
                $podeVendas_Atacado = $isAdmin || (int) ($user->vendas_atacado ?? 0) === 1;
                $podeVendas_Ecomm = $isAdmin || (int) ($user->vendas_ecommerce ?? 0) === 1;
                $podeVendas_Curitiba = $isAdmin || (int) ($user->vendas_curitiba ?? 0) === 1;

                ///--- GRUPOS ESTOQUE ---\\\
                $podeEstoque = $isAdmin || (int) ($user->domeblue_estoque ?? 0) === 1;
                $podeEstoqueLojas = $isAdmin || (int) ($user->relatorios_lojas ?? 0) === 1;
                $podeMovimentacao = $isAdmin || (int) ($user->movimento_estoque ?? 0) === 1;

                

                $base = 'flex items-center p-3 rounded-lg transition';

            ?>

                
                <li>
                    <a href="<?php echo e(route('home')); ?>"
                    class="<?php echo e($base); ?> hover:bg-blue-700 text-gray-300">
                        <i class="fas fa-home mr-3"></i> Home
                    </a>
                </li>

                
                <li>
                    <a href="<?php echo e($podeVendasDash ? route('dashboard') : '#'); ?>"
                    class="<?php echo e($base); ?> <?php echo e($podeVendasDash ? 'hover:bg-blue-700 text-gray-300' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                    <?php if (! ($podeVendasDash)): ?>
                        onclick="return semPermissao(event)"
                    <?php endif; ?>
                    >
                        <i class="fas fa-chart-line mr-3"></i> DashBoard de Vendas
                    </a>
                </li>

                
                <li>
                    <a href="<?php echo e($podeEcomm_Uf ? route('ecommerce_Uf') : '#'); ?>"
                    class="<?php echo e($base); ?> <?php echo e($podeEcomm_Uf ? 'hover:bg-blue-700 text-gray-300' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                    <?php if (! ($podeEcomm_Uf)): ?>
                        onclick="return semPermissao(event)"
                    <?php endif; ?>
                    >
                        <i class="fas fa-chart-line mr-3"></i> DashBoard Ecommerce UF
                    </a>
                </li>
                
                
                <li class="mt-2">

                    <button type="button" onclick="toggleMenu('sub-vendas')" 
                        class="w-full flex justify-between items-center text-xs font-bold text-white uppercase px-3 mt-4 mb-2 hover:text-blue-500 transition focus:outline-none">
                        <span>VENDAS POR LOJA</span>
                        <i class="fas fa-chevron-down text-[13px] ml-2"></i>
                    </button>

                    <div id="sub-vendas" class="hidden flex flex-col transition-all duration-300">
                        
                        <a href="<?php echo e($podeVenda_JK ? route('vendas_JK') : '#'); ?>"
                            class="<?php echo e($base); ?> <?php echo e($podeVenda_JK ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                            <?php if (! ($podeVenda_JK)): ?>
                                onclick="return semPermissao(event)"
                            <?php endif; ?>
                        >
                            <i class="fas fa-cash-register mr-3"></i>Vendas JK 
                        </a>

                        
                        
                        <a href="<?php echo e($podeVendas_Aplha ? route('vendas_Alphaville') : '#'); ?>"
                            class="<?php echo e($base); ?> <?php echo e($podeVendas_Aplha ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                            <?php if (! ($podeVendas_Aplha)): ?>
                                onclick="return semPermissao(event)"
                            <?php endif; ?>
                        >
                            <i class="fas fa-cash-register mr-3"></i>Vendas Alphaville 
                        </a>
                    
                        
                        <a href="<?php echo e($podeVendas_Rio ? route('vendas_Rio') : '#'); ?>"
                            class="<?php echo e($base); ?> <?php echo e($podeVendas_Rio ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                            <?php if (! ($podeVendas_Rio)): ?>
                                onclick="return semPermissao(event)"
                            <?php endif; ?>
                        >
                            <i class="fas fa-cash-register mr-3"></i>Vendas Rio
                        </a>
                        
                        
                        <a href="<?php echo e($podeVendas_Atacado ? route('vendas_Atacado') : '#'); ?>"
                            class="<?php echo e($base); ?> <?php echo e($podeVendas_Atacado ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                            <?php if (! ($podeVendas_Atacado)): ?>
                                onclick="return semPermissao(event)"
                            <?php endif; ?>
                        >
                            <i class="fas fa-cash-register mr-3"></i>Vendas Atacado 
                        </a>
                    
                        
                        <a href="<?php echo e($podeVendas_Ecomm ? route('vendas_Ecommerce') : '#'); ?>"
                            class="<?php echo e($base); ?> <?php echo e($podeVendas_Ecomm ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                            <?php if (! ($podeVendas_Ecomm)): ?>
                                onclick="return semPermissao(event)"
                            <?php endif; ?>
                        >
                            <i class="fas fa-cash-register mr-3"></i>Vendas Ecommerce 
                        </a>


                        
                        <a href="<?php echo e($podeVendas_Curitiba ? route('vendas_Curitiba') : '#'); ?>"
                            class="<?php echo e($base); ?> <?php echo e($podeVendas_Curitiba ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                            <?php if (! ($podeVendas_Curitiba)): ?>
                                onclick="return semPermissao(event)"
                            <?php endif; ?>
                        >
                            <i class="fas fa-cash-register mr-3"></i>Vendas Curitiba
                        </a>
                    
                    </div>
                </li>
            
            
            
            <li class="mt-2">

                <button type="button" onclick="toggleMenu('sub-estoque')" 
                    class="w-full flex justify-between items-center text-xs font-bold text-white  uppercase px-3 mt-4 mb-2 hover:text-blue-500 transition focus:outline-none">
                    <span>ESTOQUE</span>
                    <i class="fas fa-chevron-down text-[13px] ml-2"></i>
                </button>

                <div id="sub-estoque" class="hidden flex flex-col transition-all duration-300">

                    
                    <a href="<?php echo e($podeEstoque ? route('estoque') : '#'); ?>"
                    class="<?php echo e($base); ?> <?php echo e($podeEstoque ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?> "
                    <?php if (! ($podeEstoque)): ?>
                        onclick="return semPermissao(event)"
                    <?php endif; ?>
                    >
                        <i class="fa-solid fa-magnifying-glass mr-3"></i>  Consulta (Produto)
                    </a>

                    
                    <a href="<?php echo e($podeEstoqueLojas ? route('estoque_lojas') : '#'); ?>"
                    class="<?php echo e($base); ?> <?php echo e($podeEstoqueLojas ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?> "
                        <?php if (! ($podeEstoqueLojas)): ?>
                            onclick="return semPermissao(event)"
                        <?php endif; ?>
                    >
                        <i class="fas fa-store mr-3"></i> Relatório por Lojas
                    </a>

                    
                    <a href="<?php echo e($podeMovimentacao ? route('movimentacao_estoque') : '#'); ?>"
                        class="<?php echo e($base); ?> <?php echo e($podeMovimentacao ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'); ?>"
                        <?php if (! ($podeMovimentacao)): ?>
                            onclick="return semPermissao(event)"
                        <?php endif; ?>
                    >
                        <i class="fas fa-box mr-3"></i> Movimentação Estoque
                    </a>


                </div>
            </li>

        </ul>
    </div>

    
    <div class="flex-none pt-4">
        <hr class="mb-4 border-slate-700">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full flex items-center p-3 text-red-400 hover:bg-red-600 hover:text-white rounded-lg transition font-bold uppercase text-xs">
                <i class="fas fa-sign-out-alt mr-3"></i> Sair 
            </button>
        </form>
    </div>


</div>


<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
<?php /**PATH C:\domeblue-dash\resources\views/layouts/menu.blade.php ENDPATH**/ ?>