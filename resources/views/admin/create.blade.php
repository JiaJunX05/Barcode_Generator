@extends("admin.layouts.app")

@section("title", "Create Product")
@section("content")

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="row g-0">

                    <div class="col-md-5 d-flex align-items-center justify-content-center p-3 bg-light">
                        <img src="{{ asset('assets/icons/Logo.png') }}"
                            alt="Preview" class="img-fluid" id="preview-image" style="max-width: 100%; max-height: 300px; object-fit: contain;">
                    </div>

                    <div class="col-md-7">
                        <div class="card-body p-4">
                            <div class="container text-center">
                                <!-- Success Alert -->
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Error Alert -->
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                            </div>

                            <!-- Form Title -->
                            <h2 class="text-primary text-center mb-3">Create Product</h2>
                            <p class="text-muted text-center">Add a new product to the inventory system.</p><hr>

                            <!-- Form -->
                            <form action="{{ route('product.create.submit') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">Product Image:</label>
                                    <input type="file" class="form-control" id="image" name="image" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sku_code" class="form-label fw-bold">Product SKU:</label>
                                    <input type="text" class="form-control text-uppercase" id="sku_code" name="sku_code" placeholder="Enter Product SKU" required>
                                </div>

                                <div class="mb-3">
                                    <label for="barcode_number" class="form-label fw-bold">Barcode Number:</label>
                                    <input type="text" class="form-control" id="barcode_number" name="barcode_number" placeholder="Enter Barcode Number" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 shadow-sm mt-3">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/create.js') }}"></script>
@endsection
