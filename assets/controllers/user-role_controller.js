import { Controller } from '@hotwired/stimulus';
import {Toast} from "bootstrap";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        user: Number,
        urlAddRole: String,
        urlRemoveRole: String,
        urlState: String,
    }

    async onChangeRole(event){
        let checkAction = async () => {
            return await this.doRequest(this.urlAddRoleValue, 'role', event.currentTarget.value);
        };
        let uncheckAction = async () => {
            return await this.doRequest(this.urlRemoveRoleValue, 'role', event.currentTarget.value);
        };
        await this.onChange(event, 'role', checkAction, uncheckAction);
    }

    async onChangeState(event){
        let checkAction = async () => {
            return await this.doRequest(this.urlStateValue, 'action', 'activate');
        };
        let uncheckAction = async () => {
            return await this.doRequest(this.urlStateValue, 'action', 'dectivate');
        };
        await this.onChange(event, 'state', checkAction, uncheckAction);
    }

    async onChange(event, type, checkAction, uncheckAction){
        if(type === 'state'){
            if(!event.target.checked && !confirm('Está seguro que desea desactivar el usuario?')){
                event.target.checked = true;
                return ;
            }
        }

        this.dispatch('startChange');
        let response = null;

        if(event.target.checked){
            response = await checkAction();
            if(!response.ok){
                event.target.checked = false;
                return ;
            }

            //si se activo el usuario que se activen sus checkbox
            let roleChecks = this.element.querySelectorAll('input[disabled]');
            roleChecks.forEach((check) => {
                if(!check.classList.contains('client')){
                    check.disabled = false;
                }
            });
        }else{
            response = await uncheckAction();
            if(!response.ok){
                event.target.checked = true;
            }

            if(response.status === 422){
                event.target.disabled = true;
            }

            //si se desactivo el usuario que se desactiven sus checkbox
            let roleChecks = this.element.querySelectorAll('input');
            roleChecks.forEach((check) => {
                if(!check.classList.contains('client') && !check.classList.contains('form-check-input')){
                    check.disabled = true;
                }
            });
        }
        await this.processResponse(response);
        this.dispatch('endChange');
    }

    async processResponse(response){
        const responseText = await response.text();
        const nodes = new DOMParser().parseFromString(responseText, 'text/html').body.childNodes;
        let id = nodes[0].id;
        document.querySelector('.toast-container').appendChild(nodes[0]);

        const toastBootstrap = Toast.getOrCreateInstance(document.querySelector(`#${id}`));
        toastBootstrap.show();
    }

    async doRequest(path, option2, value2){
        const url = new URL(path, document.location.origin);
        url.searchParams.set('fetch', '1');
        const request = new Request(url.toString(), {
            headers: new Headers({'X-Requested-With': 'XMLHttpRequest'}),
        });

        let data = new FormData();
        data.set('user', this.userValue);
        data.set(option2, value2);

        return await fetch(request, {
            method: 'POST',
            body: new URLSearchParams(data),
        });
    }

}
