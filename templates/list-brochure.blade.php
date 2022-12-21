@extends('layouts.page-display')
@section('content')

    <header id="header" data-fullwidth="true">
        @include('layouts.header')
    </header>

    <section id="page-content" class="no-sidebar">
        <div class="container">
            <!-- DataTable -->
            <div class="row mb-3">
                <div class="col-lg-6">
                    <h4>Brochures</h4>
                    <p>Liste des brochures par activité et produit</p>
                    {{--{{ dd($brochures) }}--}}
                </div>
                <div class="col-lg-6 text-right">
                    <button type="button" id="add-brochure" class="btn btn-light" onclick="javascript:location.href='{{route('post.brochure')}}'"><i class="icon-plus"></i>Nouvelle brochure</button>
                    <div id="export_buttons" class="mt-2"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table id="datatable" class="table table-bordered table-hover" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Libellé</th>
                                    <th>Descriptif</th>
                                    <th>Fichier</th>
                                    <th class="noExport">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($brochures->isNotEmpty())
                                    @foreach($brochures as $brochure)
                                        <tr>
                                            <td>{{ $brochure->libelle }}</td>
                                            <td>{{ $brochure->getExcerpt($brochure->descriptif, 0, 50) }}</td>
                                            <td>{{ $brochure->name }}</td>
                                            <td> <a class="ml-2" href="{{route('edit.brochure', $brochure->id)}}" data-toggle="tooltip" data-original-title="Modifier"><i class="icon-edit"></i></a>
                                                <a class="ml-2" href="{{route('show.brochure', $brochure->id)}}" data-toggle="tooltip" data-original-title="Visualiser"><i class="icon-eye"></i></a>
                                                <a class="ml-2" href="{{route('destroy.brochure', $brochure->id)}}" data-toggle="tooltip" data-original-title="Supprimer"><i class="icon-trash-2"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Libellé</th>
                                    <th>Descriptif</th>
                                    <th>Fichier</th>
                                    <th>Actions</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end: DataTable -->
        </div>
    </section>


@endsection

@push('styles')
    <link href='css/datatables.css' rel='stylesheet' />
@endpush

@push('scripts')
    <!--Datatables plugin files-->
    <script src='js/datatables.js'></script>

    <script>
        $(document).ready(function () {
            var table = $('#datatable').DataTable({
                buttons: [{
                    extend: 'print',
                    title: 'Test Data export',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                }, {
                    extend: 'pdf',
                    title: 'Test Data export',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                }, {
                    extend: 'excel',
                    title: 'Test Data export',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                }, {
                    extend: 'csv',
                    title: 'Test Data export',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                }],
                "language": {
                    "paginate": {
                        "next": ">",
                        "previous": "<"
                    }
                }
            });
            table.buttons().container().appendTo('#export_buttons');
            $("#export_buttons .btn").removeClass('btn-secondary').addClass('btn-light');
        });
    </script>
@endpush