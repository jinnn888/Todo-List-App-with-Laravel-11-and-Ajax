<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Carbon;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $todos = Todo::query();

            return DataTables::of($todos)
            ->addIndexColumn()
            ->addColumn('created_at', function($todo) {
                return Carbon::parse($todo->created_at)->format('Y-m-d');
            })
            ->addColumn('status', function($todo) {
                return $todo->status == "incomplete" ? "<p class=' bg-danger text-white rounded px-2 w-50'>Incomplete</p>" : "<p class='bg-success text-white rounded px-2 w-50'>Complete</p>";
            })
            ->addColumn('actions', function($todo) {
                return "
                    <button id='edit-btn' type='button' data-id='". $todo->id ."'class='btn btn-sm btn-success' data-bs-toggle='modal' data-bs-target='#editModal'>Edit</button>
                    <button id='delete-btn' class='btn btn-sm btn-danger' data-id='". $todo->id ."'>Delete</button>
                ";
            })
            ->rawColumns(['actions', 'status'])
            ->toJson();
        }

        return view('todo/index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['todo' => 'required']);

        Todo::create([
            'task' => $request->todo,
            'status' => 'incomplete'
        ]);


        return response()->json(['status' => 'success', 'message' => 'Todo created successfully']);


    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        return response()->json($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $todo->update([
            "task" => $request->todo,
            "status" => $request->status
        ]);


        return response()->json(['status' => 'success', 'message' => 'Todo updated successfully']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return response()->json(['status' => 'success', 'message' => 'Todo deleted successfully']);
    }
}
