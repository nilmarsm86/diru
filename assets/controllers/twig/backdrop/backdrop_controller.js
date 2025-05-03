import AbstractController from "../../AbstractController.js";

export const HIDE = "App\\Component\\Twig\\Backdrop_hide";
export const SHOW = "App\\Component\\Twig\\Backdrop_show";

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
    connect() {
        this.addListener(this.element, HIDE, this.onHide.bind(this));
        this.addListener(this.element, SHOW, this.onShow.bind(this));
    }

    disconnect() {
        this.removeListener(this.element, HIDE, this.onHide.bind(this));
        this.removeListener(this.element, SHOW, this.onShow.bind(this));
    }

    toogle(event, display){
        event.preventDefault();

        if (this.element.dataset.id === event.detail.id) {
            this.element.style.display = display;
        }
    }

    /**
     * Show backdrop
     * @param event
     */
    onShow(event) {
        this.toogle(event, 'flex');
    }

    /**
     * Hide backdrop
     * @param event
     */
    onHide(event) {
        this.toogle(event, 'none');
    }

}
