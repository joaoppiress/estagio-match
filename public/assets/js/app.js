document.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.toast.show');
    if (toast) {
        setTimeout(() => toast.remove(), 4200);
    }

    const tabs = document.querySelectorAll('[data-auth-tab]');
    const login = document.querySelector('[data-auth-panel="login"]');
    const register = document.querySelector('[data-auth-panel="cadastro"]');

    const showPanel = (panel) => {
        if (!login || !register) return;
        login.hidden = panel !== 'login';
        register.hidden = panel !== 'cadastro';
        tabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.authTab === panel));
        if (panel === 'cadastro') {
            history.replaceState(null, '', '#cadastro');
        }
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => showPanel(tab.dataset.authTab));
    });

    if (window.location.hash === '#cadastro') {
        showPanel('cadastro');
    }

    const accountType = document.querySelector('[data-account-type]');
    const companyFields = document.querySelectorAll('[data-company-field]');
    const studentFields = document.querySelectorAll('[data-student-field]');
    const toggleAccountFields = () => {
        const isCompany = accountType && accountType.value === 'empresa';
        companyFields.forEach((el) => { el.hidden = !isCompany; });
        studentFields.forEach((el) => { el.hidden = isCompany; });
    };

    if (accountType) {
        accountType.addEventListener('change', toggleAccountFields);
        toggleAccountFields();
    }

    document.querySelectorAll('[data-card-link]').forEach((card) => {
        card.addEventListener('click', (event) => {
            if (event.target.closest('a,button,form,input,select,textarea,label')) return;
            window.location.href = card.dataset.cardLink;
        });
    });
});

