@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">Data User</h3>
                <button class="btn btn-outline-success" type="button" id="tombolTambahUser">
                    <i class="fa fa-plus-square"></i> Tambah User
                </button>
            </div>
            <div class="card-body">
                <table id="data-user" class="table table-striped table-hover display compact responsive table-sm"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data User akan dimuat dari server-side -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahUserModal" tabindex="-1" role="dialog" aria-labelledby="tambahUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tambahUserForm">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="roles">Roles</label>
                            <select class="form-control" id="roles" name="roles">
                                <option value="">-- Pilih Role --</option> <!-- Opsi tanpa nilai sebagai default -->
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nm_roles }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-eye" id="togglePassword"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                    required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-eye"
                                            id="toggleConfirmPassword"></i></span>
                                </div>
                            </div>
                            <small id="passwordError" class="form-text text-danger"></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="simpanUserBaru">Simpan</button>
                    <button type="button" class="btn btn-primary" id="updateUser">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/user-data',
                columns: [{
                        data: null,
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'nm_roles',
                        name: 'nm_roles'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return `
                                <button class="editButton btn btn-sm btn-warning" data-id="${data}">Edit</button>
                                <button class="deleteButton btn btn-sm btn-danger" data-id="${data}">Delete</button>
                            `;
                        }
                    },

                ]
            });

            $('#tombolTambahUser').click(function() {
                $('#tambahUserModal').modal('show');
                $('#staticBackdropLabel').text('Tambah User Baru');
                $('#tambahUserForm')[0].reset();
                $('#simpanUserBaru').show();
                $('#updateUser').hide();
            });

            $('#confirm_password').on('keyup', function() {
                var password = $('#password').val();
                var confirmPassword = $(this).val();
                var passwordError = $('#passwordError'); // Dapatkan elemen untuk pesan kesalahan

                if (password !== confirmPassword) {
                    passwordError.text(
                        'Password dan Konfirmasi Password tidak sama.'); // Tampilkan pesan kesalahan
                } else {
                    passwordError.text(''); // Bersihkan pesan kesalahan jika password cocok
                }
            });

            $('#simpanUserBaru').click(function() {
                var formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    roles: $('#roles').val(),
                    password: $('#password').val(),
                    _token: '{{ csrf_token() }}'
                };

                Swal.fire({
                    title: 'Sedang menyimpan...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/user/save',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: 'Data berhasil diperbarui.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#tambahUserModal').modal('hide');
                                $('#data-user').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.error
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON
                            .errors.email) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan!',
                                text: 'Email sudah digunakan. Silakan gunakan email lain.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan',
                                text: 'Tidak dapat menyimpan data: ' + xhr.statusText
                            });
                        }
                    }
                });
            });

            $('#data-user tbody').on('click', '.deleteButton', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data tidak dapat dikembalikan setelah dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/delete/' + id, // Sesuaikan dengan URL endpoint Anda
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}', // CSRF token untuk keamanan
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Dihapus!',
                                    'Data user telah berhasil dihapus.',
                                    'success'
                                );
                                $('#data-user').DataTable().ajax
                                    .reload(); // Reload data pada DataTables
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan, data tidak dapat dihapus.',
                                    'error'
                                );
                            }
                        });
                    }
                })
            });

            $('#data-user tbody').on('click', '.editButton', function() {
                var id = $(this).data('id');

                // Kirim permintaan AJAX untuk mengambil data user
                $.ajax({
                    url: '/user/edit/' + id,
                    type: 'GET',
                    success: function(data) {
                        // Isi form pada modal dengan data yang diterima
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#roles').val(data.type).trigger('change');

                        // Kosongkan password karena tidak aman untuk ditransfer atau ditampilkan
                        $('#password').val('');
                        $('#confirm_password').val('');

                        // Tampilkan modal
                        $('#tambahUserModal').modal('show');
                        $('#staticBackdropLabel').text('Edit User');

                        // Sembunyikan tombol "Simpan" dan tampilkan tombol "Update"
                        $('#simpanUserBaru').hide();
                        $('#updateUser').show();

                        // Set ID user ke tombol "Update"
                        $('#updateUser').data('id', id);

                        // Verifikasi bahwa ID telah diset dengan benar
                        console.log('ID yang diset ke tombol Update:', $('#updateUser').data(
                            'id'));
                    },
                    error: function(error) {
                        console.log(error);
                        alert('Terjadi kesalahan saat mengambil data');
                    }
                });
            });

            $('#togglePassword').click(function() {
                let password = $('#password');
                let type = password.attr('type') === 'password' ? 'text' : 'password';
                password.attr('type', type);
                // Ganti ikon
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // Toggle untuk konfirmasi password
            $('#toggleConfirmPassword').click(function() {
                let confirmPassword = $('#confirm_password');
                let type = confirmPassword.attr('type') === 'password' ? 'text' : 'password';
                confirmPassword.attr('type', type);
                // Ganti ikon
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('#updateUser').click(function() {
                var id = $('#updateUser').data(
                    'id'); // Pastikan Anda telah menetapkan id user ke tombol saat membuka modal
                console.log('ID yang dikirim:', id);
                var formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    roles: $('#roles').val(),
                    password: $('#password').val(), // Kirim password hanya jika diubah
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '/user/update/' + id,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: 'Data berhasil diperbarui.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#tambahUserModal').modal('hide');
                                $('#data-user').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.error
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Tidak dapat menyimpan data: ' + xhr.statusText
                        });
                    }
                });
            });
        });
    </script>
@endpush
