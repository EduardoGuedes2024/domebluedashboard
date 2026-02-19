<div class="flex flex-col h-screen px-1 py-2">

    {{-- 1. TOPO: Cabeçalho (Fixo) --}}
    <div class="flex-none">
        <div class="flex items-center justify-between mb-8 px-2 pt-2">
            <div class="flex items-center">
                <img src="{{ asset('imagens/logodomeblueazul.png') }}" alt="Logo DomeBlue" class="h-10 w-auto mr-3">
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
                {{ auth()->user()->nome ?? auth()->user()->email }}
            </p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-1 custom-scrollbar">
        <ul class="space-y-2">
            @php
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

                ///--- CLIENTES ---- \\\
                $podeClienteA = $isAdmin || (int) ($user->clientes_ativos ?? 0) === 1;
                

                $base = 'flex items-center p-3 rounded-lg transition';

            @endphp

            {{-- HOME --}}
            <li>
                <a href="{{ route('home') }}"
                class="{{ $base }} hover:bg-blue-700 text-gray-300">
                    <i class="fas fa-home mr-3"></i> Home
                </a>
            </li>

            {{---dash vendas---}}
            <li>
                <a href="{{ $podeVendasDash ? route('dashboard') : '#' }}"
                class="{{ $base }} {{ $podeVendasDash ? 'hover:bg-blue-700 text-gray-300' : 'opacity-50 cursor-not-allowed text-gray-400' }}"
                @unless($podeVendasDash)
                    onclick="return semPermissao(event)"
                @endunless
                >
                    <i class="fas fa-chart-line mr-3"></i> DashBoard de Vendas
                </a>
            </li>

            {{---Dash Ecommerce UF--}}
            <li>
                <a href="{{ $podeEcomm_Uf ? route('ecommerce_Uf') : '#' }}"
                class="{{ $base }} {{ $podeEcomm_Uf ? 'hover:bg-blue-700 text-gray-300' : 'opacity-50 cursor-not-allowed text-gray-400' }}"
                @unless($podeEcomm_Uf)
                    onclick="return semPermissao(event)"
                @endunless
                >
                    <i class="fas fa-chart-line mr-3"></i> DashBoard Ecommerce UF
                </a>
            </li>
            
            {{-- GRUPO VENDAS --}}
            <li class="mt-2">

                <button type="button" onclick="toggleMenu('sub-vendas')" 
                    class="w-full flex justify-between items-center text-xs font-bold text-white uppercase px-3 mt-4 mb-2 hover:text-blue-500 transition focus:outline-none">
                    <span>VENDAS POR LOJA</span>
                    <i class="fas fa-chevron-down text-[13px] ml-2"></i>
                </button>

                <div id="sub-vendas" class="hidden flex flex-col transition-all duration-300">
                    
                    {{--- Vendas JK--}}
                    <a href="{{ $podeVenda_JK ? route('vendas_JK') : '#'}}"
                        class="{{ $base }} {{ $podeVenda_JK ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeVenda_JK)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-cash-register mr-3"></i>Vendas JK 
                    </a>

                    
                    {{--- Vendas Alpha---}}
                    <a href="{{ $podeVendas_Aplha ? route('vendas_Alphaville') : '#'}}"
                        class="{{ $base }} {{ $podeVendas_Aplha ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeVendas_Aplha)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-cash-register mr-3"></i>Vendas Alphaville 
                    </a>
                
                    {{--- Vendas Rio --}}
                    <a href="{{ $podeVendas_Rio ? route('vendas_Rio') : '#'}}"
                        class="{{ $base }} {{ $podeVendas_Rio ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeVendas_Rio)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-cash-register mr-3"></i>Vendas Rio
                    </a>
                    
                    {{--- Vendas Atacado--}}
                    <a href="{{ $podeVendas_Atacado ? route('vendas_Atacado') : '#'}}"
                        class="{{ $base }} {{ $podeVendas_Atacado ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeVendas_Atacado)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-cash-register mr-3"></i>Vendas Atacado 
                    </a>
                
                    {{--- Vendas Ecommerce--}}
                    <a href="{{ $podeVendas_Ecomm ? route('vendas_Ecommerce') : '#'}}"
                        class="{{ $base }} {{ $podeVendas_Ecomm ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeVendas_Ecomm)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-cash-register mr-3"></i>Vendas Ecommerce 
                    </a>


                    {{--- Vendas Curitica--}}
                    <a href="{{ $podeVendas_Curitiba ? route('vendas_Curitiba') : '#'}}"
                        class="{{ $base }} {{ $podeVendas_Curitiba ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeVendas_Curitiba)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-cash-register mr-3"></i>Vendas Curitiba
                    </a>
                
                </div>
            </li>
            
            
            {{-- GRUPO ESTOQUE --}}
            <li class="mt-2">

                <button type="button" onclick="toggleMenu('sub-estoque')" 
                    class="w-full flex justify-between items-center text-xs font-bold text-white  uppercase px-3 mt-4 mb-2 hover:text-blue-500 transition focus:outline-none">
                    <span>ESTOQUE</span>
                    <i class="fas fa-chevron-down text-[13px] ml-2"></i>
                </button>

                <div id="sub-estoque" class="hidden flex flex-col transition-all duration-300">

                    {{-- Consulta por produto--}}
                    <a href="{{ $podeEstoque ? route('estoque') : '#' }}"
                    class="{{ $base }} {{ $podeEstoque ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}} "
                    @unless($podeEstoque)
                        onclick="return semPermissao(event)"
                    @endunless
                    >
                        <i class="fa-solid fa-magnifying-glass mr-3"></i>  Consulta (Produto)
                    </a>

                    {{-- Relatório por lojas --}}
                    <a href="{{ $podeEstoqueLojas ? route('estoque_lojas') : '#' }}"
                    class="{{ $base }} {{ $podeEstoqueLojas ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}} "
                        @unless ($podeEstoqueLojas)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-store mr-3"></i> Relatório por Lojas
                    </a>

                    {{--- Moviventacao estoque--}}
                    <a href="{{ $podeMovimentacao ? route('movimentacao_estoque') : '#'}}"
                        class="{{ $base }} {{ $podeMovimentacao ? 'hover:bg-blue-700 text-gray-300 pl-10' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                        @unless ($podeMovimentacao)
                            onclick="return semPermissao(event)"
                        @endunless
                    >
                        <i class="fas fa-box mr-3"></i> Movimentação Estoque
                    </a>


                </div>
            </li>


            {{-- CLIENTES ATIVOS --}}
            <li>
                <a href="{{ $podeClienteA ? route('clientes_Ativos') : '#' }}"
                class="{{ $base }} {{ $podeClienteA ? 'hover:bg-blue-700 text-gray-300' : 'opacity-50 cursor-not-allowed text-gray-400'}}"
                @unless ($podeClienteA)
                    onclick="return semPermissao(event)"
                @endunless
                >
                    <i class="fa-solid fa-users mr-3"></i> Consulta Clientes
                </a>
            </li>

        </ul>
    </div>

    {{-- 3. RODAPÉ: Botão Sair (Fixo no Final) --}}
    <div class="flex-none pt-4">
        <hr class="mb-4 border-slate-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
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
