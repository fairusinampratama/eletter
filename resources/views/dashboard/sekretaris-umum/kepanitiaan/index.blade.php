@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Kepanitiaan']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.kepanitiaan.index'" :filterPlaceholder="'Cari kepanitiaan...'" :showAddButton="true"
    :addButtonText="'Tambah Kepanitiaan'" :addButtonId="'add-committee-modal'">
    <livewire:table :model="App\Models\Committee::class" :routePrefix="'sekretaris-umum.kepanitiaan'" :columns="[
        ['field' => 'name', 'label' => 'Nama Kepanitiaan'],
        ['field' => 'chairman.fullname', 'label' => 'Ketua'],
        ['field' => 'secretary.fullname', 'label' => 'Sekretaris'],
    ]" :actions="[
        ['type' => 'edit', 'label' => 'Edit Kepanitiaan'],
        ['type' => 'delete', 'label' => 'Hapus Kepanitiaan'],
    ]" :withRelations="['chairman', 'secretary']" :bulkActions="[
        ['type' => 'delete', 'label' => 'Hapus']
    ]" :selectable="true" :sortable="true" :defaultSort="['field' => 'id', 'direction' => 'desc']" />

    <!-- Add Committee Modal -->
    <x-modals.add-modal id="add-committee-modal" title="Tambah Kepanitiaan"
        :route="route('sekretaris-umum.kepanitiaan.store')" :fields="[
                [
                    'name' => 'name',
                    'label' => 'Nama Kepanitiaan',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama kepanitiaan',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman_fullname',
                    'label' => 'Nama Lengkap Ketua',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap ketua',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman_username',
                    'label' => 'Nama Pengguna Ketua',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna ketua'
                ],
                [
                    'name' => 'chairman_password',
                    'label' => 'Password Ketua',
                    'type' => 'password',
                    'required' => true,
                    'placeholder' => 'Masukkan password ketua'
                ],
                [
                    'name' => 'secretary_fullname',
                    'label' => 'Nama Lengkap Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap sekretaris',
                    'colspan' => 6
                ],
                [
                    'name' => 'secretary_username',
                    'label' => 'Nama Pengguna Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna sekretaris'
                ],
                [
                    'name' => 'secretary_password',
                    'label' => 'Password Sekretaris',
                    'type' => 'password',
                    'required' => true,
                    'placeholder' => 'Masukkan password sekretaris'
                ]
            ]" />

    <!-- Edit Committee Modal -->
    <x-modals.edit-modal id="edit-committee-modal" title="Edit Kepanitiaan"
        :route="route('sekretaris-umum.kepanitiaan.index')" :fields="[
                [
                    'name' => 'name',
                    'label' => 'Nama Kepanitiaan',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama kepanitiaan',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman.fullname',
                    'label' => 'Nama Lengkap Ketua',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap ketua',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman.username',
                    'label' => 'Nama Pengguna Ketua',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna ketua'
                ],
                [
                    'name' => 'chairman.password',
                    'label' => 'Password Ketua',
                    'type' => 'password',
                    'required' => true,
                    'placeholder' => 'Masukkan password ketua'
                ],
                [
                    'name' => 'secretary.fullname',
                    'label' => 'Nama Lengkap Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap sekretaris',
                    'colspan' => 6
                ],
                [
                    'name' => 'secretary.username',
                    'label' => 'Nama Pengguna Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna sekretaris'
                ],
                [
                    'name' => 'secretary.password',
                    'label' => 'Password Sekretaris',
                    'type' => 'password',
                    'required' => true,
                    'placeholder' => 'Masukkan password sekretaris'
                ]
            ]" />

    <!-- Delete Committee Modal -->
    <x-modals.delete-modal id="delete-committee-modal" title="Hapus Kepanitiaan"
        :route="route('sekretaris-umum.kepanitiaan.destroy', ['kepanitiaan' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus kepanitiaan ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection
