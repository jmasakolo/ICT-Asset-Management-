<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In</title>
    @vite(['resources/css/app.css'])
</head>
<body class="auth-bg flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm animate-fade-in-up">
        <div class="mb-8 flex flex-col items-center text-center">
            <span class="text-2xl font-semibold tracking-tight text-gray-900">ICT Asset Management</span>
            <p class="mt-4 text-sm font-medium text-blue-700">
                IT Asset Tracking &amp; Inventory Management
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-lg shadow-blue-900/5 overflow-hidden p-0">
            <div class="h-1 bg-gradient-to-r from-blue-600 to-gray-800"></div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   autocomplete="username"
                                   class="block w-full rounded-lg border-gray-300 pl-10 shadow-sm transition focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   data-password-input
                                   class="block w-full rounded-lg border-gray-300 pl-10 shadow-sm transition focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10">
                            <button type="button" data-password-toggle="password"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-600"
                                    aria-label="Show password">
                                <svg data-icon-eye class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M1.5 12s4-7 10.5-7 10.5 7 10.5 7-4 7-10.5 7S1.5 12 1.5 12z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg data-icon-eye-off class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 10.6a3 3 0 004.24 4.24M6.36 6.36C3.86 8 1.5 12 1.5 12s4 7 10.5 7c1.85 0 3.47-.5 4.84-1.24M17.64 17.64C20.14 16 22.5 12 22.5 12s-1.06-1.86-2.86-3.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500">
                        Log in
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} ICT Asset Management. All rights reserved.
        </p>
    </div>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.getAttribute('data-password-toggle'));
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
                button.querySelector('[data-icon-eye]').classList.toggle('hidden', !showing);
                button.querySelector('[data-icon-eye-off]').classList.toggle('hidden', showing);
            });
        });
    </script>
</body>
</html>
