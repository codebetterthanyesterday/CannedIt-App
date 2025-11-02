@extends('layouts.app')

@section('title', 'Test Google OAuth - CannedIt')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Test Google OAuth Integration</h1>
        
        <div class="space-y-4">
            <!-- Authentication Status -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Authentication Status</h2>
                @auth
                    <div class="flex items-center space-x-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-10 h-10 rounded-full">
                        @else
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-primary-600"></i>
                            </div>
                        @endif
                        <div>
                            <p class="text-green-600 font-medium">✓ Logged in as: {{ auth()->user()->name }}</p>
                            <p class="text-sm text-gray-600">Email: {{ auth()->user()->email }}</p>
                            @if(auth()->user()->google_id)
                                <p class="text-sm text-blue-600">✓ Connected with Google (ID: {{ auth()->user()->google_id }})</p>
                            @else
                                <p class="text-sm text-gray-500">Not connected with Google</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-red-600">✗ Not logged in</p>
                @endauth
            </div>

            <!-- Google OAuth Test -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Google OAuth Test</h2>
                
                @guest
                    <div class="space-y-3">
                        <p class="text-gray-600">Test the Google OAuth functionality:</p>
                        
                        <a href="{{ route('auth.google') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="white" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="white" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="white" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="white" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Test Google Login
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        <p class="text-green-600">✓ You are already logged in!</p>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                @endguest
            </div>

            <!-- Configuration Check -->
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Configuration Check</h2>
                
                <div class="space-y-2 text-sm">
                    <div class="flex items-center space-x-2">
                        @if(config('services.google.client_id'))
                            <i class="fas fa-check text-green-600"></i>
                            <span class="text-green-600">Google Client ID: Configured</span>
                        @else
                            <i class="fas fa-times text-red-600"></i>
                            <span class="text-red-600">Google Client ID: Not configured</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        @if(config('services.google.client_secret'))
                            <i class="fas fa-check text-green-600"></i>
                            <span class="text-green-600">Google Client Secret: Configured</span>
                        @else
                            <i class="fas fa-times text-red-600"></i>
                            <span class="text-red-600">Google Client Secret: Not configured</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        @if(config('services.google.redirect'))
                            <i class="fas fa-check text-green-600"></i>
                            <span class="text-green-600">Redirect URI: {{ config('services.google.redirect') }}</span>
                        @else
                            <i class="fas fa-times text-red-600"></i>
                            <span class="text-red-600">Redirect URI: Not configured</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Routes Check -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Available Routes</h2>
                
                <div class="space-y-2 text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-link text-blue-600"></i>
                        <a href="{{ route('auth.google') }}" class="text-blue-600 hover:underline">
                            {{ route('auth.google') }} (Google OAuth Redirect)
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-link text-blue-600"></i>
                        <span class="text-gray-600">{{ config('app.url') }}/auth/google/callback (OAuth Callback)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-link text-blue-600"></i>
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                            {{ route('login') }} (Login Page)
                        </a>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-link text-blue-600"></i>
                        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
                            {{ route('register') }} (Register Page)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-indigo-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Setup Instructions</h2>
                
                <div class="text-sm text-gray-700 space-y-2">
                    <p><strong>1. Google Cloud Console Setup:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank" class="text-blue-600 hover:underline">Google Cloud Console</a></li>
                        <li>Create a new project or select existing one</li>
                        <li>Enable the Google+ API</li>
                        <li>Go to "Credentials" and create "OAuth 2.0 Client IDs"</li>
                        <li>Add your redirect URI: <code class="bg-gray-200 px-1 rounded">{{ config('services.google.redirect') }}</code></li>
                    </ul>
                    
                    <p class="mt-4"><strong>2. Environment Variables:</strong></p>
                    <div class="bg-gray-800 text-green-400 p-3 rounded font-mono text-xs">
                        GOOGLE_CLIENT_ID=your_google_client_id<br>
                        GOOGLE_CLIENT_SECRET=your_google_client_secret<br>
                        GOOGLE_REDIRECT_URI={{ config('services.google.redirect') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection