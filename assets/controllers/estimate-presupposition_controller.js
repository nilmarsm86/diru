import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["field", "total", "estimate"];
    static values = {
        estimate: {type: Number, default: 0},
        coefficient: {type: Number, default: 0},
        totalArea: {type: Number, default: 0},
    };
    originalEstimate = 0;

    connect() {
        let USDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        this.originalEstimate = this.estimateValue;
        this.estimateValue = this.coefficientValue * this.originalEstimate;
        this.multiply();


        this.fieldTargets.forEach((field) => {
            field.addEventListener('input', (event) => {
                if (Number(field.value) < 0) {
                    field.value = Number(field.value) * -1;
                }
                this.estimateValue = Number(event.target.value) * this.originalEstimate;
                this.multiply();
            });
        });

        window.addEventListener('live-form:submitEnd', (event) => {
            this.multiply();
        });

        document.querySelector('#building_range').addEventListener('input', (event) => {
            this.estimateTarget.innerText = USDollar.format(event.target.valueAsNumber / 100);

            this.estimateValue = event.target.valueAsNumber * this.coefficientValue;
            this.multiply();
        });
    }

    multiply() {
        let USDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        this.totalTarget.innerText = USDollar.format((this.totalAreaValue > 0) ? ((this.estimateValue / 100) / this.totalAreaValue).toFixed(2) : 0);
        this.element.querySelector('strong.multiply').innerText = USDollar.format(this.estimateValue / 100);
    }

    clearNumber(number) {
        const cleaned = number.replace(/[^0-9.]/g, '');
        return Number(cleaned.replace(/,/g, ''));
    }
}
