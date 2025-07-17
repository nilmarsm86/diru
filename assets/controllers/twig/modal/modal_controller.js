import {Modal} from "bootstrap";
import AbstractController from "../../AbstractController.js";
import {useBackdrop} from "../../../behaviors/use-backdrop.js";
import {HIDE as BACKDROP_HIDE, SHOW as BACKDROP_SHOW} from "../backdrop/backdrop_controller.js";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

export default class extends AbstractController {
    modal = null;

    static targets = ["backdrop"];

    connect() {
        useBackdrop(this);

        this.element.addEventListener('live-form:submit', this.submitForm.bind(this));
        this.element.addEventListener('live-form:submitEnd', this.submitEndForm.bind(this));

        this.element.addEventListener('live--land-form:submit', this.submitForm.bind(this));
        this.element.addEventListener('live--land-form:submitEnd', this.submitEndForm.bind(this));

        this.element.addEventListener('live--local-form:submit', this.submitForm.bind(this));
        this.element.addEventListener('live--local-form:submitEnd', this.submitEndForm.bind(this));
    }

    initialize() {
        this.modal = Modal.getOrCreateInstance(this.element);
        this.element.addEventListener('modal_close', (event) => {
            this.modal.hide();
            //     // this.modal.dispose();
            //     this.element.querySelector('button[class=btn-close]').click();
        });
    }

    // close() {
    //     this.element.querySelector('button[class=btn-close]').click();
    // }

    submitForm(event){
        const selector = `form[name='${event.detail.form.name}']`;
        if(this.element.querySelector(selector)){
            this.backdrop(this.backdropTarget, BACKDROP_SHOW);
        }
    }

    submitEndForm(event){
        const selector = `form[name='${event.detail.form.name}']`;
        if(this.element.querySelector(selector)){
            this.backdrop(this.backdropTarget, BACKDROP_HIDE);
        }
    }
}