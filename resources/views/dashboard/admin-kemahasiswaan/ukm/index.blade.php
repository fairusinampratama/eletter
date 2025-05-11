@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'UKM']]" :showFilter="true"
    :filterRoute="'admin-kemahasiswaan.ukm.index'" :filterPlaceholder="'Cari UKM...'" :showAddButton="true"
    :addButtonText="'Tambah UKM'" :addButtonId="'add-institution-modal'">
    <livewire:table :model="App\Models\Institution::class" :routePrefix="'admin-kemahasiswaan.ukm'" :columns="[
                ['field' => 'name', 'label' => 'Nama UKM', 'sortable' => true],
            ]" :actions="[
                ['type' => 'edit', 'label' => 'Edit UKM'],
                ['type' => 'delete', 'label' => 'Hapus UKM'],
            ]" :bulkActions="[
                ['type' => 'delete', 'label' => 'Hapus'],
            ]" :selectable="true" :sortable="true" :defaultSort="['field' => 'name', 'direction' => 'asc']" />

    <!-- Add Institution Modal -->
    <x-modals.add-modal id="add-institution-modal" title="Tambah UKM" :route="route('admin-kemahasiswaan.ukm.store')"
        :fields="[
                [
                    'name' => 'name',
                    'label' => 'Nama UKM',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama UKM'
                ],
            ]" />

    <!-- Edit Institution Modal -->
    <x-modals.edit-modal id="edit-institution-modal" title="Edit UKM" :route="route('admin-kemahasiswaan.ukm.index')"
        :fields="[
                [
                    'name' => 'name',
                    'label' => 'Nama UKM',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama UKM'
                ],
            ]" />

    <!-- Delete Institution Modal -->
    <x-modals.delete-modal id="delete-institution-modal" title="Hapus UKM"
        :route="route('admin-kemahasiswaan.ukm.destroy', ['ukm' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus UKM ini? Tindakan ini tidak dapat dibatalkan dan Semua data yang berhubungan dengan UKM juga akan dihapus.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection