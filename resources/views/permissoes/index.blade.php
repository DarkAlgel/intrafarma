@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col">
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
      <h1 class="text-2xl font-semibold text-gray-800">Permissões</h1>
      <div class="flex items-center gap-2">
        <a href="{{ route('permissoes.export.csv') }}" class="btn-secondary inline-flex items-center gap-2"><i class="fas fa-file-csv"></i> Exportar CSV</a>
        <a href="{{ route('permissoes.export.pdf') }}" class="btn-secondary inline-flex items-center gap-2"><i class="fas fa-file-pdf"></i> Exportar PDF</a>
        <button id="btnOpenCreateRole" class="btn-primary inline-flex items-center gap-2" aria-haspopup="dialog" aria-controls="modalCreateRole"><i class="fas fa-user-shield"></i> Criar novo Papel</button>
      </div>
    </div>
  </header>

  <main class="flex-1 p-6">
    <div class="card p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
        <input id="searchRoles" class="form-input" placeholder="Buscar por nome do papel ou permissão">
        <div class="flex items-center justify-end gap-2">
          @php $toggleDir = $dir === 'asc' ? 'desc' : 'asc'; @endphp
          <a href="{{ route('permissoes.index', ['sort' => 'name', 'dir' => $toggleDir]) }}" class="btn-secondary inline-flex items-center gap-2">
            <i class="fas fa-sort"></i> Ordenar por Nome
            <span class="text-xs">({{ $dir === 'asc' ? 'ASC' : 'DESC' }})</span>
          </a>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Papéis do Sistema</h2>
        <div class="text-xs text-gray-600">Página {{ $roles->currentPage() }} de {{ $roles->lastPage() }}</div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" aria-label="Lista de papéis">
          <thead class="bg-gray-50">
            <tr>
              <th class="table-header">Nome</th>
              <th class="table-header">Permissões atribuídas</th>
              <th class="table-header">Ações</th>
            </tr>
          </thead>
          <tbody id="rolesTableBody" class="bg-white divide-y divide-gray-200">
            @foreach($roles as $r)
              @php
                $permsForRole = $assigned->where('role_id', $r->id)->pluck('permission_id')->all();
                $permNames = $permissions->whereIn('id', $permsForRole)->pluck('name')->values()->all();
                $isDefault = in_array($r->code, $defaultCodes ?? []);
              @endphp
              <tr class="role-row">
                <td class="table-cell">
                  <div class="flex items-center gap-2">
                    <span class="font-medium">{{ $r->name }}</span>
                    @if($isDefault)
                      <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">Padrão</span>
                    @endif
                  </div>
                </td>
                <td class="table-cell">
                  <div class="flex flex-wrap gap-2">
                    @forelse($permNames as $nm)
                      <span class="inline-flex items-center px-2 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-medium perm-badge">{{ $nm }}</span>
                    @empty
                      <span class="text-gray-500 text-sm">Nenhuma</span>
                    @endforelse
                  </div>
                </td>
                <td class="table-cell">
                  <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary inline-flex items-center gap-2 btnViewRole" data-role="{{ $r->id }}"><i class="fas fa-eye"></i> Visualizar</button>
                    <button type="button" class="btn-secondary inline-flex items-center gap-2 btnEditRole" data-role="{{ $r->id }}" {{ $isDefault ? 'disabled' : '' }}><i class="fas fa-edit"></i> Editar</button>
                    <button type="button" class="btn-danger inline-flex items-center gap-2 btnDeleteRole" data-role="{{ $r->id }}" {{ $isDefault ? 'disabled' : '' }}><i class="fas fa-trash"></i> Excluir</button>
                  </div>
                  <template id="role-data-{{ $r->id }}">{{ json_encode(['id'=>$r->id,'name'=>$r->name,'code'=>$r->code,'permissions'=>$permsForRole]) }}</template>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="px-6 py-4 border-t">
        {{ $roles->links() }}
      </div>
    </div>

    <div class="card p-6 mt-6">
      <h3 class="text-md font-semibold mb-3">Atribuições rápidas</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <form method="POST" action="{{ route('permissoes.assign') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @csrf
          <select name="role_id" class="form-input" aria-label="Selecionar papel para conceder">
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->name }}</option>
            @endforeach
          </select>
          <select name="permission_id" class="form-input" aria-label="Selecionar permissão a conceder">
            @foreach($permissions as $p)
              <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
          </select>
          <button class="btn-primary">Conceder</button>
        </form>
        <form method="POST" action="{{ route('permissoes.revoke') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @csrf
          <select name="role_id" class="form-input" aria-label="Selecionar papel para revogar">
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->name }}</option>
            @endforeach
          </select>
          <select name="permission_id" class="form-input" aria-label="Selecionar permissão a revogar">
            @foreach($permissions as $p)
              <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
          </select>
          <button class="btn-secondary">Revogar</button>
        </form>
      </div>
    </div>
  </main>
</div>

<div id="overlayRoles" class="fixed inset-0 bg-black/50 hidden" aria-hidden="true"></div>

<div id="modalCreateRole" role="dialog" aria-modal="true" aria-labelledby="titleCreateRole" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-xl transform transition-all scale-95" role="document">
    <div class="px-6 py-4 border-b"><h3 id="titleCreateRole" class="text-lg font-semibold">Criar novo Papel</h3></div>
    <form id="formCreateRole" method="POST" action="{{ route('permissoes.roles.create') }}" class="px-6 py-4 grid grid-cols-1 gap-4">
      @csrf
      <input name="name" id="createRoleName" class="form-input" placeholder="Nome do papel" required>
      <div>
        <div class="text-sm text-gray-700 mb-2">Permissões disponíveis</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-auto">
          @foreach($permissions as $p)
            <label class="flex items-start gap-2 p-2 rounded border hover:bg-gray-50">
              <input type="checkbox" name="permission_ids[]" value="{{ $p->id }}" class="mt-1">
              <div>
                <div class="text-sm font-medium">{{ $p->name }}</div>
                <div class="text-xs text-gray-600">{{ $permissionDescriptions[$p->id] ?? '' }}</div>
              </div>
            </label>
          @endforeach
        </div>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" id="btnCancelCreateRole" class="btn-secondary">Cancelar</button>
        <button type="submit" class="btn-primary">Salvar</button>
      </div>
    </form>
  </div>
 </div>

<div id="modalEditRole" role="dialog" aria-modal="true" aria-labelledby="titleEditRole" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-xl transform transition-all scale-95" role="document">
    <div class="px-6 py-4 border-b"><h3 id="titleEditRole" class="text-lg font-semibold">Editar Papel</h3></div>
    <form id="formEditRole" method="POST" class="px-6 py-4 grid grid-cols-1 gap-4">
      @csrf
      @method('PUT')
      <input name="name" id="editRoleName" class="form-input" placeholder="Nome do papel" required>
      <div>
        <div class="text-sm text-gray-700 mb-2">Permissões disponíveis</div>
        <div id="editRolePerms" class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-auto"></div>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" id="btnCancelEditRole" class="btn-secondary">Cancelar</button>
        <button type="submit" class="btn-primary">Salvar</button>
      </div>
    </form>
  </div>
 </div>

<div id="modalViewRole" role="dialog" aria-modal="true" aria-labelledby="titleViewRole" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-xl transform transition-all scale-95" role="document">
    <div class="px-6 py-4 border-b"><h3 id="titleViewRole" class="text-lg font-semibold">Detalhes do Papel</h3></div>
    <div class="px-6 py-4" id="viewRoleBody"></div>
    <div class="px-6 py-4 border-t flex justify-end">
      <button type="button" id="btnCloseViewRole" class="btn-secondary">Fechar</button>
    </div>
  </div>
 </div>

<div id="modalDeleteRole" role="dialog" aria-modal="true" aria-labelledby="titleDeleteRole" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all scale-95" role="document">
    <div class="px-6 py-4 border-b"><h3 id="titleDeleteRole" class="text-lg font-semibold">Excluir Papel</h3></div>
    <div class="px-6 py-4 text-sm text-gray-700">Tem certeza que deseja excluir este papel?</div>
    <div class="px-6 py-4 border-t flex justify-end gap-2">
      <form id="formDeleteRole" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Excluir</button>
      </form>
      <button type="button" id="btnCancelDeleteRole" class="btn-secondary">Cancelar</button>
    </div>
  </div>
 </div>

<script>
const overlay = document.getElementById('overlayRoles');
function openModal(el){ el.classList.remove('hidden'); overlay.classList.remove('hidden'); setTimeout(()=>{ el.querySelector('[role="document"]').classList.remove('scale-95'); el.querySelector('[role="document"]').classList.add('scale-100'); }, 0); }
function closeModal(el){ el.querySelector('[role="document"]').classList.add('scale-95'); el.querySelector('[role="document"]').classList.remove('scale-100'); el.classList.add('hidden'); overlay.classList.add('hidden'); }
overlay && overlay.addEventListener('click', ()=>document.querySelectorAll('[role="dialog"]').forEach(m=>m.classList.add('hidden')));

const btnOpenCreateRole = document.getElementById('btnOpenCreateRole');
const modalCreateRole = document.getElementById('modalCreateRole');
const btnCancelCreateRole = document.getElementById('btnCancelCreateRole');
btnOpenCreateRole && btnOpenCreateRole.addEventListener('click', ()=>{ openModal(modalCreateRole); setTimeout(()=>document.getElementById('createRoleName').focus(),0); });
btnCancelCreateRole && btnCancelCreateRole.addEventListener('click', ()=>closeModal(modalCreateRole));

const modalEditRole = document.getElementById('modalEditRole');
const btnCancelEditRole = document.getElementById('btnCancelEditRole');
btnCancelEditRole && btnCancelEditRole.addEventListener('click', ()=>closeModal(modalEditRole));

const modalViewRole = document.getElementById('modalViewRole');
const btnCloseViewRole = document.getElementById('btnCloseViewRole');
btnCloseViewRole && btnCloseViewRole.addEventListener('click', ()=>closeModal(modalViewRole));

const modalDeleteRole = document.getElementById('modalDeleteRole');
const btnCancelDeleteRole = document.getElementById('btnCancelDeleteRole');
btnCancelDeleteRole && btnCancelDeleteRole.addEventListener('click', ()=>closeModal(modalDeleteRole));

function renderEditPerms(selected){
  const wrap = document.getElementById('editRolePerms');
  wrap.innerHTML = '';
  @foreach($permissions as $p)
    {
      const label = document.createElement('label');
      label.className = 'flex items-start gap-2 p-2 rounded border hover:bg-gray-50';
      const cb = document.createElement('input'); cb.type='checkbox'; cb.name='permission_ids[]'; cb.value='{{ $p->id }}'; cb.className='mt-1'; cb.checked = (selected || []).includes({{ $p->id }});
      const div = document.createElement('div');
      div.innerHTML = `<div class=\"text-sm font-medium\">{{ $p->name }}</div><div class=\"text-xs text-gray-600\">{{ $permissionDescriptions[$p->id] ?? '' }}</div>`;
      label.appendChild(cb); label.appendChild(div); wrap.appendChild(label);
    }
  @endforeach
}

document.querySelectorAll('.btnEditRole').forEach(btn=>btn.addEventListener('click', ()=>{
  const id = btn.getAttribute('data-role');
  const tpl = document.getElementById(`role-data-${id}`);
  if (!tpl) return;
  const data = JSON.parse(tpl.innerHTML);
  document.getElementById('editRoleName').value = data.name;
  renderEditPerms(data.permissions || []);
  const form = document.getElementById('formEditRole');
  form.action = `{{ url('/configuracoes/permissoes/roles') }}/${id}`;
  openModal(modalEditRole);
}));

document.querySelectorAll('.btnViewRole').forEach(btn=>btn.addEventListener('click', ()=>{
  const id = btn.getAttribute('data-role');
  const tpl = document.getElementById(`role-data-${id}`);
  if (!tpl) return;
  const data = JSON.parse(tpl.innerHTML);
  const body = document.getElementById('viewRoleBody');
  body.innerHTML = `<div class=\"mb-2\"><span class=\"font-medium\">Nome:</span> ${data.name}</div>`;
  const list = document.createElement('div'); list.className = 'space-y-2';
  const permsMap = {{ $permissions->keyBy('id')->toJson() }};
  const permDescMap = @json($permissionDescriptions);
  (data.permissions || []).forEach(pid=>{
    const p = permsMap[pid];
    if (p){
      const item = document.createElement('div');
      const desc = permDescMap[pid] || '';
      item.innerHTML = `<div class=\"text-sm font-medium\">${p.name}</div><div class=\"text-xs text-gray-600\">${desc}</div>`;
      list.appendChild(item);
    }
  });
  body.appendChild(list);
  openModal(modalViewRole);
}));

document.querySelectorAll('.btnDeleteRole').forEach(btn=>btn.addEventListener('click', ()=>{
  const id = btn.getAttribute('data-role');
  const form = document.getElementById('formDeleteRole');
  form.action = `{{ url('/configuracoes/permissoes/roles') }}/${id}`;
  openModal(modalDeleteRole);
}));

const search = document.getElementById('searchRoles');
search && search.addEventListener('input', (e)=>{
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('#rolesTableBody .role-row').forEach(row=>{
    const name = row.querySelector('.font-medium')?.textContent.toLowerCase() || '';
    const perms = Array.from(row.querySelectorAll('.perm-badge')).map(el=>el.textContent.toLowerCase());
    const match = name.includes(q) || perms.some(p=>p.includes(q));
    row.style.display = match ? '' : 'none';
  });
});
</script>
@endsection