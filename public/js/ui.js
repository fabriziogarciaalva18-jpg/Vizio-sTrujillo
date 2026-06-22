/**
 * VIZIO'S Pastelería - UI JavaScript
 * Funcionalidades interactivas para el frontend
 * Minimalista · Desde 2015
 */

(function() {
    'use strict';

    // ========== DOM Elements ==========
    const domElements = {
        navbar: document.querySelector('.navbar-retro'),
        roleSelector: document.getElementById('roleSelector'),
        filterButtons: document.querySelectorAll('.filter-btn'),
        productItems: document.querySelectorAll('.product-item'),
        adminOnlyElements: document.querySelectorAll('.admin-only'),
        userOnlyElements: document.querySelectorAll('.user-only'),
        orderCards: document.querySelectorAll('.order-card'),
        adminTabs: document.querySelectorAll('.admin-tab'),
        priceUpdateForms: document.querySelectorAll('.price-update-form'),
        addToCartButtons: document.querySelectorAll('.add-to-cart'),
        quantityInputs: document.querySelectorAll('.quantity-input'),
        paymentRadios: document.querySelectorAll('input[name="payment"]'),
        avatarOptions: document.querySelectorAll('.avatar-option'),
        deleteButtons: document.querySelectorAll('.btn-delete'),
        modalTriggers: document.querySelectorAll('[data-modal-trigger]')
    };

    // ========== Helper Functions ==========
    
    /**
     * Formatear número como moneda (PEN)
     */
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount / 100);
    }

    /**
     * Mostrar notificación temporal
     */
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-retro fixed-top mx-auto mt-16`; // mt-16 = margin-top 4rem
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 320px;
            background: ${type === 'success' ? '#DCFCE7' : '#FEE2E2'};
            border: 1px solid ${type === 'success' ? '#86EFAC' : '#FCA5A5'};
            color: ${type === 'success' ? '#166534' : '#991B1B'};
            padding: 0.85rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: fadeIn 0.3s ease;
        `;
        notification.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i> ${message}`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Guardar en localStorage
     */
    function saveToLocalStorage(key, data) {
        localStorage.setItem(`vizio_${key}`, JSON.stringify(data));
    }

    /**
     * Obtener de localStorage
     */
    function getFromLocalStorage(key) {
        const data = localStorage.getItem(`vizio_${key}`);
        return data ? JSON.parse(data) : null;
    }

    /**
     * Carrito de compras
     */
    const cart = {
        items: getFromLocalStorage('cart') || [],
        
        addItem(product) {
            const existing = this.items.find(item => item.id === product.id);
            if (existing) {
                existing.quantity += product.quantity || 1;
            } else {
                this.items.push({
                    ...product,
                    quantity: product.quantity || 1,
                    addedAt: new Date().toISOString()
                });
            }
            this.save();
            showNotification(`${product.name} agregado al carrito`, 'success');
        },
        
        removeItem(productId) {
            this.items = this.items.filter(item => item.id !== productId);
            this.save();
            this.updateCartCount();
        },
        
        updateQuantity(productId, quantity) {
            const item = this.items.find(item => item.id === productId);
            if (item) {
                item.quantity = Math.max(1, quantity);
                this.save();
            }
        },
        
        getTotal() {
            return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
        },
        
        getCount() {
            return this.items.reduce((count, item) => count + item.quantity, 0);
        },
        
        save() {
            saveToLocalStorage('cart', this.items);
            this.updateCartCount();
        },
        
        updateCartCount() {
            const cartCountElements = document.querySelectorAll('.cart-count');
            const count = this.getCount();
            cartCountElements.forEach(el => {
                if (count > 0) {
                    el.textContent = count;
                    el.style.display = 'inline-flex';
                } else {
                    el.style.display = 'none';
                }
            });
        },
        
        clear() {
            this.items = [];
            this.save();
        }
    };

    // ========== Role Management (Demo) ==========
    function setRole(role) {
        if (role === 'admin') {
            domElements.adminOnlyElements.forEach(el => el.style.display = 'flex');
            domElements.userOnlyElements.forEach(el => el.style.display = 'none');
            document.querySelectorAll('.admin-visible').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.user-visible').forEach(el => el.style.display = 'none');
        } else {
            domElements.adminOnlyElements.forEach(el => el.style.display = 'none');
            domElements.userOnlyElements.forEach(el => el.style.display = 'flex');
            document.querySelectorAll('.admin-visible').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.user-visible').forEach(el => el.style.display = 'block');
        }
        
        // Guardar rol seleccionado
        sessionStorage.setItem('vizio_role', role);
    }

    // ========== Filters ==========
    function initFilters() {
        if (!domElements.filterButtons.length) return;
        
        domElements.filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Update active state
                domElements.filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter products
                domElements.productItems.forEach(item => {
                    if (filter === 'all' || item.dataset.category === filter) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeInUp 0.3s ease';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // ========== Admin Tabs ==========
    function initAdminTabs() {
        if (!domElements.adminTabs.length) return;
        
        domElements.adminTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                
                domElements.adminTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.order-row').forEach(row => {
                    const status = row.dataset.status;
                    if (tabId === 'all' || status === tabId) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    }

    // ========== Price Calculation (Product Customization) ==========
    function calculateProductPrice() {
        let basePrice = parseInt(document.getElementById('basePrice')?.value || 0);
        let addonsTotal = 0;
        
        document.querySelectorAll('.addon-option:checked, .config-option:checked').forEach(option => {
            addonsTotal += parseInt(option.dataset.price || 0);
        });
        
        const quantity = parseInt(document.getElementById('quantity')?.value || 1);
        const total = (basePrice + addonsTotal) * quantity;
        
        const priceDisplay = document.getElementById('totalPrice');
        if (priceDisplay) {
            priceDisplay.innerHTML = formatCurrency(total);
        }
        
        return total;
    }

    // ========== Payment Method Toggle ==========
    function initPaymentMethods() {
        if (!domElements.paymentRadios.length) return;
        
        domElements.paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const tarjetaFields = document.getElementById('tarjetaFields');
                const transferenciaFields = document.getElementById('transferenciaFields');
                const yapeFields = document.getElementById('yapeFields');
                const plinFields = document.getElementById('plinFields');
                
                // Hide all
                if (tarjetaFields) tarjetaFields.style.display = 'none';
                if (transferenciaFields) transferenciaFields.style.display = 'none';
                if (yapeFields) yapeFields.style.display = 'none';
                if (plinFields) plinFields.style.display = 'none';
                
                // Show selected
                if (this.id === 'tarjeta' && tarjetaFields) {
                    tarjetaFields.style.display = 'block';
                } else if (this.id === 'transferencia' && transferenciaFields) {
                    transferenciaFields.style.display = 'block';
                } else if (this.id === 'yape' && yapeFields) {
                    yapeFields.style.display = 'block';
                } else if (this.id === 'plin' && plinFields) {
                    plinFields.style.display = 'block';
                }
                
                // Update modal info
                const paymentName = this.closest('.payment-option')?.querySelector('label')?.innerText || 'YAPE';
                const paymentSpan = document.getElementById('selectedPaymentMethod');
                if (paymentSpan) paymentSpan.innerText = paymentName;
            });
        });
    }

    // ========== Avatar Selection ==========
    function initAvatarSelection() {
        if (!domElements.avatarOptions.length) return;
        
        domElements.avatarOptions.forEach(option => {
            option.addEventListener('click', function() {
                const avatar = this.textContent;
                const avatarDisplay = document.querySelector('.avatar-emoji');
                if (avatarDisplay) {
                    avatarDisplay.textContent = avatar;
                }
                saveToLocalStorage('avatar', avatar);
                showNotification('Avatar actualizado', 'success');
            });
        });
        
        // Load saved avatar
        const savedAvatar = getFromLocalStorage('avatar');
        if (savedAvatar) {
            const avatarDisplay = document.querySelector('.avatar-emoji');
            if (avatarDisplay) avatarDisplay.textContent = savedAvatar;
        }
    }

    // ========== Delete Confirmation ==========
    function initDeleteButtons() {
        if (!domElements.deleteButtons.length) return;
        
        domElements.deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const itemName = this.dataset.itemName || 'este elemento';
                if (confirm(`¿Eliminar ${itemName} permanentemente?`)) {
                    // Aquí iría la llamada AJAX para eliminar
                    showNotification(`${itemName} eliminado`, 'success');
                }
            });
        });
    }

    // ========== Add to Cart ==========
    function initAddToCart() {
        if (!domElements.addToCartButtons.length) return;
        
        domElements.addToCartButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productPrice = parseInt(this.dataset.productPrice || 0);
                
                // Get customizations
                const customizations = {};
                document.querySelectorAll('#customizeForm .config-field').forEach(field => {
                    customizations[field.name] = field.value;
                });
                
                cart.addItem({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    customizations: customizations
                });
            });
        });
    }

    // ========== Quantity Inputs ==========
    function initQuantityInputs() {
        if (!domElements.quantityInputs.length) return;
        
        domElements.quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const quantity = parseInt(this.value);
                cart.updateQuantity(productId, quantity);
                calculateProductPrice();
            });
            
            // Increment/Decrement buttons
            const container = this.closest('.quantity-container');
            if (container) {
                const decrementBtn = container.querySelector('.decrement');
                const incrementBtn = container.querySelector('.increment');
                
                if (decrementBtn) {
                    decrementBtn.addEventListener('click', () => {
                        this.value = Math.max(1, parseInt(this.value) - 1);
                        this.dispatchEvent(new Event('change'));
                    });
                }
                if (incrementBtn) {
                    incrementBtn.addEventListener('click', () => {
                        this.value = parseInt(this.value) + 1;
                        this.dispatchEvent(new Event('change'));
                    });
                }
            }
        });
    }

    // ========== Navbar Scroll Effect ==========
    function initNavbarScroll() {
        if (!domElements.navbar) return;
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                domElements.navbar.classList.add('scrolled');
            } else {
                domElements.navbar.classList.remove('scrolled');
            }
        });
    }

    // ========== Smooth Scroll ==========
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ========== Form Validation ==========
    function initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.classList.add('was-validated');
            });
        });
    }

    // ========== Load More / Pagination ==========
    function initLoadMore() {
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (!loadMoreBtn) return;
        
        let currentPage = 1;
        const itemsPerPage = 6;
        const items = document.querySelectorAll('.load-item');
        const totalPages = Math.ceil(items.length / itemsPerPage);
        
        function showPage(page) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            
            items.forEach((item, index) => {
                item.style.display = (index >= start && index < end) ? 'block' : 'none';
            });
            
            if (loadMoreBtn) {
                loadMoreBtn.style.display = page >= totalPages ? 'none' : 'inline-flex';
            }
        }
        
        showPage(1);
        
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                currentPage++;
                showPage(currentPage);
            });
        }
    }

    // ========== Product Customization Modal ==========
    function setProductName(name, price) {
        const nameSpan = document.getElementById('personalizeProductName');
        const priceSpan = document.getElementById('personalizeProductPrice');
        
        if (nameSpan) nameSpan.innerText = name;
        if (priceSpan) priceSpan.innerHTML = formatCurrency(price);
    }
    
    function setCartProduct(name, price) {
        const cartProductSpan = document.getElementById('cartProductName');
        if (cartProductSpan) cartProductSpan.innerText = name;
    }
    
    function setEditProduct(name, price) {
        const editNameSpan = document.getElementById('editProductName');
        const currentPriceInput = document.getElementById('currentProductPrice');
        
        if (editNameSpan) editNameSpan.innerText = name;
        if (currentPriceInput) currentPriceInput.value = price;
    }

    // ========== Logout ==========
    function logout() {
        if (confirm('¿Cerrar sesión?')) {
            window.location.href = '/logout';
        }
    }

    // ========== Initialize All ==========
    function init() {
        // Role selector
        if (domElements.roleSelector) {
            const savedRole = sessionStorage.getItem('vizio_role') || 'user';
            domElements.roleSelector.value = savedRole;
            setRole(savedRole);
            
            domElements.roleSelector.addEventListener('change', function() {
                setRole(this.value);
            });
        }
        
        initFilters();
        initAdminTabs();
        initPaymentMethods();
        initAvatarSelection();
        initDeleteButtons();
        initAddToCart();
        initQuantityInputs();
        initNavbarScroll();
        initSmoothScroll();
        initFormValidation();
        initLoadMore();
        
        // Expose functions globally
        window.setProductName = setProductName;
        window.setCartProduct = setCartProduct;
        window.setEditProduct = setEditProduct;
        window.calculateProductPrice = calculateProductPrice;
        window.logout = logout;
        window.formatCurrency = formatCurrency;
        window.showNotification = showNotification;
        window.cart = cart;
        
        // Initialize cart count
        cart.updateCartCount();
        
        console.log('VIZIO\'S UI initialized');
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();