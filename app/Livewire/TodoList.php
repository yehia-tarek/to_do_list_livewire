<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;
    public $search;
    #[Rule('required|min:3|max:50')]
    public $editingTodoID;
    public $editingTodoName;

    public function create(){
        $validated = $this->validateOnly('name');
        Todo::create([
            'name' => $validated['name']
        ]);
        $this->reset('name');
        session()->flash('success', 'Created');
        $this->resetPage();
    }

    public function delete($todoId){
        try {
            Todo::findOrFail($todoId)->delete();
        } catch (Exception $e) {
            session()->flash('error','Failed to delete todo!');
        }
    }

    public function toggle($todoId){
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoId){
        $this->editingTodoID = $todoId;
        $this->editingTodoName = Todo::find($todoId)->name;
    }
    public function cancelEdit(){
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function update(){
        $validated = $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoID)->update([
            'name' => $this->editingTodoName
        ]);
        $this->cancelEdit();
    }
    public function render()
    {
        return view('livewire.todo-list',[
    'todos' => Todo::latest()->where('name', 'like', '%' . $this->search . '%')->paginate(5)
        ]);
    }
}
