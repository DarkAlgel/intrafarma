<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        /* ... SEUS ESTILOS CSS PERMANECEM AQUI ... */
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed; 
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 300ms ease, opacity 300ms ease, visibility 300ms ease;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 4px 8px;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15); /* Tom claro do hover */
            transform: translateX(4px);
        }
        
        /* ⭐️ CORREÇÃO: Torna o ativo igual ao hover para consistência visual ⭐️ */
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15); 
            border-left: 4px solid #fff;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .table-header {
            padding: 12px 16px;
            background-color: #f8fafc;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        
        .table-cell {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #4b5563;
        }
        
        .table-row:hover {
            background-color: #f8fafc;
            transition: background-color 0.2s ease;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* ALERTA DE ERRO para validação de formulário */
        .alert-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: none;
        }
        
        /* CLASSES DE STATUS (para tabela de estoque) */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Animações de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Responsividade para mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
{{-- Define se a sidebar deve estar visível com base na autenticação e rotas públicas --}}
@php 
    $hideForRoutes = ['login', 'register', 'password.*', 'verification.*'];
    $showSidebar = Auth::check() && !request()->routeIs($hideForRoutes); 
@endphp

<body class="bg-gray-50 font-sans">
    
    <div id="app">
        
        {{-- ⭐️ START: ESTRUTURA LAYOUT MESTRE ⭐️ --}}
        <div class="flex min-h-screen bg-gray-100"> 
            
            {{-- SIDEBAR: Renderiza o menu fixo --}}
            <div class="sidebar w-64 text-white {{ $showSidebar ? '' : 'sidebar-hidden' }}" aria-hidden="{{ $showSidebar ? 'false' : 'true' }}">
                <div class="p-4 border-b border-purple-700">
                    <h1 class="text-xl font-bold flex items-center">
                        <i class="fas fa-pills mr-2"></i>
                        INTRAFARMA
                    </h1>
                </div>
                <nav class="mt-6">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                    
                    {{-- Lógica de permissões para navegação --}}
                    @php $uid = Auth::id(); @endphp
                    
                    @if($uid && \App\Services\PermissionService::userHas($uid,'paciente_ver_medicamentos'))
                    <a href="{{ route('paciente.medicamentos') }}" class="nav-link {{ request()->routeIs('paciente.medicamentos') ? 'active' : '' }}">
                        <i class="fas fa-pills mr-3"></i>
                        Medicamentos
                    </a>
                    @endif
                    
                    @if($uid && \App\Services\PermissionService::userHas($uid,'ver_estoque'))
                    <a href="{{ route('estoque.index') }}" class="nav-link {{ request()->routeIs('estoque.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes mr-3"></i>
                        Estoque
                    </a>
                    @endif
                    
                    @if($uid && \App\Services\PermissionService::userHas($uid,'gerenciar_usuarios'))
                        {{-- ⭐️ SUBMENU PACIENTES ⭐️ --}}
                        <div x-data="{ open: {{ request()->routeIs(['pacientes.create', 'pacientes.index', 'pacientes.show', 'pacientes.edit']) ? 'true' : 'false' }} }" class="relative">
                            
                            <button @click="open = !open" 
                                    class="flex items-center justify-between w-full nav-link {{ request()->routeIs(['pacientes.create', 'pacientes.index', 'pacientes.show', 'pacientes.edit']) ? 'active' : '' }}" 
                                    style="margin: 0; padding: 12px 20px;">
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-3 text-xl"></i>
                                    <span class="font-semibold">Pacientes</span>
                                </div>
                                <i class="fas" :class="{'fa-chevron-up': open, 'fa-chevron-down': !open}"></i>
                            </button>
                            
                            <div x-show="open" x-collapse>
                                
                                <a href="{{ route('pacientes.create') }}" 
                                   class="pl-12 py-2 text-sm text-gray-300 hover:bg-white/15 transition duration-150 ease-in-out block 
                                          {{ request()->routeIs('pacientes.create') ? 'bg-white/20 text-white font-bold' : '' }}">
                                    <i class="fas fa-plus mr-2 text-xs"></i> Novo Cadastro
                                </a>
                                
                                <a href="{{ route('pacientes.index') }}" 
                                   class="pl-12 py-2 text-sm text-gray-300 hover:bg-white/15 transition duration-150 ease-in-out block 
                                          {{ request()->routeIs(['pacientes.index', 'pacientes.show', 'pacientes.edit']) ? 'bg-white/20 text-white font-bold' : '' }}">
                                    <i class="fas fa-list mr-2 text-xs"></i> Lista/Ficha
                                </a>
                                
                            </div>
                        </div>
                    @else
                        {{-- Link Simples de Pacientes (Fallback) --}}
                         <a href="{{ route('pacientes.index') }}" class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                            <i class="fas fa-users mr-3"></i>
                            Pacientes
                        </a>
                    @endif
                    
                    @if($uid && \App\Services\PermissionService::userHas($uid,'ver_dispensacoes'))
                        {{-- ⭐️ SUBMENU DISPENSAÇÕES (Com Histórico) ⭐️ --}}
                        <div x-data="{ open: {{ request()->routeIs(['dispensacoes.create', 'dispensacoes.index']) ? 'true' : 'false' }} }" class="relative">
                            
                            <button @click="open = !open" 
                                    class="flex items-center justify-between w-full nav-link {{ request()->routeIs(['dispensacoes.create', 'dispensacoes.index']) ? 'active' : '' }}" 
                                    style="margin: 0; padding: 12px 20px;">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard-list mr-3 text-xl"></i>
                                    <span class="font-semibold">Dispensações</span>
                                </div>
                                <i class="fas" :class="{'fa-chevron-up': open, 'fa-chevron-down': !open}"></i>
                            </button>
                            
                            <div x-show="open" x-collapse>
                                
                                <a href="{{ route('dispensacoes.create') }}" 
                                   class="pl-12 py-2 text-sm text-gray-300 hover:bg-white/15 transition duration-150 ease-in-out block 
                                          {{ request()->routeIs('dispensacoes.create') ? 'bg-white/20 text-white font-bold' : '' }}">
                                    <i class="fas fa-plus mr-2 text-xs"></i> Nova Dispensação
                                </a>
                                
                                <a href="{{ route('dispensacoes.index') }}" 
                                   class="pl-12 py-2 text-sm text-gray-300 hover:bg-white/15 transition duration-150 ease-in-out block 
                                          {{ request()->routeIs('dispensacoes.index') ? 'bg-white/20 text-white font-bold' : '' }}">
                                    <i class="fas fa-history mr-2 text-xs"></i> Histórico
                                </a>
                            </div>
                        </div>
                    @endif
                    {{-- FIM: SUBMENU DISPENSAÇÕES --}}
                    
                    <a href="{{ route('fornecedores.index') }}" class="nav-link {{ request()->routeIs('fornecedores.*') ? 'active' : '' }}">
                        <i class="fas fa-truck mr-3"></i>
                        Fornecedores
                    </a>
                    
                    @if($uid && (\App\Services\PermissionService::userHas($uid,'ver_minha_conta') || \App\Services\PermissionService::userHas($uid,'alterar_senha')))
                    <a href="{{ route('configuracoes.index') }}" class="nav-link {{ request()->routeIs('configuracoes.*') ? 'active' : '' }}">
                        <i class="fas fa-cog mr-3"></i>
                        Configurações
                    </a>
                    @endif
                    
                    @if($uid && \App\Services\PermissionService::userHas($uid,'paciente_ver_historico'))
                    <a href="{{ route('paciente.historico') }}" class="nav-link {{ request()->routeIs('paciente.historico') ? 'active' : '' }}">
                        <i class="fas fa-notes-medical mr-3"></i>
                        Meu Histórico
                    </a>
                    @endif
                </nav>
            </div>
            
            {{-- CONTEÚDO PRINCIPAL: Aplica a margem de deslocamento --}}
            <div class="flex-1 flex flex-col {{ $showSidebar ? 'md:ml-64' : 'w-full' }}">
                 @yield('content') {{-- Aqui o conteúdo da sua página é injetado --}}
            </div>
            
        </div>
        {{-- ⭐️ FIM DA ESTRUTURA LAYOUT MESTRE ⭐️ --}}
        
    </div>

    {{-- 💡 SCRIPTS E TOASTS 💡 --}}
    @if(session()->has('success') || session()->has('error'))
        @php
            $type = session()->has('success') ? 'success' : 'error';
            $message = session($type);
            $bgColor = ($type === 'success') ? 'bg-green-600' : 'bg-red-600';
            $icon = ($type === 'success') ? 'fas fa-check-circle' : 'fas fa-times-circle';
        @endphp
        
        <div id="toast-message" 
             class="fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl text-white transition-opacity duration-300 ease-out {{ $bgColor }}"
             style="opacity: 0;"> 
            <div class="flex items-center">
                <i class="{{ $icon }} mr-2 text-xl"></i>
                <span class="font-medium">{{ $message }}</span>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('toast-message');
                
                setTimeout(() => {
                    toast.style.opacity = '1';
                }, 100); 

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toast.remove();
                    }, 300); 
                }, 5000); 
            });
        </script>
        @endpush
    @endif
    
    @stack('scripts')
    {{-- Seu código JavaScript final permanece aqui, se necessário --}}
    <script>
      (function(){
        // ... (Seu código JavaScript de controle de modal) ...
        function getModalBox(modal){ if(!modal) return null; const box = modal.querySelector(':scope > div'); return box || modal; }
        function showModal(modal, overlay, onShown){ if(!modal||!overlay){ console.error('Modal/overlay não encontrado'); return; } overlay.classList.remove('hidden'); modal.classList.remove('hidden'); const box=getModalBox(modal); overlay.classList.add('transition-opacity','duration-200','opacity-0'); box.classList.add('transition','duration-200','opacity-0','scale-95'); requestAnimationFrame(()=>{ overlay.classList.remove('opacity-0'); overlay.classList.add('opacity-100'); box.classList.remove('opacity-0','scale-95'); box.classList.add('opacity-100','scale-100'); if(typeof onShown==='function') onShown(); }); }
        function hideModal(modal, overlay){ if(!modal||!overlay){ console.error('Modal/overlay não encontrado para fechar'); return; } const box=getModalBox(modal); overlay.classList.remove('opacity-100'); overlay.classList.add('opacity-0'); box.classList.remove('opacity-100','scale-100'); box.classList.add('opacity-0','scale-95'); setTimeout(()=>{ modal.classList.add('hidden'); overlay.classList.add('hidden'); },200); }
        function closeAllModals(){ ['modalCreate','modalRole','modalEdit','modalPassword','modalDelete'].forEach(id=>{ const m=document.getElementById(id); if(m) m.classList.add('hidden'); }); ['overlayCreate','overlayRole','overlayEdit','overlayPassword','overlayDelete'].forEach(id=>{ const o=document.getElementById(id); if(o) o.classList.add('hidden'); }); }

        document.addEventListener('DOMContentLoaded', ()=>{
          [['overlayEdit','modalEdit'],['overlayPassword','modalPassword'],['overlayDelete','modalDelete'],['overlayRole','modalRole'],['overlayCreate','modalCreate']].forEach(([oid,mid])=>{
            const o=document.getElementById(oid), m=document.getElementById(mid); if(o&&m){ o.addEventListener('click', ()=>hideModal(m,o)); }
          });
        });

        document.addEventListener('click', (e)=>{
          const target = e.target;
          const btnRole = target.closest('.btnOpenRoleModal');
          const btnEdit = target.closest('.btnOpenEditModal');
          const btnPass = target.closest('.btnOpenPasswordModal');
          const btnDel  = target.closest('.btnOpenDeleteModal');
          if(!(btnRole||btnEdit||btnPass||btnDel)) return;
          e.preventDefault();
          const btn = btnRole||btnEdit||btnPass||btnDel;
          const userId = btn.getAttribute('data-user');
          const row = btn.closest('tr');
          const nameCell = row ? row.querySelector('td[data-name]') : null;
          const emailSpan = row ? row.querySelector('td:nth-child(2) span[aria-invalid]') : null;
          const name = (nameCell?.textContent||'').trim();
          const email = (emailSpan?.textContent||'').trim();
          closeAllModals();
          if(btnRole){ const overlay=document.getElementById('overlayRole'); const modal=document.getElementById('modalRole'); const roleFormName=document.getElementById('roleFormName'); const roleFormEmail=document.getElementById('roleFormEmail'); if(roleFormName) roleFormName.value=name; if(roleFormEmail) roleFormEmail.value=email; showModal(modal, overlay, ()=>{ const roleSearch=document.getElementById('roleSearch'); roleSearch&&roleSearch.focus(); }); return; }
          if(btnEdit){ const overlay=document.getElementById('overlayEdit'); const modal=document.getElementById('modalEdit'); const editName=document.getElementById('editName'); const editEmail=document.getElementById('editEmail'); if(editName) editName.value=name; if(editEmail) editEmail.value=email; showModal(modal, overlay, ()=>{ editName&&editName.focus(); }); return; }
          if(btnPass){ const overlay=document.getElementById('overlayPassword'); const modal=document.getElementById('modalPassword'); const passName=document.getElementById('passwordFormName'); const passEmail=document.getElementById('passwordFormEmail'); const passNew=document.getElementById('passwordNew'); if(passName) passName.value=name; if(passEmail) passEmail.value=email; showModal(modal, overlay, ()=>{ passNew&&passNew.focus(); }); return; }
          if(btnDel){ const overlay=document.getElementById('overlayDelete'); const modal=document.getElementById('modalDelete'); showModal(modal, overlay); return; }
        });

        window.addEventListener('keydown', (e)=>{
          if(e.key!== 'Escape') return;
          [['overlayEdit','modalEdit'],['overlayPassword','modalPassword'],['overlayDelete','modalDelete'],['overlayRole','modalRole'],['overlayCreate','modalCreate']].forEach(([oid,mid])=>{
            const o=document.getElementById(oid), m=document.getElementById(mid); if(o&&m && !m.classList.contains('hidden')) hideModal(m,o);
          });
        });
      })();
    </script>
  </body>
</html>