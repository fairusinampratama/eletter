@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title" :breadcrumbItems="[['label' => 'Surat Panitia']]" :showFilter="true"
    :filterRoute="'sekretaris-umum.surat-panitia.index'" :filterPlaceholder="'Cari surat panitia...'" :showAddButton="true"
    :addButtonText="'Tambah Surat Panitia'" :addButtonId="'add-letter-modal'">
    <livewire:table :model="App\Models\SuratPanitia::class" :routePrefix="'sekretaris-umum.surat-panitia'" :columns="[
                ['field' => 'nomor_surat', 'label' => 'Nomor Surat'],
                ['field' => 'judul', 'label' => 'Judul Surat'],
                ['field' => 'kategori_surat.nama', 'label' => 'Kategori Surat'],
                ['field' => 'tanggal_surat', 'label' => 'Tanggal Surat'],
                ['field' => 'kepanitiaan.nama', 'label' => 'Kepanitiaan'],
            ]" :actions="[
                ['type' => 'edit', 'label' => 'Edit Surat'],
                ['type' => 'delete', 'label' => 'Hapus Surat'],
            ]" :withRelations="['kategori_surat', 'kepanitiaan']" :bulkActions="[
                ['type' => 'delete', 'label' => 'Hapus'],
            ]" :selectable="true" :sortable="true" :defaultSort="['field' => 'tanggal_surat', 'direction' => 'desc']" />

    <!-- Add Letter Modal -->
    <x-modals.add-modal id="add-letter-modal" title="Tambah Surat Panitia" :route="route('sekretaris-umum.surat-panitia.store')"
        :fields="[
                [
                    'name' => 'nomor_surat',
                    'label' => 'Nomor Surat',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nomor surat'
                ],
                [
                    'name' => 'judul',
                    'label' => 'Judul Surat',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan judul surat'
                ],
                [
                    'name' => 'kategori_surat_id',
                    'label' => 'Kategori Surat',
                    'type' => 'select',
                    'required' => true,
                    'options' => $kategoriSurat->pluck('nama', 'id')->toArray()
                ],
                [
                    'name' => 'tanggal_surat',
                    'label' => 'Tanggal Surat',
                    'type' => 'date',
                    'required' => true,
                ],
                [
                    'name' => 'kepanitiaan_id',
                    'label' => 'Kepanitiaan',
                    'type' => 'select',
                    'required' => true,
                    'options' => $kepanitiaan->pluck('nama', 'id')->toArray()
                ],
                [
                    'name' => 'file_surat',
                    'label' => 'File Surat',
                    'type' => 'file',
                    'required' => true,
                    'accept' => '.pdf,.doc,.docx'
                ]
            ]" />

    <!-- Edit Letter Modal -->
    <x-modals.edit-modal id="edit-letter-modal" title="Edit Surat Panitia" :route="route('sekretaris-umum.surat-panitia.index')"
        :fields="[
                [
                    'name' => 'nomor_surat',
                    'label' => 'Nomor Surat',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan nomor surat'
                ],
                [
                    'name' => 'judul',
                    'label' => 'Judul Surat',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Masukkan judul surat'
                ],
                [
                    'name' => 'kategori_surat_id',
                    'label' => 'Kategori Surat',
                    'type' => 'select',
                    'required' => true,
                    'options' => $kategoriSurat->pluck('nama', 'id')->toArray()
                ],
                [
                    'name' => 'tanggal_surat',
                    'label' => 'Tanggal Surat',
                    'type' => 'date',
                    'required' => true,
                ],
                [
                    'name' => 'kepanitiaan_id',
                    'label' => 'Kepanitiaan',
                    'type' => 'select',
                    'required' => true,
                    'options' => $kepanitiaan->pluck('nama', 'id')->toArray()
                ],
                [
                    'name' => 'file_surat',
                    'label' => 'File Surat (Opsional)',
                    'type' => 'file',
                    'accept' => '.pdf,.doc,.docx',
                    'placeholder' => 'Biarkan kosong jika tidak ingin mengubah file'
                ]
            ]" />

    <!-- Delete Letter Modal -->
    <x-modals.delete-modal id="delete-letter-modal" title="Hapus Surat Panitia"
        :route="route('sekretaris-umum.surat-panitia.destroy', ['surat_panitia' => 'selected'])"
        :message="'Apakah Anda yakin ingin menghapus surat panitia ini? Tindakan ini tidak dapat dibatalkan.'"
        :bulkDelete="true" />
</x-dashboard.page-wrapper>
@endsection
