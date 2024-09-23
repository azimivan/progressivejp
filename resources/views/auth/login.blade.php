<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="col-md-8 col-lg-7 col-xl-6">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg" class="img-fluid"
            alt="Phone image">
    </div>
    <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Username -->
            <div data-mdb-input-init class="form-outline mb-4">
                <x-input-label for="username" :value="__('Username')" class="form-label" />
                <x-text-input id="username" class="form-control form-control-lg" type="text" name="username"
                    :value="old('username')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <!-- Password -->
            <div data-mdb-input-init class="mt-4 form-outline mb-4">
                <x-input-label for="password" :value="__('Password')" class="form-label" />
                <x-text-input id="password" class="form-control form-control-lg" type="password" name="password"
                    required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="d-flex justify-content-around align-items-center mb-4">
                <div class="form-check">
                    <label for="remember_me" class="form-check-label">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                class="btn btn-primary btn-lg btn-block">{{ __('Log in') }}</button>
        </form>
    </div>

    <!-- Include Bootstrap JS and Popper.js FIRST -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal -->
    @if (session('inactive'))
        <div class="modal fade" id="inactiveModal" tabindex="-1" aria-labelledby="inactiveModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark" id="inactiveModalLabel">Account Inactive</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        {{ session('inactive') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trigger the modal AFTER loading Bootstrap JS -->
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('inactiveModal'), {});
            myModal.show();
        </script>
    @endif
</x-guest-layout>
