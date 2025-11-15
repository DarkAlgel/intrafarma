<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Exportação de Papéis e Permissões</title>
  <style>
    body { font-family: Arial, sans-serif; color: #111827; }
    h1 { font-size: 20px; margin: 0 0 16px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 12px; }
    th { background: #f9fafb; }
    .badge { display:inline-block; background:#ede9fe; color:#6d28d9; padding:4px 8px; border-radius:999px; font-size:11px; margin:2px; }
  </style>
</head>
<body>
  <h1>Papéis e Permissões</h1>
  <table>
    <thead>
      <tr>
        <th>Papel</th>
        <th>Permissões</th>
      </tr>
    </thead>
    <tbody>
      @foreach($roles as $r)
        @php
          $permsForRole = $assigned->where('role_id', $r->id)->pluck('permission_id')->all();
          $permNames = $permissions->whereIn('id', $permsForRole)->pluck('name')->values()->all();
        @endphp
        <tr>
          <td>{{ $r->name }}</td>
          <td>
            @forelse($permNames as $nm)
              <span class="badge">{{ $nm }}</span>
            @empty
              Nenhuma
            @endforelse
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>