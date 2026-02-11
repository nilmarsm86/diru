import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["range", "min", "max", "actual"];
    uSDollar = null;

    connect() {
        this.uSDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        this.actualTarget.textContent = this.uSDollar.format(this.rangeTarget.value / 100);
        this.minTarget.textContent = this.uSDollar.format(this.rangeTarget.getAttribute('min') / 100);
        this.maxTarget.textContent = this.uSDollar.format(this.rangeTarget.getAttribute('max') / 100);

        this.rangeTarget.addEventListener('input', () => {
            this.actualTarget.textContent = this.uSDollar.format(this.rangeTarget.value / 100);
        });
    }

    reset(){
        this.rangeTarget.value = this.rangeTarget.defaultValue;
        this.actualTarget.textContent = this.uSDollar.format(this.rangeTarget.value / 100);
    }

}
