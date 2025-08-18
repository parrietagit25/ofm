// JavaScript para la funcionalidad de Preguntas Frecuentes

document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de pestañas
    const tabButtons = document.querySelectorAll('.tab-btn');
    const faqSections = document.querySelectorAll('.faq-section');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remover clase active de todos los botones
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            
            // Ocultar todas las secciones
            faqSections.forEach(section => section.classList.remove('active'));
            // Mostrar la sección correspondiente
            document.getElementById(targetTab + '-faq').classList.add('active');
        });
    });

    // Funcionalidad de FAQ items
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Cerrar todos los items
            faqItems.forEach(faqItem => {
                faqItem.classList.remove('active');
            });
            
            // Si el item clickeado no estaba activo, abrirlo
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // Botón Expandir Todas
    const expandAllBtn = document.getElementById('expandAll');
    if (expandAllBtn) {
        expandAllBtn.addEventListener('click', function() {
            faqItems.forEach(item => {
                item.classList.add('active');
            });
        });
    }

    // Botón Contraer Todas
    const collapseAllBtn = document.getElementById('collapseAll');
    if (collapseAllBtn) {
        collapseAllBtn.addEventListener('click', function() {
            faqItems.forEach(item => {
                item.classList.remove('active');
            });
        });
    }
});
