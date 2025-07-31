/**
 * Laporin Lingkungan - Main JavaScript File
 * Handles interactive features and animations
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all components
    initAnimations();
    initFormValidation();
    initTooltips();
    initAlerts();
    initTableResponsive();
    initMobileMenu();
    
    console.log('Laporin Lingkungan - App initialized successfully!');
});

/**
 * Initialize smooth animations and transitions
 */
function initAnimations() {
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card, .form-card, .content-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Add hover effects to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'Field ini wajib diisi';
                    
                    if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                        field.parentNode.appendChild(errorDiv);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Mohon lengkapi semua field yang wajib diisi', 'danger');
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize alert system
 */
function initAlerts() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('alert-dismissible')) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }, 5000);
    });
}

/**
 * Show custom alert
 */
function showAlert(message, type = 'info') {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertContainer.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertContainer.innerHTML = `
        <i class="fas fa-${getAlertIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertContainer);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertContainer.parentNode) {
            alertContainer.remove();
        }
    }, 5000);
}

/**
 * Get alert icon based on type
 */
function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Initialize responsive table
 */
function initTableResponsive() {
    const tables = document.querySelectorAll('.table-responsive');
    
    tables.forEach(table => {
        const tableElement = table.querySelector('table');
        if (tableElement) {
            // Add horizontal scroll indicator
            const indicator = document.createElement('div');
            indicator.className = 'scroll-indicator text-center text-muted mt-2';
            indicator.innerHTML = '<i class="fas fa-arrows-alt-h me-2"></i>Geser untuk melihat lebih banyak data';
            indicator.style.display = 'none';
            
            table.parentNode.appendChild(indicator);
            
            // Show indicator if table is scrollable
            const checkScroll = () => {
                if (table.scrollWidth > table.clientWidth) {
                    indicator.style.display = 'block';
                } else {
                    indicator.style.display = 'none';
                }
            };
            
            checkScroll();
            window.addEventListener('resize', checkScroll);
        }
    });
}

/**
 * Initialize mobile menu
 */
function initMobileMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        // Close mobile menu when clicking on a link
        const navLinks = navbarCollapse.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    navbarCollapse.classList.remove('show');
                }
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                navbarCollapse.classList.remove('show');
            }
        });
    }
}

/**
 * Confirm delete action
 */
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Debounce function for performance
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function for performance
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Load more data (for pagination)
 */
function loadMoreData(url, container, page = 1) {
    fetch(`${url}?page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                container.insertAdjacentHTML('beforeend', data.html);
            }
            if (data.hasMore) {
                // Update load more button
                const loadMoreBtn = document.querySelector('.load-more-btn');
                if (loadMoreBtn) {
                    loadMoreBtn.dataset.page = page + 1;
                }
            } else {
                // Hide load more button
                const loadMoreBtn = document.querySelector('.load-more-btn');
                if (loadMoreBtn) {
                    loadMoreBtn.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error loading more data:', error);
            showAlert('Gagal memuat data tambahan', 'danger');
        });
}

/**
 * Search functionality
 */
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        const debouncedSearch = debounce(function(query) {
            // Implement search logic here
            console.log('Searching for:', query);
        }, 300);
        
        searchInput.addEventListener('input', function() {
            debouncedSearch(this.value);
        });
    }
}

/**
 * Export data to CSV
 */
function exportToCSV(data, filename = 'export.csv') {
    const csvContent = "data:text/csv;charset=utf-8," + data;
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Print page
 */
function printPage() {
    window.print();
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Berhasil disalin ke clipboard', 'success');
    }).catch(() => {
        showAlert('Gagal menyalin ke clipboard', 'danger');
    });
}

// Export functions for global use
window.LaporinApp = {
    showAlert,
    confirmDelete,
    formatCurrency,
    formatDate,
    loadMoreData,
    exportToCSV,
    printPage,
    copyToClipboard
}; 