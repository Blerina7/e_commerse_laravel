<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all(); 
        return response()->json($tasks); 
    }

    
    public function store(Request $request)
    {
        
        $task = Task::create([
            'title' => $request->title
        ]);

        return response()->json([
            'mesazhi' => 'Detyra u shtua me sukses!',
            'detyra' => $task
        ]);
    }
}
