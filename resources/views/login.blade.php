<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen">
    <div class="relative hidden w-1/2 items-center justify-center overflow-hidden bg-gradient-to-br from-blue-700 via-blue-800 to-gray-900 p-12 lg:flex">
        <div class="auth-blob auth-blob-1"></div>
        <div class="auth-blob auth-blob-3"></div>

        <div class="relative z-10 max-w-md text-white">
            <span class="text-2xl font-semibold tracking-tight">ICT Asset Management</span>
            <p class="mt-3 text-sm font-medium text-blue-200">
                IT Asset Tracking &amp; Inventory Management
            </p>

            <div class="relative mt-12 rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                <div class="absolute -top-3 right-6 flex items-center gap-1 rounded-full bg-blue-400 px-3 py-1 text-xs font-semibold text-blue-950 shadow">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.306a11.95 11.95 0 015.814-5.518l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                    +24%
                </div>

                <svg viewBox="0 0 400 260" class="h-auto w-full" fill="none">
                    <g stroke="rgba(255,255,255,0.08)" stroke-width="1">
                        <line x1="0" y1="40" x2="400" y2="40" />
                        <line x1="0" y1="100" x2="400" y2="100" />
                        <line x1="0" y1="160" x2="400" y2="160" />
                        <line x1="0" y1="220" x2="400" y2="220" />
                    </g>

                    <g fill="rgba(255,255,255,0.12)">
                        <rect x="20" y="190" width="26" height="50" rx="4" />
                        <rect x="76" y="160" width="26" height="80" rx="4" />
                        <rect x="132" y="130" width="26" height="110" rx="4" />
                        <rect x="188" y="100" width="26" height="140" rx="4" />
                    </g>

                    <path d="M20 220 C 90 200, 150 170, 210 130 C 270 90, 330 60, 380 20 L 380 240 L 20 240 Z" fill="url(#authAreaGradient)" />
                    <path d="M20 220 C 90 200, 150 170, 210 130 C 270 90, 330 60, 380 20" stroke="#ffffff" stroke-width="3" stroke-linecap="round" fill="none" />

                    <circle cx="20" cy="220" r="5" fill="#ffffff" />
                    <circle cx="210" cy="130" r="5" fill="#ffffff" />
                    <circle cx="380" cy="20" r="6" fill="#38bdf8" />

                    <defs>
                        <linearGradient id="authAreaGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#60a5fa" stop-opacity="0.35" />
                            <stop offset="100%" stop-color="#60a5fa" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                </svg>

                <p class="mt-4 text-sm text-blue-100">
                    Track assets, monitor maintenance, and keep inventory data current across every department.
                </p>
            </div>
        </div>
    </div>

    <div class="auth-bg flex w-full items-center justify-center px-4 py-12 lg:w-1/2">
        <div class="w-full max-w-sm animate-fade-in-up">
            <div class="mb-8 flex flex-col items-center text-center lg:hidden">
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
