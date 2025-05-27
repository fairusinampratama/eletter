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
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-Z0-9\s]+$',
                    'title' => 'Nama kepanitiaan harus berisi 3-50 karakter, hanya huruf, angka, dan spasi',
                    'placeholder' => 'Masukkan nama kepanitiaan',
                    'colspan' => 6
                ],
                [
                    'name' => 'year',
                    'label' => 'Tahun',
                    'type' => 'year',
                    'required' => true,
                    'placeholder' => 'Masukkan tahun',
                    'help' => 'Tahun berlaku untuk ketua dan sekretaris kepanitiaan'
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
                    'help' => 'Status aktif hanya bisa diberikan pada satu kepanitiaan per tahun'
                ],
                [
                    'name' => 'chairman.fullname',
                    'label' => 'Nama Lengkap Ketua',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-Z\s.,]+$',
                    'title' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, dan koma (3-50 karakter)',
                    'placeholder' => 'Masukkan nama lengkap ketua',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman.username',
                    'label' => 'Nama Pengguna Ketua',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 12,
                    'pattern' => '^[a-zA-Z0-9_]+$',
                    'title' => 'Username hanya boleh berisi huruf, angka, dan underscore (8-12 karakter)',
                    'placeholder' => 'Masukkan nama pengguna ketua'
                ],
                [
                    'name' => 'chairman.password',
                    'label' => 'Password Ketua',
                    'type' => 'password',
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 12,
                    'title' => 'Password harus 8-12 karakter',
                    'placeholder' => 'Masukkan password ketua'
                ],
                [
                    'name' => 'secretary.fullname',
                    'label' => 'Nama Lengkap Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-Z\s.,]+$',
                    'title' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, dan koma (3-50 karakter)',
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
                    'minlength' => 8,
                    'maxlength' => 12,
                    'title' => 'Password harus 8-12 karakter',
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
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-Z0-9\s]+$',
                    'title' => 'Nama kepanitiaan harus berisi 3-50 karakter, hanya huruf, angka, dan spasi',
                    'placeholder' => 'Masukkan nama kepanitiaan',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman.year',
                    'label' => 'Tahun',
                    'type' => 'year',
                    'required' => true,
                    'placeholder' => 'Masukkan tahun',
                    'help' => 'Tahun berlaku untuk ketua dan sekretaris kepanitiaan'
                ],
                [
                    'name' => 'chairman.is_active',
                    'label' => 'Status',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
                    ],
                    'help' => 'Status aktif hanya bisa diberikan pada satu kepanitiaan per tahun'
                ],
                [
                    'name' => 'chairman.fullname',
                    'label' => 'Nama Lengkap Ketua',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-Z\s.,]+$',
                    'title' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, dan koma (3-50 karakter)',
                    'placeholder' => 'Masukkan nama lengkap ketua',
                    'colspan' => 6
                ],
                [
                    'name' => 'chairman.username',
                    'label' => 'Nama Pengguna Ketua',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 12,
                    'pattern' => '^[a-zA-Z0-9_]+$',
                    'title' => 'Username hanya boleh berisi huruf, angka, dan underscore (8-12 karakter)',
                    'placeholder' => 'Masukkan nama pengguna ketua'
                ],
                [
                    'name' => 'chairman.password',
                    'label' => 'Password Ketua',
                    'type' => 'password',
                    'minlength' => 8,
                    'maxlength' => 12,
                    'title' => 'Password harus 8-12 karakter',
                    'placeholder' => 'Biarkan kosong untuk menjaga password saat ini'
                ],
                [
                    'name' => 'secretary.fullname',
                    'label' => 'Nama Lengkap Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 3,
                    'maxlength' => 50,
                    'pattern' => '^[a-zA-Z\s.,]+$',
                    'title' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, dan koma (3-50 karakter)',
                    'placeholder' => 'Masukkan nama lengkap sekretaris',
                    'colspan' => 6
                ],
                [
                    'name' => 'secretary.username',
                    'label' => 'Nama Pengguna Sekretaris',
                    'type' => 'text',
                    'required' => true,
                    'minlength' => 8,
                    'maxlength' => 12,
                    'pattern' => '^[a-zA-Z0-9_]+$',
                    'title' => 'Username hanya boleh berisi huruf, angka, dan underscore (8-12 karakter)',
                    'placeholder' => 'Masukkan nama pengguna sekretaris'
                ],
                [
                    'name' => 'secretary.password',
                    'label' => 'Password Sekretaris',
                    'type' => 'password',
                    'minlength' => 8,
                    'maxlength' => 12,
                    'title' => 'Password harus 8-12 karakter',
                    'placeholder' => 'Biarkan kosong untuk menjaga password saat ini'
                ]
            ]" />

    <!-- Delete Committee Modal -->
    <x-modals.delete-modal id="delete-committee-modal" title="Hapus Kepanitiaan"
        :route="route('sekretaris-umum.kepanitiaan.destroy', ['kepanitiaan' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus kepanitiaan ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection