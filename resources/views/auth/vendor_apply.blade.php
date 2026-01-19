@extends('layouts.app')

@section('title', 'Devenir Vendeur - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-gray-100">
        <div class="md:flex">
            <!-- Left Side: Image/Branding -->
            <div class="md:w-5/12 bg-gradient-to-br from-red-600 to-orange-500 p-12 text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <h1 class="text-4xl font-display font-black mb-6 leading-tight uppercase tracking-tighter">Faites décoller votre activité.</h1>
                    <p class="text-lg opacity-90 mb-10 font-medium italic leading-relaxed">Rejoignez la première plateforme dédiée aux professionnels locaux et touchez des milliers de clients.</p>
                    
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <div class="bg-white/20 p-2 rounded-xl shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="font-bold text-sm tracking-wide">Visibilité Premium & SEO</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="bg-white/20 p-2 rounded-xl shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="font-bold text-sm tracking-wide">Gestion de Catalogue Intelligente</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="bg-white/20 p-2 rounded-xl shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="font-bold text-sm tracking-wide">Paiements Sécurisés</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side: Application Form -->
            <div class="md:w-7/12 p-8 md:p-14">
                <div class="text-center md:text-left mb-10">
                    <h2 class="text-3xl font-display font-black text-gray-900 mb-2 uppercase tracking-tighter">Votre Demande</h2>
                    <p class="text-gray-400 font-medium text-sm">Complétez les informations pour vérification.</p>
                </div>
                
                <form action="{{ route('vendor.apply.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Section: Infos Basiques -->
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Nom Commercial de l'Établissement</label>
                            <input type="text" name="nom_commercial" value="{{ old('nom_commercial', auth()->user()->name) }}" required
                                   class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5 flex-1">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Type de Boutique</label>
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
                                    }" class="relative w-full">
                                    <input type="hidden" name="id_category_vendeur" :value="selected">
                                    <button type="button" @click="open = !open" 
                                            class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 flex items-center justify-between">
                                        <span x-text="selectedLabel" :class="!selected ? 'text-gray-400' : ''"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 max-h-64 overflow-y-auto">
                                        <template x-for="opt in options" :key="opt.id">
                                            <button type="button" @click="selected = opt.id; open = false"
                                                    class="w-full px-6 py-3 text-left hover:bg-red-50 transition-colors flex items-center justify-between"
                                                    :class="selected == opt.id ? 'bg-red-50 text-red-600' : 'text-gray-700'">
                                                <span class="font-bold" x-text="opt.name"></span>
                                                <svg x-show="selected == opt.id" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4 flex justify-between">
                                    Téléphone Pro
                                    <span class="text-[8px] font-medium opacity-50 lowercase tracking-normal">Optionnel</span>
                                </label>
                                <div class="flex gap-2">
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
                                        }" class="relative w-32">
                                        <input type="hidden" name="phone_prefix" :value="selected">
                                        <button type="button" @click="open = !open" 
                                                class="w-full px-4 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg" x-text="selectedOption.icon"></span>
                                                <span x-text="selected"></span>
                                            </div>
                                            <svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" x-cloak
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             class="absolute z-50 mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 max-h-64 overflow-y-auto">
                                            <template x-for="opt in options" :key="opt.prefix">
                                                <button type="button" @click="selected = opt.prefix; open = false"
                                                        class="w-full px-4 py-3 text-left hover:bg-red-50 transition-colors flex items-center gap-3"
                                                        :class="selected === opt.prefix ? 'bg-red-50 text-red-600' : 'text-gray-700'">
                                                    <span class="text-2xl" x-text="opt.icon"></span>
                                                    <div>
                                                        <div class="font-black text-sm" x-text="opt.prefix"></div>
                                                        <div class="text-[9px] font-bold text-gray-400 uppercase" x-text="opt.name"></div>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <input type="text" name="telephone_commercial" value="{{ old('telephone_commercial') }}" placeholder="00 00 00 00"
                                           class="flex-1 px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Adresse de l'Établissement / Local</label>
                            <textarea name="adresse_complete" required placeholder="Rue, Quartier, Ville..."
                                      class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 h-24 resize-none">{{ old('adresse_complete') }}</textarea>
                        </div>
                    </div>

                    <!-- Section: Vérification -->
                    <div class="pt-6 border-t border-gray-50 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">N° Registre de Commerce (RCCM)</label>
                            <input type="text" name="registre_commerce" value="{{ old('registre_commerce') }}" required placeholder="Ex: TG-LOM-..."
                                   class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4 flex justify-between">
                                    Pièce d'identité 
                                    <span class="text-[8px] font-medium opacity-50">Optionnel</span>
                                </label>
                                <div class="relative group">
                                    <input type="file" name="document_identite" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="w-full px-6 py-4 bg-gray-100 border-2 border-dashed border-gray-200 rounded-2xl flex items-center gap-3 transition-colors group-hover:border-red-200">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate">Choisir un fichier</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4 flex justify-between">
                                    Justificatif domicile
                                    <span class="text-[8px] font-medium opacity-50">Optionnel</span>
                                </label>
                                <div class="relative group">
                                    <input type="file" name="justificatif_domicile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="w-full px-6 py-4 bg-gray-100 border-2 border-dashed border-gray-200 rounded-2xl flex items-center gap-3 transition-colors group-hover:border-red-200">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate">Choisir un fichier</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" required class="sr-only peer">
                                <div class="w-5 h-5 bg-gray-100 rounded-md border-2 border-gray-200 peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-gray-900 transition-colors">J'accepte les conditions d'utilisation vendeur</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-3xl font-black text-xs uppercase tracking-[0.3em] hover:shadow-2xl hover:shadow-red-200 transition-all transform hover:-translate-y-1 active:scale-95">
                        Soumettre ma Candidature
                    </button>
                    
                    <p class="text-[9px] text-center text-gray-300 font-bold uppercase tracking-widest leading-relaxed">
                        Votre demande sera traitée sous 48h par nos administrateurs.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
