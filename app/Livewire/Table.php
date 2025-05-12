<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $model;
    public $routePrefix;
    public $columns = [];
    public $actions = [];
    public $searchable = true;
    public $withRelations = [];
    public $bulkActions = [];
    public $selectable = false;
    public $selected = [];
    public $selectAll = false;
    public $search = '';
    public $sortField = null;
    public $sortDirection = 'asc';
    public $scopes = [];
    public $defaultSort = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => null],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($model, $routePrefix = null, $columns = [], $actions = [], $searchable = true, $withRelations = [], $bulkActions = [], $selectable = false, $scopes = [], $defaultSort = null)
    {
        $this->model = $model;
        $this->routePrefix = $routePrefix;
        $this->columns = $columns;
        $this->actions = $actions;
        $this->searchable = $searchable;
        $this->withRelations = $withRelations;
        $this->bulkActions = $bulkActions;
        $this->selectable = $selectable;
        $this->scopes = $scopes;
        $this->defaultSort = $defaultSort;

        // Set default sort if not already set
        if ($this->defaultSort && !$this->sortField) {
            $this->sortField = $this->defaultSort['field'] ?? null;
            $this->sortDirection = $this->defaultSort['direction'] ?? 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        $this->selectAll = count($this->selected) === $this->getQuery()->count();
    }

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function getTotalCountProperty()
    {
        return $this->getQuery()->count();
    }

    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function edit($id)
    {
        $item = $this->model::with($this->withRelations)->find($id);
        $this->dispatch('edit', $item);
    }

    public function delete($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];

        $this->model::whereIn('id', $ids)->delete();

        $this->selected = array_diff($this->selected, $ids);

        if (empty($this->selected)) {
            $this->selectAll = false;
        }

        $this->dispatch('deleted', $ids);
    }

    public function view($id)
    {
        $item = $this->model::with($this->withRelations)->find($id);
        $this->dispatch('edit', $item);
        return redirect()->to('/storage/' . $item->file_path);
    }

    public function confirm($id)
    {
        $this->dispatch('confirm', $id);
    }

    protected function getQuery()
    {
        $query = $this->model::query();

        // Load relations
        if (!empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        // Apply scopes
        foreach ($this->scopes as $scope) {
            $query->$scope();
        }

        // Apply search using model's scopeSearch
        if ($this->searchable && !empty($this->search)) {
            $query->search($this->search);
        }

        // Apply sorting
        if ($this->sortField) {
            // Handle relationship sorting
            if (str_contains($this->sortField, '.')) {
                [$relation, $field] = explode('.', $this->sortField);

                // Get the model instance to determine table names
                $model = new $this->model;
                $relationModel = $model->$relation()->getRelated();

                // Get the foreign key name
                $foreignKey = $model->$relation()->getForeignKeyName();

                // Build the join and order by
                $query->join(
                    $relationModel->getTable() . ' as ' . $relation,
                    $model->getTable() . '.' . $foreignKey,
                    '=',
                    $relation . '.id'
                )
                    ->orderBy($relation . '.' . $field, $this->sortDirection)
                    ->select($model->getTable() . '.*');
            } else {
                // Direct field sorting
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        }

        return $query;
    }

    public function render()
    {
        $data = $this->getQuery()->paginate(10);

        return view('livewire.table', [
            'data' => $data,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'availableActions' => $this->getAvailableActions($data),
        ]);
    }

    protected function getAvailableActions($items)
    {
        $availableActions = [];
        $currentUser = auth()->user();

        foreach ($items as $item) {
            $itemActions = [];
            foreach ($this->actions as $action) {
                // if ($action['type'] === 'view' && !$item->isFullySigned()) {
                //     continue;
                // }

                if ($action['type'] === 'confirm') {
                    // Skip if user has already signed
                    if ($item->hasUserSigned($currentUser->id)) {
                        continue;
                    }

                    // Get the user's signature order
                    $userSignature = $item->signatures()
                        ->where('signer_id', $currentUser->id)
                        ->first();

                    if (!$userSignature) {
                        continue;
                    }

                    // Check if all previous signers have signed
                    $previousSignatures = $item->signatures()
                        ->where('order', '<', $userSignature->order)
                        ->whereNotNull('signed_at')
                        ->count();

                    $requiredPreviousSignatures = $userSignature->order - 1;

                    if ($previousSignatures < $requiredPreviousSignatures) {
                        continue;
                    }
                }

                $itemActions[] = $action;
            }
            $availableActions[$item->id] = $itemActions;
        }

        return $availableActions;
    }
}

