@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">Data Produk</h3>
                <button class="btn btn-outline-success" type="button" id="tombolTambahProducts">
                    <i class="fa fa-plus-square"></i> Tambah Produk
                </button>
            </div>
            <div class="card-body">
                <table id="data-products" class="table table-striped table-hover display compact responsive table-sm"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Dibuat Tanggal</th>
                            <th>Diupdate Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah/Edit Produk -->
    <div class="modal fade" id="productsModal" tabindex="-1" aria-labelledby="productsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productsModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="productsForm">
                        <input type="hidden" id="productsId" name="productsId">
                        <div class="form-group">
                            <label for="productsName">Nama Produk</label>
                            <input type="text" class="form-control" id="productsName" name="productsName" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="simpanProducts">Simpan</button>
                            <button type="submit" class="btn btn-primary" id="updateProducts">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#data-products').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/products-data',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'nama_produk',
                        name: 'nama_produk'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#tombolTambahProducts').click(function() {
                $('#productsModal').modal('show');
                $('#productsModalLabel').text('Tambah Produk Baru');
                $('#productsForm')[0].reset();
                $('#simpanProducts').show();
                $('#updateProducts').hide();
            });

            $('#productsForm').submit(function(e) {
                e.preventDefault();
                var id = $('#productsId').val();
                var name = $('#productsName').val();
                var url = id ? '/products/' + id : '/products/';
                var method = id ? 'PUT' : 'POST';

                Swal.fire({
                    title: 'Sedang menyimpan...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $(this).prop('disabled', true);

                $.ajax({
                    url: url,
                    type: method,
                    data: {
                        name: name,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: 'Produk berhasil disimpan.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#productsModal').modal('hide');
                                $('#data-products').DataTable().ajax.reload();
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Tidak dapat menyimpan data: ' + xhr.statusText
                        });
                    },
                    complete: function() {
                        $('#simpanProducts').prop('disabled', false);
                    }
                });
            });

            $('#data-products').on('click', '.delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/products/delete/' +
                                id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Produk telah dihapus.',
                                        'success'
                                    ).then(() => {
                                        $('#data-products').DataTable().ajax
                                            .reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        'Produk tidak dapat dihapus.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal!',
                                    'Terjadi kesalahan saat menghapus produk.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $('#data-products').on('click', '.edit', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '/products/' + id,
                    type: 'GET',
                    success: function(response) {
                        $('#productsId').val(response.id);
                        $('#productsName').val(response.nama_produk);
                        $('#productsModalLabel').text('Edit Produk');
                        $('#simpanProducts').hide();
                        $('#updateProducts').show();
                        $('#productsModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Error mengambil data produk');
                    }
                });
            });
        });
    </script>
@endpush
