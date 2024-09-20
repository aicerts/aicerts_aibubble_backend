@extends('layouts.app')

@section('title', 'Customers List')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customers</h1>
    </div>

    <form method="GET" action="{{ route('customer.index') }}" class="mb-4">
        <div class="row">

            <div class="col-md-3">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="1" @if(request('status')==='1' ) selected @endif>Active</option>
                        <option value="2" @if(request('status')==='2' ) selected @endif>InActive</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_from">Date From</label>
                    <input type="date" name="date_from" value="{{request('date_from')}}" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_to">Date To</label>
                    <input type="date" name="date_to" value="{{request('date_to')}}" class="form-control">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="sort_by">Sort By</label>
                    <select name="sort_by" class="form-control">
                        <option value="created_at" @if(request('sort_by')==='created_at' ) selected @endif>Date Created</option>
                        <option value="status" @if(request('sort_by')==='status' ) selected @endif>Status</option>
                        <option value="name" @if(request('sort_by')==='name' ) selected @endif>Name</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <select name="sort_order" class="form-control">
                        <option value="asc" @if(request('sort_order')==='asc' ) selected @endif>Ascending</option>
                        <option value="desc" @if(request('sort_order')==='desc' ) selected @endif>Descending</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary mt-4">Apply Filters</button>
                <a href="{{ route('customer.index') }}" class="btn btn-secondary mt-4">Reset Filters</a>
            </div>
        </div>
    </form>

    {{-- Alert Messages --}}
    @include('common.alert')

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Customers</h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="20%">Name</th>
                            <th width="25%">Email</th>
                            <th width="15%">Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                @if ($customer->status == 0)
                                <span class="badge badge-danger">Inactive</span>
                                @elseif ($customer->status == 1)
                                <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                            <td style="display: flex">
                                @if ($customer->status == 0)
                                <a href="{{ route('customer.status', ['customer_id' => $customer->id, 'status' => 1]) }}"
                                    class="btn btn-success m-2">
                                    <i class="fa fa-check"></i>
                                </a>
                                @elseif ($customer->status == 1)
                                <a href="{{ route('customer.status', ['customer_id' => $customer->id, 'status' => 0]) }}"
                                    class="btn btn-danger m-2">
                                    <i class="fa fa-ban"></i>
                                </a>
                                @endif
                                <a class="btn btn-danger m-2" href="#" data-toggle="modal" data-target="#deleteModal" data-customer-id="{{$customer->id}}">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>

@include('customer.delete-modal')

@endsection

@section('scripts')
<script>
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var customerId = button.data('customer_id');
        $('#customer_id').val(customerId);
    });
</script>
@endsection