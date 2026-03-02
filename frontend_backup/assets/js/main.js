// Mobile Menu Toggle
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const menuContainer = document.querySelector('.menu-container');

if (mobileMenuToggle && menuContainer) {
    mobileMenuToggle.addEventListener('click', () => {
        menuContainer.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
    });
}

// Role Toggle
const roleBtns = document.querySelectorAll('.role-btn');
roleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        roleBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        const searchInput = document.querySelector('.search-input');
        if (btn.dataset.role === 'client') {
            searchInput.placeholder = "Cari jasa: Web Design, Video Editing...";
        } else {
            searchInput.placeholder = "Cari projek: Redesign Landing Page...";
        }
    });
});

// Accordion
const accordionItems = document.querySelectorAll('.accordion-item');
accordionItems.forEach(item => {
    const header = item.querySelector('.accordion-header');
    if (header) {
        header.addEventListener('click', () => {
            const wasActive = item.classList.contains('active');
            accordionItems.forEach(i => i.classList.remove('active'));
            if (!wasActive) {
                item.classList.add('active');
            }
        });
    }
});

// How It Works Toggle
const switchBtns = document.querySelectorAll('.switch-btn');
const flowContents = document.querySelectorAll('.flow-content');

switchBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const target = btn.dataset.target;
        
        switchBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        flowContents.forEach(content => {
            content.classList.remove('active');
            if (content.id === target) {
                content.classList.add('active');
            }
        });
    });
});

// FAQ Toggle
const faqItems = document.querySelectorAll('.faq-item');
faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    if (question) {
        question.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    }
});

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const target = document.querySelector(targetId);
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Parallax Effect
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.gradient-orb');
    parallaxElements.forEach((el, index) => {
        const speed = 0.5 + (index * 0.1);
        el.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// Intersection Observer
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.stat-card, .flow-step, .testimonial-card, .feature-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
    observer.observe(el);
});
