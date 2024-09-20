@extends('layouts.app')

@section('title', 'Edit Symbol')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Symbol</h1>
        <a href="{{ route('symbol.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Symbol</h6>
        </div>
        <form method="POST" action="{{ route('symbol.update', $symbol->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group row">

                    {{-- Symbol --}}
                    <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="symbol">
                            <span style="color:red;">*</span> Symbol
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-user @error('symbol') is-invalid @enderror"
                            id="symbol"
                            placeholder="Symbol"
                            name="symbol"
                            value="{{ old('symbol', $symbol->symbol) }}">

                        @error('symbol')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div class="col-sm-6 mb-3 mt-3 mb-sm-0">
                        <label for="name">
                            <span style="color:red;">*</span> Name
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-user @error('name') is-invalid @enderror"
                            id="name"
                            placeholder="Name"
                            name="name"
                            value="{{ old('name', $symbol->name) }}">

                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
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
                            name="icon" accept="image/*" onchange="previewImage(event)">

                        @error('icon')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror

                        {{-- Display Old Image --}}
                        @if($symbol->icon)
                        <div class="mt-3" id="old-icon-section">
                            <label>Current Icon:</label><br>
                            <img src="{{$symbol->icon}}" alt="Current Icon" style="max-height: 100px;">
                        </div>
                        @endif

                        {{-- Preview New Image --}}
                        <div class="mt-3">
                            <label>New Icon Preview:</label><br>
                            <img id="preview" src="#" alt="New Icon" style="display: none; max-height: 100px;">
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-user float-right mb-3">Update</button>
                <a class="btn btn-primary float-right mr-3 mb-3" href="{{ route('symbol.index') }}">Cancel</a>
            </div>
        </form>
    </div>

</div>

@endsection

@section('scripts')
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview');
            var oldIconSection = document.getElementById('old-icon-section');
            if(oldIconSection) {
                oldIconSection.style.display = 'none'; // Hide the old icon section
            }
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
