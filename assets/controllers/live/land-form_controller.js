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

    static targets = ["area", "occupied", "cos", "perimeter", "details", "hectare", "floor"];

    connect() {
        useCsrfToken(this);

        this.element.querySelector('form').addEventListener('submit', (event) => {
            this.dispatch('submit', {detail: {form: event.currentTarget}});
        });

        this.calcualteCos();
        this.calculateHectare();

        this.areaTarget.addEventListener('input', (event) => {
            if (Number(this.areaTarget.value) < 0) {
                this.areaTarget.value = Number(this.areaTarget.value) * -1;
            }

            if (Number(this.occupiedTarget.value) > Number(this.areaTarget.value)) {
                this.occupiedTarget.value = this.areaTarget.value;
            }

            if (Number(this.perimeterTarget.value) > Number(this.areaTarget.value)) {
                this.perimeterTarget.value = this.areaTarget.value;
            }

            this.calculateOccupatedArea();

            this.calcualteCos(event);
            this.calculateHectare();
        });

        this.occupiedTarget.addEventListener('input', (event) => {
            if(this.occupiedTarget.value == ''){
                this.occupiedTarget.value = 0;
            }

            if (Number(this.occupiedTarget.value) < 0) {
                this.occupiedTarget.value = Number(this.occupiedTarget.value) * -1;
            }

            this.calculateOccupatedArea();

            this.calcualteCos(event);
        });

        this.perimeterTarget.addEventListener('input', (event) => {
            if (Number(this.perimeterTarget.value) < 0) {
                this.perimeterTarget.value = Number(this.perimeterTarget.value) * -1;
            }

            if (Number(this.perimeterTarget.value) > Number(this.areaTarget.value)) {
                this.perimeterTarget.value = this.areaTarget.value;
            }

            this.calculateOccupatedArea();
        });

        this.detailsTarget.addEventListener('toggle', this.calcualteCos.bind(this));
    }

    calculateOccupatedArea(){
        if (Number(this.occupiedTarget.value) > Number(this.areaTarget.value)) {
            this.occupiedTarget.value = Number(this.areaTarget.value);
        }
    }
    calcualteCos(event) {
        if (!this.detailsTarget.open) {
            this.cosTarget.innerText = 0;
            // this.occupiedTarget.value = 0;
            // this.floorTarget.value = 0;
        } else {
            this.cosTarget.innerText = (Number(this.occupiedTarget.value) * 100 / Number(this.areaTarget.value)).toFixed(2);
        }

        this.occupiedTarget.max = this.areaTarget.value;
        this.perimeterTarget.max = this.areaTarget.value;
    }

    calculateHectare() {
        this.hectareTarget.innerText = (Number(this.areaTarget.value) / 10000).toFixed(2);
    }

    async initialize() {
        this.component = await getComponent(this.element);
        this.processCsrfToken();

        this.component.on('render:finished', (component) => {
            this.dispatch('submitEnd', {detail: {form: this.element.querySelector('form')}});
        });
    }

}
