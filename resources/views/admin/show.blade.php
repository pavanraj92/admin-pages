@extends('admin::admin.layouts.master')

@section('title', 'Pages Management')
@section('meta_description')
Manage pages in the admin panel: create, edit, update, and control page titles, content, and publication status.
@endsection


@section('page-title', 'Page Details')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.pages.index') }}">Manage Pages</a></li>
    <li class="breadcrumb-item active" aria-current="page">Page Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">                    
                    <div class="table-responsive">
                         <div class="card-body">      
                            <table class="table table-bordered table-responsive-lg">                            
                                <tbody>
                                    <tr>
                                        <th scope="row">Title</th>
                                        <td scope="col">{{ $page->title ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Content</th>
                                        <td scope="col">{{ $page->content ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td scope="col">{!! $page->status ? config('pages.constants.aryPageStatusLabel')[$page->status] ?? 'N/A' : 'N/A' !!}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Created At</th>
                                        <td scope="col">{{ $page->created_at ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>   
                                             
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Back</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
