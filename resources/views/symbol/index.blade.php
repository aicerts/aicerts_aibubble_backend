@extends('layouts.app')

@section('title', 'Symbols List')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Symbols</h1>

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('symbol.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New Symbol
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Symbols</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="20%">Symbol</th>
                            <th width="25%">Name</th>
                            <th width="15%">Icon</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedSymbols as $symbol)
                        <tr>
                            <td>{{ $symbol['symbol'] }}</td>
                            <td>{{ $symbol['name'] }}</td>
                            <td>
                                <img src="{{$symbol['image']}}" alt="Image" style="max-width: 100px; height: auto;">
                            </td>
                            <td>

                                <a class="btn btn-danger m-2" href="#" data-toggle="modal" data-target="#deleteModal" data-symbol-id="{{ $symbol['symbol'] }}">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $paginatedSymbols->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>

@include('symbol.delete-modal')

@endsection

@section('scripts')
<script>
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var symbolId = button.data('symbol-id');
        $('#symbol_id_delete').val(symbolId);
    });
</script>
@endsection