<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\WithModal;

class Table extends Component
{
    use WithPagination;
    use WithModal;

    public $model;
    public $routePrefix;
    public $columns;
    public $actions;
    public $withRelations;
    public $bulkActions;
    public $selectable;
    public $selected = [];
    public $search = '';

    protected $listeners = ['refreshTable' => '$refresh'];

    public function mount($model, $routePrefix, $columns, $actions = [], $withRelations = [], $bulkActions = [], $selectable = false)
    {
        $this->model = $model;
        $this->routePrefix = $routePrefix;
        $this->columns = $columns;
        $this->actions = $actions;
        $this->withRelations = $withRelations;
        $this->bulkActions = $bulkActions;
        $this->selectable = $selectable;
    }

    public function getModelClass()
    {
        return $this->model;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function performAction($type, $id = null)
    {
        switch ($type) {
            case 'edit':
                $this->openModal('edit', $id);
                break;
            case 'delete':
                $this->openModal('delete', $id);
                break;
            default:
                break;
        }
    }

    public function performBulkAction($type)
    {
        if (empty($this->selected)) {
            return;
        }

        switch ($type) {
            case 'delete':
                // Handle bulk delete
                $this->model::whereIn('id', $this->selected)->delete();
                $this->selected = [];
                $this->emit('refreshTable');
                break;
            default:
                break;
        }
    }

    public function edit($id)
    {
        $item = $this->model::with($this->withRelations)->find($id);
        $this->dispatch('edit', $item);
    }

    public function render()
    {
        $query = $this->model::query();

        if ($this->search) {
            $query->where(function ($q) {
                foreach ($this->columns as $column) {
                    $field = $column['field'];
                    if (str_contains($field, '.')) {
                        // Handle relationship fields
                        [$relation, $field] = explode('.', $field);
                        $q->orWhereHas($relation, function ($q) use ($field) {
                            $q->where($field, 'like', '%' . $this->search . '%');
                        });
                    } else {
                        $q->orWhere($field, 'like', '%' . $this->search . '%');
                    }
                }
            });
        }

        if (!empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        $records = $query->paginate(10);

        return view('livewire.table', [
            'records' => $records,
        ]);
    }
}
