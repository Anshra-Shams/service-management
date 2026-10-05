<x-guest-layout>
    <x-slot name="title">Reset Password</x-slot>

    <div class="min-h-screen flex items-center justify-center p-6 sm:p-12 bg-slate-50 selection:bg-blue-600 selection:text-white">
        <div class="w-full max-w-md">
            
            <!-- Brand Header -->
            <div class="flex flex-col items-center mb-8">
                <a href="{{ route('login') }}" class="group flex flex-col items-center">
                    <div class="w-14 h-14 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center font-black text-2xl text-white shadow-xl shadow-blue-500/25 border border-blue-400/30 mb-3 group-hover:scale-105 transition-transform">
                        SM
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Service Management</h2>
                    <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mt-0.5">Admin Portal</span>
                </a>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-6 sm:p-10">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Forgot Password?</h3>
                    <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">
                        No worries. Enter your registered email address and we will send you a password reset link.
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="admin@example.com"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50/60 hover:bg-white focus:bg-white border @error('email') border-rose-300 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-200 focus:border-blue-600 focus:ring-blue-600/15 @enderror rounded-xl text-sm text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:ring-4" />
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full py-3.5 px-5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-[0.99] text-white font-semibold text-sm rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/35 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer">
                            <span>Send Password Reset Link</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Back to Login -->
                    <div class="pt-2 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            <span>Back to Sign In</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Service Management System
            </div>
        </div>
    </div>
</x-guest-layout>
