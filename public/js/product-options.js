// Product Options Modal Manager
function productOptionsManager() {
    return {
        mobileFiltersOpen: false,
        modalOpen: false,
        selectedPlat: null,
        selectedOptions: {}, // Format: { groupId: { variantId: quantity } }
        editingCartKey: null,
        currentImageIndex: 0,
        allImages: [], // {type: 'image'|'video', url: '...'}

        openModal(plat, cartKey = null, initialSelections = null) {
            this.selectedPlat = plat;
            this.editingCartKey = cartKey;
            this.selectedOptions = {};

            // Initialiser les groupes
            if (plat.groupes_variantes) {
                plat.groupes_variantes.forEach(g => {
                    this.selectedOptions[g.id_groupe] = {};
                });
            }

            // Appliquer les sélections initiales si présentes
            if (initialSelections) {
                Object.keys(initialSelections).forEach(groupId => {
                    const selections = initialSelections[groupId];
                    if (typeof selections === 'object' && !Array.isArray(selections)) {
                        this.selectedOptions[groupId] = selections;
                    } else if (Array.isArray(selections)) {
                        selections.forEach(id => {
                            this.selectedOptions[groupId][id] = 1;
                        });
                    } else if (selections) {
                        this.selectedOptions[groupId][selections] = 1;
                    }
                });
            }

            this.modalOpen = true;
            this.currentImageIndex = 0;
            this.allImages = [];

            // Image principale
            if (plat.image_principale) {
                this.allImages.push({ 
                    type: 'image', 
                    url: plat.image_principale.startsWith('http') ? plat.image_principale : `/storage/${plat.image_principale}` 
                });
            }

            // Galerie (medias) - Only if vendor plan allows it
            const canShowGallery = plat.vendeur && plat.vendeur.plan && plat.vendeur.plan.has_gallery;
            
            if (canShowGallery && plat.medias && plat.medias.length > 0) {
                plat.medias.forEach(m => {
                    this.allImages.push({
                        type: m.type,
                        url: m.type === 'video' ? m.chemin : (m.chemin.startsWith('http') ? m.chemin : `/storage/${m.chemin}`)
                    });
                });
            }

            // Fallback si rien
            if (this.allImages.length === 0) {
                this.allImages.push({ type: 'image', url: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&fit=crop' });
            }

            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.modalOpen = false;
            this.selectedPlat = null;
            this.selectedOptions = {};
            this.editingCartKey = null;
            document.body.style.overflow = 'auto';
            this.currentImageIndex = 0;
            this.allImages = [];
        },

        nextImage() {
            if (this.allImages.length <= 1) return;
            this.currentImageIndex = (this.currentImageIndex + 1) % this.allImages.length;
        },

        prevImage() {
            if (this.allImages.length <= 1) return;
            this.currentImageIndex = (this.currentImageIndex - 1 + this.allImages.length) % this.allImages.length;
        },

        toggleOption(group, option) {
            const variantId = option.id_variante;
            const groupId = group.id_groupe;

            if (this.selectedOptions[groupId][variantId]) {
                delete this.selectedOptions[groupId][variantId];
            } else {
                const count = Object.keys(this.selectedOptions[groupId]).length;
                if (count < (group.max_choix || 99)) {
                    this.selectedOptions[groupId][variantId] = 1;
                }
            }
        },

        selectSingleOption(group, option) {
            this.selectedOptions[group.id_groupe] = { [option.id_variante]: 1 };
        },

        isSelected(group, option) {
            return !!this.selectedOptions[group.id_groupe][option.id_variante];
        },

        getOptionQuantity(group, option) {
            return this.selectedOptions[group.id_groupe][option.id_variante] || 0;
        },

        incrementOption(group, option) {
            const variantId = option.id_variante;
            const groupId = group.id_groupe;
            if (this.selectedOptions[groupId][variantId]) {
                this.selectedOptions[groupId][variantId]++;
            } else {
                this.toggleOption(group, option);
            }
        },

        decrementOption(group, option) {
            const variantId = option.id_variante;
            const groupId = group.id_groupe;
            if (this.selectedOptions[groupId][variantId] > 1) {
                this.selectedOptions[groupId][variantId]--;
            } else {
                delete this.selectedOptions[groupId][variantId];
            }
        },

        calculateTotal() {
            if (!this.selectedPlat) return 0;
            let total = parseFloat(this.selectedPlat.prix_promotion || this.selectedPlat.prix);

            if (this.selectedPlat.groupes_variantes) {
                this.selectedPlat.groupes_variantes.forEach(g => {
                    const groupSelections = this.selectedOptions[g.id_groupe];
                    Object.keys(groupSelections).forEach(variantId => {
                        const variant = g.variantes.find(v => v.id_variante == variantId);
                        if (variant) {
                            total += parseFloat(variant.prix_supplement) * groupSelections[variantId];
                        }
                    });
                });
            }
            return total;
        },

        isValidSelection() {
            if (!this.selectedPlat || !this.selectedPlat.groupes_variantes) return true;

            return this.selectedPlat.groupes_variantes.every(g => {
                const selectionCount = Object.keys(this.selectedOptions[g.id_groupe] || {}).length;
                if (g.obligatoire) {
                    return selectionCount >= (g.min_choix || 1);
                }
                return true;
            });
        },

        confirmAddToCart() {
            const payload = {
                options: this.selectedOptions,
                replace_key: this.editingCartKey,
                _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            };

            fetch(`/panier/ajouter/${this.selectedPlat.id_plat}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.cart_count !== undefined) {
                            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                        }

                        this.closeModal();

                        if (this.editingCartKey) {
                            window.location.href = '/panier';
                        }

                        if (window.showToast) {
                            window.showToast(data.success, 'success');
                        }
                    } else if (data.error) {
                        if (data.can_clear && confirm(data.error)) {
                            // Logic to clear cart could be added here or via a separate call
                            fetch('/panier/vider', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(() => this.confirmAddToCart()); // Retry after clearing
                            return;
                        }

                        if (window.showToast) {
                            window.showToast(data.error, 'error');
                        } else {
                            alert(data.error);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    if (window.showToast) {
                        window.showToast('Une erreur est survenue. Veuillez réessayer.', 'error');
                    } else {
                        alert('Une erreur est survenue. Veuillez réessayer.');
                    }
                });
        },

        formatPrice(price) {
            return new Intl.NumberFormat('fr-FR').format(price);
        }
    }
}
