import {useSubmitFormAsync} from "../behaviors/submit-form-async/use-submit-form-async.js";
import AbstractController from "./AbstractController.js";

export const SUCCESS = 'delete-form-container:success';
export const FAILURE = 'delete-form-container:failure';
export const START = 'delete-form-container:startSubmit';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends AbstractController {

    connect(){
        useSubmitFormAsync(this, {
            container: this.element,
            // eventSuccessName: this.eventSuccessNameValue || "success",
            // eventFailureName: this.eventFailureNameValue || "failure"
        });

        this.element.addEventListener('submit', this.remove.bind(this));
    }

    remove(event){
        if(event.returnValue){
            //disparar evento de mosrtar el spinner
            this.submit(event);
        }
    }

}
