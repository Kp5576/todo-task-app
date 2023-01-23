<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Paginate the authenticated user's tasks.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // paginate the authorized user's tasks with 5 per page
        $tasks = Auth::user()
            ->tasks()
            ->orderBy('is_complete')
            ->orderByDesc('created_at')
            ->paginate(5);

        // return task index view with paginated tasks
        return view('tasks', [
            'tasks' => $tasks
        ]);
    }


    public function show()
    {
        $tasks = Auth::user()
            ->tasks()
            ->select('*')->get(); 
            if($tasks){
                return response()->json($tasks);
            }
            else{
                return response()->json(array('message' => 'No Record Found.', 'status' => false));
            }
    } 

    /**
     * Store a new incomplete task for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // validate the given request
        $data = $this->validate($request, [
            'title' => 'required|string|max:255',
        ]);

        // create a new incomplete task with the given title
        $tasks =  Auth::user()->tasks()->create([
            'title' => $data['title'],
            'is_complete' => false,
        ]);

        if($tasks){
            return response()->json(array('message' => ' Task Created!', 'status' => True));
        }
        else{
            return response()->json(array('message' => 'Task not Created!', 'status' => false));
        }

       
    }

    /**
     * Mark the given task as complete and redirect to tasks index.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $task, $id)
    {
        // check if the authenticated user can complete the task
        //$this->authorize('complete', $task);

        // mark the task as complete and save it
      
        
       $task =  Task::where('id', $task->id)
  ->update(['is_complete' => true]);
        // flash a success message to the session
        if($task){
            return response()->json(array('message' => ' Task Complete!', 'status' => True));
        }
        else{
            return response()->json(array('message' => 'Task not Complete!', 'status' => false));
        }
    }

    public function destroy(Request $task,$id)
    {
       $task =  Task::where('id', $task->id)->delete();
    
        if($task){
            return response()->json(array('message' => ' Task Deleted!', 'status' => True));
        }
        else{
            return response()->json(array('message' => 'Task not Deleted!', 'status' => false));
        }
    }

    public function edits(Request $task)
    {
        $task =  Task::where('id', $task->id)->first();
    
        if($task){
            return response()->json($task);
        }
        else{
            return response()->json(array('message' => 'Some Error', 'status' => false));
        }    }

        public function updateedit(Request $task){

            $taskd =  Task::where('id', $task->id)
  ->update(['is_complete' => $task->is_complete,'title' => $task->title]);

            if($taskd){
                return response()->json(array('message' => 'Update Success', 'status' => True));
            }
            else{
                return response()->json(array('message' => 'Some Error', 'status' => false));
            } 

        }
}