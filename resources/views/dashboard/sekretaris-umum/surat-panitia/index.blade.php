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
                ['field' => 'signing_status', 'label' => 'Status Tanda Tangan', 'type' => 'component', 'component' => 'signing-status'],
            ]" :actions="[
                ['type' => 'confirm', 'label' => 'Tandatangani Surat'],
                ['type' => 'view', 'label' => 'Lihat Surat'],
            ]" :withRelations="['category', 'creator', 'signatures.signer']" :selectable="false" :sortable="true"
        :defaultSort="['field' => 'date', 'direction' => 'desc']" :scopes="['committeeOnly']" />

</x-dashboard.page-wrapper>
@endsection