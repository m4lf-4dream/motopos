<x-guest-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-screen" style="min-height: 100vh;">

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center text-center" style="background-color:#EDDCC6">
                <div>
                    <h3 class="fw-bold text-dark mb-2">Welcome Back Again</h3>
                    <p class="text-dark">Login to continue to Motopos</p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center" style="background-color: #bf4646;">

                <div class="card shadow-lg border-0 rounded-4 w-100 mx-3" style="max-width: 450px; background-color: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                    <div class="card-body p-4 p-md-5">

                        <h2 class="text-white text-center fw-light display-5 mb-2">Welcome!</h2>
                        <h4 class="text-white text-center fw-normal opacity-75 mb-5">Login</h4>

                        <x-auth-session-status class="mb-4 text-info" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label text-white-50 ms-2 small">Email Address</label>
                                <input id="email" type="email" name="email"
                                    class="form-control rounded-pill py-2 px-4 border-0 shadow-sm"
                                    placeholder="Masukan Email" value="{{ old('email') }}" required autofocus
                                    autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label text-white-50 ms-2 small">Password</label>
                                <input id="password" type="password" name="password"
                                    class="form-control rounded-pill py-2 px-4 border-0 shadow-sm"
                                    placeholder="Masukan Password" required autocomplete="current-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label text-white-50 small" for="remember_me">
                                        Ingat saya
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="text-decoration-none text-white-50 small" href="{{ route('password.request') }}">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="btn btn-light rounded-pill w-100 py-2 fw-bold text-dark shadow-sm">
                                    Login
                                </button>
                            </div>

                            <div class="mt-4 text-center">
                                <p class="text-white-50 small">Belum punya akun?
                                    <a href="{{ route('register') }}" class="text-white fw-bold text-decoration-none">Daftar</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-guest-layout>
