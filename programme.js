// ========================================
// FICHIER : programme.js
// RÔLE : Validation côté client et gestion des messages
// PROJET : Événement & Co - DWWM Bac+2
// ========================================

document.addEventListener('DOMContentLoaded', function() {

    // ========================================
    // PARTIE 0 : Pré-remplissage du formulaire selon la carte cliquée
    // ========================================

    const rendezVousModal = document.getElementById('rendezVousModal');
    const evenementSelect = document.getElementById('evenement_rdv');

    // Écouter l'événement d'ouverture du modal
    rendezVousModal.addEventListener('show.bs.modal', function(event) {
        // Le bouton qui a déclenché le modal
        const button = event.relatedTarget;

        if (button) {
            // Récupérer les données du bouton
            const eventId = button.getAttribute('data-event-id');
            const eventName = button.getAttribute('data-event-name');

            // Pré-sélectionner l'événement si les données existent
            if (eventId && evenementSelect) {
                evenementSelect.value = eventId;
            }
        }
    });

    // Réinitialiser le formulaire quand le modal se ferme
    rendezVousModal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('formRendezVous');
        if (form) {
            form.reset();
        }
    });

    // ========================================
    // PARTIE 1 : Affichage des messages de succès/erreur
    // ========================================

    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    const messages = urlParams.get('messages');

    const form = document.getElementById('formRendezVous');

    // Message de succès
    if (success === '1') {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.setAttribute('role', 'alert');
        successDiv.style.marginTop = '100px'; // Espace pour le header fixe
        successDiv.style.marginLeft = 'auto';
        successDiv.style.marginRight = 'auto';
        successDiv.style.marginBottom = '20px';
        successDiv.style.maxWidth = '900px';
        successDiv.innerHTML = `
            <div class="container">
                <strong>✅ Succès !</strong> Votre demande de réservation a été envoyée avec succès.
                <br>Vous recevrez un email de confirmation très prochainement.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        `;

        // Insérer le message en haut de la page principale (après le header)
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.insertBefore(successDiv, mainContent.firstChild);
        } else {
            // Fallback : insérer après le header
            const header = document.querySelector('header');
            if (header && header.nextSibling) {
                header.parentNode.insertBefore(successDiv, header.nextSibling);
            } else {
                document.body.insertBefore(successDiv, document.body.firstChild);
            }
        }

        // Faire défiler vers le message en prenant en compte le header fixe
        setTimeout(() => {
            const headerHeight = document.querySelector('header')?.offsetHeight || 80;
            const elementPosition = successDiv.getBoundingClientRect().top + window.pageYOffset;
            const offsetPosition = elementPosition - headerHeight - 20; // 20px de marge

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }, 100);

        // Nettoyer l'URL après 5 secondes
        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 5000);
    }

    // Messages d'erreur
    if (error) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.setAttribute('role', 'alert');
        errorDiv.style.marginTop = '100px'; // Espace pour le header fixe
        errorDiv.style.marginLeft = 'auto';
        errorDiv.style.marginRight = 'auto';
        errorDiv.style.marginBottom = '20px';
        errorDiv.style.maxWidth = '900px';

        let errorMessage = '';

        switch (error) {
            case 'db':
                errorMessage = '<strong>❌ Erreur de base de données.</strong> Un problème technique est survenu. Veuillez réessayer plus tard.';
                break;

            case 'email':
                errorMessage = '<strong>⚠️ Attention !</strong> Votre réservation a été enregistrée mais l\'email de confirmation n\'a pas pu être envoyé. Nous vous contacterons directement.';
                break;

            case 'validation':
                if (messages) {
                    const errorList = messages.split('|');
                    errorMessage = '<strong>⚠️ Erreurs de validation :</strong><ul class="mb-0 mt-2">';
                    errorList.forEach(msg => {
                        errorMessage += `<li>${msg}</li>`;
                    });
                    errorMessage += '</ul>';
                } else {
                    errorMessage = '<strong>⚠️ Erreur de validation.</strong> Veuillez vérifier vos informations.';
                }
                break;

            default:
                errorMessage = '<strong>❌ Erreur inconnue.</strong> Veuillez réessayer.';
        }

        errorDiv.innerHTML = `
            <div class="container">
                ${errorMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        `;

        // Insérer le message en haut de la page principale (après le header)
        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.insertBefore(errorDiv, mainContent.firstChild);
        } else {
            // Fallback : insérer après le header
            const header = document.querySelector('header');
            if (header && header.nextSibling) {
                header.parentNode.insertBefore(errorDiv, header.nextSibling);
            } else {
                document.body.insertBefore(errorDiv, document.body.firstChild);
            }
        }

        // Faire défiler vers le message en prenant en compte le header fixe
        setTimeout(() => {
            const headerHeight = document.querySelector('header')?.offsetHeight || 80;
            const elementPosition = errorDiv.getBoundingClientRect().top + window.pageYOffset;
            const offsetPosition = elementPosition - headerHeight - 20; // 20px de marge

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }, 100);

        // Nettoyer l'URL après 7 secondes (plus long pour les erreurs)
        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 7000);
    }

    // ========================================
    // PARTIE 2 : Validation en temps réel
    // ========================================

    if (form) {
        // Champs du formulaire
        const nomField = document.getElementById('nom_rdv');
        const emailField = document.getElementById('email_rdv');
        const evenementField = document.getElementById('evenement_rdv');
        const dateField = document.getElementById('date_rdv');
        const villeField = document.getElementById('ville_rdv');
        const invitesField = document.getElementById('invites_rdv');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Validation du nom (au blur)
        if (nomField) {
            nomField.addEventListener('blur', function() {
                const value = this.value.trim();
                this.classList.remove('is-valid', 'is-invalid');

                if (value === '') {
                    this.classList.add('is-invalid');
                } else if (value.length >= 2) {
                    this.classList.add('is-valid');
                }
            });
        }

        // Validation de l'email (au blur)
        if (emailField) {
            emailField.addEventListener('blur', function() {
                const emailValue = this.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                this.classList.remove('is-valid', 'is-invalid');

                if (emailValue === '') {
                    this.classList.add('is-invalid');
                } else if (!emailRegex.test(emailValue)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.add('is-valid');
                }
            });
        }

        // Validation de la date (au change)
        if (dateField) {
            dateField.addEventListener('change', function() {
                const selectedDate = this.value;
                const today = new Date().toISOString().split('T')[0];

                this.classList.remove('is-valid', 'is-invalid');

                if (selectedDate === '') {
                    this.classList.add('is-invalid');
                } else if (selectedDate <= today) {
                    this.classList.add('is-invalid');
                    // Afficher un message personnalisé
                    if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'La date doit être dans le futur.';
                        this.parentNode.appendChild(feedback);
                    }
                } else {
                    this.classList.add('is-valid');
                    // Supprimer le message d'erreur s'il existe
                    const feedback = this.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.remove();
                    }
                }
            });
        }

        // Validation du nombre d'invités (au input)
        if (invitesField) {
            invitesField.addEventListener('input', function() {
                const value = parseInt(this.value);

                this.classList.remove('is-valid', 'is-invalid');

                if (isNaN(value) || value < 10 || value > 500) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.add('is-valid');
                }
            });
        }

        // ========================================
        // PARTIE 3 : Empêcher la double soumission
        // ========================================

        form.addEventListener('submit', function(e) {
            // Vérifier que les champs obligatoires sont remplis
            let isValid = true;

            // Vérifier le nom
            if (nomField && nomField.value.trim() === '') {
                nomField.classList.add('is-invalid');
                isValid = false;
            }

            // Vérifier l'email
            if (emailField && emailField.value.trim() === '') {
                emailField.classList.add('is-invalid');
                isValid = false;
            }

            // Vérifier le type d'événement
            if (evenementField && evenementField.value === '') {
                evenementField.classList.add('is-invalid');
                isValid = false;
            }

            // Vérifier la date
            if (dateField && dateField.value === '') {
                dateField.classList.add('is-invalid');
                isValid = false;
            }

            // Vérifier la ville
            if (villeField && villeField.value === '') {
                villeField.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            // Désactiver le bouton pour éviter les doubles clics
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = '⏳ Envoi en cours...';
                submitBtn.classList.add('disabled');
            }
        });
    }

    // ========================================
    // PARTIE 4 : Compteur de caractères pour le message
    // ========================================

    const messageField = document.getElementById('message_rdv');
    if (messageField) {
        const maxLength = 500;

        // Créer le div du compteur
        const counterDiv = document.createElement('div');
        counterDiv.className = 'form-text text-end';
        counterDiv.textContent = maxLength + ' caractères restants';
        messageField.parentNode.appendChild(counterDiv);

        // Mettre à jour le compteur
        messageField.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            counterDiv.textContent = remaining + ' caractères restants';

            if (remaining < 50) {
                counterDiv.style.color = 'red';
                counterDiv.style.fontWeight = 'bold';
            } else if (remaining < 100) {
                counterDiv.style.color = 'orange';
                counterDiv.style.fontWeight = 'normal';
            } else {
                counterDiv.style.color = '';
                counterDiv.style.fontWeight = 'normal';
            }
        });
    }

});
