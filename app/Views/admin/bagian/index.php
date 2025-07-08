<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Bagian</h5>
                <button type="button" class="btn btn-primary" id="btnTambahBagian">
                    <i class="bi bi-plus"></i> Tambah Bagian
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tableBagian">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bagian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        let table = $('#tableBagian').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('bagian/getAll') ?>',
                type: 'POST',
                data: function(d) {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    return d;
                },
                dataSrc: function(response) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.token);
                    return response.data;
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'namabagian'
                },
                {
                    data: 'actions',
                    orderable: false
                }
            ],
            order: [
                [1, 'asc']
            ]
        });

        // Tombol tambah bagian
        $('#btnTambahBagian').on('click', function() {
            $('#formBagian')[0].reset();
            $('#idbagian').val('');
            $('#modalBagianLabel').text('Tambah Bagian');
            $('#namabagian-error').text('');
            $('#namabagian').removeClass('is-invalid');
            ModalHelper.showModal('modalBagian');
        });

        // Reset form when modal is closed
        $('#modalBagian').on('hidden.bs.modal', function() {
            $('#formBagian')[0].reset();
            $('#idbagian').val('');
            $('#modalBagianLabel').text('Tambah Bagian');
            $('#namabagian-error').text('');
            $('#namabagian').removeClass('is-invalid');
        });

        // Handle form submission
        $('#formBagian').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let id = $('#idbagian').val();
            let url = id ? `<?= site_url('bagian/update/') ?>${id}` : '<?= site_url('bagian/store') ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.token);

                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        ModalHelper.hideModal('modalBagian');
                        table.ajax.reload();
                    } else {
                        if (response.errors) {
                            if (response.errors.namabagian) {
                                $('#namabagian').addClass('is-invalid');
                                $('#namabagian-error').text(response.errors.namabagian);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        });

        // Handle edit button
        $(document).on('click', '.edit-bagian', function() {
            let id = $(this).data('id');

            $.ajax({
                url: `<?= site_url('bagian/edit/') ?>${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.token);

                    if (response.status) {
                        $('#modalBagianLabel').text('Edit Bagian');
                        $('#idbagian').val(response.data.idbagian);
                        $('#namabagian').val(response.data.namabagian);
                        ModalHelper.showModal('modalBagian');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data'
                    });
                }
            });
        });

        // Handle delete button
        $(document).on('click', '.delete-bagian', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data bagian akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= site_url('bagian/delete/') ?>${id}`,
                        type: 'DELETE',
                        data: {
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('input[name="<?= csrf_token() ?>"]').val(response.token);

                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat menghapus data'
                            });
                        }
                    });
                }
            });
        });

        // Initialize modal
        ModalHelper.initModal('modalBagian');
    });
</script>
<!-- Modal Bagian -->
<div class="modal fade" id="modalBagian" tabindex="-1" aria-labelledby="modalBagianLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBagianLabel">Tambah Bagian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBagian">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="idbagian" id="idbagian">
                    <div class="mb-3">
                        <label for="namabagian" class="form-label">Nama Bagian</label>
                        <input type="text" class="form-control" id="namabagian" name="namabagian" required>
                        <div class="invalid-feedback" id="namabagian-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>