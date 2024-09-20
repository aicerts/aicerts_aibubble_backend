@extends('layouts.app')

@section('title', 'Add Symbol')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Symbol</h1>
        <a href="{{route('symbol.index')}}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add New Symbol</h6>
        </div>
        <form method="POST" action="{{route('symbol.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group row">

                    {{-- Symbol --}}
                    <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="symbol"><span style="color:red;">*</span> Symbol</label>
                        <select id="symbol" class="form-control" name="symbol" style="width: 100%" required></select>

                        @error('symbol')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="name"><span style="color:red;">*</span> Name</label>
                        <input
                            type="text"
                            class="form-control form-control-user @error('name') is-invalid @enderror"
                            id="exampleName"
                            placeholder="Name"
                            name="name"
                            value="{{ old('name') }}">

                        @error('name')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    {{-- Icon --}}
                    <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="icon" class="form-label">
                            <span style="color:red;">*</span> Icon
                        </label>
                        <input
                            type="file"
                            class="form-control form-control-user @error('icon') is-invalid @enderror"
                            id="icon"
                            name="icon" accept="image/*">

                        @error('icon')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-user float-right mb-3">Save</button>
                <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('symbol.index') }}">Cancel</a>
            </div>
        </form>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#symbol').select2({
            placeholder: 'Select Symbol',
            allowClear: true,
            minimumResultsForSearch: 0,
            ajax: {
                url: '{{ route("symbol.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    const query = {
                        search: params.term || '',
                        offset: params.offset || 0,
                        limit: 100
                    };
                    return query;
                },
                processResults: function(data, params) {

                    params.offset = params.offset || 0;

                    const moreResultsAvailable = (data.pagination.offset + data.pagination.count) < data.pagination.total;
                    if (moreResultsAvailable) {
                        params.offset += data.pagination.limit;
                    }

                    const filteredResults = data.data.filter(obj => obj.has_intraday);

                    const results = filteredResults.map(obj => ({
                        id: obj.symbol,
                        text: `${obj.name} (${obj.symbol})`,
                        name: obj.name
                    }));

                    return {
                        results: results,
                        pagination: {
                            more: moreResultsAvailable
                        }
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            $('input[name="name"]').val(data.name);
        });
    });
</script>
@endsection