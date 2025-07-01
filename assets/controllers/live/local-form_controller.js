import {useCsrfToken} from "../../behaviors/use-csrf-token.js";
import AbstractController from "../AbstractController.js";
import {getComponent} from '@symfony/ux-live-component';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static values = {
        modal: {type: String, default: ''},
    };

    static targets = ["area"];

    connect() {
        useCsrfToken(this);

        this.element.querySelector('form').addEventListener('submit', (event) => {
            this.dispatch('submit', {detail: {form: event.currentTarget}});
        });

        this.areaTarget.addEventListener('input', (event) => {
            if (Number(this.areaTarget.value) > Number(this.areaTarget.getAttribute('max'))) {
                this.areaTarget.value = this.areaTarget.getAttribute('max');
            }
        });
    }

    async initialize() {
        this.component = await getComponent(this.element);
        this.processCsrfToken();

        this.component.on('render:finished', (component) => {
            this.dispatch('submitEnd', {detail: {form: this.element.querySelector('form')}});
        });
    }

}
