@extends('layouts.master')

@section('content')
    <style>
        .status-filter {
            cursor: pointer;
            transition: 0.3s;
            border-radius: 8px;
            padding: 15px;
        }

        .status-filter:hover {
            background: rgba(0, 123, 255, 0.1);
            transform: scale(1.05);
        }

        .card-body h6 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .card-body .badge {
            font-size: 16px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .status-filter {
                font-size: 12px;
            }
        }
    </style>
    <div class="container-fluid">
        <!-- Status Filter Buttons -->
        <div class="row justify-content-center">
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                <div class="card shadow text-center status-filter btn-outline-primary" data-status="1">
                    <div class="card-body">
                        <h6 class="text-uppercase font-weight-bold">Pending</h6>
                        <span class="badge badge-primary p-2" id="count_pending">0</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                <div class="card shadow text-center status-filter btn-outline-warning" data-status="2">
                    <div class="card-body">
                        <h6 class="text-uppercase font-weight-bold">Pemotongan</h6>
                        <span class="badge badge-warning p-2" id="count_pemotongan">0</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                <div class="card shadow text-center status-filter btn-outline-info" data-status="3">
                    <div class="card-body">
                        <h6 class="text-uppercase font-weight-bold">Perakitan</h6>
                        <span class="badge badge-info p-2" id="count_perakitan">0</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                <div class="card shadow text-center status-filter btn-outline-success" data-status="4">
                    <div class="card-body">
                        <h6 class="text-uppercase font-weight-bold">Completed</h6>
                        <span class="badge badge-success p-2" id="count_completed">0</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                <div class="card shadow text-center status-filter btn-outline-danger" data-status="5">
                    <div class="card-body">
                        <h6 class="text-uppercase font-weight-bold">Canceled</h6>
                        <span class="badge badge-danger p-2" id="count_canceled">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable Container -->
        <div class="card shadow">
            <div class="card-header">
                <h5 class="m-0 text-primary text-center" id="datatable-title">Work Orders - Pending</h5>
            </div>
            <div class="card-body">
                <table id="workOrderTable"
                    class="table table-striped table-hover display compact responsive table-sm w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No</th>
                            <th>Nomer Work Order</th>
                            <th>Nama Produk</th>
                            <th>Jumlah Order</th>
                            <th>Jumlah Final</th>
                            <th>Deadline</th>
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
            var table = $('#workOrderTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('operator.getDataOperator') }}',
                    data: function(d) {
                        d.status = $('.status-filter.active').data('status') || 1;
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
                        data: 'operator_name',
                        name: 'operator_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.status === 4) {
                                return `<span class="badge badge-success">Completed</span>`;
                            } else if (row.status === 5) {
                                return `<span class="badge badge-danger">Canceled</span>`;
                            } else {
                                let tooltipText = row.status === 3 ? "Selesaikan atau Kembalikan" :
                                    "Lanjutkan ke tahap berikutnya";
                                return `<button class="btn btn-outline-info btn-sm update-status"
                                    data-id="${row.id}"
                                    data-status="${row.status}"
                                    data-quantity="${row.quantity}"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="${tooltipText}">
                                    <i class="fa fa-hand-pointer"></i>
                                </button>`;
                            }
                        }
                    }
                ],
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('#workOrderTable tbody').on('click', 'td.details-control', function() {
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

            $('.status-filter').click(function() {
                $('.status-filter').removeClass('active btn-primary').addClass('btn-outline-primary');
                $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

                var status = $(this).data('status');
                $('#datatable-title').text("Work Orders - " + getStatusText(status));

                table.ajax.url('{{ route('operator.getDataOperator') }}?status=' + status).load();
            });

            $('#workOrderTable').on('click', '.update-status', function() {
                var id = $(this).data('id');
                var currentStatus = $(this).data('status');
                var nextStatus = currentStatus + 1;
                var maxQuantity = $(this).data('quantity');

                if (currentStatus === 3) {
                    Swal.fire({
                        title: "Pilih Aksi",
                        text: "Apakah Anda ingin menyelesaikan atau membatalkan?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Selesaikan",
                        cancelButtonText: "Batalkan"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: "Masukkan Jumlah yang Diproses",
                                html: `
                        <input type="number" id="swal-quantity" class="swal2-input" placeholder="Jumlah Layak & Diproses (maks ${maxQuantity})" min="1" max="${maxQuantity}" required>
                        <input type="text" id="swal-stage-note" class="swal2-input" placeholder="Keterangan Tahapan (Opsional)">
                    `,
                                showCancelButton: true,
                                confirmButtonText: "Selesaikan",
                                cancelButtonText: "Batal",
                                preConfirm: () => {
                                    return {
                                        quantity: document.getElementById(
                                                'swal-quantity')
                                            .value,
                                        stage_note: document.getElementById(
                                            'swal-stage-note').value
                                    };
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let quantity = result.value.quantity;
                                    let stageNote = result.value.stage_note;

                                    if (!quantity || quantity <= 0 || quantity >
                                        maxQuantity) {
                                        Swal.fire("Error", "Jumlah tidak valid!", "error");
                                        return;
                                    }

                                    updateWorkOrderStatus(id, nextStatus, quantity,
                                        stageNote);
                                }
                            });
                        } else if (result.isDismissed) {
                            updateWorkOrderStatus(id, 5, maxQuantity, "Dibatalkan");
                        }
                    });
                } else if (currentStatus === 4 || currentStatus === 5) {
                    return;
                } else {
                    Swal.fire({
                        title: "Masukkan Jumlah yang Diproses",
                        html: `
                <input type="number" id="swal-quantity" class="swal2-input" placeholder="Jumlah Layak & Diproses (maks ${maxQuantity})" min="1" max="${maxQuantity}" required>
                <input type="text" id="swal-stage-note" class="swal2-input" placeholder="Keterangan Tahapan (Opsional)">
            `,
                        showCancelButton: true,
                        confirmButtonText: "Lanjutkan",
                        cancelButtonText: "Batal",
                        preConfirm: () => {
                            return {
                                quantity: document.getElementById('swal-quantity').value,
                                stage_note: document.getElementById('swal-stage-note').value
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let quantity = result.value.quantity;
                            let stageNote = result.value.stage_note;

                            if (!quantity || quantity <= 0 || quantity > maxQuantity) {
                                Swal.fire("Error", "Jumlah tidak valid!", "error");
                                return;
                            }

                            updateWorkOrderStatus(id, nextStatus, quantity, stageNote);
                        }
                    });
                }
            });

            function updateWorkOrderStatus(id, status, quantity, stageNote) {
                $.ajax({
                    url: `/operator/workorders/${id}/update`,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status,
                        quantity: quantity,
                        stage_note: stageNote
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", "Status telah diperbarui.", "success");
                        $('#workOrderTable').DataTable().ajax.reload();
                        fetchCounts();
                    },
                    error: function() {
                        Swal.fire("Gagal!", "Terjadi kesalahan saat memperbarui status.", "error");
                    }
                });
            }


            function getStatusText(status) {
                switch (status) {
                    case 1:
                        return "Pending";
                    case 2:
                        return "In Progress";
                    case 3:
                        return "Perakitan";
                    case 4:
                        return "Completed";
                    case 5:
                        return "Canceled";
                    default:
                        return "Unknown";
                }
            }

            function fetchCounts() {
                $.get('{{ route('workorders.counts') }}', function(data) {
                    $('#count_pending').text(data.pending);
                    $('#count_pemotongan').text(data.pemotongan);
                    $('#count_perakitan').text(data.perakitan);
                    $('#count_completed').text(data.completed);
                    $('#count_canceled').text(data.canceled);
                });
            }

            fetchCounts();
        });
    </script>
@endpush
