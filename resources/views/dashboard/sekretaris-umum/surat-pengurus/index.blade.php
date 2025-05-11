@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Surat Pengurus']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.surat-pengurus.index'" :filterPlaceholder="'Cari surat pengurus...'"
    :showAddButton="true" :addButtonText="'Tambah Surat Pengurus'" :addButtonId="'add-letter-modal'">
    <livewire:table :model="App\Models\Letter::class" :routePrefix="'sekretaris-umum.surat-pengurus'" :columns="[
                ['field' => 'code', 'label' => 'Kode'],
                ['field' => 'category.name', 'label' => 'Kategori'],
                ['field' => 'creator.fullname', 'label' => 'Pembuat'],
                ['field' => 'date', 'label' => 'Tanggal'],
                ['field' => 'status', 'label' => 'Status'],
                ['field' => 'signing_status', 'label' => 'Status Tanda Tangan', 'type' => 'component', 'component' => 'signing-status'],
            ]" :actions="[
                ['type' => 'confirm', 'label' => 'Tandatangani Surat'],
                ['type' => 'view', 'label' => 'Lihat Surat'],
            ]" :withRelations="['category', 'creator', 'signatures.signer']" :selectable="false" :sortable="true"
        :defaultSort="['field' => 'date', 'direction' => 'desc']" />

    <!-- Add Letter Modal -->
    <x-modals.add-modal id="add-letter-modal" title="Tambah Surat Pengurus"
        :route="route('sekretaris-umum.surat-pengurus.store')" :fields="[
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
                'name' => 'pembina_id',
                'label' => 'Pembina',
                'type' => 'toggle',
                'required' => false,
                'value' => $users->where('role_id', 6)->first()->id,
                'label_text' => $users->where('role_id', 6)->first()->fullname,
                'helper' => 'Bersifat opsional'
            ]
        ]" />

    <!-- Sign Letter Confirmation Modal -->
    <x-modals.confirm-modal id="sign-letter-modal" title="Tandatangani Surat"
        message="Apakah Anda yakin ingin menandatangani surat ini?" :route="route('signatures.sign')"
        confirmText="Ya, Tandatangani" cancelText="Batal" type="confirm" />

</x-dashboard.page-wrapper>
@endsection