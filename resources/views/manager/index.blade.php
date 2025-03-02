@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">Data Work Order</h3>
                <button class="btn btn-outline-success" type="button" id="tombolTambahWorkOrder">
                    <i class="fa fa-plus-square"></i> Tambah Work Order
                </button>
                <button class="btn btn-outline-success" type="button" id="exportExcel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="filter_status">Filter Status</label>
                        <select class="form-control" id="filter_status">
                            <option value="">Semua Status</option>
                            <option value="1">Pending</option>
                            <option value="2">In Progress</option>
                            <option value="3">Completed</option>
                            <option value="4">Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="filter_date">Filter Deadline</label>
                        <input type="date" class="form-control" id="filter_date">
                    </div>
                </div>
                <table id="data-work-order" class="table table-striped table-hover display compact responsive table-sm"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No</th>
                            <th>Nomer Work Order</th>
                            <th>Nama Produk</th>
                            <th>Jumlah</th>
                            <th>Hasil Jadi</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Operator</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#data-work-order').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('workorders.dataManager') }}',
                    data: function(d) {
                        d.status = $('#filter_status').val();
                        d.deadline = $('#filter_date').val();
                    }
                },
                columns: [{
                        "data": null,
                        "className": 'details-control text-center',
                        "orderable": false,
                        "defaultContent": '<i class="fas fa-plus-square" style="cursor:pointer; font-size:18px;"></i>',
                        "width": "2%"
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'work_order_number',
                        name: 'work_order_number'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'quantity_final',
                        name: 'quantity_final'
                    },
                    {
                        data: 'production_deadline',
                        name: 'production_deadline'
                    },
                    {
                        data: 'status_text',
                        name: 'status_text',
                        render: function(data) {
                            let statusClass;
                            switch (data) {
                                case 'Pending':
                                    statusClass = 'btn-warning';
                                    break;
                                case 'In Progress':
                                    statusClass = 'btn-info';
                                    break;
                                case 'Completed':
                                    statusClass = 'btn-success';
                                    break;
                                case 'Canceled':
                                    statusClass = 'btn-danger';
                                    break;
                                default:
                                    statusClass = 'btn-secondary';
                            }
                            return `<button class="btn ${statusClass} btn-sm">${data}</button>`;
                        }
                    },
                    {
                        data: 'operator_name',
                        name: 'operator_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#formTambahWorkOrder').submit(function(e) {
                e.preventDefault();
                var id = $('#work_order_id').val();
                var url = id ? '{{ route('workorder.update', '') }}/' + id :
                    '{{ route('workorder.store') }}';
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Work Order berhasil disimpan!', 'success');
                            $('#modalTambahWorkOrder').modal('hide');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', 'Gagal menyimpan Work Order!', 'error');
                        }
                    }
                });
            });

            $('#data-work-order').on('click', '.delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Work Order akan dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('/workorders') }}/' + id,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Terhapus!',
                                        'Work Order berhasil dihapus.', 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Gagal!', 'Gagal menghapus Work Order!',
                                        'error');
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
