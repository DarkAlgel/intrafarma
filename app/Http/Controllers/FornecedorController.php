<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;

class FornecedorController extends Controller
{
    public function index(Request $request)
    {
        $sort = in_array($request->get('sort'), ['nome', 'tipo', 'contato']) ? $request->get('sort') : 'nome';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';

        $fornecedores = Fornecedor::orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('fornecedores.index', [
            'fornecedores' => $fornecedores,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}