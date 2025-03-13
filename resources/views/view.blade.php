@extends("layouts.app")

@section("title", "View Product")
@section("content")

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="row g-0">

                    <div class="col-md-5 d-flex align-items-center justify-content-center p-3 bg-light">
                        <img src="{{ asset('assets/' . $product->image) }}" alt="{{ $product->sku_code }}" class="img-fluid" id="preview-image" style="max-width: 100%; max-height: 300px; object-fit: contain;">
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
                            <h2 class="text-primary text-center mb-3">View Product</h2>
                            <p class="text-muted text-center">View and manage your product here.</p>
                            <hr>

                            <!-- Product Details -->
                            <div class="mb-3">
                                <label for="sku_code" class="form-label fw-bold">Product SKU:</label>
                                <input type="text" class="form-control" id="sku_code" name="sku_code" value="{{ $product->sku_code }}" readonly>
                            </div>

                            <div class="mb-5">
                                <label for="barcode-number" class="form-label fw-bold me-2">Barcode Number:</label>
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ $product->barcode ? asset('assets/' . $product->barcode->barcode_image) : '' }}"
                                         alt="{{ $product->sku_code }}" class="img-fluid" style="max-width: 200px;">
                                    <span class="fw-bold fs-5 mt-2">{{ $product->barcode ? $product->barcode->barcode_number : 'No Barcode' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
