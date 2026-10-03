<?php

namespace App\Http\Controllers\Admin;

use App\Models\Classe;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $classes = Classe::withCount('students')->get();
        return view('admin.classes', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:classes',
            'department' => 'required|string|max:100',
            'level' => 'required|string|max:20',
            'campus' => 'required|in:ons,mharza'
        ]);

        $class = Classe::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Classe créée avec succès',
            'class' => $class
        ]);
    }

    public function update(Request $request, $id)
    {
        $class = Classe::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:50|unique:classes,name,' . $id,
            'department' => 'required|string|max:100',
            'level' => 'required|string|max:20',
            'campus' => 'required|in:ons,mharza'
        ]);

        $class->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Classe mise à jour',
            'class' => $class
        ]);
    }

    public function destroy($id)
    {
        $class = Classe::findOrFail($id);
        
        if ($class->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cette classe contient des étudiants. Supprimez-les d\'abord.'
            ], 422);
        }

        $class->delete();

        return response()->json([
            'success' => true,
            'message' => 'Classe supprimée'
        ]);
    }
}