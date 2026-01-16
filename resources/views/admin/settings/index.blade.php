@extends('layouts.admin')

@section('title', 'Paramètres Système')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ activeTab: 'general' }">
    <div class="mb-10">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Configuration Système</h1>
        <p class="text-gray-500 font-medium mt-1 text-sm uppercase tracking-widest">Gérez les services tiers et les préférences de la plateforme</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-10">
        <!-- Sidebar Navigation -->
        <aside class="w-full lg:w-72 space-y-2">
            @foreach($settings as $group => $items)
            <button 
                @click="activeTab = '{{ $group }}'"
                :class="activeTab === '{{ $group }}' ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100'"
                class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all group overflow-hidden relative">
                <span class="relative z-10 font-black uppercase text-[11px] tracking-widest">{{ $group }}</span>
                <svg :class="activeTab === '{{ $group }}' ? 'translate-x-0 opacity-100' : '-translate-x-4 opacity-0'" class="w-4 h-4 relative z-10 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </button>
            @endforeach
        </aside>

        <!-- Main Configuration Content -->
        <div class="flex-1">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @foreach($settings as $group => $items)
                <div x-show="activeTab === '{{ $group }}'" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-8">
                    
                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-10">
                        <div class="mb-10 flex items-center gap-4">
                            @php
                                $icons = [
                                    'general' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />',
                                    'branding' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                                    'payment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />',
                                    'location' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />',
                                    'seo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
                                ];
                            @endphp
                            <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $icons[$group] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>' !!}
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight capitalize">{{ $group }}</h3>
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mt-0.5">Configuration des options pour {{ $group }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($items as $setting)
                            <div class="space-y-3 {{ in_array($setting->type, ['textarea', 'image']) ? 'md:col-span-2' : '' }}">
                                <label for="{{ $setting->key }}" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] ml-1">{{ $setting->label }}</label>
                                <div class="relative">
                                    @if($setting->type === 'textarea')
                                        <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="4" 
                                            class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-3xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all resize-none shadow-sm">{{ $setting->value }}</textarea>
                                    @elseif($setting->type === 'image')
                                        <div class="flex flex-col md:flex-row items-center gap-6 p-6 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200 hover:border-red-200 transition-all">
                                            <div class="w-32 h-32 bg-white rounded-2xl flex items-center justify-center overflow-hidden border border-gray-100 shadow-sm">
                                                @if($setting->value)
                                                    <img src="{{ asset('storage/' . $setting->value) }}" class="w-full h-full object-contain p-2">
                                                @else
                                                    <div class="text-[10px] font-black uppercase text-gray-300">Aperçu</div>
                                                @endif
                                            </div>
                                            <div class="flex-1 space-y-4 text-center md:text-left">
                                                <div>
                                                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest">Choisir un nouveau fichier</h4>
                                                    <p class="text-[10px] font-bold text-gray-400 mt-1">PNG, JPG ou WEBP. Maximum 2Mo.</p>
                                                </div>
                                                <input type="file" name="{{ $setting->key }}" id="{{ $setting->key }}" accept="image/*"
                                                    class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-red-50 file:text-red-600 hover:file:bg-red-100 transition-all cursor-pointer">
                                            </div>
                                        </div>
                                    @elseif($setting->type === 'password')
                                        <input type="password" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                            class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all shadow-sm">
                                    @elseif($setting->type === 'number')
                                        <input type="number" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                            class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all shadow-sm">
                                    @else
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                            class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all shadow-sm">
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="flex justify-end mt-10">
                    <button type="submit" class="px-10 py-5 bg-gray-900 text-white rounded-[2rem] text-sm font-black uppercase tracking-widest hover:bg-black transition-all shadow-2xl active:scale-95 flex items-center gap-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer ces paramètres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
