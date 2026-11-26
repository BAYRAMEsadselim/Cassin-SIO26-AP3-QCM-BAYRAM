/**
 * Passage d'un QCM : affiche une question à la fois et sauvegarde chaque réponse.
 *
 * Données attendues (injectées par la vue PHP dans data-questions, SANS les bonnes réponses) :
 * [{ id, enonce, type: 'unique'|'multiple'|'negative'|'classement',
 *    propositions: [{ id, libelle }] }, ...]
 *
 * SQUELETTE : les types unique/multiple/negative sont gérés ; le classement
 * (glisser-déposer) est laissé en exercice (voir TODO).
 */
export class QuizPlayer {
    #index = 0;
    #answers = new Map(); // question_id -> [proposition_id, ...]

    /**
     * @param {HTMLElement} root
     * @param {object[]} questions
     * @param {import('./ApiClient.js').ApiClient} api
     * @param {number} tentativeId
     */
    constructor(root, questions, api, tentativeId) {
        this.root = root;
        this.questions = questions;
        this.api = api;
        this.tentativeId = tentativeId;
    }

    start() {
        this.#render();
    }

    #render() {
        const q = this.questions[this.#index];
        const inputType = q.type === 'multiple' || q.type === 'negative' ? 'checkbox' : 'radio';
        const saved = this.#answers.get(q.id) ?? [];

        this.root.innerHTML = '';
        const box = document.createElement('div');
        box.className = 'question';

        const title = document.createElement('h2');
        title.textContent = `Question ${this.#index + 1} / ${this.questions.length}`;
        const enonce = document.createElement('p');
        enonce.textContent = q.enonce; // textContent : pas d'injection HTML

        box.append(title, enonce);

        if (q.type === 'classement') {
            // TODO : liste triable (drag and drop), envoyer { proposition_id, rang }
            const todo = document.createElement('p');
            todo.textContent = 'Classement : à implémenter.';
            box.append(todo);
        } else {
            for (const p of q.propositions) {
                const label = document.createElement('label');
                const input = document.createElement('input');
                input.type = inputType;
                input.name = `q${q.id}`;
                input.value = String(p.id);
                input.checked = saved.includes(p.id);
                input.addEventListener('change', () => this.#onChange(q, box));
                label.append(input, ' ', p.libelle);
                box.append(label);
            }
        }

        const nav = document.createElement('p');
        if (this.#index > 0) {
            nav.append(this.#button('Précédent', () => this.#go(-1)));
        }
        if (this.#index < this.questions.length - 1) {
            nav.append(this.#button('Suivant', () => this.#go(1)));
        } else {
            nav.append(this.#button('Valider définitivement', () => this.#validate()));
        }
        box.append(nav);
        this.root.append(box);
    }

    async #onChange(question, box) {
        const checked = [...box.querySelectorAll('input:checked')].map((i) => Number(i.value));
        this.#answers.set(question.id, checked);
        await this.api.post(`/api/tentatives/${this.tentativeId}/reponse`, {
            question_id: question.id,
            propositions: checked,
        });
    }

    #go(delta) {
        this.#index += delta;
        this.#render();
    }

    async #validate() {
        if (!confirm('Une fois validé, le QCM ne peut plus être modifié. Continuer ?')) {
            return;
        }
        // TODO : route POST /etudiant/qcm/{id}/valider côté PHP, puis redirection vers le résultat
        await this.api.post(`/api/tentatives/${this.tentativeId}/valider`, {});
        window.location.reload();
    }

    #button(text, onClick) {
        const b = document.createElement('button');
        b.type = 'button';
        b.textContent = text;
        b.addEventListener('click', onClick);
        return b;
    }
}
