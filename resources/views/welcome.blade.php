<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmácia</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-200">
    <h1 class="text-3xl font-bold text-blue-600 underline p-10">
        Olá, Tailwind!
    </h1>

    <div class="bg-gray-100 p-10 m-10 rounded-lg shadow-lg">
        <p>Seu sistema de farmácia está funcionando com Laravel e Tailwind!</p>
    </div>

    <div class="bg-white p-10 m-10 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4">Teste de Conexão (PostgreSQL)</h2>

        @if(isset($dbStatus) && $dbStatus == 'Conectado com sucesso!')
            
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">
                <p class="font-bold">Status: {{ $dbStatus }}</p>
                <p class="text-sm mt-2">Info: <code class="text-xs">{{ $dbInfo }}</code></p>
            </div>

        @else

            <div class="p-4 bg-red-100 text-red-800 rounded-lg">
                <p class="font-bold">Status: {{ $dbStatus ?? 'Erro desconhecido' }}</p>
                <p class="text-sm mt-2">Erro: <code class="text-xs">{{ $dbInfo }}</code></p>
            </div>

        @endif

    </div>

</body>
</html>