<x-guest-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-screen" style="min-height: 100vh;">

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center text-center"
                style="background-color: #EDDCC6;">

                <div class="px-5">
                    <h2 class="fw-bold text-dark mb-2">Hai Motopart</h2>
                    <p class="text-dark opacity-75 mb-0">Join Us Now</p>
                </div>

            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center" style="background-color: #BF4646;">

                <div class="card shadow-lg border-0 rounded-4 w-100 mx-3"
                    style="max-width: 500px; background-color: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                    <div class="card-body p-4 p-md-5">

                        <h2 class="text-white text-center fw-light mb-2">Create Account</h2>
                        <p class="text-white text-center opacity-50 small mb-4">Silahkan isi data diri Anda</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label text-white-50 ms-2 small">Full Name</label>
                                <input id="name" type="text" name="name"
                                    class="form-control rounded-pill py-2 px-4 border-0 shadow-sm"
                                    placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-1 text-danger small" />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label text-white-50 ms-2 small">Email Address</label>
                                <input id="email" type="email" name="email"
                                    class="form-control rounded-pill py-2 px-4 border-0 shadow-sm"
                                    placeholder="Email@domain.com" value="{{ old('email') }}" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label text-white-50 ms-2 small">Password</label>
                                    <input id="password" type="password" name="password"
                                        class="form-control rounded-pill py-2 px-4 border-0 shadow-sm"
                                        placeholder="********" required />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation"
                                        class="form-label text-white-50 ms-2 small">Confirm</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        class="form-control rounded-pill py-2 px-4 border-0 shadow-sm"
                                        placeholder="********" required />
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="text-danger small" />

                            <div class="mt-4">
                                <button type="submit" class="btn btn-light rounded-pill w-100 py-2 fw-bold shadow-sm">
                                    Register Account
                                </button>
                            </div>

                            <div class="mt-4 text-center">
                                <span class="text-white-50 small">Sudah punya akun?</span>
                                <a class="text-decoration-none text-white fw-bold small ms-1"
                                    href="{{ route('login') }}">
                                    Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
