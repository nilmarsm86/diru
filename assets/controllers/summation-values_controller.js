import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["field", "total"];

    connect() {
        this.summation();

        this.fieldTargets.forEach((field) => {
            field.addEventListener('input', (event) => {
                if (Number(field.value) < 0) {
                    field.value = Number(field.value) * -1;
                }
                this.summation();
            });
        });

        //just for USD currency
        this.element.parentElement.addEventListener('toggle', (event) => {
            this.fieldTargets.forEach((field) => {
                let inputGroupText = field.parentElement.querySelector('.input-group-text');
                if (event.currentTarget.open) {
                    if (inputGroupText.innerText === 'US$') {
                        inputGroupText.innerText = 'USD';
                    }
                }
            });
        });

        window.addEventListener('live-form:submitEnd', (event) => {
            this.summation();
        });
    }

    summation() {
        let USDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });
        this.totalTarget.innerText = USDollar.format(this.fieldTargets.reduce((accumulator, field) => accumulator + this.clearNumber(field.value), 0));
        if (this.element.querySelector('.vecpppt')) {
            let cleanTotal = document.querySelectorAll("[data-vecpppt]").values().reduce((accumulator, field) => accumulator + this.clearNumber(field.value), 0);
            let up = cleanTotal + (cleanTotal * 20 / 100);
            let down = cleanTotal - (cleanTotal * 20 / 100);
            if(this.element.querySelector('.vecpppt') instanceof HTMLInputElement){
                // this.element.querySelector('.vecpppt').value = cleanTotal;
                // this.element.querySelector('.vecpppt').setAttribute('min', down);
                // this.element.querySelector('.vecpppt').setAttribute('max', up);
            }else{
                this.element.querySelector('.vecpppt').innerText = USDollar.format(cleanTotal);
            }
        }
    }

    clearNumber(number) {
        const cleaned = number.replace(/[^0-9.]/g, '');
        return Number(cleaned.replace(/,/g, ''));
    }
}
