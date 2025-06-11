import {useContentLoader} from "../../behaviors/content-loader/use-content-loader.js";
import AbstractController from "../AbstractController.js";
import {useProcessResponse} from "../../behaviors/use-process-response.js";
import {Modal} from "bootstrap";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
export default class extends AbstractController {
    static targets = ['select', 'detail', 'add'];
    static values = {
        addUrl: {type: String, default: ''},
        addId: {type: String, default: ''},
        addPlaceholder: {type: String, default: ''},
        addTitle: {type: String, default: ''},

        detailUrl: {type: String, default: ''},
        detailPlaceholder: {type: String, default: ''},
        detailId: {type: String, default: ''},
        detailTitle: {type: String, default: ''},
    };
    message = '';

    connect() {
        useProcessResponse(this);
        useContentLoader(this, {
            // url: '',
            container: this.selectTarget,
            // eventLoadedName: "loaded",
        });

        window.addEventListener(this.identifier + ':update', this.updateList.bind(this));
        // this.element.addEventListener(this.identifier+':loaded', async (event) => {
        //     await this.processResponseToast(this.message);//show toast
        //     this.selectTarget.disabled = false;
        //     // if(this.selectTarget.dataset.model && this.selectTarget.dataset.model.startsWith('norender|')){
        //
        //         setTimeout(() => {
        //             console.log('change');
        //             this.selectTarget.dispatchEvent(new Event('change'));
        //         }, 3000);
        //
        //     // }
        // });

        if (this.hasDetailTarget) {
            this.detailTarget.addEventListener('click', (event) => {
                event.preventDefault();

                if (!this.selectTarget.value) {
                    alert('Debe seleccionar al menos un valor para el detalle del mismo.');//TODO: dar la posibilidad de personalizar
                    return;
                }

                const modalBody = document.querySelector('#'+this.detailIdValue+' .modal-body-content');
                this.addChildsNodes(this.detailPlaceholderValue, modalBody);

                this.addChildsNodes(this.detailTitleValue, document.querySelector('#'+this.detailIdValue+' .modal-title'));
                Modal.getOrCreateInstance(document.querySelector('#'+this.detailIdValue)).show();

                const eventDetail = new CustomEvent('eventDetail', {
                    detail: {
                        url: this.detailUrlValue.replace('0', this.selectTarget.value),
                        container: modalBody,
                        eventLoadedName: 'loadDetail',
                    }
                });
                this.refreshContent(eventDetail);
            });
        }

        if (this.hasAddTarget) {
            this.addTarget.addEventListener('click', (event) => {
                event.preventDefault();

                const modalBody = document.querySelector('#'+this.addIdValue+' .modal-body-content');
                this.addChildsNodes(this.addPlaceholderValue, modalBody);

                this.addChildsNodes(this.addTitleValue, document.querySelector('#'+this.addIdValue+' .modal-title'));
                Modal.getOrCreateInstance(document.querySelector('#'+this.addIdValue)).show();

                const eventDetail = new CustomEvent('eventAdd', {
                    detail: {
                        url: this.addUrlValue,
                        container: modalBody,
                        eventLoadedName: 'loadAdd',
                    }
                });
                this.refreshContent(eventDetail);
            });
        }


    }

    /**
     * When add form show
     * @param event
     */
    async updateList(event) {
        if (this.addIdValue === event.detail.modal) {
            if (this.addUrlValue.length > 0) {
                // this.selectTarget.disabled = true;
                // this.addChildsNodes("<option>Cargando...</option>", this.selectTarget);
                //
                // this.message = event.detail.response;
                // for(let item in event.detail.data){
                //     event.detail.url = this.urlValue.replace('0', event.detail.data[item]);
                // }
                // this.refreshContent(event);
                // setTimeout(()=>{
                //     this.selectTarget.selectedIndex = (this.selectTarget.options.length - 1);
                //     // console.log('updateList');
                // }, 3000);
            }

            await this.processResponseToast(event.detail.response);
        }
    }

}
