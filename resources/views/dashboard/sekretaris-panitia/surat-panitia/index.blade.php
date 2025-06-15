@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Surat Panitia']]" :showFilter="true"
    :filterRoute="'sekretaris-panitia.surat-panitia.index'" :filterPlaceholder="'Cari surat panitia...'"
    :showAddButton="true" :addButtonText="'Tambah Surat Panitia'"
    :addButtonRoute="route('sekretaris-panitia.surat-panitia.create')">
    <livewire:table :model="App\Models\Letter::class" :routePrefix="'sekretaris-panitia.surat-panitia'" :columns="[
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

    <!-- Sign Letter Confirmation Modal -->
    <x-modals.confirm-modal id="sign-letter-modal" title="Tandatangani Surat"
        message="Apakah Anda yakin ingin menandatangani surat ini?" :route="route('signatures.sign')"
        confirmText="Ya, Tandatangani" cancelText="Batal" type="confirm" />

</x-dashboard.page-wrapper>
@endsection