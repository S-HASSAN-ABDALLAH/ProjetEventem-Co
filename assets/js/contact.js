// Formulaire de contact - validation et messages
// Événement & Co

document.addEventListener('DOMContentLoaded', function() {

    // Afficher les messages selon les paramètres URL
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    const messages = urlParams.get('messages');

    const form = document.querySelector('form');

    if (success === '1') {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.innerHTML = `
            <strong>Succès !</strong><br>
            Votre message a été enregistré avec succès !<br>
            Un email de confirmation vous a été envoyé.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentNode.insertBefore(successDiv, form);
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } else if (error === 'db') {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            <strong>Erreur</strong><br>
            Une erreur s'est produite lors de l'enregistrement. Veuillez réessayer.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentNode.insertBefore(errorDiv, form);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } else if (error === 'email') {
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning alert-dismissible fade show';
        warningDiv.innerHTML = `
            <strong>Message enregistré</strong><br>
            Votre message a été enregistré, mais nous n'avons pas pu envoyer l'email de confirmation.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentNode.insertBefore(warningDiv, form);
        warningDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

    } else if (error === 'validation' && messages) {
        const errorMessages = decodeURIComponent(messages).split('|');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        let errorHTML = '<strong>Erreurs de validation :</strong><ul>';
        errorMessages.forEach(function(msg) {
            errorHTML += '<li>' + msg + '</li>';
        });
        errorHTML += '</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        errorDiv.innerHTML = errorHTML;
        form.parentNode.insertBefore(errorDiv, form);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Validation en temps réel
    const nomField = document.getElementById('nom');
    const emailField = document.getElementById('email');
    const messageField = document.getElementById('message');

    nomField.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    emailField.addEventListener('blur', function() {
        const emailValue = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailValue === '' || !emailRegex.test(emailValue)) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    messageField.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Compteur de caractères
    const maxLength = 500;

    const counterDiv = document.createElement('div');
    counterDiv.id = 'char-counter';
    counterDiv.className = 'form-text text-muted mt-1';
    counterDiv.textContent = maxLength + ' caractères restants';
    messageField.parentNode.appendChild(counterDiv);

    messageField.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        counterDiv.textContent = remaining + ' caractères restants';

        if (remaining < 50) {
            counterDiv.style.color = 'red';
        } else if (remaining < 100) {
            counterDiv.style.color = 'orange';
        } else {
            counterDiv.style.color = '';
        }
    });

    // Empêcher le double-clic sur le bouton d'envoi
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        let isValid = true;

        if (nomField.value.trim() === '') {
            nomField.classList.add('is-invalid');
            isValid = false;
        }

        if (emailField.value.trim() === '' || !emailField.value.includes('@')) {
            emailField.classList.add('is-invalid');
            isValid = false;
        }

        if (messageField.value.trim() === '') {
            messageField.classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires correctement.');
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi en cours...';
    });

});