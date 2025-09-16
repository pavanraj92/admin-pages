@extends('admin::admin.layouts.master')

@section('title', 'Pages Management')

@section('page-title', isset($page) ? 'Edit Page' : 'Create Page')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.pages.index') }}">CMS Pages Manager</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($page) ? 'Edit Page' : 'Create Page' }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ isset($page) ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}" method="POST"
            id="pageForm">
            @if (isset($page))
                @method('PUT')
            @endif
            @csrf
            <!-- Start Page Content -->
            <div class="row">
                <div class="col-8">
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ $page?->title ?? old('title') }}" required>
                                    @error('title')
                                        <div class="text-danger validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status<span class="text-danger">*</span></label>
                                    <select name="status" class="form-control select2" required>
                                        @foreach (config('pages.constants.status', []) as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ (isset($page) && $page?->status == $key) || old('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="text-danger validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Content<span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control description-editor">{{ $page?->content ?? old('content') }}</textarea>
                            @error('content')
                                <div class="text-danger validation-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"
                                id="saveBtn">{{ isset($page) ? 'Update' : 'Save' }}</button>
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Back</a>
                        </div>

                    </div>
                </div>

                <div class="col-md-4">
                    @include('admin::admin.seo_meta_data.seo', ['seo' => $seo ?? null])
                </div>
            </div>
            <!-- End PAge Content -->
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Initialize CKEditor -->
    <script>
        $(document).ready(function() {
            $('#content').summernote({
                height: 250, // ✅ editor height
                minHeight: 250,
                maxHeight: 250,
                toolbar: [
                    // ✨ Add "code view" toggle button
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['view', ['codeview']] // ✅ source code button
                ],
                callbacks: {
                    onChange: function(contents, $editable) {
                        // keep textarea updated
                        $('#content').val(contents);
                        // trigger validation if needed
                        $('#content').trigger('keyup');
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for any select elements with the class 'select2'
            $('.select2').select2();

            //jquery validation for the form
            $('#pageForm').validate({
                ignore: [],
                rules: {
                    title: {
                        required: true,
                        minlength: 3
                    },
                    content: {
                        required: true,
                        minlength: 3
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    title: {
                        required: "Please enter a title",
                        minlength: "Title must be at least 3 characters long"
                    },
                    content: {
                        required: "Please enter content",
                        minlength: "Content must be at least 3 characters long"
                    },
                    status: {
                        required: "Please select a status"
                    }
                },
                submitHandler: function(form) {
                    // Update textarea before submit
                    $('#content').val($('#content').summernote('code'));

                    const $btn = $('#saveBtn');
                    if ($btn.text().trim().toLowerCase() === 'update') {
                        $btn.prop('disabled', true).text('Updating...');
                    } else {
                        $btn.prop('disabled', true).text('Saving...');
                    }

                    // Now submit
                    form.submit();
                },
                errorElement: 'div',
                errorClass: 'text-danger custom-error',
                errorPlacement: function(error, element) {
                    $('.validation-error').hide(); // hide blade errors
                    if (element.attr("id") === "content") {
                        error.insertAfter($('.note-editor'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endpush
