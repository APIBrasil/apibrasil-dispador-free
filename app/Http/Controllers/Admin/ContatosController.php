<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tags;
use App\Models\Contatos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ContatosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contatos = Contatos::all();
        $tags = Tags::orderBy('id', 'desc')->where('status', 'active')->get();

        return view('admin.contatos')
        ->with('contatos', $contatos)
        ->with('tags', $tags);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'number' => 'required|max:13|min:11',
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'number.required' => 'O campo número é obrigatório.',
                'number.max' => 'O campo número deve ter no máximo 13 caracteres.',
                'number.min' => 'O campo número deve ter no mínimo 11 caracteres.',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validation->errors()
                ], 400);
            }

            $request->merge([
                'number' => preg_replace('/\D/', '', $request->number)
            ]);
            
            $contato = new Contatos();
            
            $contato->name = $request->name;
            $contato->number = $request->number;
            $contato->tag_id = $request->tag_id;

            $contato->save();

            return response()->json([
                'error' => false,
                'message' => 'Contato cadastrado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $contato = Contatos::find($id);

            return response()->json($contato);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'number' => 'required|max:13|min:11',
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'number.required' => 'O campo número é obrigatório.',
                'number.max' => 'O campo número deve ter no máximo 13 caracteres.',
                'number.min' => 'O campo número deve ter no mínimo 11 caracteres.',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validation->errors()
                ], 400);
            }

            $request->merge([
                'number' => preg_replace('/\D/', '', $request->number)
            ]);
            
            $contato = Contatos::find($id);

            $contato->name = $request->name;
            $contato->number = $request->number;
            $contato->tag_id = $request->tag_id;

            $contato->save();

            return response()->json([
                'error' => false,
                'message' => 'Contato atualizado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $contato = Contatos::find($id);
            $contato->delete();

            return response()->json([
                'error' => false,
                'message' => 'Contato deletado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
}
