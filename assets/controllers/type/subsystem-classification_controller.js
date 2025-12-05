import {useContentLoader} from "../../behaviors/content-loader/use-content-loader.js";
import AbstractController from "../AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
export default class extends AbstractController {
    static targets = ['type', 'subType'];
    static values = {
        url: {type: String, default: ''},
    };

    connect() {
        useContentLoader(this, {
            // url: '',
            container: this.subTypeTarget,
            // eventLoadedName: "loaded",
        });

        this.typeTarget.addEventListener('change', this.selectType.bind(this));
    }

    /**
     * When select type
     * @param event
     */
    selectType(event){
        event.detail = {};
        event.detail.url = this.urlValue.replace('0', this.typeTarget.value);
        this.refreshContent(event);
        // this.municipalityTarget.disabled = (!this.provinceTarget.value);
        // this.municipalityTarget.dispatchEvent(new Event('change'));
    }

}
