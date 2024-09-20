@extends('layouts.app')

@section('title', 'Business Settings - Terms & Conditions')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Terms & Conditions</h1>
    </div>

    {{-- Alert Messages --}}
    @include('common.alert')
   
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Terms & Conditions</h6>
        </div>
        <form method="POST" action="{{route('settings.terms.store')}}">
            @csrf
            <div class="card-body">
                <div class="form-group row">

                    {{-- Terms & Conditions --}}
                    <div class="col-sm-12 mb-3 mt-3 mb-sm-0">
                        <span style="color:red;">*</span> Terms & Conditions
                        <textarea 
                            class="form-control form-control-user @error('terms_conditions') is-invalid @enderror" 
                            id="terms_conditions"
                            placeholder="Terms & Conditions" 
                            name="terms_conditions">{{ old('terms_conditions',$terms) }}</textarea>

                        @error('terms_conditions')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-user float-right mb-3">Save</button>
            </div>
        </form>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        ClassicEditor
            .create(document.querySelector('#terms_conditions'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'alignment', '|',
                    'insertTable', 'blockQuote', 'imageUpload', '|',
                    'undo', 'redo'
                ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                image: {
                    toolbar: [
                        'imageTextAlternative', 'imageStyle:full', 'imageStyle:side'
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn', 'tableRow', 'mergeTableCells'
                    ]
                },
                height: '300px',
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
@endsection
