import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    connect(string, radix) {
        let originalValue = 0;
        let USDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        let spanTotal = this.element.querySelector('[data-summation-values-target=total]');

        let ptp = this.element.querySelector('#building_projectPriceTechnicalPreparation') ? Number(this.element.querySelector('#building_projectPriceTechnicalPreparation').value) : 0;
        let value = 0;

        document.querySelector('#building_range').addEventListener('input', (event) => {
            if(originalValue === 0){
                originalValue = spanTotal ? this.clearNumber(spanTotal.innerText.replace('$', '')) : 0;
                value = (originalValue * 100) - (ptp * 100);
            }

            let updateValue = value + Number(event.target.valueAsNumber);
            spanTotal.innerText = USDollar.format(updateValue / 100);
        });
    }

    clearNumber(number) {
        const cleaned = number.replace(/[^0-9.]/g, '');
        return Number(cleaned.replace(/,/g, ''));
    }
}
