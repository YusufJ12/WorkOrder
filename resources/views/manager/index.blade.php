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

    <!-- Modal Tambah/Edit Work Order -->
    <div class="modal fade" id="modalTambahWorkOrder" tabindex="-1" aria-labelledby="modalTambahWorkOrderLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Work Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahWorkOrder" method="POST">
                        @csrf <!-- CSRF Token -->
                        <input type="hidden" id="work_order_id" name="work_order_id"> <!-- Hidden field for editing -->

                        <div class="form-group">
                            <label for="nomer_work_order">Nomer Work Order</label>
                            <input type="text" class="form-control" id="nomer_work_order" name="nomer_work_order"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_produk">Nama Produk</label>
                            <select class="form-control" id="nama_produk" name="nama_produk" required></select>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                        </div>
                        <div class="form-group">
                            <label for="deadline">Deadline</label>
                            <input type="date" class="form-control" id="deadline" name="deadline" required>
                        </div>
                        <div class="form-group">
                            <label for="operator">Operator</label>
                            <select class="form-control" id="operator" name="operator" required></select>
                        </div>

                        <!-- Status Dropdown (Hidden by Default) -->
                        <div class="form-group" id="status_container" style="display: none;">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1">Pending</option>
                                <option value="2">In Progress</option>
                                <option value="3">In Progress</option>
                                <option value="4">Completed</option>
                                <option value="5">Canceled</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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

            $('#data-work-order tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('fa-minus-square').addClass('fa-plus-square');
                } else {
                    var workOrderId = row.data().id;

                    $.ajax({
                        url: '/operator/workorders/' + workOrderId + '/detail',
                        method: 'GET',
                        success: function(response) {
                            console.log(response);

                            var childContent = `
                    <div class="inner-card" style="padding: 10px;">
                        <table class="table table-sm table-striped table-bordered display detail-table" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Detail Status</th> 
                                    <th>Quantity Processed</th>
                                    <th>Reject Quantity</th>
                                    <th>Durasi</th> 
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response.data.map((log, index) => {
                                    return `
                                                                                                <tr>
                                                                                                    <td>${index + 1}</td>
                                                                                                    <td>${log.detail_status}</td> <!-- Data dari controller -->
                                                                                                    <td>${log.quantity_processed}</td>
                                                                                                    <td>${log.reject_quantity}</td>
                                                                                                    <td>${log.formatted_duration}</td> <!-- Data dari controller -->
                                                                                                    <td>${log.stage_note || '-'}</td>
                                                                                                    </tr>
                                                                                                `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>`;

                            row.child(childContent).show();
                            tr.addClass('shown');
                        },
                        error: function() {
                            console.error("Gagal mengambil data detail.");
                        }
                    });
                }
            });

            $('#filter_status').on('change', function() {
                table.ajax.reload();
            });

            $('#filter_date').on('change', function() {
                table.ajax.reload();
            });

            $('#filter_status, #filter_date').on('input', function() {
                if (!$('#filter_status').val() && !$('#filter_date').val()) {
                    table.ajax.reload();
                }
            });

            $('#tombolTambahWorkOrder').click(function() {
                $('#modalTitle').text('Tambah Work Order');
                $('#work_order_id').val('');
                $('#status_container').hide();

                $('#formTambahWorkOrder')[0].reset();
                $('#modalTambahWorkOrder').modal('show');

                $.ajax({
                    url: '{{ route('workorder.generateNomor') }}',
                    method: 'GET'
                }).done(function(data) {
                    $('#nomer_work_order').val(data.nomer_work_order);
                });

                $.ajax({
                    url: '{{ route('workorder.getNamaProduk') }}',
                    method: 'GET'
                }).done(function(data) {
                    let options = '<option value="">Pilih Nama Produk</option>';
                    data.forEach(product => options +=
                        `<option value="${product.id}">${product.nama_produk}</option>`);
                    $('#nama_produk').html(options);
                });

                $.ajax({
                    url: '{{ route('workorder.getOperator') }}',
                    method: 'GET'
                }).done(function(data) {
                    let options = '<option value="">Pilih Operator</option>';
                    data.forEach(operator => options +=
                        `<option value="${operator.id}">${operator.name}</option>`);
                    $('#operator').html(options);
                });
            });

            $('#data-work-order').on('click', '.edit', function() {
                var id = $(this).data('id');
                $('#modalTitle').text('Edit Work Order');
                $('#status_container').show();
                $('#modalTambahWorkOrder').modal('show');

                $.get('{{ url('/workorders') }}/' + id + '/edit', function(data) {
                    $('#work_order_id').val(data.id);
                    $('#nomer_work_order').val(data.nomer_work_order);
                    $('#jumlah').val(data.jumlah);
                    $('#deadline').val(data.deadline);

                    $.when(
                        $.ajax({
                            url: '{{ route('workorder.getNamaProduk') }}',
                            method: 'GET'
                        }),
                        $.ajax({
                            url: '{{ route('workorder.getOperator') }}',
                            method: 'GET'
                        })
                    ).done(function(productsResponse, operatorsResponse) {
                        let products = productsResponse[0];
                        let operators = operatorsResponse[0];

                        let productOptions = '<option value="">Pilih Nama Produk</option>';
                        products.forEach(product => {
                            productOptions +=
                                `<option value="${product.id}" ${product.id == data.nama_produk ? 'selected' : ''}>${product.nama_produk}</option>`;
                        });
                        $('#nama_produk').html(productOptions);

                        let operatorOptions = '<option value="">Pilih Operator</option>';
                        operators.forEach(operator => {
                            operatorOptions +=
                                `<option value="${operator.id}" ${operator.id == data.operator ? 'selected' : ''}>${operator.name}</option>`;
                        });
                        $('#operator').html(operatorOptions);

                        $('#status').val(data.status).trigger('change');
                    });
                });
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

            $('#exportExcel').on('click', function() {
                Swal.fire({
                    title: 'Pilih Jenis Laporan',
                    input: 'radio',
                    inputOptions: {
                        'rekapitulasi': 'Rekapitulasi Work Order',
                        'operator': 'Hasil Tiap Operator',
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Harap pilih salah satu laporan';
                        }
                    },
                    confirmButtonText: 'Lanjutkan',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const reportType = result.value;

                        window.location.href = `/workorders/export?type=${reportType}`;
                    }
                });
            });


        });
    </script>
@endpush
