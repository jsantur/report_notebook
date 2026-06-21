document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const sections = document.querySelectorAll('section');
    const navItems = document.querySelectorAll('.nav-item, .nav-subitem');
    const mainContent = document.querySelector('.main-content');
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebar = document.querySelector('.sidebar');
    const themeToggle = document.getElementById('themeToggle');

    // Funcionalidad de Búsqueda Interna
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();

        sections.forEach(section => {
            const text = section.innerText.toLowerCase();
            if (text.includes(query)) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    });

    // Resaltar sección activa en el menú lateral al hacer scroll
    mainContent.addEventListener('scroll', () => {
        let currentSection = '';
        const scrollPosition = mainContent.scrollTop + 100; // Offset para mejor detección

        sections.forEach(section => {
            if (section.style.display !== 'none') {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    currentSection = section.getAttribute('id');
                }
            }
        });

        if (currentSection) {
            navItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === `#${currentSection}`) {
                    item.classList.add('active');
                    // Si es un subitem, resaltar también el item padre
                    if (item.classList.contains('nav-subitem')) {
                        const parentHref = item.getAttribute('data-parent');
                        if (parentHref) {
                            const parentItem = document.querySelector(`.nav-item[href="${parentHref}"]`);
                            if (parentItem) parentItem.classList.add('active');
                        }
                    }
                }
            });
        }
    });

    // Smooth scroll para los enlaces del menú y efecto Flash amarillo
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            const targetId = item.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                e.preventDefault();
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    mainContent.scrollTo({
                        top: targetSection.offsetTop - 40,
                        behavior: 'smooth'
                    });
                    
                    // Aplicar animación de flash amarillo
                    targetSection.classList.remove('flash-highlight');
                    void targetSection.offsetWidth; // Forzar reflow para reiniciar animación
                    targetSection.classList.add('flash-highlight');
                    
                    setTimeout(() => {
                        targetSection.classList.remove('flash-highlight');
                    }, 1500);

                    // Cerrar sidebar en móviles
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('open');
                    }
                }
            }
        });
    });

    // Toggle menú en móviles
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Modo Oscuro
    if (themeToggle) {
        const body = document.body;
        const savedTheme = localStorage.getItem('manual_theme');
        
        // Iconos SVG
        const iconMoon = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
        const iconSun = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';

        // Cargar preferencia guardada
        if (savedTheme === 'dark') {
            body.classList.add('dark-theme');
            themeToggle.innerHTML = iconSun;
        } else {
            themeToggle.innerHTML = iconMoon;
        }

        // Alternar tema al hacer click
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            
            if (body.classList.contains('dark-theme')) {
                localStorage.setItem('manual_theme', 'dark');
                themeToggle.innerHTML = iconSun;
            } else {
                localStorage.setItem('manual_theme', 'light');
                themeToggle.innerHTML = iconMoon;
            }
        });
    }

    // --- Efecto Lightbox (Lupa) para Imágenes ---
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("img01");
    const captionText = document.getElementById("modalCaption");
    const spanClose = document.getElementsByClassName("close-modal")[0];
    const images = document.querySelectorAll('.main-content img');

    // Abrir modal al hacer clic en cualquier imagen
    images.forEach(img => {
        img.addEventListener('click', function() {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        });
    });

    // Función para cerrar modal
    const closeModal = () => {
        modal.style.display = "none";
    };

    // Cerrar con la X
    if (spanClose) {
        spanClose.addEventListener('click', closeModal);
    }

    // Cerrar al hacer clic fuera de la imagen (en el fondo oscuro)
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target !== modalImg) {
                closeModal();
            }
        });
    }

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape" && modal && modal.style.display === "block") {
            closeModal();
        }
    });

});
