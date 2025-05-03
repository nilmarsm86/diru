import AbstractController from "../../AbstractController.js";
import {SHOW as BACKDROP_SHOW, HIDE as BACKDROP_HIDE} from "../backdrop/backdrop_controller.js";
import {useBackdrop} from "../../../behaviors/use-backdrop.js";

export const NEW = "App\\Component\\Twig\\Card\\Card_new";
export const CLOSE = "App\\Component\\Twig\\Card\\Card_close";

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

    static targets = ["backdrop"];

    connect() {
        useBackdrop(this);

        this.element.addEventListener('live-form:submit', this.submit.bind(this));
        this.element.addEventListener('live-form:submitEnd', this.submitEnd.bind(this));

        this.element.addEventListener('municipality-form:submit', this.submit.bind(this));
        this.element.addEventListener('municipality-form:submitEnd', this.submitEnd.bind(this));

        this.element.addEventListener('corporate-entity-form:submit', this.submit.bind(this));
        this.element.addEventListener('corporate-entity-form:submitEnd', this.submitEnd.bind(this));
    }

    submit(event){
        const selector = `form[name='${event.detail.form.name}']`;
        if(this.element.querySelector(selector)){
            this.backdrop(this.backdropTarget, BACKDROP_SHOW);
        }
    }

    submitEnd(event){
        const selector = `form[name='${event.detail.form.name}']`;
        if(this.element.querySelector(selector)){
            this.backdrop(this.backdropTarget, BACKDROP_HIDE);
        }
    }

    /**
     * Create a new element
     * @param event
     */
    newElement(event) {
        this.dispatch(NEW, {detail: {url: event.params.url}});
    }

    /**
     * close card
     * @param event
     */
    close(event) {
        this.dispatch(CLOSE);
    }

}