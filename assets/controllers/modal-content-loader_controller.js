import {useContentLoader} from "../behaviors/content-loader/use-content-loader.js";
import AbstractController from "./AbstractController.js";
import {Modal} from "bootstrap";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
export default class extends AbstractController {
    static targets = ['button'];
    static values = {
        url: {type: String, default: ''},
        id: {type: String, default: ''},
        placeholder: {type: String, default: ''},
        title: {type: String, default: ''},
    };
    message = '';

    connect() {
        // useProcessResponse(this);
        useContentLoader(this, {
            // url: '',
            // container: this.selectTarget,
            // eventLoadedName: "loaded",
        });


        this.buttonTarget.addEventListener('click', (event) => {
            event.preventDefault();

            const modalBody = document.querySelector('#' + this.idValue + ' .modal-body-content');
            this.addChildsNodes(this.placeholderValue, modalBody);

            this.addChildsNodes(this.titleValue, document.querySelector('#' + this.idValue + ' .modal-title'));
            Modal.getOrCreateInstance(document.querySelector('#' + this.idValue)).show();

            const eventDetail = new CustomEvent('loadData', {
                detail: {
                    url: this.urlValue,
                    container: modalBody,
                    eventLoadedName: 'loadedData',
                }
            });
            this.refreshContent(eventDetail);
        });

    }

}
