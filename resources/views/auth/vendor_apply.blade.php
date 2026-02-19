@extends('layouts.app')

@section('title', 'Devenir Vendeur - ' . config('app.name'))

@section('content')

{{-- ==================== MOBILE VIEW (< lg) ==================== --}}
<div class="block lg:hidden bg-gray-50 dark:bg-slate-950 min-h-screen pb-24" x-data="{ step: 1 }">

    {{-- Mobile Hero --}}
    <div class="relative bg-gradient-to-br from-red-600 via-orange-600 to-red-700 overflow-hidden" style="height:38vh;min-height:220px;">
        {{-- Animated blobs --}}
        <div class="absolute -top-14 -right-14 w-48 h-48 bg-white/10 rounded-full animate-pulse"></div>
        <div class="absolute top-6 -left-8 w-28 h-28 bg-white/10 rounded-full animate-ping" style="animation-duration:4s"></div>
        <div class="absolute bottom-6 right-6 w-16 h-16 bg-white/15 rounded-full"></div>

        {{-- Back button --}}
        <a href="{{ url()->previous() }}" class="absolute top-4 left-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white active:scale-90 transition-transform z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>

        {{-- Hero content --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8 gap-3">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-2xl border border-white/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Devenir Vendeur</h1>
                <p class="text-orange-100 text-xs font-medium mt-1">Faites décoller votre activité 🚀</p>
            </div>
            {{-- Benefits chips --}}
            <div class="flex gap-2 flex-wrap justify-center mt-1">
                <span class="px-3 py-1 bg-white/20 rounded-full text-white text-[9px] font-black uppercase tracking-wider">Visibilité Premium</span>
                <span class="px-3 py-1 bg-white/20 rounded-full text-white text-[9px] font-black uppercase tracking-wider">Paiements sécurisés</span>
                <span class="px-3 py-1 bg-white/20 rounded-full text-white text-[9px] font-black uppercase tracking-wider">Support 24/7</span>
            </div>
        </div>
    </div>

    {{-- Bottom-sheet form --}}
    <div class="bg-white dark:bg-slate-900 rounded-t-[2rem] -mt-6 relative z-10 shadow-2xl min-h-screen">
        <div class="px-5 pt-5 pb-8">
            {{-- Pull indicator --}}
            <div class="w-10 h-1.5 bg-gray-200 dark:bg-slate-700 rounded-full mx-auto mb-5"></div>

            {{-- Flash errors --}}
            @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 rounded-2xl">
                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-2">Erreurs à corriger</p>
                @foreach($errors->all() as $error)
                <p class="text-xs text-red-600 font-medium leading-relaxed">• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- Flash success --}}
            @if(session('success'))
            <div class="mb-5 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <p class="text-xs font-black text-green-700 dark:text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            <h2 class="text-xl font-black text-gray-900 dark:text-white mb-1">Votre demande</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-5 font-medium">Complétez les informations pour vérification.</p>

            <form action="{{ route('vendor.apply.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- === SECTION 1 : INFOS BASIQUES === --}}
                <div class="space-y-1 pb-1">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2">
                        <span class="w-4 h-4 bg-red-500 text-white rounded-md flex items-center justify-center text-[8px]">1</span>
                        Informations de l'établissement
                    </p>
                </div>

                {{-- Nom commercial --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom Commercial <span class="text-red-500">*</span></label>
                    <div class="flex items-center bg-gray-50 dark:bg-slate-800 rounded-2xl border-2 border-transparent focus-within:border-red-500 focus-within:bg-white dark:focus-within:bg-slate-700 transition-all">
                        <div class="pl-4 text-gray-400 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                        <input type="text" name="nom_commercial" value="{{ old('nom_commercial', auth()->user()->name) }}" required
                               class="w-full pl-3 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400"
                               placeholder="Ex: Maquis Chez Tante Marie">
                    </div>
                </div>

                {{-- Type de boutique --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Type de boutique <span class="text-red-500">*</span></label>
                    <div x-data="{
                            open: false,
                            selected: '{{ old('id_category_vendeur') }}',
                            options: [
                                @foreach($categories as $category)
                                    { id: '{{ $category->id_category_vendeur }}', name: '{{ $category->name }}' },
                                @endforeach
                            ],
                            get selectedLabel() {
                                const opt = this.options.find(o => o.id == this.selected);
                                return opt ? opt.name : 'Choisir un type...';
                            }
                        }" class="relative">
                        <input type="hidden" name="id_category_vendeur" :value="selected">
                        <button type="button" @click="open = !open"
                                class="w-full px-4 py-4 bg-gray-50 dark:bg-slate-800 rounded-2xl border-2 border-transparent flex items-center justify-between text-sm font-bold transition-all"
                                :class="open ? 'border-red-500 bg-white dark:bg-slate-700' : ''">
                            <span :class="!selected ? 'text-gray-400' : 'text-gray-900 dark:text-white'" x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-2xl shadow-2xl shadow-black/10 border border-gray-100 dark:border-slate-700 py-2 max-h-52 overflow-y-auto">
                            <template x-for="opt in options" :key="opt.id">
                                <button type="button" @click="selected = opt.id; open = false"
                                        class="w-full px-4 py-3 text-left flex items-center justify-between"
                                        :class="selected == opt.id ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700'">
                                    <span class="text-sm font-bold" x-text="opt.name"></span>
                                    <svg x-show="selected == opt.id" class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Téléphone Pro --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex justify-between">
                        Téléphone Pro
                        <span class="text-[8px] font-bold normal-case text-gray-300 tracking-normal">Optionnel</span>
                    </label>
                    <div x-data="{
                            open: false,
                            selected: '{{ old('phone_prefix', '+228') }}',
                            options: [
                                @foreach($countries as $country)
                                    { prefix: '{{ $country->phone_prefix }}', name: '{{ $country->name }}', icon: '{{ $country->flag_icon }}' },
                                @endforeach
                            ],
                            get selectedOption() {
                                return this.options.find(o => o.prefix === this.selected) || (this.options.length > 0 ? this.options[0] : {prefix: '', icon: ''});
                            }
                        }" class="flex gap-2">
                        <input type="hidden" name="phone_prefix" :value="selected">
                        <div class="relative">
                            <button type="button" @click="open = !open"
                                    class="px-3 py-4 bg-gray-50 dark:bg-slate-800 rounded-2xl border-2 border-transparent focus:border-red-500 flex items-center gap-2 font-bold text-sm text-gray-900 dark:text-white transition-all whitespace-nowrap h-full">
                                <span class="text-base" x-text="selectedOption.icon"></span>
                                <span class="text-xs font-black" x-text="selected"></span>
                                <svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 class="absolute z-50 mt-2 w-60 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 py-2 max-h-52 overflow-y-auto left-0">
                                <template x-for="opt in options" :key="opt.prefix">
                                    <button type="button" @click="selected = opt.prefix; open = false"
                                            class="w-full px-4 py-2.5 text-left flex items-center gap-3 transition-colors"
                                            :class="selected === opt.prefix ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : 'hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300'">
                                        <span class="text-xl" x-text="opt.icon"></span>
                                        <div>
                                            <div class="text-xs font-black" x-text="opt.prefix"></div>
                                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-tight" x-text="opt.name"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="flex-1 flex items-center bg-gray-50 dark:bg-slate-800 rounded-2xl border-2 border-transparent focus-within:border-red-500 focus-within:bg-white dark:focus-within:bg-slate-700 transition-all">
                            <input type="text" name="telephone_commercial" value="{{ old('telephone_commercial') }}"
                                   class="w-full px-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400"
                                   placeholder="00 00 00 00">
                        </div>
                    </div>
                </div>

                {{-- Adresse --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Adresse de l'établissement <span class="text-red-500">*</span></label>
                    <div class="flex items-start bg-gray-50 dark:bg-slate-800 rounded-2xl border-2 border-transparent focus-within:border-red-500 focus-within:bg-white dark:focus-within:bg-slate-700 transition-all">
                        <div class="pl-4 pt-4 text-gray-400 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <textarea name="adresse_complete" required placeholder="Rue, Quartier, Ville..."
                                  class="w-full pl-3 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400 resize-none h-20">{{ old('adresse_complete') }}</textarea>
                    </div>
                </div>

                {{-- Séparateur section --}}
                <div class="pt-2 space-y-1">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1 flex items-center gap-2">
                        <span class="w-4 h-4 bg-red-500 text-white rounded-md flex items-center justify-center text-[8px]">2</span>
                        Vérification & Documents
                    </p>
                </div>

                {{-- RCCM --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">N° RCCM <span class="text-red-500">*</span></label>
                    <div class="flex items-center bg-gray-50 dark:bg-slate-800 rounded-2xl border-2 border-transparent focus-within:border-red-500 focus-within:bg-white dark:focus-within:bg-slate-700 transition-all">
                        <div class="pl-4 text-gray-400 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                        <input type="text" name="registre_commerce" value="{{ old('registre_commerce') }}" required
                               class="w-full pl-3 pr-4 py-4 bg-transparent border-none focus:ring-0 focus:outline-none text-sm font-bold text-gray-900 dark:text-white placeholder-gray-400"
                               placeholder="Ex: TG-LOM-...">
                    </div>
                </div>

                {{-- Documents upload --}}
                <div class="grid grid-cols-2 gap-3">
                    {{-- Pièce identité --}}
                    <div class="space-y-1" x-data="{ fileName: null }">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex justify-between">
                            Pièce d'identité
                            <span class="text-[8px] font-bold normal-case text-gray-300 tracking-normal">Optionnel</span>
                        </label>
                        <label class="relative group cursor-pointer block">
                            <input type="file" name="document_identite" accept="image/*,.pdf"
                                   @change="fileName = $event.target.files[0]?.name ?? null"
                                   class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer">
                            <div class="py-5 bg-gray-50 dark:bg-slate-800 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl flex flex-col items-center gap-2 group-hover:border-red-300 group-hover:bg-red-50/30 transition-all"
                                 :class="fileName ? 'border-green-400 bg-green-50/30' : ''">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4" :class="fileName ? 'text-green-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <p class="text-[8px] font-black uppercase tracking-widest text-center" :class="fileName ? 'text-green-600' : 'text-gray-400'" x-text="fileName ? 'Fichier choisi' : 'Choisir'"></p>
                            </div>
                        </label>
                    </div>

                    {{-- Justificatif domicile --}}
                    <div class="space-y-1" x-data="{ fileName: null }">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex justify-between">
                            Justificatif
                            <span class="text-[8px] font-bold normal-case text-gray-300 tracking-normal">Optionnel</span>
                        </label>
                        <label class="relative group cursor-pointer block">
                            <input type="file" name="justificatif_domicile" accept="image/*,.pdf"
                                   @change="fileName = $event.target.files[0]?.name ?? null"
                                   class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer">
                            <div class="py-5 bg-gray-50 dark:bg-slate-800 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl flex flex-col items-center gap-2 group-hover:border-red-300 group-hover:bg-red-50/30 transition-all"
                                 :class="fileName ? 'border-green-400 bg-green-50/30' : ''">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4" :class="fileName ? 'text-green-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <p class="text-[8px] font-black uppercase tracking-widest text-center" :class="fileName ? 'text-green-600' : 'text-gray-400'" x-text="fileName ? 'Fichier choisi' : 'Choisir'"></p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- CGU --}}
                <label class="flex items-start gap-3 cursor-pointer py-1 group">
                    <div class="relative mt-0.5">
                        <input type="checkbox" required class="sr-only peer">
                        <div class="w-5 h-5 bg-gray-100 dark:bg-slate-700 rounded-lg border-2 border-gray-200 dark:border-slate-600 peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">
                        J'accepte les <a href="#" class="text-red-500 font-black underline">conditions d'utilisation vendeur</a>
                    </span>
                </label>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-red-600 to-orange-500 text-white font-black rounded-2xl text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-red-500/30 active:scale-[0.97] transition-all">
                    Soumettre ma candidature →
                </button>

                <p class="text-[9px] text-center text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">
                    Traitement sous 48h par nos administrateurs
                </p>
            </form>
        </div>
    </div>
</div>
{{-- ==================== END MOBILE VIEW ==================== --}}

{{-- ==================== DESKTOP VIEW (>= lg) ==================== --}}
<div class="hidden lg:block">
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-gray-100">
        <div class="md:flex">
            {{-- Left Side --}}
            <div class="md:w-5/12 bg-gradient-to-br from-red-600 to-orange-500 p-12 text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <h1 class="text-4xl font-display font-black mb-6 leading-tight uppercase tracking-tighter">Faites décoller votre activité.</h1>
                    <p class="text-lg opacity-90 mb-10 font-medium italic leading-relaxed">Rejoignez la première plateforme dédiée aux professionnels locaux et touchez des milliers de clients.</p>
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4"><div class="bg-white/20 p-2 rounded-xl shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div><span class="font-bold text-sm tracking-wide">Visibilité Premium & SEO</span></li>
                        <li class="flex items-start gap-4"><div class="bg-white/20 p-2 rounded-xl shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div><span class="font-bold text-sm tracking-wide">Gestion de Catalogue Intelligente</span></li>
                        <li class="flex items-start gap-4"><div class="bg-white/20 p-2 rounded-xl shrink-0"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><span class="font-bold text-sm tracking-wide">Paiements Sécurisés</span></li>
                    </ul>
                </div>
            </div>
            {{-- Right Side --}}
            <div class="md:w-7/12 p-8 md:p-14">
                <div class="text-center md:text-left mb-10">
                    <h2 class="text-3xl font-display font-black text-gray-900 mb-2 uppercase tracking-tighter">Votre Demande</h2>
                    <p class="text-gray-400 font-medium text-sm">Complétez les informations pour vérification.</p>
                </div>
                <form action="{{ route('vendor.apply.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Nom Commercial de l'Établissement</label>
                            <input type="text" name="nom_commercial" value="{{ old('nom_commercial', auth()->user()->name) }}" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5 flex-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Type de Boutique</label>
                                <div x-data="{ open: false, selected: '{{ old('id_category_vendeur') }}', options: [@foreach($categories as $category){ id: '{{ $category->id_category_vendeur }}', name: '{{ $category->name }}' },@endforeach], get selectedLabel() { const opt = this.options.find(o => o.id == this.selected); return opt ? opt.name : 'Choisir un type...'; } }" class="relative w-full">
                                    <input type="hidden" name="id_category_vendeur" :value="selected">
                                    <button type="button" @click="open = !open" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 flex items-center justify-between"><span x-text="selectedLabel" :class="!selected ? 'text-gray-400' : ''"></span><svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                    <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 max-h-64 overflow-y-auto"><template x-for="opt in options" :key="opt.id"><button type="button" @click="selected = opt.id; open = false" class="w-full px-6 py-3 text-left hover:bg-red-50 transition-colors flex items-center justify-between" :class="selected == opt.id ? 'bg-red-50 text-red-600' : 'text-gray-700'"><span class="font-bold" x-text="opt.name"></span><svg x-show="selected == opt.id" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></button></template></div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4 flex justify-between">Téléphone Pro <span class="text-[8px] font-medium opacity-50 lowercase tracking-normal">Optionnel</span></label>
                                <div class="flex gap-2">
                                    <div x-data="{ open: false, selected: '{{ old('phone_prefix', '+228') }}', options: [@foreach($countries as $country){ prefix: '{{ $country->phone_prefix }}', name: '{{ $country->name }}', icon: '{{ $country->flag_icon }}' },@endforeach], get selectedOption() { return this.options.find(o => o.prefix === this.selected) || (this.options.length > 0 ? this.options[0] : {prefix: '', icon: ''}); } }" class="relative w-32">
                                        <input type="hidden" name="phone_prefix" :value="selected">
                                        <button type="button" @click="open = !open" class="w-full px-4 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 flex items-center justify-between"><div class="flex items-center gap-2"><span class="text-lg" x-text="selectedOption.icon"></span><span x-text="selected"></span></div><svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
                                        <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="absolute z-50 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 max-h-64 overflow-y-auto"><template x-for="opt in options" :key="opt.prefix"><button type="button" @click="selected = opt.prefix; open = false" class="w-full px-4 py-3 text-left hover:bg-red-50 transition-colors flex items-center gap-3" :class="selected === opt.prefix ? 'bg-red-50 text-red-600' : 'text-gray-700'"><span class="text-2xl" x-text="opt.icon"></span><div><div class="font-black text-sm" x-text="opt.prefix"></div><div class="text-[9px] font-bold text-gray-400 uppercase" x-text="opt.name"></div></div></button></template></div>
                                    </div>
                                    <input type="text" name="telephone_commercial" value="{{ old('telephone_commercial') }}" placeholder="00 00 00 00" class="flex-1 px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Adresse de l'Établissement / Local</label>
                            <textarea name="adresse_complete" required placeholder="Rue, Quartier, Ville..." class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 h-24 resize-none">{{ old('adresse_complete') }}</textarea>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-gray-50 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">N° Registre de Commerce (RCCM)</label>
                            <input type="text" name="registre_commerce" value="{{ old('registre_commerce') }}" required placeholder="Ex: TG-LOM-..." class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4 flex justify-between">Pièce d'identité <span class="text-[8px] font-medium opacity-50">Optionnel</span></label>
                                <div class="relative group"><input type="file" name="document_identite" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"><div class="w-full px-6 py-4 bg-gray-100 border-2 border-dashed border-gray-200 rounded-2xl flex items-center gap-3 transition-colors group-hover:border-red-200"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg><span class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate">Choisir un fichier</span></div></div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4 flex justify-between">Justificatif domicile <span class="text-[8px] font-medium opacity-50">Optionnel</span></label>
                                <div class="relative group"><input type="file" name="justificatif_domicile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"><div class="w-full px-6 py-4 bg-gray-100 border-2 border-dashed border-gray-200 rounded-2xl flex items-center gap-3 transition-colors group-hover:border-red-200"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg><span class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate">Choisir un fichier</span></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4">
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative"><input type="checkbox" required class="sr-only peer"><div class="w-5 h-5 bg-gray-100 rounded-md border-2 border-gray-200 peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center"><svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg></div></div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-gray-900 transition-colors">J'accepte les conditions d'utilisation vendeur</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-3xl font-black text-xs uppercase tracking-[0.3em] hover:shadow-2xl hover:shadow-red-200 transition-all transform hover:-translate-y-1 active:scale-95">Soumettre ma Candidature</button>
                    <p class="text-[9px] text-center text-gray-300 font-bold uppercase tracking-widest leading-relaxed">Votre demande sera traitée sous 48h par nos administrateurs.</p>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
{{-- ==================== END DESKTOP VIEW ==================== --}}

<style>[x-cloak] { display: none !important; }</style>
@endsection
