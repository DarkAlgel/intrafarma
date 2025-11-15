@extends('layouts.app')

@section('content')
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Usuários</h1>
                <button id="btnOpenCreate" class="btn-primary inline-flex items-center gap-2" aria-label="Criar Usuário">
                    <i class="fas fa-user-plus"></i>
                    Criar Usuário
                </button>
            </div>
        </header>
        <main class="flex-1 p-6">
            

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Lista</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" aria-label="Tabela de usuários">
                        <thead class="bg-gray-50">
                            <tr>
                                <th id="colNome" class="table-header cursor-pointer select-none" aria-sort="ascending" aria-label="Ordenar por nome">
                                    <span class="inline-flex items-center"><i class="fas fa-user mr-2"></i>Nome</span>
                                    <i id="iconSortNome" class="fas fa-sort-up ml-2"></i>
                                </th>
                                <th class="table-header"><span class="inline-flex items-center"><i class="fas fa-envelope mr-2"></i>E-mail</span></th>
                                <th class="table-header"><span class="inline-flex items-center"><i class="fas fa-key mr-2"></i>Permissão atribuída</span></th>
                                <th class="table-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $u)
                            <tr>
                                <td class="table-cell" data-name="{{ strtolower($u->name) }}">{{ $u->name }}</td>
                                <td class="table-cell">
                                    @php $validEmail = filter_var($u->email, FILTER_VALIDATE_EMAIL); @endphp
                                    <span class="inline-flex items-center {{ $validEmail ? 'text-green-700' : 'text-red-700' }}">
                                        <i class="fas {{ $validEmail ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-red-600' }} mr-2" aria-hidden="true"></i>
                                        <span aria-invalid="{{ $validEmail ? 'false' : 'true' }}">{{ $u->email }}</span>
                                    </span>
                                </td>
                                <td class="table-cell">
                                    @php 
                                        $assignedIds = ($userPerms[$u->id] ?? collect())->pluck('permission_id')->all();
                                        $assignedNames = collect($assignedIds)->map(fn($pid)=>optional($permissions->firstWhere('id', $pid))->name)->filter()->values()->all();
                                    @endphp
                                    <div class="flex flex-wrap gap-2" id="rolePermsRow{{ $u->id }}">
                                        @forelse($assignedNames as $nm)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-medium">{{ $nm }}</span>
                                        @empty
                                            <span class="text-gray-500 text-sm">Sem permissão</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="table-cell">
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-user="{{ $u->id }}" class="btn-primary inline-flex items-center p-2 btnOpenRoleModal" aria-label="Alterar Papel" aria-haspopup="dialog" aria-controls="modalRole"><i class="fas fa-user-tag"></i></button>
                                        <button type="button" data-user="{{ $u->id }}" class="btn-secondary inline-flex items-center p-2 btnOpenPermModal" aria-label="Alterar Permissão" aria-haspopup="dialog" aria-controls="modalPerm"><i class="fas fa-key"></i></button>
                                        <button type="button" data-user="{{ $u->id }}" class="btn-secondary inline-flex items-center p-2 btnOpenEditModal" aria-label="Editar" aria-haspopup="dialog" aria-controls="modalEdit"><i class="fas fa-edit"></i></button>
                                        <button type="button" data-user="{{ $u->id }}" class="btn-secondary inline-flex items-center p-2 btnOpenPasswordModal" aria-label="Alterar Senha" aria-haspopup="dialog" aria-controls="modalPassword"><i class="fas fa-lock"></i></button>
                                        <button type="button" data-user="{{ $u->id }}" class="btn-secondary inline-flex items-center p-2 btnOpenDeleteModal" aria-label="Excluir" aria-haspopup="dialog" aria-controls="modalDelete"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const rolePermsMap = @json($rolePermNames);
  const permDescriptions = @json($permissionDescriptions);
  function renderBadges(container, names) {
    container.innerHTML = '';
    (names || []).forEach(n => {
      const s = document.createElement('span');
      s.className = 'inline-flex items-center px-2 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-medium';
      s.textContent = n;
      container.appendChild(s);
    });
  }
  // Modal Create
  const btnOpenCreate = document.getElementById('btnOpenCreate');
  const modalCreate = document.getElementById('modalCreate');
  const overlayCreate = document.getElementById('overlayCreate');
  const formCreate = document.getElementById('formCreate');
  function openCreate(){ modalCreate.classList.remove('hidden'); overlayCreate.classList.remove('hidden'); setTimeout(()=>document.getElementById('create_name').focus(),0); }
  function closeCreate(){ modalCreate.classList.add('hidden'); overlayCreate.classList.add('hidden'); }
  if(btnOpenCreate){btnOpenCreate.addEventListener('click', openCreate)}
  if(overlayCreate){overlayCreate.addEventListener('click', closeCreate)}
  const btnCancelCreate = document.getElementById('btnCancelCreate');
  if(btnCancelCreate){btnCancelCreate.addEventListener('click', closeCreate)}
  function setLoading(button, loading){
    if(!button) return;
    button.disabled = !!loading;
    button.setAttribute('aria-busy', loading ? 'true' : 'false');
    if(loading){
      button.dataset.originalText = button.textContent;
      button.textContent = 'Salvando...';
    } else if(button.dataset.originalText){
      button.textContent = button.dataset.originalText;
    }
  }
  function validateCreate(){
    const name = document.getElementById('create_name');
    const email = document.getElementById('create_email');
    const pass = document.getElementById('create_password');
    const conf = document.getElementById('create_password_confirmation');
    let ok = true; [name,email,pass,conf].forEach(el=>el.classList.remove('ring-2','ring-red-400'));
    if(!name.value.trim()){name.classList.add('ring-2','ring-red-400'); ok=false}
    if(!/^\S+@\S+\.\S+$/.test(email.value)){email.classList.add('ring-2','ring-red-400'); ok=false}
    if(pass.value.length<8){pass.classList.add('ring-2','ring-red-400'); ok=false}
    if(pass.value!==conf.value){conf.classList.add('ring-2','ring-red-400'); ok=false}
    return ok;
  }
  ['create_name','create_email','create_password','create_password_confirmation'].forEach(id=>{
    const el = document.getElementById(id);
    el && el.addEventListener('input', ()=>validateCreate());
  });
  formCreate && formCreate.addEventListener('submit', (e)=>{
    const submitBtn = formCreate.querySelector('button[type="submit"]');
    if(!validateCreate()){
      e.preventDefault();
      return;
    }
    setLoading(submitBtn, true);
  });
  // Ordenação por Nome (client-side)
  const colNome = document.getElementById('colNome');
  const iconSortNome = document.getElementById('iconSortNome');
  let sortAsc = true;
  function sortByName(){
    const tbody = document.querySelector('table tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.sort((a,b)=>{
      const na = a.querySelector('td[data-name]')?.dataset.name || '';
      const nb = b.querySelector('td[data-name]')?.dataset.name || '';
      return sortAsc ? na.localeCompare(nb) : nb.localeCompare(na);
    });
    rows.forEach(r=>tbody.appendChild(r));
  }
  colNome && colNome.addEventListener('click', ()=>{
    sortAsc = !sortAsc;
    iconSortNome.className = sortAsc ? 'fas fa-sort-up ml-2' : 'fas fa-sort-down ml-2';
    colNome.setAttribute('aria-sort', sortAsc ? 'ascending' : 'descending');
    sortByName();
  });
  sortByName();
  let currentUserId = null;
  const getRowData = (userId) => {
    const rowBtn = document.querySelector(`button[data-user="${userId}"]`);
    const row = rowBtn ? rowBtn.closest('tr') : null;
    const nameCell = row ? row.querySelector('td[data-name]') : null;
    const emailSpan = row ? row.querySelector('td:nth-child(2) span[aria-invalid]') : null;
    return { name: (nameCell?.textContent || '').trim(), email: (emailSpan?.textContent || '').trim() };
  };

  const permOverlay = document.getElementById('overlayPerm');
  const permModal = document.getElementById('modalPerm');
  const permSearch = document.getElementById('permSearch');
  const permList = document.getElementById('permList');
  const permForm = document.getElementById('permForm');
  const permFormName = document.getElementById('permFormName');
  const permFormEmail = document.getElementById('permFormEmail');
  function openPerm(userId){ currentUserId = userId; const rd = getRowData(userId); if(permFormName) permFormName.value = rd.name; if(permFormEmail) permFormEmail.value = rd.email; renderPermList(''); permModal.classList.remove('hidden'); permOverlay.classList.remove('hidden'); setTimeout(()=>permSearch.focus(),0); }
  function closePerm(){ permModal.classList.add('hidden'); permOverlay.classList.add('hidden'); }
  document.querySelectorAll('.btnOpenPermModal').forEach(btn=>btn.addEventListener('click', ()=>openPerm(btn.getAttribute('data-user'))));
  permOverlay && permOverlay.addEventListener('click', closePerm);
  const btnCancelPerm = document.getElementById('btnCancelPerm');
  btnCancelPerm && btnCancelPerm.addEventListener('click', closePerm);
  const btnCancelPermFooter = document.getElementById('btnCancelPermFooter');
  btnCancelPermFooter && btnCancelPermFooter.addEventListener('click', closePerm);
  function renderPermList(q){
    permList.innerHTML='';
    @foreach($permissions as $p)
      if(!q || '{{ $p->name }}'.toLowerCase().includes(q.toLowerCase())){
        const li = document.createElement('label');
        li.className = 'block p-3 rounded border hover:bg-gray-50 cursor-pointer';
        li.innerHTML = `<input type=\"radio\" name=\"set_permission_id\" value=\"{{ $p->id }}\" class=\"mr-2\"> <span class=\"font-medium text-sm\">{{ $p->name }}</span><div class=\"text-xs text-gray-600 mt-1\">{{ $permissionDescriptions[$p->id] }}</div>`;
        permList.appendChild(li);
      }
    @endforeach
  }
  permSearch && permSearch.addEventListener('input', (e)=>renderPermList(e.target.value));
  const btnConfirmPerm = document.getElementById('btnConfirmPerm');
  btnConfirmPerm && btnConfirmPerm.addEventListener('click', ()=>{
    if(!currentUserId) return;
    setLoading(btnConfirmPerm, true);
    permForm.setAttribute('action', `{{ url('/configuracoes/usuarios') }}/${currentUserId}`);
    permForm.submit();
  });

  // Role modal
  const roleOverlay = document.getElementById('overlayRole');
  const roleModal = document.getElementById('modalRole');
  const roleSearch = document.getElementById('roleSearch');
  const roleList = document.getElementById('roleList');
  const roleForm = document.getElementById('roleForm');
  const roleFormName = document.getElementById('roleFormName');
  const roleFormEmail = document.getElementById('roleFormEmail');
  function openRole(userId){ currentUserId = userId; const rd = getRowData(userId); if(roleFormName) roleFormName.value = rd.name; if(roleFormEmail) roleFormEmail.value = rd.email; renderRoleList(''); roleModal.classList.remove('hidden'); roleOverlay.classList.remove('hidden'); setTimeout(()=>roleSearch.focus(),0); }
  function closeRole(){ roleModal.classList.add('hidden'); roleOverlay.classList.add('hidden'); }
  document.querySelectorAll('.btnOpenRoleModal').forEach(btn=>btn.addEventListener('click', ()=>openRole(btn.getAttribute('data-user'))));
  roleOverlay && roleOverlay.addEventListener('click', closeRole);
  const btnCancelRole = document.getElementById('btnCancelRole');
  btnCancelRole && btnCancelRole.addEventListener('click', closeRole);
  const btnCancelRoleFooter = document.getElementById('btnCancelRoleFooter');
  btnCancelRoleFooter && btnCancelRoleFooter.addEventListener('click', closeRole);
  function renderRoleList(q){
    roleList.innerHTML='';
    @foreach($roles as $r)
      if(!q || '{{ $r->name }}'.toLowerCase().includes(q.toLowerCase())){
        const li = document.createElement('label');
        li.className = 'block p-3 rounded border hover:bg-gray-50 cursor-pointer';
        const perms = (rolePermsMap['{{ $r->id }}'] || []).join(', ');
        li.innerHTML = `<input type=\"radio\" name=\"role_id\" value=\"{{ $r->id }}\" class=\"mr-2\"> <span class=\"font-medium text-sm\">{{ $r->name }}</span>${perms ? `<div class=\"text-xs text-gray-600 mt-1\">Permissões: ${perms}</div>` : ''}`;
        roleList.appendChild(li);
      }
    @endforeach
  }
  roleSearch && roleSearch.addEventListener('input', (e)=>renderRoleList(e.target.value));
  const btnConfirmRole = document.getElementById('btnConfirmRole');
  btnConfirmRole && btnConfirmRole.addEventListener('click', ()=>{
    if(!currentUserId) return;
    setLoading(btnConfirmRole, true);
    roleForm.setAttribute('action', `{{ url('/configuracoes/usuarios') }}/${currentUserId}`);
    roleForm.submit();
  });
  const editOverlay = document.getElementById('overlayEdit');
  const editModal = document.getElementById('modalEdit');
  const editForm = document.getElementById('editForm');
  const editName = document.getElementById('editName');
  const editEmail = document.getElementById('editEmail');
  function openEdit(userId){ currentUserId = userId; const rd = getRowData(userId); if(editName) editName.value = rd.name; if(editEmail) editEmail.value = rd.email; editModal.classList.remove('hidden'); editOverlay.classList.remove('hidden'); setTimeout(()=>editName.focus(),0); }
  function closeEdit(){ editModal.classList.add('hidden'); editOverlay.classList.add('hidden'); }
  document.querySelectorAll('.btnOpenEditModal').forEach(btn=>btn.addEventListener('click', ()=>openEdit(btn.getAttribute('data-user'))));
  editOverlay && editOverlay.addEventListener('click', closeEdit);
  const btnCancelEdit = document.getElementById('btnCancelEdit');
  btnCancelEdit && btnCancelEdit.addEventListener('click', closeEdit);
  const btnConfirmEdit = document.getElementById('btnConfirmEdit');
  btnConfirmEdit && btnConfirmEdit.addEventListener('click', ()=>{ if(!currentUserId) return; setLoading(btnConfirmEdit, true); editForm.setAttribute('action', `{{ url('/configuracoes/usuarios') }}/${currentUserId}`); editForm.submit(); });
  const passOverlay = document.getElementById('overlayPassword');
  const passModal = document.getElementById('modalPassword');
  const passForm = document.getElementById('passwordForm');
  const passNew = document.getElementById('passwordNew');
  const passFormName = document.getElementById('passwordFormName');
  const passFormEmail = document.getElementById('passwordFormEmail');
  function openPassword(userId){ currentUserId = userId; const rd = getRowData(userId); if(passFormName) passFormName.value = rd.name; if(passFormEmail) passFormEmail.value = rd.email; passModal.classList.remove('hidden'); passOverlay.classList.remove('hidden'); setTimeout(()=>passNew.focus(),0); }
  function closePassword(){ passModal.classList.add('hidden'); passOverlay.classList.add('hidden'); }
  document.querySelectorAll('.btnOpenPasswordModal').forEach(btn=>btn.addEventListener('click', ()=>openPassword(btn.getAttribute('data-user'))));
  passOverlay && passOverlay.addEventListener('click', closePassword);
  const btnCancelPassword = document.getElementById('btnCancelPassword');
  btnCancelPassword && btnCancelPassword.addEventListener('click', closePassword);
  const btnConfirmPassword = document.getElementById('btnConfirmPassword');
  btnConfirmPassword && btnConfirmPassword.addEventListener('click', ()=>{ if(!currentUserId) return; setLoading(btnConfirmPassword, true); passForm.setAttribute('action', `{{ url('/configuracoes/usuarios') }}/${currentUserId}`); passForm.submit(); });
  const deleteOverlay = document.getElementById('overlayDelete');
  const deleteModal = document.getElementById('modalDelete');
  const btnCancelDelete = document.getElementById('btnCancelDelete');
  const btnConfirmDelete = document.getElementById('btnConfirmDelete');
  function openDelete(userId){ currentUserId = userId; deleteModal.classList.remove('hidden'); deleteOverlay.classList.remove('hidden'); }
  function closeDelete(){ deleteModal.classList.add('hidden'); deleteOverlay.classList.add('hidden'); }
  document.querySelectorAll('.btnOpenDeleteModal').forEach(btn=>btn.addEventListener('click', ()=>openDelete(btn.getAttribute('data-user'))));
  deleteOverlay && deleteOverlay.addEventListener('click', closeDelete);
  btnCancelDelete && btnCancelDelete.addEventListener('click', closeDelete);
  // Acessibilidade: fechar modais com ESC e trap de foco
  function trapFocus(modal){
    const focusable = modal.querySelectorAll('a[href], button, textarea, input, select');
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    modal.addEventListener('keydown', (e)=>{
      if(e.key === 'Escape'){
        if(modal === modalCreate) closeCreate(); else if(modal === roleModal) closeRole(); else if(modal === permModal) closePerm(); else if(modal === editModal) closeEdit(); else if(modal === passModal) closePassword(); else if(modal === deleteModal) closeDelete();
      }
      if(e.key === 'Tab'){
        if(e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
        else if(!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
      }
    });
  }
  modalCreate && trapFocus(modalCreate);
  permModal && trapFocus(permModal);
  roleModal && trapFocus(roleModal);
  editModal && trapFocus(editModal);
  passModal && trapFocus(passModal);
  deleteModal && trapFocus(deleteModal);
});
</script>
<div id="overlayCreate" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" aria-hidden="true"></div>
<div id="modalCreate" role="dialog" aria-modal="true" aria-labelledby="titleCreate" class="fixed inset-0 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-xl">
    <div class="px-6 py-4 border-b"><h3 id="titleCreate" class="text-lg font-semibold">Criar Usuário</h3></div>
    <form id="formCreate" method="POST" action="{{ route('usuarios.store') }}" class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      <div class="md:col-span-2">
        <label class="text-sm text-gray-700">Nome completo</label>
        <input id="create_name" name="name" class="form-input" required>
      </div>
      <div class="md:col-span-2">
        <label class="text-sm text-gray-700">E-mail</label>
        <input id="create_email" name="email" type="email" class="form-input" required>
      </div>
      <div>
        <label class="text-sm text-gray-700">Senha</label>
        <input id="create_password" name="password" type="password" class="form-input" required>
      </div>
      <div>
        <label class="text-sm text-gray-700">Confirmar senha</label>
        <input id="create_password_confirmation" name="password_confirmation" type="password" class="form-input" required>
      </div>
      <div class="md:col-span-2">
        <label class="text-sm text-gray-700">Permissão inicial</label>
        <select name="initial_permission_id" class="form-input">
          <option value="">Selecione</option>
          @foreach($permissions as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-2 flex justify-end gap-2 mt-2">
        <button type="button" id="btnCancelCreate" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancelar</button>
        <button type="submit" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">Salvar</button>
      </div>
    </form>
  </div>
</div>


<div id="overlayRole" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" aria-hidden="true"></div>
<div id="modalRole" role="dialog" aria-modal="true" aria-labelledby="titleRole" class="fixed inset-0 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 id="titleRole" class="text-lg font-semibold">Alterar Papel</h3>
        <button id="btnCancelRole" class="text-sm text-gray-600 hover:text-gray-800">Fechar</button>
    </div>
    <div class="px-6 py-4">
        <input id="roleSearch" class="form-input w-full" placeholder="Buscar papel pelo nome">
        <div id="roleList" class="mt-4 space-y-2" role="listbox" aria-label="Lista de papéis"></div>
    </div>
    <div class="px-6 py-4 border-t flex justify-end gap-2">
        <form id="roleForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="roleFormName" name="name">
            <input type="hidden" id="roleFormEmail" name="email">
            <button type="button" id="btnConfirmRole" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">Confirmar</button>
        </form>
        <button type="button" id="btnCancelRoleFooter" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancelar</button>
 </div>
</div>

<div id="overlayPerm" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" aria-hidden="true"></div>
<div id="modalPerm" role="dialog" aria-modal="true" aria-labelledby="titlePerm" class="fixed inset-0 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 id="titlePerm" class="text-lg font-semibold">Alterar Permissão</h3>
        <button id="btnCancelPerm" class="text-sm text-gray-600 hover:text-gray-800">Fechar</button>
    </div>
    <div class="px-6 py-4">
        <input id="permSearch" class="form-input w-full" placeholder="Buscar permissão pelo nome">
        <div id="permList" class="mt-4 space-y-2" role="listbox" aria-label="Lista de permissões"></div>
    </div>
    <div class="px-6 py-4 border-t flex justify-end gap-2">
        <form id="permForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="permFormName" name="name">
            <input type="hidden" id="permFormEmail" name="email">
            <button type="button" id="btnConfirmPerm" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">Confirmar</button>
        </form>
        <button type="button" id="btnCancelPermFooter" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancelar</button>
    </div>
  </div>
 </div>

<div id="overlayEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" aria-hidden="true"></div>
<div id="modalEdit" role="dialog" aria-modal="true" aria-labelledby="titleEdit" class="fixed inset-0 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-xl">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 id="titleEdit" class="text-lg font-semibold">Editar Usuário</h3>
        <button id="btnCancelEdit" class="text-sm text-gray-600 hover:text-gray-800">Fechar</button>
    </div>
    <form id="editForm" method="POST" class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      @method('PUT')
      <div class="md:col-span-2">
        <label class="text-sm text-gray-700">Nome</label>
        <input id="editName" name="name" class="form-input" required>
      </div>
      <div class="md:col-span-2">
        <label class="text-sm text-gray-700">E-mail</label>
        <input id="editEmail" name="email" type="email" class="form-input" required>
      </div>
      <div class="md:col-span-2 flex justify-end gap-2 mt-2">
        <button type="button" id="btnConfirmEdit" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">Salvar</button>
      </div>
    </form>
  </div>
 </div>

<div id="overlayPassword" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" aria-hidden="true"></div>
<div id="modalPassword" role="dialog" aria-modal="true" aria-labelledby="titlePassword" class="fixed inset-0 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-xl">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 id="titlePassword" class="text-lg font-semibold">Alterar Senha</h3>
        <button id="btnCancelPassword" class="text-sm text-gray-600 hover:text-gray-800">Fechar</button>
    </div>
    <form id="passwordForm" method="POST" class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      @method('PUT')
      <input type="hidden" id="passwordFormName" name="name">
      <input type="hidden" id="passwordFormEmail" name="email">
      <div>
        <label class="text-sm text-gray-700">Nova senha</label>
        <input id="passwordNew" name="password" type="password" class="form-input" required>
      </div>
      <div>
        <label class="text-sm text-gray-700">Confirmar senha</label>
        <input id="passwordConf" name="password_confirmation" type="password" class="form-input" required>
      </div>
      <div class="md:col-span-2 flex justify-end gap-2 mt-2">
        <button type="button" id="btnConfirmPassword" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">Salvar</button>
      </div>
    </form>
  </div>
 </div>

<div id="overlayDelete" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" aria-hidden="true"></div>
<div id="modalDelete" role="dialog" aria-modal="true" aria-labelledby="titleDelete" class="fixed inset-0 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
    <div class="px-6 py-4 border-b">
        <h3 id="titleDelete" class="text-lg font-semibold">Excluir Usuário</h3>
    </div>
    <div class="px-6 py-4">
        <p class="text-sm text-gray-700">Confirma a exclusão deste usuário?</p>
    </div>
    <div class="px-6 py-4 border-t flex justify-end gap-2">
        <button type="button" id="btnCancelDelete" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancelar</button>
        <button type="button" id="btnConfirmDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Excluir</button>
    </div>
  </div>
 </div>
 </div>
@endsection