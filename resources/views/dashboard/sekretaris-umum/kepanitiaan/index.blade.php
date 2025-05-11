@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Kepanitiaan']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.kepanitiaan.index'" :filterPlaceholder="'Cari kepanitiaan...'" :showAddButton="true"
    :addButtonText="'Tambah Kepanitiaan'" :addButtonId="'add-committee-modal'">
    <livewire:table :model="App\Models\Kepanitiaan::class" :routePrefix="'sekretaris-umum.kepanitiaan'" :columns="[
                ['field' => 'nama', 'label' => 'Nama Kepanitiaan'],
                ['field' => 'tahun', 'label' => 'Tahun'],
                ['field' => 'ketua', 'label' => 'Ketua'],
            ]" :actions="[
                ['type' => 'edit', 'label' => 'Edit Kepanitiaan'],
                ['type' => 'delete', 'label' => 'Hapus Kepanitiaan'],
            ]" :selectable="true" :sortable="true" :defaultSort="['field' => 'tahun', 'direction' => 'desc']" />

    <!-- Add Committee Modal -->
    <x-modals.add-modal id="add-committee-modal" title="Tambah Kepanitiaan" :route="route('sekretaris-umum.kepanitiaan.store')"
        :fields="[
                [
                    'name' => 'nama',
                    'label' => 'Nama Kepanitiaan',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama kepanitiaan'
                ],
                [
                    'name' => 'tahun',
                    'label' => 'Tahun',
                    'type' => 'number',
                    'required' => true,
                    'placeholder' => 'Masukkan tahun'
                ],
                [
                    'name' => 'ketua',
                    'label' => 'Ketua',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama ketua'
                ]
            ]" />

    <!-- Edit Committee Modal -->
    <x-modals.edit-modal id="edit-committee-modal" title="Edit Kepanitiaan" :route="route('sekretaris-umum.kepanitiaan.index')"
        :fields="[
                [
                    'name' => 'nama',
                    'label' => 'Nama Kepanitiaan',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama kepanitiaan'
                ],
                [
                    'name' => 'tahun',
                    'label' => 'Tahun',
                    'type' => 'number',
                    'required' => true,
                    'placeholder' => 'Masukkan tahun'
                ],
                [
                    'name' => 'ketua',
                    'label' => 'Ketua',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama ketua'
                ]
            ]" />

    <!-- Delete Committee Modal -->
    <x-modals.delete-modal id="delete-committee-modal" title="Hapus Kepanitiaan"
        :route="route('sekretaris-umum.kepanitiaan.destroy', ['kepanitiaan' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus kepanitiaan ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection