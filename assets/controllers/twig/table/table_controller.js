import AbstractController from "../../AbstractController.js";

import {CHANGE as AMOUNT_CHANGE} from "./amount_controller.js";
import {BLUR as FILTER_BLUR} from "./filter_controller.js";
import {NAVIGATE} from "./navigation_controller.js";
import {SHOW as BACKDROP_SHOW, HIDE as BACKDROP_HIDE} from "../backdrop/backdrop_controller.js";
import {SUCCESS as DELETE_SUCCESS, FAILURE as DELETE_FAILURE, START as DELETE_START} from "../../delete-form-container_controller.js";

export const DETAIL = "App\\Component\\Twig\\Table\\Table_detail";
export const EDIT = "App\\Component\\Twig\\Table\\Table_edit";
export const SELECT = "App\\Component\\Twig\\Table\\Table_select";
export const REFRESH = "App\\Component\\Twig\\Table\\Table_refresh";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static targets = [ "backdrop" ];

    connect() {
        this.addListener(this.element, AMOUNT_CHANGE, this.dataChange.bind(this), {}, 'twig/table/amount');
        this.addListener(this.element, FILTER_BLUR, this.dataChange.bind(this), {}, 'twig/table/filter');
        this.addListener(this.element, NAVIGATE, this.dataChange.bind(this), {}, 'twig/table/navigation');

        this.element.addEventListener(DELETE_START, (event) => {
            this.backdrop(this.backdropTarget, BACKDROP_SHOW);
        });

        this.element.addEventListener(DELETE_SUCCESS, (event) => {
            this.backdrop(this.backdropTarget, BACKDROP_HIDE);
        });

        this.element.addEventListener(DELETE_FAILURE, (event) => {
            this.backdrop(this.backdropTarget, BACKDROP_HIDE);
        });
    }

    /**
     * Detail element
     * @param event
     */
    detailElement(event){
        this.dispatch(DETAIL, {detail: {url: event.params.url}});
    }

    /**
     * Edit element
     * @param event
     */
    editElement(event){
        this.dispatch(EDIT, {detail: {url: event.params.url}});
    }

    /**
     * Edit element
     * @param event
     */
    selectElement(event){
        this.dispatch(SELECT, {detail: {url: event.params.url}});
    }

    dataChange(event){
        // if(event.detail.url.searchParams.has('page')){
        //     event.detail.url.searchParams.set('page', '1');
        // }
        // this.backdrop(this.backdropTarget, BACKDROP_SHOW);
        history.pushState({}, '', event.detail.url);
        super.dispatch(REFRESH, {detail:{url:event.detail.url}});
    }

    backdrop(backdropElement, action){
        const backdrop = super.getController(backdropElement, 'twig/backdrop/backdrop');
        backdrop.dispatch(action, {detail:{id: backdropElement.dataset.id}});
    }

}
