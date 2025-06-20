@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Surat Panitia']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.surat-panitia.index'" :filterPlaceholder="'Cari surat panitia...'"
    :showAddButton="false">
    <livewire:table :model="App\Models\Letter::class" :routePrefix="'sekretaris-umum.surat-panitia'" :columns="[
                ['field' => 'code', 'label' => 'Kode'],
                ['field' => 'category.name', 'label' => 'Kategori'],
                ['field' => 'creator.fullname', 'label' => 'Pembuat'],
                ['field' => 'date', 'label' => 'Tanggal'],
                ['field' => 'status', 'label' => 'Status'],
                ['field' => 'signing_status', 'label' => 'Status Tanda Tangan', 'type' => 'component', 'component' => 'signing-status', 'sortable' => false],
            ]" :actions="[
                ['type' => 'confirm', 'label' => 'Tandatangani'],
                ['type' => 'view', 'label' => 'Lihat'],
                ['type' => 'verify', 'label' => 'Verifikasi'],
            ]" :withRelations="['category', 'creator', 'signatures.signer']" :selectable="false" :sortable="true"
        :defaultSort="['field' => 'date', 'direction' => 'desc']" :scopes="['committeeOnly']" />

    <!-- Add Letter Modal -->
    <x-modals.add-modal id="add-letter-modal" title="Tambah Surat Panitia"
        :route="route('sekretaris-panitia.surat-panitia.store')" :fields="[
            [
                'name' => 'code',
                'label' => 'Kode Surat',
                'type' => 'text',
                'required' => false,
                'readonly' => true,
                'placeholder' => 'Contoh: ABC-20240601-001',
                'helper' => 'Kode surat akan dihasilkan otomatis setelah kategori dipilih',
                'colspan' => 6
            ],
            [
                'name' => 'category_id',
                'label' => 'Kategori Surat',
                'type' => 'select',
                'required' => true,
                'options' => $categories->pluck('name', 'id')->toArray()
            ],
            [
                'name' => 'date',
                'label' => 'Tanggal Surat',
                'type' => 'date',
                'required' => true,
            ],
            [
                'name' => 'file_path',
                'label' => 'File Surat',
                'type' => 'file',
                'required' => true,
                'accept' => '.pdf',
                'helper' => 'Format file harus PDF dengan ukuran maksimal 10MB',
                'colspan' => 6
            ],
            [
                'name' => 'sekretaris_panitia_id',
                'label' => 'Sekretaris Panitia',
                'type' => 'toggle',
                'required' => false,
                'value' => $users->where('role_id', 4)->first()?->id ?? null,
                'label_text' => $users->where('role_id', 4)->first()?->fullname ?? 'Belum ada Sekretaris Panitia',
                'helper' => 'Bersifat opsional'
            ],
            [
                'name' => 'ketua_panitia_id',
                'label' => 'Ketua Panitia',
                'type' => 'toggle',
                'required' => true,
                'value' => $users->where('role_id', 5)->first()?->id ?? null,
                'label_text' => $users->where('role_id', 5)->first()?->fullname ?? 'Belum ada Ketua Panitia',
                'helper' => 'Bersifat wajib'
            ],
            [
                'name' => 'ketua_umum_id',
                'label' => 'Ketua Umum',
                'type' => 'toggle',
                'required' => true,
                'value' => $users->where('role_id', 2)->first()?->id ?? null,
                'label_text' => $users->where('role_id', 2)->first()?->fullname ?? 'Belum ada Ketua Umum',
                'helper' => 'Bersifat wajib'
            ],
            [
                'name' => 'pembina_id',
                'label' => 'Pembina',
                'type' => 'toggle',
                'required' => false,
                'value' => $users->where('role_id', 6)->first()?->id ?? null,
                'label_text' => $users->where('role_id', 6)->first()?->fullname ?? 'Belum ada Pembina',
                'helper' => 'Bersifat opsional'
            ]
        ]" />

</x-dashboard.page-wrapper>
@endsection