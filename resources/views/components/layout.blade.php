<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Farmácia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Navbar temporária --}}
    <nav class="bg-blue-700 text-white p-4">
        <div class="container mx-auto">
            <span class="font-bold text-lg">Sistema Farmácia Comunitária</span>
        </div>
    </nav>

    <main class="container mx-auto mt-6">
        {{ $slot }}
    </main>

</body>
</html>
