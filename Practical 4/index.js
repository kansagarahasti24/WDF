const faqToggle = document.querySelector('.faq-toggle');
const faqPanel = document.getElementById('faq-panel');
const faqQuestions = document.querySelectorAll('.faq-question');

if (faqToggle && faqPanel) {
    faqToggle.addEventListener('click', () => {
        const isExpanded = faqToggle.getAttribute('aria-expanded') === 'true';
        faqToggle.setAttribute('aria-expanded', String(!isExpanded));
        faqPanel.classList.toggle('open', !isExpanded);
        faqPanel.setAttribute('aria-hidden', String(isExpanded));
    });

    document.addEventListener('click', (event) => {
        if (!faqPanel.contains(event.target) && !faqToggle.contains(event.target)) {
            faqToggle.setAttribute('aria-expanded', 'false');
            faqPanel.classList.remove('open');
            faqPanel.setAttribute('aria-hidden', 'true');
        }
    });
}

faqQuestions.forEach((question) => {
    question.addEventListener('click', () => {
        const answer = question.nextElementSibling;
        const isOpen = question.classList.contains('active');

        faqQuestions.forEach((item) => {
            item.classList.remove('active');
            const sibling = item.nextElementSibling;
            if (sibling) {
                sibling.classList.remove('open');
            }
        });

        if (!isOpen) {
            question.classList.add('active');
            if (answer) {
                answer.classList.add('open');
            }
        }
    });
});
