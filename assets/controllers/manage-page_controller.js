import {useProcessResponse} from "../behaviors/use-process-response.js";
import {useContentLoader} from "../behaviors/content-loader/use-content-loader.js";
import {useVisibility} from "../behaviors/visibility/use-visibility.js";
import AbstractController from "./AbstractController.js";

import {SHOW as BACKDROP_SHOW, HIDE as BACKDROP_HIDE} from "./twig/backdrop/backdrop_controller.js";
import {NEW as CARD_NEW, CLOSE as CARD_CLOSE, REFRESH as CARD_REFRESH} from "./twig/card/card_controller.js";
// import {SUCCESS as FORM_SUCCESS} from "./live-form_controller.js";
const FORM_SUCCESS = ':form_success';
import {
    DETAIL as TABLE_DETAIL,
    EDIT as TABLE_EDIT,
    REFRESH as TABLE_REFRESH,
    SELECT as TABLE_SELECT
} from "./twig/table/table_controller.js";
import {SUCCESS as DELETE_FORM_SUCCESS, START as START_REMOVE_ITEM} from "./delete-form-container_controller.js";
import {Tooltip} from "bootstrap";

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

    static targets = ["listContainer", "formContainer", "backdrop"];

    connect() {
        useProcessResponse(this);
        useContentLoader(this, {
            // url: '',
            container: this.formContainerTarget,//ponerlo como un target del controlador
            // eventLoadedName: "loaded",
        });

        useVisibility(this, {
            targets: [this.formContainerTarget],//ponerlo como un target del controlador
            // query: this.queryValue || '',
            // cssClass: this.cssClassValue || '',
            // eventShowName: this.eventShowNameValue || "show",
            // eventHideName: this.eventHideNameValue || "hide",
            // eventToggleName: this.eventToggleNameValue || "toggle",
        });

        //agregar el formulario de nuevo elemento
        this.addListener(this.element, CARD_NEW, this.onFormContainerAction.bind(this), {}, 'twig/card/card');

        //ya cargo el codigo del formulario del nuevo/editar elemento o detalle
        this.element.addEventListener('manage-page:loaded', this.contentLoaded.bind(this));

        // //mostrar el detalle
        // this.addListener(this.element, TABLE_DETAIL, this.onFormContainerAction.bind(this), {}, 'twig/table/table');
        // //agregar el formulario de editar
        // this.addListener(this.element, TABLE_EDIT, this.onFormContainerAction.bind(this), {}, 'twig/table/table');
        //seleccionar una fila, ya sea para editar o mostrar el detalle
        this.addListener(this.element, TABLE_SELECT, this.onFormContainerAction.bind(this), {}, 'twig/table/table');

        //refrescar el contenido de la tabla
        this.addListener(this.element, TABLE_REFRESH, this.refreshListContent.bind(this), {}, 'twig/table/table');

        //refrescar el contenido de la card
        this.addListener(this.element, CARD_REFRESH, this.refreshListContent.bind(this), {}, 'twig/card/card');

        //ocultar el contenedor del formulario
        this.addListener(this.element, CARD_CLOSE, this.onCardClose.bind(this), {}, 'twig/card/card');

        //procesar una respuesta de una accion de un formulario bien enviado
        this.element.addEventListener(FORM_SUCCESS, this.onManagePageProcessResponse.bind(this));
        this.element.addEventListener(DELETE_FORM_SUCCESS, this.deleteFormSuccess.bind(this));
        // window.addEventListener(START_REMOVE_ITEM, this.startRemoveItem.bind(this));

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))

        const toastList = document.querySelectorAll('.toast');
        [...toastList].map(toast => {
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        });

    }

    backdrop(backdropElement, action) {
        const backdrop = super.getController(backdropElement, 'twig/backdrop/backdrop');
        backdrop.dispatch(action, {detail: {id: backdropElement.dataset.id}});
    }

    onFormContainerAction(event) {
        this.hide(event);
        this.backdrop(this.backdropTarget, BACKDROP_SHOW);
        this.refreshContent(event);
        this.removeAllChilds(this.formContainerTarget);
        this.containerHash('#' + this.formContainerTarget.id);
        this.show(event);
    }

    onCardClose(event) {
        this.hide(event);
        this.containerHash('#top');
        this.removeAllChilds(this.formContainerTarget);
    }

    contentLoaded(event) {
        if (event.detail.container.id === this.formContainerTarget.id) {
            this.backdrop(this.backdropTarget, BACKDROP_HIDE);
        }
    }

    /**
     * Go to hash
     * @param hash
     */
    containerHash(hash) {
        if (document.location.hash !== hash) {
            document.location.hash = hash;
        }
    }

    async onManagePageProcessResponse(event) {
        const formBackdrop = this.formContainerTarget.querySelector('[data-id=card-backdrop]');
        this.backdrop(formBackdrop, BACKDROP_SHOW);

        await this.deleteFormSuccess(event);
        this.onCardClose(event);
    }

    async deleteFormSuccess(event) {
        await this.processResponseToast(event.detail.response);//show toast
        this.refreshListContent(event);
        this.onCardClose(event);
    }

    startRemoveItem(event){
        // const listBackdrop = this.listContainerTarget.querySelector('[data-id=card-backdrop]');
        // this.backdrop(listBackdrop, BACKDROP_SHOW);
    }

    refreshListContent(event) {
        const listBackdrop = this.listContainerTarget.querySelector('[data-id=card-backdrop]');
        this.backdrop(listBackdrop, BACKDROP_SHOW);

        event.detail.container = this.listContainerTarget;
        if (!event.detail.url) {
            event.detail.url = document.location.href;
        }

        this.refreshContent(event);
    }

}