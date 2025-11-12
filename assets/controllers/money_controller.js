import {Controller} from '@hotwired/stimulus';
import {getComponent} from '@symfony/ux-live-component';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        let currency = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        this.element.addEventListener('change', (event) => {
            const cleaned = event.currentTarget.value.replace(/[^0-9.]/g, '');
            const value = Number(cleaned.replace(/,/g, ''));
            if (!isNaN(value)) {
                event.currentTarget.value = currency.format(value).replace('$', '');
            }
        });
    }
}
