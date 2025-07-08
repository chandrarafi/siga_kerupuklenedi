<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Pegawai</h5>
                <div>
                    <a href="<?= site_url('admin/pegawai/report') ?>" class="btn btn-success me-2" target="_blank">
                        <i class="bi bi-file-earmark-text"></i> Lihat Laporan
                    </a>
                    <button type="button" class="btn btn-primary" id="btnTambahPegawai">
                        <i class="bi bi-plus"></i> Tambah Pegawai
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablePegawai">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Pegawai</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Jabatan</th>
                                <th>Bagian</th>
                                <th>Jenis Kelamin</th>
                                <th>No HP</th>
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
        let table = $('#tablePegawai').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('pegawai/getAll') ?>',
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
                    data: 'idpegawai'
                },
                {
                    data: 'namapegawai'
                },
                {
                    data: 'nik'
                },
                {
                    data: 'namajabatan'
                },
                {
                    data: 'namabagian'
                },
                {
                    data: 'jenkel'
                },
                {
                    data: 'nohp'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-warning edit-pegawai" data-id="${row.idpegawai}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-pegawai" data-id="${row.idpegawai}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                    orderable: false
                }
            ],
            order: [
                [2, 'asc']
            ]
        });

        // Load Jabatan for dropdown
        function loadJabatan() {
            $.ajax({
                url: '<?= site_url('pegawai/getJabatan') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        let html = '<option value="" selected disabled>Pilih Jabatan</option>';
                        $.each(response.data, function(index, jabatan) {
                            html += `<option value="${jabatan.idjabatan}">${jabatan.namajabatan} (${jabatan.namabagian})</option>`;
                        });
                        $('#jabatanid').html(html);
                    }
                }
            });
        }

        // Tombol tambah pegawai
        $('#btnTambahPegawai').on('click', function() {
            $('#formPegawai')[0].reset();
            $('#modalPegawaiLabel').text('Tambah Pegawai');
            $('.invalid-feedback').text('');
            $('.is-invalid').removeClass('is-invalid');

            // Load data untuk form
            loadJabatan();

            // Generate ID Pegawai - pastikan ID selalu dihasilkan
            $.ajax({
                url: '<?= site_url('pegawai/create') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.idpegawai) {
                        $('#idpegawai').val(response.idpegawai);
                    } else {
                        console.error('Gagal mendapatkan ID pegawai');
                        // Jika gagal, coba generate ID secara lokal
                        const today = new Date();
                        const dateStr = today.getFullYear() +
                            ('0' + (today.getMonth() + 1)).slice(-2) +
                            ('0' + today.getDate()).slice(-2);
                        const randomNum = Math.floor(1000 + Math.random() * 9000);
                        $('#idpegawai').val('PGW' + dateStr + randomNum);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    // Jika error, coba generate ID secara lokal
                    const today = new Date();
                    const dateStr = today.getFullYear() +
                        ('0' + (today.getMonth() + 1)).slice(-2) +
                        ('0' + today.getDate()).slice(-2);
                    const randomNum = Math.floor(1000 + Math.random() * 9000);
                    $('#idpegawai').val('PGW' + dateStr + randomNum);
                }
            });

            var myModal = new bootstrap.Modal(document.getElementById('modalPegawai'));
            myModal.show();
        });

        // Auto-generate name in lowercase for username suggestion
        // Fungsi auto-username dihilangkan sesuai permintaan

        // Handle form submission
        $('#formPegawai').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            console.log('Form data:', formData);

            $.ajax({
                url: '<?= site_url('pegawai/store') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    console.log('Sending request to:', '<?= site_url('pegawai/store') ?>');
                },
                success: function(response) {
                    console.log('Response:', response);
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
                        var modalElement = document.getElementById('modalPegawai');
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
                    console.error('AJAX error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        });

        // Handle delete button
        $(document).on('click', '.delete-pegawai', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pegawai dan akun user terkait akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= site_url('pegawai/delete/') ?>${id}`,
                        type: 'DELETE',
                        data: {
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('input[name="<?= csrf_token() ?>"]').val(response.token);

                            if (response.success) {
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

        // Handle edit button
        $(document).on('click', '.edit-pegawai', function() {
            let id = $(this).data('id');
            $('#formEditPegawai')[0].reset();
            $('#modalEditPegawaiLabel').text('Edit Pegawai');
            $('.invalid-feedback').text('');
            $('.is-invalid').removeClass('is-invalid');

            // Ambil data pegawai untuk edit
            $.ajax({
                url: `<?= site_url('pegawai/edit/') ?>${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        // Isi form dengan data pegawai
                        $('#edit_idpegawai').val(response.pegawai.idpegawai);
                        $('#edit_jabatanid').val(response.pegawai.jabatanid);
                        $('#edit_namapegawai').val(response.pegawai.namapegawai);
                        $('#edit_nik').val(response.pegawai.nik);
                        $('#edit_jenkel').val(response.pegawai.jenkel);
                        $('#edit_nohp').val(response.pegawai.nohp);
                        $('#edit_alamat').val(response.pegawai.alamat);
                        $('#edit_username').val(response.user.username);
                        $('#edit_email').val(response.user.email);

                        // Load jabatan untuk dropdown
                        let html = '<option value="" disabled>Pilih Jabatan</option>';
                        $.each(response.jabatan, function(index, jabatan) {
                            let selected = (jabatan.idjabatan == response.pegawai.jabatanid) ? 'selected' : '';
                            html += `<option value="${jabatan.idjabatan}" ${selected}>${jabatan.namajabatan} (${jabatan.namabagian})</option>`;
                        });
                        $('#edit_jabatanid').html(html);

                        // Tampilkan modal
                        var myModal = new bootstrap.Modal(document.getElementById('modalEditPegawai'));
                        myModal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data pegawai'
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

        // Handle form edit submission
        $('#formEditPegawai').on('submit', function(e) {
            e.preventDefault();

            let id = $('#edit_idpegawai').val();
            let formData = $(this).serialize();
            console.log('Form data edit:', formData);

            $.ajax({
                url: `<?= site_url('pegawai/update/') ?>${id}`,
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    console.log('Sending update request to:', `<?= site_url('pegawai/update/') ?>${id}`);
                },
                success: function(response) {
                    console.log('Response:', response);
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
                        var modalElement = document.getElementById('modalEditPegawai');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        modal.hide();

                        table.ajax.reload();
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                $(`#edit_${field}`).addClass('is-invalid');
                                $(`#edit_${field}-error`).text(message);
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
                    console.error('AJAX error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        });
    });
</script>
<!-- Modal Tambah Pegawai -->
<div class="modal fade" id="modalPegawai" tabindex="-1" aria-labelledby="modalPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPegawaiLabel">Tambah Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPegawai">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="idpegawai" class="form-label">ID Pegawai</label>
                            <input type="text" class="form-control" id="idpegawai" name="idpegawai" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="jabatanid" class="form-label">Jabatan</label>
                            <select class="form-select" id="jabatanid" name="jabatanid" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                <!-- Options will be loaded by AJAX -->
                            </select>
                            <div class="invalid-feedback" id="jabatanid-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="namapegawai" class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" id="namapegawai" name="namapegawai" required>
                            <div class="invalid-feedback" id="namapegawai-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" name="nik" maxlength="16">
                            <div class="invalid-feedback" id="nik-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jenkel" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jenkel" name="jenkel" required>
                                <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback" id="jenkel-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="nohp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="nohp" name="nohp" maxlength="15">
                            <div class="invalid-feedback" id="nohp-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        <div class="invalid-feedback" id="alamat-error"></div>
                    </div>

                    <hr class="my-4">
                    <h5>Data Akun User</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback" id="username-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password minimal 6 karakter.</div>
                        <div class="invalid-feedback" id="password-error"></div>
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

<!-- Modal Edit Pegawai -->
<div class="modal fade" id="modalEditPegawai" tabindex="-1" aria-labelledby="modalEditPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPegawaiLabel">Edit Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPegawai">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_idpegawai" class="form-label">ID Pegawai</label>
                            <input type="text" class="form-control" id="edit_idpegawai" name="idpegawai" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jabatanid" class="form-label">Jabatan</label>
                            <select class="form-select" id="edit_jabatanid" name="jabatanid" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                <!-- Options will be loaded by AJAX -->
                            </select>
                            <div class="invalid-feedback" id="edit_jabatanid-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_namapegawai" class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" id="edit_namapegawai" name="namapegawai" required>
                            <div class="invalid-feedback" id="edit_namapegawai-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="edit_nik" name="nik" maxlength="16">
                            <div class="invalid-feedback" id="edit_nik-error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_jenkel" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="edit_jenkel" name="jenkel" required>
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback" id="edit_jenkel-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nohp" class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="edit_nohp" name="nohp" maxlength="15">
                            <div class="invalid-feedback" id="edit_nohp-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                        <div class="invalid-feedback" id="edit_alamat-error"></div>
                    </div>

                    <hr class="my-4">
                    <h5>Data Akun User</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" readonly>
                            <div class="form-text">Username tidak dapat diubah.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback" id="edit_email-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                        <div class="invalid-feedback" id="edit_password-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdate">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>