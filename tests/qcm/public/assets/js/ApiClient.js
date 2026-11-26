/**
 * Encapsule fetch : ajoute le jeton CSRF et uniformise la gestion des erreurs.
 */
export class ApiClient {
    constructor() {
        this.csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    }

    async get(url) {
        return this.#request(url, { method: 'GET' });
    }

    async post(url, data) {
        return this.#request(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
    }

    async #request(url, options) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'X-CSRF-Token': this.csrf,
                ...(options.headers ?? {}),
            },
            credentials: 'same-origin',
        });
        if (!response.ok) {
            throw new Error(`Erreur ${response.status} sur ${url}`);
        }
        return response.json();
    }
}
