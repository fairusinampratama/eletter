@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Kategori Surat']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.kategori-surat.index'" :filterPlaceholder="'Cari kategori surat...'"
    :showAddButton="true" :addButtonText="'Tambah Kategori Surat'" :addButtonId="'add-letter-category-modal'">
    <livewire:table :model="App\Models\LetterCategory::class" :routePrefix="'sekretaris-umum.kategori-surat'" :columns="[
                ['field' => 'name', 'label' => 'Nama Kategori']
            ]" :actions="[
                ['type' => 'edit', 'label' => 'Edit Kategori'],
                ['type' => 'delete', 'label' => 'Hapus Kategori'],
            ]" :selectable="true" :bulkActions="[
            ['type' => 'delete', 'label' => 'Hapus'],
        ]" :sortable="true" :defaultSort="['field' => 'id', 'direction' => 'desc']" :scopes="['nonCommittee']" />


    <!-- Add Letter Category Modal -->
    <x-modals.add-modal id="add-letter-category-modal" title="Tambah Kategori Surat"
        :route="route('sekretaris-umum.kategori-surat.store')" :fields="[
                [
                    'name' => 'name',
                    'label' => 'Nama Kategori',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama kategori surat',
                    'minlength' => '3',
                    'maxlength' => '50',
                    'pattern' => '^[a-zA-Z0-9\s]+$',
                    'title' => 'Nama kategori hanya boleh berisi huruf, dan angka (3-50 karakter)'
                ]
            ]" />

    <!-- Edit Letter Category Modal -->
    <x-modals.edit-modal id="edit-letter-category-modal" title="Edit Kategori Surat"
        :route="route('sekretaris-umum.kategori-surat.index')" :fields="[
                [
                    'name' => 'name',
                    'label' => 'Nama Kategori',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama kategori surat',
                    'minlength' => '3',
                    'maxlength' => '50',
                    'pattern' => '^[a-zA-Z0-9\s]+$',
                    'title' => 'Nama kategori hanya boleh berisi huruf, dan angka (3-50 karakter)'
                ]
            ]" />

    <!-- Delete Letter Category Modal -->
    <x-modals.delete-modal id="delete-letter-category-modal" title="Hapus Kategori Surat"
        :route="route('sekretaris-umum.kategori-surat.destroy', ['kategori_surat' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus kategori surat ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection