import {Toast} from "bootstrap";

export const useCsrfToken = (controller, options) => {
    Object.assign(controller, {

        async processCsrfToken(response) {
            this.component.on('render:finished', (component) => {
                let formElement = this.element.querySelector('form');
                if (formElement) {
                    let fieldCsrfToken = this.element.querySelector('input[data-controller="csrf-protection"]');
                    fieldCsrfToken.removeAttribute('data-csrf-protection-cookie-value');
                    fieldCsrfToken.value = 'csrf-token';
                }
            });
        }
    });
};