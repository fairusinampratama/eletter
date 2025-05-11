@props([
'title' => '',
'breadcrumbItems' => [],
'showFilter' => false,
'filterRoute' => null,
'filterPlaceholder' => 'Search...',
'showAddButton' => false,
'addButtonText' => 'Add New',
'addButtonId' => null,
])

<x-dashboard.content-wrapper :title="$title" :breadcrumbItems="$breadcrumbItems" :showFilter="$showFilter"
    :filterRoute="$filterRoute" :filterPlaceholder="$filterPlaceholder" :showAddButton="$showAddButton"
    :addButtonText="$addButtonText" :addButtonId="$addButtonId">
    {{ $slot }}
</x-dashboard.content-wrapper>
