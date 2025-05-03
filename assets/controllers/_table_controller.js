import { Controller } from '@hotwired/stimulus';
import Backdrop from "../components/backdrop.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['backdropComponent']

    connect(){
        if(!customElements.get("backdrop-component")){
            customElements.define("backdrop-component", Backdrop);//register un webcomponent
        }

        // this.element.addEventListener('select-navigate-querystring:onChange', this.goToFirstPage.bind(this));
        // this.element.querySelectorAll('.page-link').forEach((pageLink) => {
        //     pageLink.addEventListener('click',this.navigate.bind(this));
        // });
    }

    /**
     * Navigate
     * @param event
     */
    navigate(event){
        event.preventDefault();

        this.showBackdrop();
        document.location = event.currentTarget.href;
    }

    /**
     * When change amount got to first page
     * @param event
     */
    goToFirstPage(event){
        if(event.detail.url.searchParams.has('page')){
            event.detail.url.searchParams.set('page', '1');
        }
        this.showBackdrop();
    }

    /**
     * Show backdrop
     * @param event
     */
    showBackdrop(event){
        this.backdropComponentTarget?.show();
    }

    /**
     * Hide backdrop
     * @param event
     */
    hideBackdrop(event){
        this.backdropComponentTarget?.hide();
    }

    /**
     * Filter data in table
     * @param event
     */
    filter(event){
        event.preventDefault();
        this.showBackdrop();
        event.currentTarget.submit();
    }

    /**
     * Filter data in table
     * @param event
     */
    filterSearch(event){
        this.showBackdrop();
        this.element.querySelector('form').submit();
    }

}
