@extends('admin::admin.layouts.master')

@section('title', 'CMS Pages Management')

@section('page-title', 'CMS Pages Manager')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">CMS Pages Manager</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <h4 class="card-title">Filter</h4>
                    <form action="{{ route('admin.pages.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control"
                                        value="{{ app('request')->query('keyword') }}" placeholder="Enter title">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="">All</option>
                                        @foreach (config('pages.constants.status') as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ app('request')->query('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto mt-1 text-right">
                                <div class="form-group">
                                    <label for="created_at">&nbsp;</label>
                                    <button type="submit" form="filterForm" class="btn btn-primary mt-4">Filter</button>
                                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary mt-4">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{-- <h4 class="card-title">Manage Pages</h4> --}}
                        @admincan('pages_manager_create')
                            <div class="text-right">
                                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary mb-3">Create New Page</a>
                            </div>
                        @endadmincan

                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">S. No.</th>
                                        <th>@sortablelink('title', 'Title', [], ['class' => 'text-dark'])</th>
                                        <th>@sortablelink('status', 'Status', [], ['class' => 'text-dark'])</th>
                                        <th>@sortablelink('created_at', 'Created At', [], ['class' => 'text-dark'])</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($pages) && $pages->count() > 0)
                                        @php
                                            $i = ($pages->currentPage() - 1) * $pages->perPage() + 1;
                                        @endphp
                                        @foreach ($pages as $page)
                                            <tr>
                                                <th scope="row">{{ $i }}</th>
                                                <td>{{ $page->title }}</td>
                                                <td>
                                                    <!-- create update status functionality-->
                                                    @if ($page->status == 'published')
                                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                                            data-placement="top" title="Click to change status to draft"
                                                            data-url="{{ route('admin.pages.updateStatus') }}"
                                                            data-method="POST" data-status="draft"
                                                            data-id="{{ $page->id }}"
                                                            class="btn btn-success btn-sm update-status">Published</a>
                                                    @else
                                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                                            data-placement="top" title="Click to change status to published"
                                                            data-url="{{ route('admin.pages.updateStatus') }}"
                                                            data-method="POST" data-status="published"
                                                            data-id="{{ $page->id }}"
                                                            class="btn btn-warning btn-sm update-status">Draft</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $page->created_at ? $page->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s') : '—' }}
                                                </td>
                                                <td style="width: 10%;">
                                                    @admincan('pages_manager_view')
                                                        <a href="{{ route('admin.pages.show', $page) }}" data-toggle="tooltip"
                                                            data-placement="top" title="View this record"
                                                            class="btn btn-warning btn-sm"><i class="mdi mdi-eye"></i></a>
                                                    @endadmincan
                                                    @admincan('pages_manager_edit')
                                                        <a href="{{ route('admin.pages.edit', $page) }}" data-toggle="tooltip"
                                                            data-placement="top" title="Edit this record"
                                                            class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>
                                                    @endadmincan
                                                    @admincan('pages_manager_delete')
                                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                                            title="Delete this record"
                                                            data-url="{{ route('admin.pages.destroy', $page) }}"
                                                            data-text="Are you sure you want to delete this record?"
                                                            data-method="DELETE" class="btn btn-danger btn-sm delete-record"><i
                                                                class="mdi mdi-delete"></i></a>
                                                    @endadmincan
                                                </td>
                                            </tr>
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <!--pagination move the right side-->
                            @if ($pages->count() > 0)
                                {{ $pages->links('admin::pagination.custom-admin-pagination') }}
                            @endif

                        </div>
                    </div>
                    
                </div>
            </div>


        </div>
        <!-- End PAge Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
