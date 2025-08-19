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

@push('styles')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css">
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom CSS for the page -->
    <link rel="stylesheet" href="{{ asset('backend/custom.css') }}">
@endpush

@push('scripts')
    <!-- Then the jQuery Validation plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <!-- Include the CKEditor script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <!-- Select2 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Initialize CKEditor -->
    <script>
        let ckEditorInstance;

        ClassicEditor
            .create(document.querySelector('#content'))
            .then(editor => {
                ckEditorInstance = editor;

                // optional styling
                editor.ui.view.editable.element.style.minHeight = '250px';
                editor.ui.view.editable.element.style.maxHeight = '250px';
                editor.ui.view.editable.element.style.overflowY = 'auto';

                // 🔥 Trigger validation on typing
                editor.model.document.on('change:data', () => {
                    const contentVal = editor.getData();
                    $('#content').val(contentVal); // keep textarea updated
                    $('#content').trigger('keyup'); // trigger validation manually
                });
            })
            .catch(error => {
                console.error(error);
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
                    if (ckEditorInstance) {
                        $('#content').val(ckEditorInstance.getData());
                    }
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
                        error.insertAfter($('.ck-editor')); // show below CKEditor UI
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endpush
