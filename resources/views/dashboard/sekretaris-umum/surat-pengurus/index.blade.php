@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Surat Pengurus']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.surat-pengurus.index'" :filterPlaceholder="'Cari surat pengurus...'"
    :showAddButton="true" :addButtonText="'Tambah Surat Pengurus'"
    :addButtonRoute="route('sekretaris-umum.surat-pengurus.create')">
    <livewire:table :model="App\Models\Letter::class" :routePrefix="'sekretaris-umum.surat-pengurus'" :columns="[
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
        :defaultSort="['field' => 'date', 'direction' => 'desc']" :scopes="['nonCommittee']" />

    <!-- Sign Letter Confirmation Modal -->
    <x-modals.confirm-modal id="sign-letter-modal" title="Tandatangani Surat"
        message="Apakah Anda yakin ingin menandatangani surat ini?" :route="route('signatures.sign')"
        confirmText="Ya, Tandatangani" cancelText="Batal" type="confirm" />

</x-dashboard.page-wrapper>
@endsection