@extends('layouts.auth')

@section('title', 'Connexion Employé')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-red-50 px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Vendor Branding -->
        <div class="text-center mb-8">
            @if($vendor->image_principale)
                <img src="{{ asset('storage/' . $vendor->image_principale) }}" alt="{{ $vendor->nom_commercial }}" class="w-20 h-20 mx-auto rounded-2xl shadow-lg mb-4 object-cover">
            @else
                <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-[#ff4d00] to-orange-600 flex items-center justify-center shadow-lg mb-4">
                    <span class="text-3xl font-black text-white">{{ substr($vendor->nom_commercial, 0, 1) }}</span>
                </div>
            @endif
            <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $vendor->nom_commercial }}</h1>
            <p class="text-gray-500 font-medium">Espace Employé</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Connexion</h2>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('vendor.staff.login.post', ['vendor_slug' => $vendor->slug]) }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition"
                           placeholder="votre.email@exemple.com">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mot de passe</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition"
                           placeholder="••••••••">
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#ff4d00] to-orange-600 text-white font-black py-4 rounded-xl hover:shadow-lg hover:shadow-orange-200 transition-all transform hover:scale-[1.02]">
                    Se connecter
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">
                    Lien d'accès unique fourni par votre responsable
                </p>
            </div>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Besoin d'aide ? Contactez votre responsable
        </p>
    </div>
</div>
@endsection
