import {Controller} from '@hotwired/stimulus';
import {getComponent} from '@symfony/ux-live-component';
import {useCsrfToken} from "../behaviors/use-csrf-token.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends Controller {

    async initialize() {
        this.component = await getComponent(this.element);

        //refactorizar en un behavior
        // this.component.on('render:finished', (component) => {
        //     let formElement = this.element.querySelector('form');
        //     if (formElement) {
        //         let fieldCsrfToken = this.element.querySelector('input[data-controller="csrf-protection"]');
        //         fieldCsrfToken.removeAttribute('data-csrf-protection-cookie-value');
        //         fieldCsrfToken.value = 'csrf-token';
        //     }
        // });
        this.processCsrfToken();
    }

    connect(){
        useCsrfToken(this);

        this.element.addEventListener("App\\Component\\Live\\DeleteForm_pre_delete", this.onDeleteFormPreDelete.bind(this));
        this.element.addEventListener("App\\Component\\Live\\DeleteForm_pre_delete_error", this.onDeleteFormPreDeleteError.bind(this));
    }

    onDeleteFormPreDelete(event){
        if(confirm(event.detail.message)){
            this.component.action('delete', {'valid': true});
        }
    }

    onDeleteFormPreDeleteError(event){
        alert(event.detail.message);
    }

}
