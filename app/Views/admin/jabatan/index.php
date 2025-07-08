<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Jabatan</h5>
                <button type="button" class="btn btn-primary" id="btnTambahJabatan">
                    <i class="bi bi-plus"></i> Tambah Jabatan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tableJabatan">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bagian</th>
                                <th>Nama Jabatan</th>
                                <th>Gaji Pokok</th>
                                <th>Tunjangan</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        let table = $('#tableJabatan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('jabatan/getAll') ?>',
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
                    data: 'namajabatan'
                },
                {
                    data: 'gajipokok'
                },
                {
                    data: 'tunjangan'
                },
                {
                    data: 'actions',
                    orderable: false
                }
            ],
            order: [
                [2, 'asc']
            ]
        });

        // Load Bagian for dropdown
        function loadBagian() {
            $.ajax({
                url: '<?= site_url('jabatan/getBagian') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        let html = '<option value="">-- Pilih Bagian --</option>';
                        $.each(response.data, function(index, bagian) {
                            html += `<option value="${bagian.idbagian}">${bagian.namabagian}</option>`;
                        });
                        $('#bagianid').html(html);
                    }
                }
            });
        }

        // Format currency function
        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return 'Rp ' + rupiah;
        }

        // Tombol tambah jabatan
        $('#btnTambahJabatan').on('click', function() {
            $('#formJabatan')[0].reset();
            $('#idjabatan').val('');
            $('#modalJabatanLabel').text('Tambah Jabatan');
            $('.invalid-feedback').text('');
            $('.is-invalid').removeClass('is-invalid');

            // Reset nilai currency (kosongkan, bukan nilai 0)
            $('#gajipokok').val('');
            $('#tunjangan').val('');

            var myModal = new bootstrap.Modal(document.getElementById('modalJabatan'));
            myModal.show();
        });

        // Reset form when modal is closed
        $('#modalJabatan').on('hidden.bs.modal', function() {
            $('#formJabatan')[0].reset();
            $('#idjabatan').val('');
            $('#modalJabatanLabel').text('Tambah Jabatan');
            $('.invalid-feedback').text('');
            $('.is-invalid').removeClass('is-invalid');
        });

        // Handle form submission
        $('#formJabatan').on('submit', function(e) {
            e.preventDefault();

            // Hapus format rupiah sebelum submit
            let gajipokok = $('#gajipokok').val().replace(/[^\d]/g, '');
            let tunjangan = $('#tunjangan').val().replace(/[^\d]/g, '');

            $('#gajipokok').val(gajipokok);
            $('#tunjangan').val(tunjangan);

            let formData = $(this).serialize();
            let id = $('#idjabatan').val();
            let url = id ? `<?= site_url('jabatan/update/') ?>${id}` : '<?= site_url('jabatan/store') ?>';

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

                        // Tutup modal dengan Bootstrap native
                        var modalElement = document.getElementById('modalJabatan');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        modal.hide();

                        table.ajax.reload();
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(message);
                            });
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
        $(document).on('click', '.edit-jabatan', function() {
            let id = $(this).data('id');

            $.ajax({
                url: `<?= site_url('jabatan/edit/') ?>${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('input[name="<?= csrf_token() ?>"]').val(response.token);

                    if (response.status) {
                        $('#modalJabatanLabel').text('Edit Jabatan');
                        $('#idjabatan').val(response.data.idjabatan);
                        $('#bagianid').val(response.data.bagianid);
                        $('#namajabatan').val(response.data.namajabatan);

                        // Format nilai currency
                        $('#gajipokok').val(formatRupiah(response.data.gajipokok));
                        $('#tunjangan').val(formatRupiah(response.data.tunjangan));

                        var myModal = new bootstrap.Modal(document.getElementById('modalJabatan'));
                        myModal.show();
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

        // Format input currency
        $('#gajipokok, #tunjangan').on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (value) {
                $(this).val(formatRupiah(value));
            } else {
                $(this).val('');
            }
        });

        // Handle delete button
        $(document).on('click', '.delete-jabatan', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data jabatan akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= site_url('jabatan/delete/') ?>${id}`,
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

        // Initialize on page load
        loadBagian();
    });
</script>
<!-- Modal Jabatan -->
<div class="modal fade" id="modalJabatan" tabindex="-1" aria-labelledby="modalJabatanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalJabatanLabel">Tambah Jabatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formJabatan">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="idjabatan" id="idjabatan">

                    <div class="mb-3">
                        <label for="bagianid" class="form-label">Bagian</label>
                        <select class="form-select" id="bagianid" name="bagianid" required>
                            <option value="">-- Pilih Bagian --</option>
                            <!-- Options will be loaded by AJAX -->
                        </select>
                        <div class="invalid-feedback" id="bagianid-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="namajabatan" class="form-label">Nama Jabatan</label>
                        <input type="text" class="form-control" id="namajabatan" name="namajabatan" required>
                        <div class="invalid-feedback" id="namajabatan-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="gajipokok" class="form-label">Gaji Pokok</label>
                        <input type="text" class="form-control" id="gajipokok" name="gajipokok" placeholder="Masukkan gaji pokok" required>
                        <div class="invalid-feedback" id="gajipokok-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="tunjangan" class="form-label">Tunjangan</label>
                        <input type="text" class="form-control" id="tunjangan" name="tunjangan" placeholder="Masukkan tunjangan" required>
                        <div class="invalid-feedback" id="tunjangan-error"></div>
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