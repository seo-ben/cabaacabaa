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
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Type de Boutique</label>
                                <select name="id_category_vendeur" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900 appearance-none">
                                    <option value="" disabled selected>Choisir un type...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id_category_vendeur }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-4">Téléphone Pro</label>
                                <input type="text" name="telephone_commercial" value="{{ old('telephone_commercial') }}" required placeholder="+228..."
                                       class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm font-bold text-gray-900">
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
