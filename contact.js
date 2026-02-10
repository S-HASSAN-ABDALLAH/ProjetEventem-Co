// ==========================================
// Fichier JavaScript pour le formulaire de contact
// contact.js - Niveau DWWM Bac+2
// ==========================================

// Attendre que la page soit complètement chargée
document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 0️⃣ AFFICHER LES MESSAGES DE SUCCÈS/ERREUR
    // عرض رسائل النجاح/الخطأ
    // ==========================================

    // Récupérer les paramètres de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    const messages = urlParams.get('messages');

    // Trouver le formulaire pour insérer le message avant
    const form = document.querySelector('form');

    if (success === '1') {
        // ✅ Message de succès
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.innerHTML = `
            <strong>✅ Succès !</strong><br>
            Votre message a été enregistré avec succès !<br>
            Un email de confirmation vous a été envoyé.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentNode.insertBefore(successDiv, form);

        // Faire défiler jusqu'au message
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else if (error === 'db') {
        // ❌ Erreur de base de données
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            <strong>❌ Erreur de sauvegarde</strong><br>
            Une erreur s'est produite lors de l'enregistrement. Veuillez réessayer.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentNode.insertBefore(errorDiv, form);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else if (error === 'email') {
        // ⚠️ Erreur d'envoi d'email
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning alert-dismissible fade show';
        warningDiv.innerHTML = `
            <strong>⚠️ Message enregistré</strong><br>
            Votre message a été enregistré, mais nous n'avons pas pu envoyer l'email de confirmation.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentNode.insertBefore(warningDiv, form);
        warningDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else if (error === 'validation' && messages) {
        // ❌ Erreurs de validation
        const errorMessages = decodeURIComponent(messages).split('|');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        let errorHTML = '<strong>❌ Erreurs de validation :</strong><ul>';
        errorMessages.forEach(function(msg) {
            errorHTML += '<li>' + msg + '</li>';
        });
        errorHTML += '</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        errorDiv.innerHTML = errorHTML;
        form.parentNode.insertBefore(errorDiv, form);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // ==========================================
    // 1️⃣ VALIDATION EN TEMPS RÉEL
    // التحقق الفوري من البيانات
    // ==========================================

    const nomField = document.getElementById('nom');
    const emailField = document.getElementById('email');
    const messageField = document.getElementById('message');

    // Validation du nom
    nomField.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Validation de l'email
    emailField.addEventListener('blur', function() {
        const emailValue = this.value.trim();
        // Regex simple pour vérifier le format email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailValue === '' || !emailRegex.test(emailValue)) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // Validation du message
    messageField.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    // ==========================================
    // 2️⃣ COMPTEUR DE CARACTÈRES
    // عداد الأحرف المتبقية
    // ==========================================

    const maxLength = 500;

    // Créer l'élément du compteur
    const counterDiv = document.createElement('div');
    counterDiv.id = 'char-counter';
    counterDiv.className = 'form-text text-muted mt-1';
    counterDiv.textContent = maxLength + ' caractères restants';

    // Ajouter le compteur après le champ message
    messageField.parentNode.appendChild(counterDiv);

    // Mettre à jour le compteur à chaque saisie
    messageField.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        counterDiv.textContent = remaining + ' caractères restants';

        // Changer la couleur si proche de la limite
        if (remaining < 50) {
            counterDiv.style.color = 'red';
        } else if (remaining < 100) {
            counterDiv.style.color = 'orange';
        } else {
            counterDiv.style.color = '';
        }
    });

    // ==========================================
    // 3️⃣ DÉSACTIVATION DU BOUTON PENDANT L'ENVOI
    // منع النقر المزدوج على زر الإرسال
    // ==========================================

    // Réutiliser la variable 'form' déjà déclarée plus haut
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        // Vérifier si tous les champs requis sont valides
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

        // Si invalide, empêcher l'envoi
        if (!isValid) {
            e.preventDefault();
            alert('⚠️ Veuillez remplir tous les champs obligatoires correctement.');
            return false;
        }

        // Désactiver le bouton pour empêcher double-clic
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ Envoi en cours...';
        submitBtn.classList.add('btn-secondary');
        submitBtn.classList.remove('btn-primary');
    });

});

