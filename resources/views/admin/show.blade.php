@extends('admin::admin.layouts.master')

@section('title', 'CMS Pages Management')

@section('page-title', 'CMS Page Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a href="{{ route('admin.pages.index') }}">CMS Page Manager</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">CMS Page Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header with Back button -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="card-title mb-0">{{ $page->title ?? 'N/A' }} - Page</h4>
                            <div>
                                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary ml-2">
                                    Back
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <!-- CMS Page Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Page Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Title:</label>
                                            <p>{{ $page->title ?? 'N/A' }}</p>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Content:</label>
                                            <p>{!! $page->content ?? 'N/A' !!}</p>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Status:</label>
                                                    <p>{!! $page->status ? config('pages.constants.aryPageStatusLabel')[$page->status] ?? 'N/A' : 'N/A' !!}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Created At:</label>
                                                    <p>
                                                        {{ $page->created_at ? $page->created_at->format(config('GET.admin_date_time_format') ?? 'Y-m-d H:i:s') : '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions & SEO -->
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card">
                                        @include('admin::admin.seo_meta_data.view', ['seo' => $seo])
                                    </div>
                                    <div class="card-header bg-primary">
                                        <h5 class="mb-0 text-white font-bold">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column">
                                            @admincan('cms_pages_manager_edit')
                                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-warning mb-2">
                                                    <i class="mdi mdi-pencil"></i> Edit Page
                                                </a>
                                            @endadmincan

                                            @admincan('cms_pages_manager_delete')
                                                <button type="button" class="btn btn-danger delete-btn delete-record"
                                                    title="Delete this record"
                                                    data-url="{{ route('admin.pages.destroy', $page) }}"
                                                    data-redirect="{{ route('admin.pages.index') }}"
                                                    data-text="Are you sure you want to delete this record?"
                                                    data-method="DELETE">
                                                    <i class="mdi mdi-delete"></i> Delete Page
                                                </button>
                                            @endadmincan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- row end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Container fluid  -->
@endsection
