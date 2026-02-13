<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DomeBlue')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- FontAwesome (pro menu) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('imagens/favicondomeblueazul.png') }}">
</head>

<body class="bg-blue-200 font-sans leading-normal tracking-normal">

    <div class="min-h-screen">

        {{-- TOP BAR (só aparece no mobile) --}}
        <div class="md:hidden flex items-center justify-between px-4 py-3 bg-slate-800 text-white">
            <div class="flex items-center gap-2">
                <img src="{{ asset('imagens/logodomeblueazul.png') }}" class="h-8 w-auto" alt="DomeBlue">
                <span class="text-blue-500 font-black text-lg">DomeBlue</span>
            </div>

            <button id="btnMenu"
                class="p-2 rounded-lg hover:bg-slate-700 transition"
                aria-label="Abrir menu"
                type="button">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>

        {{-- OVERLAY (mobile) --}}
        <div id="menuOverlay"
             class="fixed inset-0 bg-black/40 z-40 hidden md:hidden">
        </div>

        <div class="flex">

            {{-- SIDEBAR --}}
            <aside id="sidebar"
                class="
                    fixed inset-y-0 left-0 z-50
                    w-64 bg-slate-800 text-white
                    transform -translate-x-full md:translate-x-0
                    transition-transform duration-200
                    md:fixed md:inset-y-0 md:left-0
                ">

                @include('layouts.menu')

            </aside>

            {{-- CONTEÚDO --}}
            <main class="flex-1 min-h-screen p-4 md:p-8 md:ml-64">
                @yield('content')
            </main>


        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('menuOverlay');
            const btn = document.getElementById('btnMenu');

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            if (btn) btn.addEventListener('click', openMenu);
            if (overlay) overlay.addEventListener('click', closeMenu);

            // Fecha ao clicar em um link (mobile)
            sidebar.addEventListener('click', (e) => {
                const a = e.target.closest('a');
                if (!a) return;
                if (window.innerWidth < 768) closeMenu();
            });

            // Ajusta ao redimensionar
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    overlay.classList.add('hidden');
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        })();

        function toggleMenu(id) {
            const submenu = document.getElementById(id);
            const icon = submenu.previousElementSibling.querySelector('.fa-chevron-down');

            // Alterna a classe hidden
            submenu.classList.toggle('hidden');

            // Gira o ícone de seta (opcional, para dar o efeito visual)
            if (submenu.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>

    @stack('scripts')
    
</body>
</html>
