import AbstractController from "../../AbstractController.js";
import {SHOW as BACKDROP_SHOW, HIDE as BACKDROP_HIDE} from "../backdrop/backdrop_controller.js";
import {useBackdrop} from "../../../behaviors/use-backdrop.js";
import {FILTER as FILTER_DROPDOWN} from "./filter-drop-down_controller.js";
import {FILTER as FILTER_REFRESH} from "./refresh_controller.js";


export const NEW = "App\\Component\\Twig\\Card\\Card_new";
export const CLOSE = "App\\Component\\Twig\\Card\\Card_close";
export const REFRESH = "App\\Component\\Twig\\Card\\Card_refresh";


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

        this.element.addEventListener('live--local-form:submit', this.submit.bind(this));
        this.element.addEventListener('live--local-form:submitEnd', this.submitEnd.bind(this));

        // this.element.addEventListener('corporate-entity-form:submit', this.submit.bind(this));
        // this.element.addEventListener('corporate-entity-form:submitEnd', this.submitEnd.bind(this));

        this.addListener(this.element, FILTER_DROPDOWN, this.filter.bind(this), {}, 'twig/card/filter-drop-down');
        this.addListener(this.element, FILTER_REFRESH, this.filter.bind(this), {}, 'twig/card/refresh');
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
        super.dispatch(NEW, {detail: {url: event.params.url}});
    }

    /**
     * close card
     * @param event
     */
    close(event) {
        super.dispatch(CLOSE);
    }

    filter(event){
        history.pushState({}, '', event.detail.url);
        super.dispatch(REFRESH, {detail:{url:event.detail.url}});
    }

}