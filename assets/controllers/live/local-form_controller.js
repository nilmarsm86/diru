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
        isNew: {type: Boolean, default: false},
    };

    static targets = ["area", "type", "height", "technicalStatus"];

    connect() {
        useCsrfToken(this);

        this.element.querySelector('form').addEventListener('submit', (event) => {
            this.dispatch('submit', {detail: {form: event.currentTarget}});
        });

        this.areaTarget.addEventListener('input', (event) => {
            if (this.areaTarget.getAttribute('max')) {
                if (Number(this.areaTarget.value) > Number(this.areaTarget.getAttribute('max'))) {
                    this.areaTarget.value = this.areaTarget.getAttribute('max');
                }
            }
        });

        // this.heightTechnicalStatus();

        this.typeTarget.addEventListener('change', (event) => {
            // if (this.isNewValue === true) {//si es un nuevo local, el estado tecnico debe de ser bueno por defecto
            //     this.technicalStatusTarget.value = 4;
            // }
            //
            // if (event.currentTarget.value == 0) {//area de vacio
            //     this.heightTarget.value = 0;
            //     this.heightTarget.min = 0;
            //     this.technicalStatusTarget.value = 4;//estado bueno
            // }
            //
            // if (event.currentTarget.value == 1) {// local
            //     this.heightTarget.value = 2.40;
            //     this.heightTarget.min = 1;
            //     if (this.isNewValue === false) {//si es un nuevo local, el estado tecnico debe de ser bueno por defecto
            //         this.technicalStatusTarget.value = '';
            //     }
            // }
            //
            // if (event.currentTarget.value == 2) {// area de muro
            //     this.heightTarget.value = 1;
            //     this.heightTarget.min = 1;
            //     if (this.isNewValue === false) {//si es un nuevo local, el estado tecnico debe de ser bueno por defecto
            //         this.technicalStatusTarget.value = '';
            //     }
            // }
            this.heightTechnicalStatus();
        });
    }

    async initialize() {
        this.component = await getComponent(this.element);
        this.processCsrfToken();

        this.component.on('render:finished', (component) => {
            // this.heightTechnicalStatus();
            this.dispatch('submitEnd', {detail: {form: this.element.querySelector('form')}});

        });

        // this.heightTechnicalStatus();
    }

    heightTechnicalStatus(){
        console.log(this.typeTarget.value);
        if (this.isNewValue === true) {//si es un nuevo local, el estado tecnico debe de ser bueno por defecto
            this.technicalStatusTarget.value = 4;
        }

        if (this.typeTarget.value == 0) {//area de vacio
            // this.heightTarget.value = 0;
            this.heightTarget.min = 0;
            this.technicalStatusTarget.value = 4;//estado bueno
        }

        if (this.typeTarget.value == 1) {// local
            // this.heightTarget.value = '2.40';
            this.heightTarget.min = 1;
            if (this.isNewValue === false) {//si es un nuevo local, el estado tecnico debe de ser bueno por defecto
                this.technicalStatusTarget.value = '';
            }
        }

        if (this.typeTarget.value == 2) {// area de muro
            // this.heightTarget.value = 1;
            this.heightTarget.min = 1;
            if (this.isNewValue === false) {//si es un nuevo local, el estado tecnico debe de ser bueno por defecto
                this.technicalStatusTarget.value = '';
            }
        }
    }

}
