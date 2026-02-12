document.querySelectorAll('.carte-impact-lien').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();

        const carte = link.closest('.carte-impact');
        const texte = carte.querySelector('.carte-impact-texte');

        texte.classList.toggle('expanded');

        link.textContent = texte.classList.contains('expanded')
            ? 'Réduire...'
            : 'Lire la suite...';
    });
});


document.querySelectorAll('.faq-question').forEach(item => {
    item.addEventListener('click', () => {
        const parent = item.parentElement;

        if (parent.classList.contains('active')) {
            parent.classList.remove('active');
        } else {
            document.querySelectorAll('.faq-item').forEach(child => child.classList.remove('active'));
            parent.classList.add('active');
        }
    });
});