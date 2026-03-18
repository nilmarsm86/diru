import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["field", "total", "estimate"];
    static values = {
        estimate: {type: Number, default: 0},
    };

    connect() {
        let USDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        this.multiply();

        this.fieldTargets.forEach((field) => {
            field.addEventListener('input', (event) => {
                if (Number(field.value) < 0) {
                    field.value = Number(field.value) * -1;
                }
                this.multiply();
            });
        });

        window.addEventListener('live-form:submitEnd', (event) => {
            this.multiply();
        });

        document.querySelector('#building_range').addEventListener('input', (event) => {
            this.estimateTarget.innerText = USDollar.format(event.target.valueAsNumber / 100);

            this.estimateValue = event.target.valueAsNumber;
            this.multiply();
        });
    }

    multiply() {
        let USDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        this.totalTarget.innerText = USDollar.format(this.clearNumber(this.fieldTarget.value) * this.estimateValue / 100);
    }

    clearNumber(number) {
        const cleaned = number.replace(/[^0-9.]/g, '');
        return Number(cleaned.replace(/,/g, ''));
    }
}
