@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Pengguna']]" :showFilter="true"
    :filterRoute="'admin-kemahasiswaan.pengguna.index'" :filterPlaceholder="'Cari pengguna...'" :showAddButton="true"
    :addButtonText="'Tambah Pengguna'" :addButtonId="'add-user-modal'">
    <livewire:table :model="App\Models\User::class" :routePrefix="'admin-kemahasiswaan.pengguna'" :columns="[
                ['field' => 'username', 'label' => 'Nama Pengguna'],
                ['field' => 'fullname', 'label' => 'Nama Lengkap'],
                ['field' => 'role.name', 'label' => 'Peran'],
                ['field' => 'institution.name', 'label' => 'Institusi'],
            ]" :actions="[
                ['type' => 'edit', 'label' => 'Edit Pengguna'],
                ['type' => 'delete', 'label' => 'Hapus Pengguna'],
            ]" :withRelations="['role', 'institution']" :bulkActions="[
                ['type' => 'delete', 'label' => 'Hapus'],
            ]" :selectable="true" :sortable="true" :defaultSort="['field' => 'id', 'direction' => 'desc']" />

    <!-- Add User Modal -->
    <x-modals.add-modal id="add-user-modal" title="Tambah Pengguna" :route="route('admin-kemahasiswaan.pengguna.store')"
        :fields="[
                [
                    'name' => 'username',
                    'label' => 'Nama Pengguna',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna'
                ],
                [
                    'name' => 'fullname',
                    'label' => 'Nama Lengkap',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap'
                ],
                [
                    'name' => 'role_id',
                    'label' => 'Peran',
                    'type' => 'select',
                    'required' => true,
                    'options' => $roles->pluck('name', 'id')->toArray()
                ],
                [
                    'name' => 'institution_id',
                    'label' => 'Institusi',
                    'type' => 'select',
                    'required' => true,
                    'options' => $institutions->pluck('name', 'id')->toArray()
                ],
                [
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password',
                    'required' => true,
                    'placeholder' => 'Enter password'
                ]
            ]" />

    <!-- Edit User Modal -->
    <x-modals.edit-modal id="edit-user-modal" title="Edit Pengguna" :route="route('admin-kemahasiswaan.pengguna.index')"
        :fields="[
                [
                    'name' => 'username',
                    'label' => 'Nama Pengguna',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna'
                ],
                [
                    'name' => 'fullname',
                    'label' => 'Nama Lengkap',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap'
                ],
                [
                    'name' => 'role_id',
                    'label' => 'Peran',
                    'type' => 'select',
                    'required' => true,
                    'options' => $roles->pluck('name', 'id')->toArray()
                ],
                [
                    'name' => 'institution_id',
                    'label' => 'Institusi',
                    'type' => 'select',
                    'required' => true,
                    'options' => $institutions->pluck('name', 'id')->toArray()
                ],
                [
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password',
                    'placeholder' => 'Biarkan kosong untuk menjaga password saat ini'
                ]
            ]" />

    <!-- Delete User Modal -->
    <x-modals.delete-modal id="delete-user-modal" title="Hapus Pengguna"
        :route="route('admin-kemahasiswaan.pengguna.destroy', ['pengguna' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection