import AbstractController from "./AbstractController.js";
import {useProcessResponse} from "../behaviors/use-process-response.js";
import {useContentLoader} from "../behaviors/content-loader/use-content-loader.js";
import {HIDE as BACKDROP_HIDE, SHOW as BACKDROP_SHOW} from "./twig/backdrop/backdrop_controller.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends AbstractController {
    static targets = ['percent', 'button', 'import'];
    static values = {
        urlSaveData: {type: String, default: ''},
        urlLoadData: {type: String, default: ''},
        urlResetData: {type: String, default: ''},
        estimatedAdjust: {type: Number, default: 0},
    };

    connect() {
        useProcessResponse(this);
        useContentLoader(this, {
            url: this.urlLoadDataValue,
            container: document.querySelector('.data-container'),
        });

        this.uSDollar = new Intl.NumberFormat('es-CU', {
            style: 'currency',
            currency: 'CUP',
        });

        // this.element.addEventListener('separate-concept:loaded', (event) => {
        //     const listBackdrop = document.querySelector('[data-id=table-backdrop]');
        //     const backdrop = super.getController(listBackdrop, 'twig/backdrop/backdrop');
        //     backdrop.dispatch(BACKDROP_HIDE, {detail: {id: listBackdrop.dataset.id}});
        // });
    }

    async save() {
        const request = new Request(this.urlSaveDataValue, {
            headers: new Headers({
                'X-Requested-With': 'XMLHttpRequest'
            })
        });

        const response = await fetch(request, {
            method: 'post',
            body: new URLSearchParams({
                'percent': this.percentTarget.value
            }),
        });

        await this.processResponseToast(response);

        //poner el valor del importe
        this.importTarget.innerText = this.uSDollar.format((this.estimatedAdjustValue * this.percentTarget.value / 100) / 100);

        //mostrar backdrop
        const listBackdrop = document.querySelector('[data-id=table-backdrop]');
        const backdrop = super.getController(listBackdrop, 'twig/backdrop/backdrop');
        backdrop.dispatch(BACKDROP_SHOW, {detail: {id: listBackdrop.dataset.id}});

        const eventDetail = new CustomEvent('eventDetail', {detail: {}});
        this.refreshContent(eventDetail);
    }

    async saveImport() {
        const request = new Request(this.urlSaveDataValue, {
            headers: new Headers({
                'X-Requested-With': 'XMLHttpRequest'
            })
        });

        const response = await fetch(request, {
            method: 'post',
            body: new URLSearchParams({
                'import': this.importTarget.value.replace(',', '')
            }),
        });

        await this.processResponseToast(response);

        //poner el valor del importe
        this.percentTarget.innerText = (this.importTarget.value * 100) / this.estimatedAdjustValue;

        //mostrar backdrop
        const listBackdrop = document.querySelector('[data-id=table-backdrop]');
        const backdrop = super.getController(listBackdrop, 'twig/backdrop/backdrop');
        backdrop.dispatch(BACKDROP_SHOW, {detail: {id: listBackdrop.dataset.id}});

        const eventDetail = new CustomEvent('eventDetail', {detail: {}});
        this.refreshContent(eventDetail);
    }

    async reset() {
        const request = new Request(this.urlResetDataValue, {
            headers: new Headers({
                'X-Requested-With': 'XMLHttpRequest'
            })
        });

        const response = await fetch(request, {
            method: 'get',
        });

        await this.processResponseToast(response);

        //mostrar backdrop
        const listBackdrop = document.querySelector('[data-id=table-backdrop]');
        const backdrop = super.getController(listBackdrop, 'twig/backdrop/backdrop');
        backdrop.dispatch(BACKDROP_SHOW, {detail: {id: listBackdrop.dataset.id}});

        const eventDetail = new CustomEvent('eventDetail', {detail: {}});
        this.refreshContent(eventDetail);
    }
}
