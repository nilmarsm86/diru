import {useContentLoader} from "../../behaviors/content-loader/use-content-loader.js";
import AbstractController from "../AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
export default class extends AbstractController {
    static targets = ['province', 'municipality'];
    static values = {
        url: {type: String, default: ''},
    };

    connect() {
        useContentLoader(this, {
            // url: '',
            container: this.municipalityTarget,
            // eventLoadedName: "loaded",
        });

        this.provinceTarget.addEventListener('change', this.selectProvince.bind(this));
    }

    /**
     * When add form show
     * @param event
     */
    selectProvince(event){
        event.detail = {};
        event.detail.url = this.urlValue.replace('0', this.provinceTarget.value);
        this.refreshContent(event);
        // this.municipalityTarget.disabled = (!this.provinceTarget.value);
        // this.municipalityTarget.dispatchEvent(new Event('change'));
    }

}
