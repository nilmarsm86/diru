import { Controller } from '@hotwired/stimulus';
import {useProcessResponse} from "../behaviors/use-process-response.js";
import {generateCsrfToken} from "./csrf_protection_controller.js";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = ['formContainer', 'listContainer'];
    static values = {
        listContainerUrl: String
    };

    connect() {
        useProcessResponse(this);
    }

    /**
     * Generate form token
     */
    generateCsrfToken(){
        let formElement = this.formContainerTarget.querySelector('form');
        if(formElement){
            generateCsrfToken(formElement);
        }
    }

    /**
     * When form send success
     * @param event
     */
    async sendFormSuccess(event){
        const form = event.detail.form;
        form.reset();

        const response = event.detail.response;
        console.log(response);
        await this.processResponseToast(response);

        let url = (this.listContainerUrlValue.length === 0) ? document.location : this.listContainerUrlValue;
        super.dispatch('onSendFormSuccess',{detail:{container:this.listContainerTarget, url: url}});
    }

    /**
     * When add form show
     * @param event
     */
    showFormContent(event){
        event.preventDefault();

        super.dispatch('onShowFormContent',{detail:{container:this.formContainerTarget, url: event.currentTarget.href}});
        this.containerHash(this.formContainerTarget);
    }

    /**
     * When hide form content
     * @param event
     */
    hideFormContent(event){
        event.preventDefault();

        super.dispatch('onHideFormContent');
        // this.containerHash(this.listContainerTarget);
        if(document.location.hash !== '#top'){
            document.location.hash = '#top';
        }
    }

    containerHash(container){
        let hash = '#'+container.getAttribute('id');
        if(document.location.hash !== hash){
            document.location.hash = hash;
        }
    }

    async state(event){
        //event.preventDefault();

        const request = new Request(event.currentTarget.href, {
            headers: new Headers({'X-Requested-With': 'XMLHttpRequest'}),
        });

        let data = new FormData();
        data.set('id', event.params.id);
        data.set('state', event.params.state);

        let response = await fetch(request, {
            method: 'POST',
            body: new URLSearchParams(data),
        });
        await this.processResponseToast(response);

        super.dispatch('onChangeState',{detail:{container:this.listContainerTarget, url: document.location}});
    }

    // place(event){
    //     event.preventDefault();
    //
    //     //const place = event.currentTarget.innerText.trim().replace(/ /g, '+');
    //     const url = new URL(document.location);
    //     url.searchParams.set('filter', event.currentTarget.innerText);
    //
    //     document.location = url.toString();
    // }


}
