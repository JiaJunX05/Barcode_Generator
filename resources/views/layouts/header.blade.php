<!-- Header -->
<nav class="navbar navbar-expand-lg bg-light shadow-sm border-bottom border-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="bi bi-house-door-fill me-2"></i> Barcode Generator
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item me-2">
                    <a class="btn btn-primary fw-bold" href="{{ route('admin.login') }}">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
