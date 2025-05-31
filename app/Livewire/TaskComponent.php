<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class TaskComponent extends Component
{
    public $tasks = [];
    public $uniqueTask;
    public $titulo;
    public $descripcion;
    public $id;
    public $modal = false;
    public $modal2 = false;
    public $users = [];
    public $user_id;
    public $permission;

    public function mount()
    {
        $this->tasks = $this->getTask()->sortByDesc('id');
        $this->users = User::where('id', '!=', auth()->user()->id)->get();
    }

    public function getTask()
    {
        $user = auth()->user();
        $tasks = Task::where('user_id', auth()->user()->id)->get();
        $shareTasks = $user->shareTasks()->get();
        return $shareTasks->merge($tasks);
        // return Task::where('user_id', auth()->user()->id)->get();
    }

    public function renderAllTasks()
    {
        $this->tasks = $this->getTask()->sortByDesc('id');
    }

    public function render()
    {
        return view('livewire.task-component');
    }

    private function clearFields()
    {
        $this->titulo = '';
        $this->descripcion = '';
    }

    public function openModalTask(Task $task = null)
    {
        if($task){
            $this->uniqueTask = $task;
            $this->titulo = $task->titulo;
            $this->descripcion = $task->descripcion;
            $this->id = $task->id;
        }else{
            $this->clearFields();
        }
        $this->modal = true;
    }

    public function openModalShare(Task $task)
    {
        $this->modal2 = true;
        $this->uniqueTask = $task;
    }

    public function closeModalTask()
    {
        $this->modal = false;
    }

    public function closeModalShare()
    {
        $this->modal2 = false;
    }

    public function createUpdateTask()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        if ($this->id) {
            // Actualizar tarea existente
            Task::where('id', $this->id)->update([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion
            ]);
        } else {
            // Crear nueva tarea
            $task = new Task();
            $task->titulo = $this->titulo;
            $task->descripcion = $this->descripcion;
            $task->user_id = auth()->user()->id;
            $task->save();
        }

        $this->tasks = $this->getTask();
        $this->closeModalTask();
    }

    public function deletTask($id)
    {
        Task::find($id)->delete();
        $this->tasks = $this->getTask()->sortByDesc('id');
    }

    public function shareTask()
    {
        $task = Task::find($this->uniqueTask->id);
        $user = User::find($this->user_id);
        $user->shareTasks()->attach($task->id, ['permission' => $this->permission]);
        $task->shareWith($this->user_id, $this->permission);
        $this->closeModalShare();
        $this->tasks = $this->getTask()->sortByDesc('id');
    }

    public function notShareTask(Task $task)
    {
        $task->shareWith()->detach(auth()->user()->id);
        $this->tasks = $this->getTask()->sortByDesc('id');
    }
}
