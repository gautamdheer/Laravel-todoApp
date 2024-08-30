<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(){
        $tasks = Task::all();
        return view('tasks.index',compact('tasks'));
    }

    public function store(Request $request){
      $request->validate([
        'title'=>'required|unique:tasks,title|max:255',
      ]);
      $task = Task::create([
        'title'=>$request->title,
      ]);
        return response()->json($task);
    }

    public function toggleCompletion(Task $task){
        $task->is_completed = !$task->is_completed;
        $task->save();
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|unique:tasks,title,' . $task->id . '|max:255',
        ]);

        $task->title = $request->title;
        $task->save();

        return response()->json($task);
    }


    public function destroy(Task $task){
        $task->delete();
        return response()->json(['message'=>'Task deleted']);
    }
}
