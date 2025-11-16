@extends('layouts.app')

@section('content')

{{-- ⭐️ x-data no topo para inicializar o Alpine (mantido para o estado inicial, se houver) ⭐️ --}}
<div x-data="{ someState: false }"> 
    
    {{-- O conteúdo principal agora é injetado diretamente no container deslocado do app.blade.php --}}
    
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-clipboard-list mr-2 text-purple-600"></i>
                Nova Dispensação
            </h1>
            
            {{-- Barra de Status e Sair --}}
            <div class="flex items-center space-x-4">
                @auth
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600">Bem-vindo, {{ Auth::user()->name }}!</span>
                </div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Sair
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 p-6">
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Registrar Dispensação</h2>
                <p class="text-sm text-gray-600 mt-1">Selecione paciente, medicamento/lote e informe quantidade</p>
            </div>

            <form method="POST" action="{{ route('dispensacoes.store') }}" class="px-6 py-6 space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Paciente</label>
                    <select name="paciente_id" class="form-input">
                        <option value="">Selecione um paciente</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" {{ old('paciente_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nome }} — CPF: {{ $p->cpf }}
                            </option>
                        @endforeach
                    </select>
                    @error('paciente_id')
                        <div class="alert-error mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lote / Medicamento</label>
                    <select name="lote_id" class="form-input">
                        <option value="">Selecione um lote disponível</option>
                        @foreach($lotes as $l)
                            <option value="{{ $l->lote_id }}" {{ old('lote_id') == $l->lote_id ? 'selected' : '' }}>
                                {{ $l->medicamento }} | Validade: {{ date('d/m/Y', strtotime($l->validade)) }} | Saldo: {{ number_format($l->quantidade_disponivel, 2, ',', '.') }} {{ $l->unidade_base }}
                            </option>
                        @endforeach
                    </select>
                    @error('lote_id')
                        <div class="alert-error mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade informada</label>
                        <input type="number" step="0.001" min="0" name="quantidade_informada" value="{{ old('quantidade_informada') }}" class="form-input" />
                        @error('quantidade_informada')
                            <div class="alert-error mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidade</label>
                        <select name="unidade" class="form-input">
                            <option value="">Selecione a unidade</option>
                            @foreach($unidades as $u)
                                <option value="{{ $u }}" {{ old('unidade') == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                            @endforeach
                        </select>
                        @error('unidade')
                            <div class="alert-error mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número da receita (se aplicável)</label>
                        <input type="text" name="numero_receita" value="{{ old('numero_receita') }}" class="form-input" />
                        @error('numero_receita')
                            <div class="alert-error mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Registrar Dispensação
                    </button>
                </div>
            </form>
        </div>
    </main>

</div>
@endsection