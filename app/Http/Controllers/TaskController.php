<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('fk_user_id', auth()->id());

    if ($request->has('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    $tasks = $query->get();

    return view('home.dashboard', compact('tasks'));
    }

    // Store task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'fk_user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Task created successfully!');
    } 

    // Edit task
    public function edit(Task $task)
    {
        if ($task->fk_user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    //Update task
    public function update(Request $request, Task $task)
    {
        if ($task->fk_user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $task->update($request->all());

        return redirect()->route('dashboard')->with('success', 'Task updated successfully!');
    }

    // Delete task
    public function destroy(Task $task)
    {
        if ($task->fk_user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    // Update task status
    public function updateStatus(Request $request, Task $task)
    {
        if ($task->fk_user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Toggle the status
        $task->status = $task->status === 'pending' ? 'completed' : 'pending';
        $task->save();

        return response()->json([
            'message' => 'Status updated successfully!',
            'new_status' => $task->status
        ]);
    }

}
