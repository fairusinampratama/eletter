@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Pengguna']]" :showFilter="true"
    :filterRoute="'admin-kemahasiswaan.pengguna.index'" :filterPlaceholder="'Cari pengguna...'" :showAddButton="true"
    :addButtonText="'Tambah Pengguna'" :addButtonId="'add-user-modal'">
    <livewire:table :model="App\Models\User::class" :routePrefix="'admin-kemahasiswaan.pengguna'" :columns="[
                ['field' => 'username', 'label' => 'Username'],
                ['field' => 'fullname', 'label' => 'Nama Lengkap'],
                ['field' => 'role.name', 'label' => 'Peran'],
                ['field' => 'institution.name', 'label' => 'Institusi'],
                ['field' => 'year', 'label' => 'Tahun'],
                ['field' => 'is_active', 'label' => 'Status', 'type' => 'boolean'],
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
                    'label' => 'Username',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama pengguna',
                    'minlength' => '8',
                    'maxlength' => '12',
                    'pattern' => '^[a-zA-Z0-9_]+$',
                    'title' => 'Username hanya boleh berisi huruf, angka, dan underscore (8-12 karakter)'
                ],
                [
                    'name' => 'fullname',
                    'label' => 'Nama Lengkap',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap',
                    'minlength' => '3',
                    'maxlength' => '50',
                    'pattern' => '^[a-zA-Z\s.,]+$',
                    'title' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, dan koma (3-50 karakter)'
                ],
                [
                    'name' => 'role_id',
                    'label' => 'Peran',
                    'type' => 'select',
                    'required' => true,
                    'options' => $roles->pluck('name', 'id')->toArray(),
                    'help' => 'Hanya satu pengguna aktif yang diperbolehkan untuk peran Ketua Umum, Sekretaris Umum, dan Pembina per institusi per tahun'
                ],
                [
                    'name' => 'institution_id',
                    'label' => 'Institusi',
                    'type' => 'select',
                    'required' => true,
                    'options' => $institutions->pluck('name', 'id')->toArray()
                ],
                [
                    'name' => 'year',
                    'label' => 'Tahun',
                    'type' => 'year',
                    'required' => true,
                    'placeholder' => 'Masukkan tahun'
                ],
                [
                    'name' => 'is_active',
                    'label' => 'Status',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
                    ],
                    'help' => 'Status aktif hanya bisa diberikan pada satu pengguna per peran per institusi per tahun'
                ],
                [
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password',
                    'required' => true,
                    'placeholder' => 'Masukkan password',
                    'minlength' => '8',
                    'maxlength' => '12',
                    'title' => 'Password harus 8-12 karakter'
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
                    'placeholder' => 'Masukkan nama pengguna',
                    'minlength' => '8',
                    'maxlength' => '12',
                    'pattern' => '^[a-zA-Z0-9_]+$',
                    'title' => 'Username hanya boleh berisi huruf, angka, dan underscore (8-12 karakter)'
                ],
                [
                    'name' => 'fullname',
                    'label' => 'Nama Lengkap',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nama lengkap',
                    'minlength' => '3',
                    'maxlength' => '50',
                    'pattern' => '^[a-zA-Z\s.,]+$',
                    'title' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, dan koma (3-50 karakter)'
                ],
                [
                    'name' => 'role_id',
                    'label' => 'Peran',
                    'type' => 'select',
                    'required' => true,
                    'options' => $roles->pluck('name', 'id')->toArray(),
                    'help' => 'Hanya satu pengguna aktif yang diperbolehkan untuk peran Ketua Umum, Sekretaris Umum, dan Pembina per institusi per tahun'
                ],
                [
                    'name' => 'institution_id',
                    'label' => 'Institusi',
                    'type' => 'select',
                    'required' => true,
                    'options' => $institutions->pluck('name', 'id')->toArray()
                ],
                [
                    'name' => 'year',
                    'label' => 'Tahun',
                    'type' => 'year',
                    'required' => true,
                    'placeholder' => 'Masukkan tahun'
                ],
                [
                    'name' => 'is_active',
                    'label' => 'Status',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
                    ],
                    'help' => 'Status aktif hanya bisa diberikan pada satu pengguna per peran per institusi per tahun'
                ],
                [
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password',
                    'placeholder' => 'Biarkan kosong untuk menjaga password saat ini',
                    'minlength' => '8',
                    'maxlength' => '12',
                    'title' => 'Password harus 8-12 karakter'
                ]
            ]" />

    <!-- Delete User Modal -->
    <x-modals.delete-modal id="delete-user-modal" title="Hapus Pengguna"
        :route="route('admin-kemahasiswaan.pengguna.destroy', ['pengguna' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection