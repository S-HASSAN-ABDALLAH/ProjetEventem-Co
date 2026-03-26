// Validation et gestion des réservations
// Événement & Co

document.addEventListener('DOMContentLoaded', function() {

    // Pré-remplir le formulaire selon la carte cliquée
    const rendezVousModal = document.getElementById('rendezVousModal');
    const evenementSelect = document.getElementById('evenement_rdv');

    rendezVousModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (button) {
            const eventId = button.getAttribute('data-event-id');
            if (eventId && evenementSelect) {
                evenementSelect.value = eventId;
            }
        }
    });

    rendezVousModal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById('formRendezVous');
        if (form) {
            form.reset();
        }
    });

    // Afficher les messages selon les paramètres URL
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    const messages = urlParams.get('messages');

    const form = document.getElementById('formRendezVous');

    if (success === '1') {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.setAttribute('role', 'alert');
        successDiv.style.marginTop = '100px';
        successDiv.style.marginLeft = 'auto';
        successDiv.style.marginRight = 'auto';
        successDiv.style.marginBottom = '20px';
        successDiv.style.maxWidth = '900px';
        successDiv.innerHTML = `
            <div class="container">
                <strong>Succès !</strong> Votre demande de réservation a été envoyée.
                <br>Vous recevrez un email de confirmation très prochainement.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        `;

        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.insertBefore(successDiv, mainContent.firstChild);
        } else {
            const header = document.querySelector('header');
            if (header && header.nextSibling) {
                header.parentNode.insertBefore(successDiv, header.nextSibling);
            } else {
                document.body.insertBefore(successDiv, document.body.firstChild);
            }
        }

        setTimeout(() => {
            const headerHeight = document.querySelector('header')?.offsetHeight || 80;
            const elementPosition = successDiv.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({ top: elementPosition - headerHeight - 20, behavior: 'smooth' });
        }, 100);

        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 5000);
    }

    if (error) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.setAttribute('role', 'alert');
        errorDiv.style.marginTop = '100px';
        errorDiv.style.marginLeft = 'auto';
        errorDiv.style.marginRight = 'auto';
        errorDiv.style.marginBottom = '20px';
        errorDiv.style.maxWidth = '900px';

        let errorMessage = '';

        switch (error) {
            case 'db':
                errorMessage = '<strong>Erreur technique.</strong> Veuillez réessayer plus tard.';
                break;
            case 'email':
                errorMessage = '<strong>Attention !</strong> Votre réservation a été enregistrée mais l\'email de confirmation n\'a pas pu être envoyé.';
                break;
            case 'validation':
                if (messages) {
                    const errorList = messages.split('|');
                    errorMessage = '<strong>Erreurs de validation :</strong><ul class="mb-0 mt-2">';
                    errorList.forEach(msg => {
                        errorMessage += `<li>${msg}</li>`;
                    });
                    errorMessage += '</ul>';
                } else {
                    errorMessage = '<strong>Erreur de validation.</strong> Veuillez vérifier vos informations.';
                }
                break;
            default:
                errorMessage = '<strong>Erreur.</strong> Veuillez réessayer.';
        }

        errorDiv.innerHTML = `
            <div class="container">
                ${errorMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        `;

        const mainContent = document.querySelector('main');
        if (mainContent) {
            mainContent.insertBefore(errorDiv, mainContent.firstChild);
        } else {
            const header = document.querySelector('header');
            if (header && header.nextSibling) {
                header.parentNode.insertBefore(errorDiv, header.nextSibling);
            } else {
                document.body.insertBefore(errorDiv, document.body.firstChild);
            }
        }

        setTimeout(() => {
            const headerHeight = document.querySelector('header')?.offsetHeight || 80;
            const elementPosition = errorDiv.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({ top: elementPosition - headerHeight - 20, behavior: 'smooth' });
        }, 100);

        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 7000);
    }

    // Validation en temps réel
    if (form) {
        const nomField = document.getElementById('nom_rdv');
        const emailField = document.getElementById('email_rdv');
        const dateField = document.getElementById('date_rdv');
        const villeField = document.getElementById('ville_rdv');
        const evenementField = document.getElementById('evenement_rdv');
        const invitesField = document.getElementById('invites_rdv');
        const submitBtn = form.querySelector('button[type="submit"]');

        if (nomField) {
            nomField.addEventListener('blur', function() {
                this.classList.remove('is-valid', 'is-invalid');
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else if (this.value.trim().length >= 2) {
                    this.classList.add('is-valid');
                }
            });
        }

        if (emailField) {
            emailField.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                this.classList.remove('is-valid', 'is-invalid');
                if (this.value.trim() === '' || !emailRegex.test(this.value.trim())) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.add('is-valid');
                }
            });
        }

        if (dateField) {
            dateField.addEventListener('change', function() {
                const today = new Date().toISOString().split('T')[0];
                this.classList.remove('is-valid', 'is-invalid');
                if (this.value === '' || this.value <= today) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.add('is-valid');
                }
            });
        }

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

        // Empêcher la double soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;

            if (nomField && nomField.value.trim() === '') {
                nomField.classList.add('is-invalid');
                isValid = false;
            }
            if (emailField && emailField.value.trim() === '') {
                emailField.classList.add('is-invalid');
                isValid = false;
            }
            if (evenementField && evenementField.value === '') {
                evenementField.classList.add('is-invalid');
                isValid = false;
            }
            if (dateField && dateField.value === '') {
                dateField.classList.add('is-invalid');
                isValid = false;
            }
            if (villeField && villeField.value === '') {
                villeField.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Envoi en cours...';
            }
        });
    }

    // Compteur de caractères pour le message
    const messageField = document.getElementById('message_rdv');
    if (messageField) {
        const maxLength = 500;
        const counterDiv = document.createElement('div');
        counterDiv.className = 'form-text text-end';
        counterDiv.textContent = maxLength + ' caractères restants';
        messageField.parentNode.appendChild(counterDiv);

        messageField.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            counterDiv.textContent = remaining + ' caractères restants';
            counterDiv.style.color = remaining < 50 ? 'red' : remaining < 100 ? 'orange' : '';
        });
    }

});