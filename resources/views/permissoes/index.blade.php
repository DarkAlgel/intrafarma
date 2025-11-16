@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col bg-gray-50">
  <header class="bg-white/90 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
      <div>
        <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
          <i class="fas fa-user-shield text-gray-500"></i>
          Permissões
        </h1>
        <p class="text-sm text-gray-500 mt-1">
          Gerencie papéis do sistema, permissões e atribuições rápidas.
        </p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('permissoes.export.csv') }}" class="btn-secondary inline-flex items-center gap-2 text-sm">
          <i class="fas fa-file-csv"></i>
          <span>Exportar CSV</span>
        </a>
        <a href="{{ route('permissoes.export.pdf') }}" class="btn-secondary inline-flex items-center gap-2 text-sm">
          <i class="fas fa-file-pdf"></i>
          <span>Exportar PDF</span>
        </a>
        <button
          id="btnOpenCreateRole"
          class="btn-primary inline-flex items-center gap-2 text-sm shadow-sm"
          aria-haspopup="dialog"
          aria-controls="modalCreateRole"
        >
          <i class="fas fa-plus-circle"></i>
          <span>Criar novo Papel</span>
        </button>
      </div>
    </div>
  </header>

  <main class="flex-1 p-6 space-y-6">
    {{-- FILTROS / BUSCA --}}
    <div class="card p-5 bg-white/90 border border-gray-100 shadow-sm rounded-xl">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="w-full md:max-w-md">
          <label for="searchRoles" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
            Buscar papéis e permissões
          </label>
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
              id="searchRoles"
              class="form-input pl-9 text-sm"
              placeholder="Digite o nome do papel ou permissão..."
            >
          </div>
        </div>

        <div class="flex items-center gap-2 justify-end">
          @php $toggleDir = $dir === 'asc' ? 'desc' : 'asc'; @endphp
          <a
            href="{{ route('permissoes.index', ['sort' => 'name', 'dir' => $toggleDir]) }}"
            class="btn-secondary inline-flex items-center gap-2 text-sm"
          >
            <i class="fas fa-sort text-gray-500"></i>
            <span>Ordenar por Nome</span>
            <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
              {{ $dir === 'asc' ? 'ASC' : 'DESC' }}
            </span>
          </a>
        </div>
      </div>
    </div>

    {{-- TABELA DE PAPÉIS --}}
    <div class="card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-semibold text-gray-800">Papéis do Sistema</h2>
          <p class="text-xs text-gray-500 mt-1">
            Visualize, edite e gerencie as permissões atribuídas a cada papel.
          </p>
        </div>
        <div class="flex flex-col text-right">
          <span class="text-xs text-gray-600">Página {{ $roles->currentPage() }} de {{ $roles->lastPage() }}</span>
          <span class="text-xs text-gray-400 mt-1">
            Total: {{ $roles->total() }} papéis
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm" aria-label="Lista de papéis">
          <thead class="bg-gray-50/80">
            <tr>
              <th scope="col" class="table-header text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">
                Nome
              </th>
              <th scope="col" class="table-header text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">
                Permissões atribuídas
              </th>
              <th scope="col" class="table-header text-right text-xs font-semibold text-gray-500 uppercase tracking-wide px-6 py-3">
                Ações
              </th>
            </tr>
          </thead>
          <tbody
            id="rolesTableBody"
            class="bg-white divide-y divide-gray-100"
          >
            @forelse($roles as $r)
              @php
                $permsForRole = $assigned->where('role_id', $r->id)->pluck('permission_id')->all();
                $permNames = $permissions->whereIn('id', $permsForRole)->pluck('name')->values()->all();
                $isDefault = in_array($r->code, $defaultCodes ?? []);
              @endphp
              <tr class="role-row hover:bg-gray-50 transition-colors">
                <td class="table-cell px-6 py-4 align-top">
                  <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                      <span class="font-medium text-gray-800">{{ $r->name }}</span>
                      @if($isDefault)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px] uppercase tracking-wide border border-gray-200">
                          Padrão
                        </span>
                      @endif
                    </div>
                    <span class="text-xs text-gray-500">
                      {{ count($permsForRole) }} {{ Str::plural('permissão', count($permsForRole)) }}
                    </span>
                  </div>
                </td>

                <td class="table-cell px-6 py-4 align-top">
                  <div class="flex flex-wrap gap-1.5">
                    @forelse($permNames as $nm)
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-[11px] font-medium perm-badge border border-purple-100">
                        {{ $nm }}
                      </span>
                    @empty
                      <span class="text-gray-400 text-sm italic">Nenhuma permissão atribuída</span>
                    @endforelse
                  </div>
                </td>

                <td class="table-cell px-6 py-4 align-top">
                  <div class="flex flex-wrap items-center justify-end gap-2">
                    <button
                      type="button"
                      class="btn-secondary inline-flex items-center gap-2 text-xs"
                      data-role="{{ $r->id }}"
                    >
                      <i class="fas fa-eye"></i>
                      <span>Visualizar</span>
                    </button>
                    <button
                      type="button"
                      class="btn-secondary inline-flex items-center gap-2 text-xs btnEditRole {{ $isDefault ? 'opacity-60 cursor-not-allowed' : '' }}"
                      data-role="{{ $r->id }}"
                      {{ $isDefault ? 'disabled' : '' }}
                    >
                      <i class="fas fa-edit"></i>
                      <span>Editar</span>
                    </button>
                    <button
                      type="button"
                      class="btn-danger inline-flex items-center gap-2 text-xs btnDeleteRole {{ $isDefault ? 'opacity-60 cursor-not-allowed' : '' }}"
                      data-role="{{ $r->id }}"
                      {{ $isDefault ? 'disabled' : '' }}
                    >
                      <i class="fas fa-trash"></i>
                      <span>Excluir</span>
                    </button>
                  </div>

                  <template id="role-data-{{ $r->id }}">
                    {{ json_encode(['id'=>$r->id,'name'=>$r->name,'code'=>$r->code,'permissions'=>$permsForRole]) }}
                  </template>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                  Nenhum papel encontrado.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-6 py-4 border-t bg-gray-50/80 flex items-center justify-between text-sm text-gray-600">
        <div>
          {{ $roles->links() }}
        </div>
      </div>
    </div>

    {{-- ATRIBUIÇÕES RÁPIDAS --}}
    <div class="card p-6 bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-md font-semibold text-gray-800 flex items-center gap-2">
          <i class="fas fa-bolt text-gray-500"></i>
          Atribuições rápidas
        </h3>
        <p class="text-xs text-gray-500">
          Conceda ou revogue permissões sem sair da página.
        </p>
      </div>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- CONCEDER --}}
        <div class="rounded-lg border border-gray-100 bg-gray-50/60 p-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fas fa-plus text-gray-500 text-xs"></i>
            Conceder permissão
          </h4>
          <form
            method="POST"
            action="{{ route('permissoes.assign') }}"
            class="grid grid-cols-1 gap-3"
          >
            @csrf
            <select
              name="role_id"
              class="form-input text-sm"
              aria-label="Selecionar papel para conceder"
            >
              @foreach($roles as $r)
                <option value="{{ $r->id }}">{{ $r->name }}</option>
              @endforeach
            </select>
            <select
              name="permission_id"
              class="form-input text-sm"
              aria-label="Selecionar permissão a conceder"
            >
              @foreach($permissions as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
            <button class="btn-primary text-sm inline-flex items-center justify-center gap-2">
              <i class="fas fa-check-circle"></i>
              <span>Conceder</span>
            </button>
          </form>
        </div>

        {{-- REVOGAR --}}
        <div class="rounded-lg border border-gray-100 bg-gray-50/60 p-4">
          <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fas fa-minus text-gray-500 text-xs"></i>
            Revogar permissão
          </h4>
          <form
            method="POST"
            action="{{ route('permissoes.revoke') }}"
            class="grid grid-cols-1 gap-3"
          >
            @csrf
            <select
              name="role_id"
              class="form-input text-sm"
              aria-label="Selecionar papel para revogar"
            >
              @foreach($roles as $r)
                <option value="{{ $r->id }}">{{ $r->name }}</option>
              @endforeach
            </select>
            <select
              name="permission_id"
              class="form-input text-sm"
              aria-label="Selecionar permissão a revogar"
            >
              @foreach($permissions as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
            <button class="btn-secondary text-sm inline-flex items-center justify-center gap-2">
              <i class="fas fa-times-circle"></i>
              <span>Revogar</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>

{{-- OVERLAY --}}
<div
  id="overlayRoles"
  class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden"
  aria-hidden="true"
></div>

{{-- MODAL CRIAR PAPEL --}}
<div
  id="modalCreateRole"
  role="dialog"
  aria-modal="true"
  aria-labelledby="titleCreateRole"
  class="fixed inset-0 hidden items-center justify-center p-4 z-30"
>
  <div
    class="bg-white rounded-xl shadow-xl w-full max-w-xl transform transition-all scale-95 border border-gray-100 flex flex-col max-h-[90vh]"
    role="document"
  >
    <div class="px-6 py-4 border-b flex items-center justify-between">
      <h3 id="titleCreateRole" class="text-lg font-semibold text-gray-800">Criar novo Papel</h3>
      <button type="button" class="btn-secondary px-2 py-1 text-xs" id="btnCancelCreateRole">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form
      id="formCreateRole"
      method="POST"
      action="{{ route('permissoes.roles.create') }}"
      class="px-6 py-4 grid grid-cols-1 gap-4 overflow-y-auto"
    >
      @csrf
      <div>
        <label for="createRoleName" class="block text-xs font-semibold text-gray-600 mb-1">
          Nome do papel
        </label>
        <input
          name="name"
          id="createRoleName"
          class="form-input text-sm"
          placeholder="Ex.: Administrador"
          required
        >
      </div>
      <div>
        <div class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
          Permissões disponíveis
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-auto pr-1">
          @foreach($permissions as $p)
            <label class="flex items-start gap-2 p-2 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
              <input type="checkbox" name="permission_ids[]" value="{{ $p->id }}" class="mt-1">
              <div>
                <div class="text-sm font-medium text-gray-800">{{ $p->name }}</div>
                <div class="text-xs text-gray-600">{{ $permissionDescriptions[$p->id] ?? '' }}</div>
              </div>
            </label>
          @endforeach
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
        <button type="button" class="btn-secondary" id="btnCancelCreateRoleFooter">Cancelar</button>
        <button type="submit" class="btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDITAR PAPEL --}}
<div
  id="modalEditRole"
  role="dialog"
  aria-modal="true"
  aria-labelledby="titleEditRole"
  class="fixed inset-0 hidden items-center justify-center p-4 z-30"
>
  <div
    class="bg-white rounded-xl shadow-xl w-full max-w-xl transform transition-all scale-95 border border-gray-100 flex flex-col max-h-[90vh]"
    role="document"
  >
    <div class="px-6 py-4 border-b flex items-center justify-between">
      <h3 id="titleEditRole" class="text-lg font-semibold text-gray-800">Editar Papel</h3>
      <button type="button" class="btn-secondary px-2 py-1 text-xs" id="btnCancelEditRole">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form
      id="formEditRole"
      method="POST"
      class="px-6 py-4 grid grid-cols-1 gap-4 overflow-y-auto"
    >
      @csrf
      @method('PUT')
      <div>
        <label for="editRoleName" class="block text-xs font-semibold text-gray-600 mb-1">
          Nome do papel
        </label>
        <input
          name="name"
          id="editRoleName"
          class="form-input text-sm"
          placeholder="Nome do papel"
          required
        >
      </div>
      <div>
        <div class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
          Permissões disponíveis
        </div>
        <div
          id="editRolePerms"
          class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-auto pr-1"
        ></div>
      </div>
      <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
        <button type="button" class="btn-secondary" id="btnCancelEditRoleFooter">Cancelar</button>
        <button type="submit" class="btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL VER PAPEL --}}
<div
  id="modalViewRole"
  role="dialog"
  aria-modal="true"
  aria-labelledby="titleViewRole"
  class="fixed inset-0 hidden items-center justify-center p-4 z-30"
>
  <div
    class="bg-white rounded-xl shadow-xl w-full max-w-xl transform transition-all scale-95 border border-gray-100 flex flex-col max-h-[80vh]"
    role="document"
  >
    <div class="px-6 py-4 border-b flex items-center justify-between">
      <h3 id="titleViewRole" class="text-lg font-semibold text-gray-800">Detalhes do Papel</h3>
      <button type="button" class="btn-secondary px-2 py-1 text-xs" id="btnCloseViewRole">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="px-6 py-4 overflow-y-auto" id="viewRoleBody"></div>
    <div class="px-6 py-4 border-t flex justify-end">
      <button type="button" id="btnCloseViewRoleFooter" class="btn-secondary">Fechar</button>
    </div>
  </div>
</div>

{{-- MODAL EXCLUIR PAPEL --}}
<div
  id="modalDeleteRole"
  role="dialog"
  aria-modal="true"
  aria-labelledby="titleDeleteRole"
  class="fixed inset-0 hidden items-center justify-center p-4 z-30"
>
  <div
    class="bg-white rounded-xl shadow-xl w-full max-w-md transform transition-all scale-95 border border-gray-100"
    role="document"
  >
    <div class="px-6 py-4 border-b">
      <h3 id="titleDeleteRole" class="text-lg font-semibold text-gray-800">Excluir Papel</h3>
    </div>
    <div class="px-6 py-4 text-sm text-gray-700">
      Tem certeza que deseja excluir este papel? Esta ação não pode ser desfeita.
    </div>
    <div class="px-6 py-4 border-t flex justify-end gap-2">
      <form id="formDeleteRole" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger inline-flex items-center gap-2 text-sm">
          <i class="fas fa-trash-alt"></i>
          <span>Excluir</span>
        </button>
      </form>
      <button type="button" id="btnCancelDeleteRole" class="btn-secondary text-sm">
        Cancelar
      </button>
    </div>
  </div>
</div>

<script>
const overlay = document.getElementById('overlayRoles');

function openModal(el) {
  el.classList.remove('hidden');
  el.classList.add('flex');
  overlay.classList.remove('hidden');
  setTimeout(() => {
    const doc = el.querySelector('[role="document"]');
    doc.classList.remove('scale-95');
    doc.classList.add('scale-100');
  }, 0);
}

function closeModal(el) {
  const doc = el.querySelector('[role="document"]');
  doc.classList.add('scale-95');
  doc.classList.remove('scale-100');
  el.classList.add('hidden');
  el.classList.remove('flex');
  overlay.classList.add('hidden');
}

overlay && overlay.addEventListener('click', () => {
  document.querySelectorAll('[role="dialog"]').forEach(m => {
    m.classList.add('hidden');
    m.classList.remove('flex');
  });
  overlay.classList.add('hidden');
});

// ESC fecha qualquer modal aberto
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('[role="dialog"]').forEach(m => {
      if (!m.classList.contains('hidden')) {
        closeModal(m);
      }
    });
  }
});

const btnOpenCreateRole = document.getElementById('btnOpenCreateRole');
const modalCreateRole = document.getElementById('modalCreateRole');
const btnCancelCreateRole = document.getElementById('btnCancelCreateRole');
const btnCancelCreateRoleFooter = document.getElementById('btnCancelCreateRoleFooter');

btnOpenCreateRole &&
  btnOpenCreateRole.addEventListener('click', () => {
    openModal(modalCreateRole);
    setTimeout(() => document.getElementById('createRoleName').focus(), 0);
  });

[btnCancelCreateRole, btnCancelCreateRoleFooter].forEach(btn => {
  btn && btn.addEventListener('click', () => closeModal(modalCreateRole));
});

const modalEditRole = document.getElementById('modalEditRole');
const btnCancelEditRole = document.getElementById('btnCancelEditRole');
const btnCancelEditRoleFooter = document.getElementById('btnCancelEditRoleFooter');

[btnCancelEditRole, btnCancelEditRoleFooter].forEach(btn => {
  btn && btn.addEventListener('click', () => closeModal(modalEditRole));
});

const modalViewRole = document.getElementById('modalViewRole');
const btnCloseViewRole = document.getElementById('btnCloseViewRole');
const btnCloseViewRoleFooter = document.getElementById('btnCloseViewRoleFooter');

[btnCloseViewRole, btnCloseViewRoleFooter].forEach(btn => {
  btn && btn.addEventListener('click', () => closeModal(modalViewRole));
});

const modalDeleteRole = document.getElementById('modalDeleteRole');
const btnCancelDeleteRole = document.getElementById('btnCancelDeleteRole');

btnCancelDeleteRole &&
  btnCancelDeleteRole.addEventListener('click', () => closeModal(modalDeleteRole));

function renderEditPerms(selected) {
  const wrap = document.getElementById('editRolePerms');
  wrap.innerHTML = '';
  @foreach($permissions as $p)
    {
      const label = document.createElement('label');
      label.className = 'flex items-start gap-2 p-2 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer';
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.name = 'permission_ids[]';
      cb.value = '{{ $p->id }}';
      cb.className = 'mt-1';
      cb.checked = (selected || []).includes({{ $p->id }});
      const div = document.createElement('div');
      div.innerHTML = `<div class="text-sm font-medium text-gray-800">{{ $p->name }}</div><div class="text-xs text-gray-600">{{ $permissionDescriptions[$p->id] ?? '' }}</div>`;
      label.appendChild(cb);
      label.appendChild(div);
      wrap.appendChild(label);
    }
  @endforeach
}

document.querySelectorAll('.btnEditRole').forEach(btn =>
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-role');
    const tpl = document.getElementById(`role-data-${id}`);
    if (!tpl) return;
    const data = JSON.parse(tpl.innerHTML);
    document.getElementById('editRoleName').value = data.name;
    renderEditPerms(data.permissions || []);
    const form = document.getElementById('formEditRole');
    form.action = `{{ url('/configuracoes/permissoes/roles') }}/${id}`;
    openModal(modalEditRole);
  })
);

document.querySelectorAll('.btnViewRole').forEach(btn =>
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-role');
    const tpl = document.getElementById(`role-data-${id}`);
    if (!tpl) return;
    const data = JSON.parse(tpl.innerHTML);
    const body = document.getElementById('viewRoleBody');
    body.innerHTML = `
      <div class="mb-4 space-y-1">
        <div><span class="font-medium text-gray-700">Nome:</span> <span class="text-gray-800">${data.name}</span></div>
        <div><span class="font-medium text-gray-700">Código:</span> <span class="text-gray-700 text-xs bg-gray-100 px-2 py-0.5 rounded">${data.code || '-'}</span></div>
      </div>
    `;
    const list = document.createElement('div');
    list.className = 'space-y-2';
    const permsMap = {!! $permissions->keyBy('id')->toJson() !!};
    const permDescMap = @json($permissionDescriptions);
    (data.permissions || []).forEach(pid => {
      const p = permsMap[pid];
      if (p) {
        const item = document.createElement('div');
        item.className = 'border border-gray-100 rounded-lg p-2 bg-gray-50/80';
        const desc = permDescMap[pid] || '';
        item.innerHTML = `
          <div class="text-sm font-medium text-gray-800">${p.name}</div>
          <div class="text-xs text-gray-600">${desc}</div>
        `;
        list.appendChild(item);
      }
    });
    if (!list.children.length) {
      const empty = document.createElement('div');
      empty.className = 'text-sm text-gray-500 italic';
      empty.textContent = 'Nenhuma permissão atribuída.';
      list.appendChild(empty);
    }
    body.appendChild(list);
    openModal(modalViewRole);
  })
);

document.querySelectorAll('.btnDeleteRole').forEach(btn =>
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-role');
    const form = document.getElementById('formDeleteRole');
    form.action = `{{ url('/configuracoes/permissoes/roles') }}/${id}`;
    openModal(modalDeleteRole);
  })
);

const search = document.getElementById('searchRoles');
search &&
  search.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#rolesTableBody .role-row').forEach(row => {
      const name = row.querySelector('.font-medium')?.textContent.toLowerCase() || '';
      const perms = Array.from(row.querySelectorAll('.perm-badge')).map(el => el.textContent.toLowerCase());
      const match = name.includes(q) || perms.some(p => p.includes(q));
      row.style.display = match ? '' : 'none';
    });
  });
</script>
@endsection