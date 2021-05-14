<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    public function index(Request $request)
    {
        $todos = TodoResource::collection(
            Todo::where('user_id', $request->user()->id)->paginate(50)
        );

        return $todos;
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks',
            'memo' => 'required'
        ]);

        $todo = new Todo([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'memo' => $request->memo,
            'completed' => false
        ]);

        if ($todo->save()) {
            return response()->json([
                'message' => 'Todo created.',
                'model' => new TodoResource($todo)
            ]);
        }
    }

    public function update(Request $request, Todo $todo, $id)
    {
        $request->validate([
            'title' => 'required|unique:tasks',
            'memo' => 'required'
        ]);

        $todo = $todo::findOrFail($id);

        $todo->update([
            'title' => $request->title,
            'memo' => $request->memo,
        ]);

        if ($todo->save()) {
            return response()->json([
                'message' => 'Todo updated.',
                'model' => new TodoResource($todo)
            ]);
        }
    }

    public function updatePinnedStatus(Request $request, Todo $todo, $id)
    {
        $request->validate([
            'pinned' => 'required'
        ]);

        $todo = $todo::findOrFail($id);

        $todo->update([
            'pinned' => $request->pinned
        ]);

        if ($todo->save()) {
            return response()->json([
                'message' => 'Todo pinned.',
                'model' => new TodoResource($todo)
            ]);
        }
    }

    public function updateCompletedStatus(Request $request, Todo $todo, $id)
    {
        $request->validate([
            'completed' => 'required'
        ]);

        $todo = $todo::findOrFail($id);

        $todo->update([
            'completed' => $request->completed
        ]);

        if ($todo->save()) {
            return response()->json([
                'message' => 'Todo completed.',
                'model' => new TodoResource($todo)
            ]);
        }
    }

    public function delete(Todo $todo, $id)
    {
        $todo = $todo::findOrFail($id);

        if ($todo->delete()) {
            return response()->json(['message' => 'Todo deleted.']);
        }
    }
}
