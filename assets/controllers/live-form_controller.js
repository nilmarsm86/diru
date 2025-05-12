import {getComponent} from '@symfony/ux-live-component';
import {useCsrfToken} from "../behaviors/use-csrf-token.js";
import AbstractController from "./AbstractController.js";

export const SUCCESS = "App\\Component\\Twig\\ProvinceForm_form_success";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static values = {
        modal: {type: String, default: ''},
        // render: {type: Boolean, default: false},
    };

    connect() {
        useCsrfToken(this);

        this.element.querySelector('form').addEventListener('submit', (event) => {
            this.dispatch('submit', {detail: {form: event.currentTarget}});
        });

        window.addEventListener('type--entity-plus:update', (event) => {
            if (this.modalValue === '' || (event.detail.modal === 'add-province' && this.modalValue === 'add-municipality')) {
                for (let item in event.detail.data) {
                    try {
                        this.component.set((item), event.detail.data[item]);
                    } catch (e) {
                    }
                }
                this.component.render();
            }
        });

        window.addEventListener('type--address:loaded', (event) => {
            if (this.modalValue === 'add-municipality') {
                try {
                    this.component.set('province', event.target.querySelectorAll('select')[0].value);
                } catch (e) {
                }
                this.component.render();
            }
        });
    }

    async initialize() {
        this.component = await getComponent(this.element);
        this.processCsrfToken();

        this.component.on('render:finished', (component) => {
            this.dispatch('submitEnd', {detail: {form: this.element.querySelector('form')}});

            //if an entity-plus has double same option, deleted
            const selects = this.element.querySelectorAll('select[data-type--entity-plus-target=select]');
            selects.forEach(this.removeDoubleSameOption.bind(this));
        });
    }

    removeDoubleSameOption(select){
        if(select.dataset['type-AddressTarget'] && select.dataset['type-AddressTarget'] === 'municipality'){
            for (let i = 0; i < select.options.length; i++) {
                if(select.options[i].dataset.ajax !== undefined && !select.options[i].selected){
                    select.options.remove(i);
                }
            }

            let ind = null;
            for (let i = 0; i < select.options.length; i++) {
                if(select.options[i].attributes.selected){
                    ind = i;
                }
            }

            select.selectedIndex = ind;
        }
    }

}
